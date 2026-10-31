<?php

use App\Http\Controllers\ClinicalAlertController;
use App\Http\Controllers\ClinicalEncounterController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncounterObservationController;
use App\Http\Controllers\HealthTimelineController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\PatientProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThresholdRuleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/health-profile', [PatientProfileController::class, 'edit'])->name('patient-profile.edit');
    Route::patch('/health-profile', [PatientProfileController::class, 'update'])->name('patient-profile.update');
    Route::get('/medical-history', [MedicalHistoryController::class, 'edit'])->name('medical-history.edit');
    Route::patch('/medical-history', [MedicalHistoryController::class, 'update'])->name('medical-history.update');
    Route::get('/consents', [ConsentController::class, 'index'])->name('consents.index');
    Route::post('/consents/{type}', [ConsentController::class, 'store'])->name('consents.store');
    Route::resource('encounters', ClinicalEncounterController::class)->except(['destroy']);
    Route::post('/encounters/{encounter}/symptoms', [EncounterObservationController::class, 'storeSymptom'])->name('encounters.symptoms.store');
    Route::post('/encounters/{encounter}/vital-signs', [EncounterObservationController::class, 'storeVitals'])->name('encounters.vital-signs.store');
    Route::post('/encounters/{encounter}/clinical-results', [EncounterObservationController::class, 'storeClinicalResult'])->name('encounters.clinical-results.store');
    Route::get('/timeline', [HealthTimelineController::class, 'index'])->name('timeline.index');
    Route::get('/patients/{patientProfile}/timeline', [HealthTimelineController::class, 'show'])->name('timeline.patient');
    Route::get('/alerts', [ClinicalAlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/{alert}', [ClinicalAlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts/{alert}/acknowledge', [ClinicalAlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/{alert}/resolve', [ClinicalAlertController::class, 'resolve'])->name('alerts.resolve');
    Route::get('/threshold-rules', [ThresholdRuleController::class, 'index'])->name('threshold-rules.index');
    Route::post('/threshold-rules', [ThresholdRuleController::class, 'store'])->name('threshold-rules.store');
    Route::post('/threshold-rules/{thresholdRule}/approve', [ThresholdRuleController::class, 'approve'])->name('threshold-rules.approve');
    Route::post('/threshold-rules/{thresholdRule}/deactivate', [ThresholdRuleController::class, 'deactivate'])->name('threshold-rules.deactivate');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
