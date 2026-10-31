<?php

namespace App\Http\Controllers;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Enums\UserRole;
use App\Http\Requests\StoreClinicalEncounterRequest;
use App\Http\Requests\UpdateClinicalEncounterRequest;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClinicalEncounterController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ClinicalEncounter::query()
            ->with(['patientProfile.user', 'createdBy', 'assignedClinician'])
            ->latest('occurred_at');

        if ($user->hasRole(UserRole::Patient)) {
            abort_if($user->patientProfile === null, 404);
            $query->where('patient_profile_id', $user->patientProfile->id);
        } else {
            Gate::authorize('viewAny', ClinicalEncounter::class);

            $query->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('search'));
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('encounter_number', 'like', "%{$search}%")
                        ->orWhereHas('patientProfile', function (Builder $profileQuery) use ($search): void {
                            $profileQuery->where('patient_number', 'like', "%{$search}%")
                                ->orWhereHas('user', fn (Builder $userQuery) => $userQuery
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%"));
                        });
                });
            });

            $query->when(
                EncounterStatus::tryFrom((string) $request->input('status')),
                fn (Builder $query, EncounterStatus $status) => $query->where('status', $status->value),
            );
        }

        return view('encounters.index', [
            'encounters' => $query->paginate(12)->withQueryString(),
            'statuses' => EncounterStatus::cases(),
            'isPatient' => $user->hasRole(UserRole::Patient),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', ClinicalEncounter::class);

        return view('encounters.create', [
            'patients' => PatientProfile::query()->with('user')->orderBy('patient_number')->get(),
            'clinicians' => $this->clinicians(),
            'types' => EncounterType::cases(),
        ]);
    }

    public function store(StoreClinicalEncounterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $encounter = ClinicalEncounter::create([
            ...$validated,
            'encounter_number' => ClinicalEncounter::generateNumber(),
            'status' => EncounterStatus::Open,
            'created_by_user_id' => $request->user()->id,
            'assigned_clinician_id' => $validated['assigned_clinician_id']
                ?? ($request->user()->hasRole(UserRole::Clinician) ? $request->user()->id : null),
        ]);

        return redirect()->route('encounters.show', $encounter)
            ->with('status', 'encounter-created');
    }

    public function show(ClinicalEncounter $encounter): View
    {
        Gate::authorize('view', $encounter);
        $encounter->load([
            'patientProfile.user',
            'patientProfile.medicalHistory',
            'createdBy',
            'assignedClinician',
            'symptoms' => fn ($query) => $query->with(['recordedBy', 'verifiedBy'])->latest(),
            'vitalSignObservations' => fn ($query) => $query->with(['recordedBy', 'verifiedBy'])->latest('measured_at'),
            'clinicalResults' => fn ($query) => $query->with(['recordedBy', 'verifiedBy'])->latest('resulted_at'),
        ]);

        return view('encounters.show', compact('encounter'));
    }

    public function edit(ClinicalEncounter $encounter): View
    {
        Gate::authorize('update', $encounter);
        $encounter->load('patientProfile.user');

        return view('encounters.edit', [
            'encounter' => $encounter,
            'clinicians' => $this->clinicians(),
            'types' => EncounterType::cases(),
            'statuses' => EncounterStatus::cases(),
        ]);
    }

    public function update(UpdateClinicalEncounterRequest $request, ClinicalEncounter $encounter): RedirectResponse
    {
        $validated = $request->validated();
        $status = EncounterStatus::from($validated['status']);

        $encounter->update([
            ...$validated,
            'completed_at' => $status === EncounterStatus::Completed ? now() : null,
        ]);

        return redirect()->route('encounters.show', $encounter)
            ->with('status', 'encounter-updated');
    }

    private function clinicians()
    {
        return User::query()
            ->where('role', UserRole::Clinician)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
