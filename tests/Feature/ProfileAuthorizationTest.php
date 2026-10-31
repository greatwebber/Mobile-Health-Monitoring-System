<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_profile_access_follows_least_privilege_rules(): void
    {
        $owner = User::factory()->patient()->create();
        $otherPatient = User::factory()->patient()->create();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();
        $administrator = User::factory()->administrator()->create();
        $profile = PatientProfile::factory()->for($owner)->create();

        $this->assertTrue($owner->can('view', $profile));
        $this->assertTrue($owner->can('update', $profile));
        $this->assertFalse($otherPatient->can('view', $profile));
        $this->assertFalse($otherPatient->can('update', $profile));
        $this->assertTrue($nurse->can('view', $profile));
        $this->assertFalse($nurse->can('update', $profile));
        $this->assertTrue($clinician->can('view', $profile));
        $this->assertFalse($clinician->can('update', $profile));
        $this->assertTrue($administrator->can('view', $profile));
        $this->assertTrue($administrator->can('update', $profile));
    }

    public function test_staff_profile_access_is_restricted_to_the_owner_and_administrator(): void
    {
        $clinician = User::factory()->clinician()->create();
        $nurse = User::factory()->nurse()->create();
        $administrator = User::factory()->administrator()->create();
        $profile = StaffProfile::factory()->for($clinician)->create();

        $this->assertTrue($clinician->can('view', $profile));
        $this->assertFalse($clinician->can('update', $profile));
        $this->assertFalse($nurse->can('view', $profile));
        $this->assertTrue($administrator->can('view', $profile));
        $this->assertTrue($administrator->can('update', $profile));
    }

    public function test_non_patient_accounts_cannot_open_the_patient_self_service_form(): void
    {
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->get('/health-profile')
            ->assertForbidden();
    }
}
