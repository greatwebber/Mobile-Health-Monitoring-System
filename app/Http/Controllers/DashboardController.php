<?php

namespace App\Http\Controllers;

use App\Enums\AlertStatus;
use App\Enums\UserRole;
use App\Models\ClinicalAlert;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $canViewAlerts = $user->hasRole(UserRole::Patient, UserRole::Nurse, UserRole::Clinician);
        $activeAlerts = 0;

        if ($canViewAlerts) {
            $query = ClinicalAlert::query()->where('status', '!=', AlertStatus::Resolved->value);

            if ($user->hasRole(UserRole::Patient)) {
                $query->where('patient_profile_id', $user->patientProfile?->id ?? 0);
            }

            $activeAlerts = $query->count();
        }

        return view('dashboard', compact('activeAlerts', 'canViewAlerts'));
    }
}
