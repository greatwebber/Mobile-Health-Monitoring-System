<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_creates_an_active_patient_account(): void
    {
        $this->post('/register', [
            'name' => 'Amina Adeyemi',
            'email' => 'amina@example.test',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => UserRole::Administrator->value,
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'amina@example.test')->firstOrFail();

        $this->assertSame(UserRole::Patient, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole(UserRole::Patient));
        $this->assertFalse($user->hasRole(UserRole::Administrator));
    }

    public function test_inactive_accounts_cannot_authenticate(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }
}
