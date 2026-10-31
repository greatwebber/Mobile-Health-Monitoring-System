<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BottomNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_sees_the_mobile_first_patient_navigation_dock(): void
    {
        $patient = User::factory()->patient()->create();
        PatientProfile::factory()->for($patient)->create();

        $this->actingAs($patient)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('data-testid="bottom-navigation"', escape: false)
            ->assertSee('data-nav-label="Home"', escape: false)
            ->assertSee('data-nav-label="Profile"', escape: false)
            ->assertSee('data-nav-label="Record"', escape: false)
            ->assertSee('data-nav-label="Timeline"', escape: false)
            ->assertSee('data-nav-label="Visits"', escape: false)
            ->assertDontSee('data-nav-label="Patients"', escape: false);
    }

    public function test_clinical_staff_see_the_role_specific_navigation_dock(): void
    {
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('data-testid="bottom-navigation"', escape: false)
            ->assertSee('data-nav-label="Home"', escape: false)
            ->assertSee('data-nav-label="Patients"', escape: false)
            ->assertSee('data-nav-label="Triage"', escape: false)
            ->assertSee('data-nav-label="Alerts"', escape: false)
            ->assertSee('data-nav-label="Reports"', escape: false)
            ->assertDontSee('data-nav-label="Profile"', escape: false);
    }

    public function test_active_dock_item_is_exposed_to_assistive_technology(): void
    {
        $patient = User::factory()->patient()->create();
        PatientProfile::factory()->for($patient)->create();

        $this->actingAs($patient)
            ->get('/health-profile')
            ->assertOk()
            ->assertSee('data-nav-label="Profile"', escape: false)
            ->assertSee('aria-current="page"', escape: false);
    }

    public function test_patient_timeline_navigation_is_enabled_and_active(): void
    {
        $profile = PatientProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('timeline.index'))
            ->assertOk()
            ->assertSee('data-nav-label="Timeline"', escape: false)
            ->assertSee('aria-current="page"', escape: false)
            ->assertDontSee('title="Timeline — coming soon"', escape: false);
    }
}
