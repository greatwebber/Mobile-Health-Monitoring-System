<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_are_seeded_with_expected_roles_and_password(): void
    {
        config()->set('demo.seed_enabled', true);
        config()->set('demo.seed_password', 'A-secure-demo-password');

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('patient_profiles', 1);
        $this->assertDatabaseCount('staff_profiles', 3);
        $this->assertDatabaseCount('medical_histories', 1);
        $this->assertDatabaseCount('consent_records', 4);
        $this->assertDatabaseCount('clinical_encounters', 1);
        $this->assertDatabaseCount('encounter_symptoms', 1);
        $this->assertDatabaseCount('vital_sign_observations', 3);
        $this->assertDatabaseCount('clinical_results', 1);
        $this->assertDatabaseCount('threshold_rules', 1);
        $this->assertDatabaseCount('clinical_alerts', 0);

        $expectedRoles = [
            'admin@edehealth.test' => UserRole::Administrator,
            'clinician@edehealth.test' => UserRole::Clinician,
            'nurse@edehealth.test' => UserRole::Nurse,
            'patient@edehealth.test' => UserRole::Patient,
        ];

        foreach ($expectedRoles as $email => $role) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertSame($role, $user->role);
            $this->assertTrue($user->is_active);
            $this->assertTrue(Hash::check('A-secure-demo-password', $user->password));
        }
    }

    public function test_demo_account_seeding_is_idempotent(): void
    {
        config()->set('demo.seed_enabled', true);
        config()->set('demo.seed_password', 'A-secure-demo-password');

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('patient_profiles', 1);
        $this->assertDatabaseCount('staff_profiles', 3);
        $this->assertDatabaseCount('medical_histories', 1);
        $this->assertDatabaseCount('consent_records', 4);
        $this->assertDatabaseCount('clinical_encounters', 1);
        $this->assertDatabaseCount('encounter_symptoms', 1);
        $this->assertDatabaseCount('vital_sign_observations', 3);
        $this->assertDatabaseCount('clinical_results', 1);
        $this->assertDatabaseCount('threshold_rules', 1);
        $this->assertDatabaseCount('clinical_alerts', 0);
    }
}
