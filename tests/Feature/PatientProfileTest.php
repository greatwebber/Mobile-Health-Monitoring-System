<?php

namespace Tests\Feature;

use App\Enums\BloodGroup;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_unique_incomplete_patient_profile(): void
    {
        $this->post('/register', [
            'name' => 'New Patient',
            'email' => 'new.patient@example.test',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'new.patient@example.test')->firstOrFail();
        $profile = $user->patientProfile;

        $this->assertNotNull($profile);
        $this->assertSame(PatientProfile::numberFor($user), $profile->patient_number);
        $this->assertSame(0, $profile->completionPercentage());
        $this->assertNull($profile->profile_completed_at);
    }

    public function test_patient_can_view_and_update_their_health_profile(): void
    {
        $user = User::factory()->patient()->create();
        $profile = PatientProfile::factory()->incomplete()->for($user)->create();

        $this->actingAs($user)
            ->get('/health-profile')
            ->assertOk()
            ->assertSee($profile->patient_number);

        $this->actingAs($user)
            ->patch('/health-profile', [
                'date_of_birth' => '2001-05-12',
                'sex_at_birth' => SexAtBirth::Female->value,
                'phone' => '08012345678',
                'address' => 'Federal Polytechnic Ede campus',
                'emergency_contact_name' => 'Ade Adeyemi',
                'emergency_contact_phone' => '08087654321',
                'blood_group' => BloodGroup::OPositive->value,
                'genotype' => Genotype::AA->value,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('patient-profile.edit'));

        $profile->refresh();

        $this->assertSame(SexAtBirth::Female, $profile->sex_at_birth);
        $this->assertSame(BloodGroup::OPositive, $profile->blood_group);
        $this->assertSame(Genotype::AA, $profile->genotype);
        $this->assertSame(100, $profile->completionPercentage());
        $this->assertNotNull($profile->profile_completed_at);
    }

    public function test_health_profile_rejects_invalid_clinical_and_contact_values(): void
    {
        $user = User::factory()->patient()->create();
        PatientProfile::factory()->incomplete()->for($user)->create();

        $this->actingAs($user)
            ->from('/health-profile')
            ->patch('/health-profile', [
                'date_of_birth' => now()->addDay()->format('Y-m-d'),
                'sex_at_birth' => 'unsupported',
                'phone' => 'not-a-phone',
                'blood_group' => 'X+',
                'genotype' => 'ZZ',
            ])
            ->assertRedirect('/health-profile')
            ->assertSessionHasErrors([
                'date_of_birth',
                'sex_at_birth',
                'phone',
                'blood_group',
                'genotype',
            ]);
    }
}
