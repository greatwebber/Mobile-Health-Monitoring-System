<?php

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Enums\EncounterStatus;
use App\Models\Appointment;
use App\Models\ClinicalAlert;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\ConsentRecord;
use App\Models\EncounterSymptom;
use App\Models\MedicalHistory;
use App\Models\PatientProfile;
use App\Models\StaffProfile;
use App\Models\ThresholdRule;
use App\Models\User;
use App\Models\VitalSignObservation;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SensitiveModelObserver
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function created(Model $model): void
    {
        $this->record($this->createdEvent($model), $model);
    }

    public function updated(Model $model): void
    {
        $changedFields = collect(array_keys($model->getChanges()))
            ->reject(fn (string $field) => in_array($field, ['updated_at', 'remember_token', 'password'], true))
            ->values()
            ->all();

        if ($changedFields === []) {
            return;
        }

        $this->record($this->updatedEvent($model), $model, ['changed_fields' => $changedFields]);
    }

    public function deleted(Model $model): void
    {
        $this->record($this->prefix($model).'.deleted', $model);
    }

    private function record(string $event, Model $model, array $metadata = []): void
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            return;
        }

        $this->auditLogger->record($event, $model, $actor, $metadata);
    }

    private function createdEvent(Model $model): string
    {
        return match (true) {
            $model instanceof ConsentRecord => $model->granted ? 'consent.granted' : 'consent.withdrawn',
            $model instanceof Appointment => 'appointment.scheduled',
            default => $this->prefix($model).'.created',
        };
    }

    private function updatedEvent(Model $model): string
    {
        return match (true) {
            $model instanceof ThresholdRule && $model->wasChanged('approved_at') => 'threshold_rule.approved',
            $model instanceof ThresholdRule && $model->wasChanged('deactivated_at') => 'threshold_rule.deactivated',
            $model instanceof ClinicalAlert && $model->wasChanged('resolved_at') => 'clinical_alert.resolved',
            $model instanceof ClinicalAlert && $model->wasChanged('acknowledged_at') => 'clinical_alert.acknowledged',
            $model instanceof Appointment && $model->wasChanged('status_changed_at') => 'appointment.'.$this->appointmentOutcome($model),
            $model instanceof Appointment && $model->wasChanged('scheduled_at') => 'appointment.rescheduled',
            $model instanceof ClinicalEncounter && $model->status === EncounterStatus::Completed => 'clinical_encounter.completed',
            default => $this->prefix($model).'.updated',
        };
    }

    private function appointmentOutcome(Appointment $appointment): string
    {
        return match ($appointment->status) {
            AppointmentStatus::Completed => 'completed',
            AppointmentStatus::Cancelled => 'cancelled',
            AppointmentStatus::NoShow => 'no_show',
            AppointmentStatus::Scheduled => 'updated',
        };
    }

    private function prefix(Model $model): string
    {
        return match (true) {
            $model instanceof User => 'account',
            $model instanceof PatientProfile => 'patient_profile',
            $model instanceof StaffProfile => 'staff_profile',
            $model instanceof MedicalHistory => 'medical_history',
            $model instanceof ClinicalEncounter => 'clinical_encounter',
            $model instanceof EncounterSymptom => 'encounter_symptom',
            $model instanceof VitalSignObservation => 'vital_sign_observation',
            $model instanceof ClinicalResult => 'clinical_result',
            $model instanceof ThresholdRule => 'threshold_rule',
            $model instanceof ClinicalAlert => 'clinical_alert',
            $model instanceof Appointment => 'appointment',
            default => Str::snake(class_basename($model)),
        };
    }
}
