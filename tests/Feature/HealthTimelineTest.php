<?php

namespace Tests\Feature;

use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\EncounterSymptom;
use App\Models\PatientProfile;
use App\Models\User;
use App\Models\VitalSignObservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_timeline_combines_only_their_clinical_events(): void
    {
        $profile = PatientProfile::factory()->create();
        $encounter = ClinicalEncounter::factory()->for($profile)->create([
            'chief_complaint' => 'Timeline visit marker',
        ]);
        EncounterSymptom::factory()->for($encounter, 'clinicalEncounter')->create(['name' => 'Timeline symptom marker']);
        VitalSignObservation::factory()->for($encounter, 'clinicalEncounter')->create([
            'notes' => 'Timeline vital marker',
            'verified_at' => now(),
        ]);
        ClinicalResult::factory()->for($encounter, 'clinicalEncounter')->create(['test_name' => 'Timeline result marker']);
        ClinicalEncounter::factory()->create(['chief_complaint' => 'Other patient secret marker']);

        $this->actingAs($profile->user)
            ->get(route('timeline.index'))
            ->assertOk()
            ->assertSee('Timeline visit marker')
            ->assertSee('Timeline symptom marker')
            ->assertSee('Temperature: 36.8 °C')
            ->assertSee('Timeline result marker')
            ->assertDontSee('Other patient secret marker');
    }

    public function test_only_owner_nurse_and_clinician_can_access_a_patient_timeline(): void
    {
        $profile = PatientProfile::factory()->create();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();
        $administrator = User::factory()->administrator()->create();
        $otherPatient = PatientProfile::factory()->create()->user;

        $this->actingAs($nurse)
            ->get(route('timeline.patient', $profile))
            ->assertOk();

        $this->actingAs($clinician)
            ->get(route('timeline.patient', $profile))
            ->assertOk();

        $this->actingAs($administrator)
            ->get(route('timeline.patient', $profile))
            ->assertForbidden();

        $this->actingAs($otherPatient)
            ->get(route('timeline.patient', $profile))
            ->assertForbidden();

        $this->get(route('timeline.index'))->assertOk();
    }

    public function test_trend_charts_use_verified_vitals_in_chronological_order(): void
    {
        $profile = PatientProfile::factory()->create();
        $encounter = ClinicalEncounter::factory()->for($profile)->create();

        VitalSignObservation::factory()->for($encounter, 'clinicalEncounter')->create([
            'measured_at' => now()->subDays(2),
            'temperature_c' => 36.5,
            'heart_rate_bpm' => 70,
            'verified_at' => now()->subDays(2),
        ]);
        VitalSignObservation::factory()->for($encounter, 'clinicalEncounter')->create([
            'measured_at' => now()->subDay(),
            'temperature_c' => 37.1,
            'heart_rate_bpm' => 76,
            'verified_at' => now()->subDay(),
        ]);
        VitalSignObservation::factory()->for($encounter, 'clinicalEncounter')->create([
            'measured_at' => now(),
            'temperature_c' => 39.9,
            'heart_rate_bpm' => 120,
            'verified_at' => null,
        ]);

        $response = $this->actingAs($profile->user)->get(route('timeline.index'));
        $temperature = collect($response->viewData('trendCharts'))->firstWhere('id', 'temperature');
        $heartRate = collect($response->viewData('trendCharts'))->firstWhere('id', 'heart-rate');

        $response->assertOk()->assertSee('Temperature trend chart');
        $this->assertSame([36.5, 37.1], $temperature['datasets'][0]['data']);
        $this->assertSame([70.0, 76.0], $heartRate['datasets'][0]['data']);
        $this->assertSame(2, $temperature['point_count']);
    }

    public function test_empty_timeline_has_a_clear_zero_state(): void
    {
        $profile = PatientProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('timeline.index'))
            ->assertOk()
            ->assertSee('No verified measurements yet')
            ->assertSee('No health activity recorded yet');
    }

    public function test_chart_runtime_is_turbo_aware_and_uses_chart_js(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));
        $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertStringContainsString("import('chart.js')", $script);
        $this->assertStringContainsString('if (!chartContainers.length) return;', $script);
        $this->assertStringContainsString("document.addEventListener('turbo:before-render', destroyHealthCharts)", $script);
        $this->assertStringContainsString("document.addEventListener('turbo:load', initializeHealthCharts)", $script);
        $this->assertSame('^4.6.1', $package['dependencies']['chart.js']);
    }
}
