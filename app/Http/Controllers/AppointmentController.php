<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\TransitionAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Appointment::query()
            ->with(['patientProfile.user', 'assignedClinician'])
            ->orderByRaw('scheduled_at < ? asc', [now()])
            ->orderBy('scheduled_at');

        if ($user->hasRole(UserRole::Patient)) {
            abort_if($user->patientProfile === null, 404);
            $query->where('patient_profile_id', $user->patientProfile->id);
        } else {
            Gate::authorize('viewAny', Appointment::class);
            $query->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('search'));
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('appointment_number', 'like', "%{$search}%")
                        ->orWhereHas('patientProfile', fn (Builder $profileQuery) => $profileQuery
                            ->where('patient_number', 'like', "%{$search}%")
                            ->orWhereHas('user', fn (Builder $userQuery) => $userQuery
                                ->where('name', 'like', "%{$search}%")));
                });
            });
        }

        $query->when(
            AppointmentStatus::tryFrom((string) $request->input('status')),
            fn (Builder $query, AppointmentStatus $status) => $query->where('status', $status->value),
        );

        return view('appointments.index', [
            'appointments' => $query->paginate(15)->withQueryString(),
            'statuses' => AppointmentStatus::cases(),
            'isPatient' => $user->hasRole(UserRole::Patient),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Appointment::class);

        return view('appointments.create', [
            ...$this->formData(),
            'selectedPatientId' => $request->integer('patient'),
            'selectedEncounterId' => $request->integer('encounter'),
        ]);
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $appointment = Appointment::create([
            ...$request->validated(),
            'appointment_number' => Appointment::generateNumber(),
            'status' => AppointmentStatus::Scheduled,
            'scheduled_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('appointments.show', $appointment)->with('status', 'appointment-created');
    }

    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);
        $appointment->load([
            'patientProfile.user',
            'clinicalEncounter',
            'assignedClinician',
            'scheduledBy',
            'statusChangedBy',
        ]);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('update', $appointment);

        return view('appointments.edit', [
            'appointment' => $appointment,
            ...$this->formData(),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update($request->validated());

        return redirect()->route('appointments.show', $appointment)->with('status', 'appointment-updated');
    }

    public function transition(TransitionAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'status' => AppointmentStatus::from($request->validated('status')),
            'outcome_notes' => trim($request->validated('outcome_notes')),
            'status_changed_by_user_id' => $request->user()->id,
            'status_changed_at' => now(),
        ]);

        return redirect()->route('appointments.show', $appointment)->with('status', 'appointment-status-updated');
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return [
            'patients' => PatientProfile::query()->with('user')->orderBy('patient_number')->get(),
            'clinicians' => User::query()
                ->where('role', UserRole::Clinician)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'encounters' => ClinicalEncounter::query()
                ->with('patientProfile.user')
                ->latest('occurred_at')
                ->limit(200)
                ->get(),
        ];
    }
}
