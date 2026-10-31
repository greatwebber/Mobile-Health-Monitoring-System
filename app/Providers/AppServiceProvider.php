<?php

namespace App\Providers;

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
use App\Observers\SensitiveModelObserver;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::useScriptTagAttributes(['data-turbo-track' => 'reload']);
        Vite::useStyleTagAttributes(['data-turbo-track' => 'reload']);

        foreach ([
            User::class,
            PatientProfile::class,
            StaffProfile::class,
            MedicalHistory::class,
            ConsentRecord::class,
            ClinicalEncounter::class,
            EncounterSymptom::class,
            VitalSignObservation::class,
            ClinicalResult::class,
            ThresholdRule::class,
            ClinicalAlert::class,
            Appointment::class,
        ] as $model) {
            $model::observe(SensitiveModelObserver::class);
        }

        Event::listen(Login::class, fn (Login $event) => app(AuditLogger::class)
            ->record('authentication.login', $event->user, $event->user));
        Event::listen(Logout::class, fn (Logout $event) => app(AuditLogger::class)
            ->record('authentication.logout', $event->user, $event->user));
        Event::listen(Registered::class, fn (Registered $event) => app(AuditLogger::class)
            ->record('account.registered', $event->user, $event->user));
        Event::listen(Verified::class, fn (Verified $event) => app(AuditLogger::class)
            ->record('account.email_verified', $event->user, $event->user));
        Event::listen(PasswordReset::class, fn (PasswordReset $event) => app(AuditLogger::class)
            ->record('authentication.password_reset', $event->user, $event->user));
        Event::listen(Failed::class, function (Failed $event): void {
            $identifier = mb_strtolower((string) ($event->credentials['email'] ?? 'unknown'));
            app(AuditLogger::class)->record('authentication.failed', metadata: [
                'account_identifier_hash' => hash('sha256', $identifier),
            ]);
        });
    }
}
