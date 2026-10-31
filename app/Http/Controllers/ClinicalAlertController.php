<?php

namespace App\Http\Controllers;

use App\Enums\AlertSeverity;
use App\Enums\AlertStatus;
use App\Enums\UserRole;
use App\Http\Requests\ResolveClinicalAlertRequest;
use App\Models\ClinicalAlert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClinicalAlertController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ClinicalAlert::query()
            ->with(['patientProfile.user', 'clinicalEncounter'])
            ->latest('detected_at');

        if ($user->hasRole(UserRole::Patient)) {
            abort_if($user->patientProfile === null, 404);
            $query->where('patient_profile_id', $user->patientProfile->id);
        } else {
            Gate::authorize('viewAny', ClinicalAlert::class);
        }

        $query->when(
            AlertStatus::tryFrom((string) $request->input('status')),
            fn (Builder $query, AlertStatus $status) => $query->where('status', $status->value),
        )->when(
            AlertSeverity::tryFrom((string) $request->input('severity')),
            fn (Builder $query, AlertSeverity $severity) => $query->where('severity', $severity->value),
        );

        return view('alerts.index', [
            'alerts' => $query->paginate(20)->withQueryString(),
            'statuses' => AlertStatus::cases(),
            'severities' => AlertSeverity::cases(),
            'isPatient' => $user->hasRole(UserRole::Patient),
        ]);
    }

    public function show(ClinicalAlert $alert): View
    {
        Gate::authorize('view', $alert);
        $alert->load([
            'patientProfile.user',
            'clinicalEncounter',
            'vitalSignObservation',
            'thresholdRule.approvedBy',
            'acknowledgedBy',
            'resolvedBy',
        ]);

        return view('alerts.show', compact('alert'));
    }

    public function acknowledge(ClinicalAlert $alert): RedirectResponse
    {
        Gate::authorize('acknowledge', $alert);

        $alert->update([
            'status' => AlertStatus::Acknowledged,
            'acknowledged_by_user_id' => request()->user()->id,
            'acknowledged_at' => now(),
        ]);

        return redirect()->route('alerts.show', $alert)->with('status', 'alert-acknowledged');
    }

    public function resolve(ResolveClinicalAlertRequest $request, ClinicalAlert $alert): RedirectResponse
    {
        $alert->update([
            'status' => AlertStatus::Resolved,
            'acknowledged_by_user_id' => $alert->acknowledged_by_user_id ?? $request->user()->id,
            'acknowledged_at' => $alert->acknowledged_at ?? now(),
            'resolved_by_user_id' => $request->user()->id,
            'resolved_at' => now(),
            'resolution_notes' => trim($request->validated('resolution_notes')),
        ]);

        return redirect()->route('alerts.show', $alert)->with('status', 'alert-resolved');
    }
}
