<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use App\Enums\UserRole;
use App\Http\Requests\UpdatePatientProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PatientProfileController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->hasRole(UserRole::Patient), 403);

        $profile = $request->user()->patientProfile;

        abort_if($profile === null, 404, 'A patient profile has not been created for this account.');
        Gate::authorize('update', $profile);

        return view('patient-profile.edit', [
            'profile' => $profile,
            'sexOptions' => SexAtBirth::cases(),
            'bloodGroups' => BloodGroup::cases(),
            'genotypes' => Genotype::cases(),
        ]);
    }

    public function update(UpdatePatientProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->patientProfile;

        $profile->fill($request->validated())->save();
        $profile->refreshCompletionStatus();

        return redirect()->route('patient-profile.edit')->with('status', 'health-profile-updated');
    }
}
