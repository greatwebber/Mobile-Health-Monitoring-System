<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagedAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_provision_an_active_staff_account_and_profile(): void
    {
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($administrator)
            ->post(route('admin.accounts.store'), $this->validCreatePayload())
            ->assertRedirect();

        $account = User::query()->where('email', 'new.clinician@edehealth.test')->firstOrFail();
        $this->assertSame(UserRole::Clinician, $account->role);
        $this->assertTrue($account->is_active);
        $this->assertNotNull($account->email_verified_at);
        $this->assertTrue(Hash::check('Temporary-Password-123!', $account->password));
        $this->assertSame(StaffProfile::numberFor($account), $account->staffProfile->staff_number);
        $this->assertSame('Clinical Services', $account->staffProfile->department);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $administrator->id,
            'event' => 'account.created',
            'auditable_type' => User::class,
            'auditable_id' => $account->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $administrator->id,
            'event' => 'staff_profile.created',
        ]);
    }

    public function test_non_administrator_cannot_access_or_provision_managed_accounts(): void
    {
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)->get(route('admin.accounts.index'))->assertForbidden();
        $this->get(route('admin.accounts.create'))->assertForbidden();
        $this->post(route('admin.accounts.store'), $this->validCreatePayload())->assertForbidden();
    }

    public function test_administrator_can_update_profile_and_status_but_not_change_existing_role(): void
    {
        $administrator = User::factory()->administrator()->create();
        $clinician = User::factory()->clinician()->create();
        $profile = StaffProfile::factory()->for($clinician)->create();

        $this->actingAs($administrator)
            ->put(route('admin.accounts.update', $clinician), [
                ...$this->validUpdatePayload($clinician),
                'name' => 'Dr Updated Name',
                'department' => 'Outpatient Services',
                'role' => UserRole::Administrator->value,
            ])
            ->assertRedirect(route('admin.accounts.edit', $clinician));

        $clinician->refresh();
        $this->assertSame('Dr Updated Name', $clinician->name);
        $this->assertSame(UserRole::Clinician, $clinician->role);
        $this->assertSame('Outpatient Services', $profile->refresh()->department);

        $log = AuditLog::query()->where('event', 'account.updated')->latest('id')->firstOrFail();
        $this->assertNotContains('role', $log->metadata['changed_fields']);
    }

    public function test_administrator_cannot_deactivate_self(): void
    {
        $administrator = User::factory()->administrator()->create();
        StaffProfile::factory()->for($administrator)->create();

        $this->actingAs($administrator)
            ->put(route('admin.accounts.update', $administrator), [
                ...$this->validUpdatePayload($administrator),
                'is_active' => 0,
            ])
            ->assertSessionHasErrors('is_active');

        $this->assertTrue($administrator->fresh()->is_active);
    }

    public function test_deactivation_terminates_database_sessions_and_inactive_sessions_are_rejected(): void
    {
        $administrator = User::factory()->administrator()->create();
        $nurse = User::factory()->nurse()->create();
        StaffProfile::factory()->for($nurse)->create();

        DB::table('sessions')->insert([
            'id' => 'managed-account-session',
            'user_id' => $nurse->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Feature test',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($administrator)
            ->put(route('admin.accounts.update', $nurse), [
                ...$this->validUpdatePayload($nurse),
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.accounts.edit', $nurse));

        $this->assertFalse($nurse->fresh()->is_active);
        $this->assertDatabaseMissing('sessions', ['user_id' => $nurse->id]);

        $this->actingAs($nurse->refresh())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_account_register_can_filter_by_role_status_and_identifier(): void
    {
        $administrator = User::factory()->administrator()->create();
        $activeClinician = User::factory()->clinician()->create(['name' => 'Visible Clinician']);
        StaffProfile::factory()->for($activeClinician)->create(['staff_number' => 'FPE-MC-STAFF-SEARCH']);
        User::factory()->nurse()->inactive()->create(['name' => 'Hidden Nurse']);

        $this->actingAs($administrator)
            ->get(route('admin.accounts.index', [
                'search' => 'FPE-MC-STAFF-SEARCH',
                'role' => UserRole::Clinician->value,
                'status' => 'active',
            ]))
            ->assertOk()
            ->assertSee('Visible Clinician')
            ->assertDontSee('Hidden Nurse');
    }

    /** @return array<string, mixed> */
    private function validCreatePayload(): array
    {
        return [
            'name' => 'Dr New Clinician',
            'email' => 'new.clinician@edehealth.test',
            'role' => UserRole::Clinician->value,
            'professional_title' => 'Medical Officer',
            'department' => 'Clinical Services',
            'license_number' => 'MDCN-TEST-1001',
            'phone' => '08012345678',
            'password' => 'Temporary-Password-123!',
            'password_confirmation' => 'Temporary-Password-123!',
        ];
    }

    /** @return array<string, mixed> */
    private function validUpdatePayload(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => 1,
            'professional_title' => $user->staffProfile?->professional_title ?? 'Nursing Officer',
            'department' => $user->staffProfile?->department ?? 'Nursing Services',
            'license_number' => $user->staffProfile?->license_number,
            'phone' => $user->staffProfile?->phone,
        ];
    }
}
