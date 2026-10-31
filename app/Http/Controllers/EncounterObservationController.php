<?php

namespace App\Http\Controllers;

use App\Enums\ObservationSource;
use App\Http\Requests\StoreClinicalResultRequest;
use App\Http\Requests\StoreEncounterSymptomRequest;
use App\Http\Requests\StoreVitalSignObservationRequest;
use App\Models\ClinicalEncounter;
use App\Services\ThresholdAlertEvaluator;
use Illuminate\Http\RedirectResponse;

class EncounterObservationController extends Controller
{
    public function storeSymptom(StoreEncounterSymptomRequest $request, ClinicalEncounter $encounter): RedirectResponse
    {
        $encounter->symptoms()->create([
            ...$request->validated(),
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => $request->user()->id,
            'verified_by_user_id' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return $this->backToEncounter($encounter, 'symptom-recorded');
    }

    public function storeVitals(
        StoreVitalSignObservationRequest $request,
        ClinicalEncounter $encounter,
        ThresholdAlertEvaluator $alertEvaluator,
    ): RedirectResponse {
        $observation = $encounter->vitalSignObservations()->create([
            ...$request->validated(),
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => $request->user()->id,
            'verified_by_user_id' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $alertEvaluator->evaluate($observation);

        return $this->backToEncounter($encounter, 'vitals-recorded');
    }

    public function storeClinicalResult(StoreClinicalResultRequest $request, ClinicalEncounter $encounter): RedirectResponse
    {
        $encounter->clinicalResults()->create([
            ...$request->validated(),
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => $request->user()->id,
            'verified_by_user_id' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return $this->backToEncounter($encounter, 'clinical-result-recorded');
    }

    private function backToEncounter(ClinicalEncounter $encounter, string $status): RedirectResponse
    {
        return redirect()->to(route('encounters.show', $encounter).'#observations')
            ->with('status', $status);
    }
}
