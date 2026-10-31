<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\SexAtBirth;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_sensitive_access_attempt_is_recorded_with_resource_context(): void
    {
        $appointment = Appointment::factory()->create();
        $owner = $appointment->patientProfile->user;

        $this->actingAs($owner)
            ->get(route('appointments.show', $appointment))
            ->assertOk();

        $log = AuditLog::query()->where('event', 'access.attempted')->sole();
        $this->assertSame($owner->id, $log->actor_user_id);
        $this->assertSame(Appointment::class, $log->auditable_type);
        $this->assertSame($appointment->id, $log->auditable_id);
        $this->assertSame('appointments.show', $log->route_name);
        $this->assertSame('GET', $log->request_method);
    }

    public function test_sensitive_update_records_field_names_without_copying_clinical_values(): void
    {
        $profile = PatientProfile::factory()->incomplete()->create();
        $privateAddress = 'Private residence that must not appear in audit metadata';

        $this->actingAs($profile->user)
            ->patch(route('patient-profile.update'), [
                'date_of_birth' => '2001-05-12',
                'sex_at_birth' => SexAtBirth::Female->value,
                'phone' => '08012345678',
                'address' => $privateAddress,
                'emergency_contact_name' => 'Demo Contact',
                'emergency_contact_phone' => '08087654321',
            ])
            ->assertRedirect(route('patient-profile.edit'));

        $logs = AuditLog::query()->where('event', 'patient_profile.updated')->get();
        $this->assertNotEmpty($logs);
        $this->assertTrue($logs->contains(fn (AuditLog $log) => in_array('address', $log->metadata['changed_fields'] ?? [], true)));
        $this->assertStringNotContainsString($privateAddress, $logs->toJson());
        $this->assertStringNotContainsString('08012345678', $logs->toJson());
    }

    public function test_appointment_outcome_has_a_semantic_audit_event(): void
    {
        $appointment = Appointment::factory()->create(['scheduled_at' => now()->subHour()]);
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->post(route('appointments.transition', $appointment), [
                'status' => AppointmentStatus::Completed->value,
                'outcome_notes' => 'Patient attended and the follow-up review was completed.',
            ])
            ->assertRedirect(route('appointments.show', $appointment));

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $clinician->id,
            'event' => 'appointment.completed',
            'auditable_type' => Appointment::class,
            'auditable_id' => $appointment->id,
        ]);

        $log = AuditLog::query()->where('event', 'appointment.completed')->sole();
        $this->assertContains('status', $log->metadata['changed_fields']);
        $this->assertStringNotContainsString('follow-up review', $log->toJson());
    }

    public function test_successful_failed_and_logout_authentication_events_are_audited_safely(): void
    {
        $user = User::factory()->create(['email' => 'audit-user@example.test']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $failed = AuditLog::query()->where('event', 'authentication.failed')->sole();
        $this->assertNull($failed->actor_user_id);
        $this->assertArrayHasKey('account_identifier_hash', $failed->metadata);
        $this->assertStringNotContainsString($user->email, $failed->toJson());

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->post('/logout')->assertRedirect('/');

        $this->assertDatabaseHas('audit_logs', ['event' => 'authentication.login', 'actor_user_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['event' => 'authentication.logout', 'actor_user_id' => $user->id]);
    }

    public function test_only_administrator_can_review_append_only_audit_history(): void
    {
        $administrator = User::factory()->administrator()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)->get(route('audit-logs.index'))->assertForbidden();

        $this->actingAs($administrator)
            ->get(route('audit-logs.index'))
            ->assertOk()
            ->assertSee('Immutable audit trail');

        $log = AuditLog::query()->latest('id')->firstOrFail();
        $this->assertTrue($administrator->can('view', $log));
        $this->assertFalse($administrator->can('update', $log));
        $this->assertFalse($administrator->can('delete', $log));
        $this->assertFalse($administrator->can('create', AuditLog::class));
    }
}
