<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HealthTimelineController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole(UserRole::Patient), 403);

        $profile = $request->user()->patientProfile;
        abort_if($profile === null, 404);

        return $this->timelineView($profile);
    }

    public function show(Request $request, PatientProfile $patientProfile): View
    {
        abort_unless(
            $request->user()->is_active
            && $request->user()->hasRole(UserRole::Nurse, UserRole::Clinician),
            403,
        );

        return $this->timelineView($patientProfile);
    }

    private function timelineView(PatientProfile $profile): View
    {
        $profile->load('user');

        $encounters = $profile->clinicalEncounters()
            ->with([
                'symptoms',
                'vitalSignObservations',
                'clinicalResults',
                'assignedClinician',
            ])
            ->latest('occurred_at')
            ->limit(50)
            ->get();

        $timeline = $encounters->flatMap(function ($encounter): array {
            $items = [[
                'type' => 'encounter',
                'occurred_at' => $encounter->occurred_at,
                'title' => $encounter->type->label().' encounter',
                'description' => $encounter->chief_complaint,
                'meta' => $encounter->status->label(),
                'badge_classes' => $encounter->status->badgeClasses(),
                'href' => route('encounters.show', $encounter),
            ]];

            foreach ($encounter->symptoms as $symptom) {
                $items[] = [
                    'type' => 'symptom',
                    'occurred_at' => $symptom->started_at ?? $symptom->created_at,
                    'title' => $symptom->name,
                    'description' => $symptom->notes ?: ($symptom->duration ? 'Reported duration: '.$symptom->duration : 'Structured symptom recorded.'),
                    'meta' => $symptom->severity->label(),
                    'badge_classes' => $symptom->severity->badgeClasses(),
                    'href' => route('encounters.show', $encounter).'#observations',
                ];
            }

            foreach ($encounter->vitalSignObservations as $observation) {
                $items[] = [
                    'type' => 'vitals',
                    'occurred_at' => $observation->measured_at,
                    'title' => 'Vital signs recorded',
                    'description' => collect($observation->formattedReadings())->map(fn ($value, $label) => $label.': '.$value)->take(4)->implode(' · '),
                    'meta' => $observation->source->label(),
                    'badge_classes' => 'bg-sky-100 text-sky-800',
                    'href' => route('encounters.show', $encounter).'#observations',
                ];
            }

            foreach ($encounter->clinicalResults as $result) {
                $items[] = [
                    'type' => 'result',
                    'occurred_at' => $result->resulted_at,
                    'title' => $result->test_name,
                    'description' => $result->displayValue().($result->reference_range ? ' · Reference: '.$result->reference_range : ''),
                    'meta' => $result->flag->label(),
                    'badge_classes' => $result->flag->badgeClasses(),
                    'href' => route('encounters.show', $encounter).'#observations',
                ];
            }

            return $items;
        })->sortByDesc('occurred_at')->take(100)->values();

        $vitals = $encounters->flatMap->vitalSignObservations
            ->whereNotNull('verified_at')
            ->sortBy('measured_at')
            ->take(-30)
            ->values();

        return view('timeline.index', [
            'profile' => $profile,
            'timeline' => $timeline,
            'trendCharts' => $this->trendCharts($vitals),
            'summary' => [
                'encounters' => $encounters->count(),
                'symptoms' => $encounters->sum(fn ($encounter) => $encounter->symptoms->count()),
                'vitals' => $vitals->count(),
                'results' => $encounters->sum(fn ($encounter) => $encounter->clinicalResults->count()),
            ],
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function trendCharts(Collection $vitals): array
    {
        $definitions = [
            ['id' => 'temperature', 'title' => 'Temperature', 'unit' => '°C', 'fields' => [['temperature_c', 'Temperature', '#0f766e']]],
            ['id' => 'blood-pressure', 'title' => 'Blood pressure', 'unit' => 'mmHg', 'fields' => [['systolic_bp', 'Systolic', '#e11d48'], ['diastolic_bp', 'Diastolic', '#2563eb']]],
            ['id' => 'heart-rate', 'title' => 'Heart rate', 'unit' => 'bpm', 'fields' => [['heart_rate_bpm', 'Heart rate', '#7c3aed']]],
            ['id' => 'oxygen-saturation', 'title' => 'Oxygen saturation', 'unit' => '%', 'fields' => [['oxygen_saturation_percent', 'SpO₂', '#0284c7']]],
            ['id' => 'weight', 'title' => 'Weight', 'unit' => 'kg', 'fields' => [['weight_kg', 'Weight', '#b45309']]],
            ['id' => 'blood-glucose', 'title' => 'Blood glucose', 'unit' => 'mg/dL', 'fields' => [['blood_glucose_mg_dl', 'Blood glucose', '#be123c']]],
        ];

        return collect($definitions)->map(function (array $definition) use ($vitals): array {
            $relevant = $vitals->filter(fn ($vital) => collect($definition['fields'])->contains(fn ($field) => $vital->{$field[0]} !== null));

            return [
                'id' => $definition['id'],
                'title' => $definition['title'],
                'unit' => $definition['unit'],
                'labels' => $relevant->map(fn ($vital) => $vital->measured_at->format('j M, H:i'))->values()->all(),
                'datasets' => collect($definition['fields'])->map(fn ($field) => [
                    'label' => $field[1],
                    'data' => $relevant->map(fn ($vital) => $vital->{$field[0]} !== null ? (float) $vital->{$field[0]} : null)->values()->all(),
                    'borderColor' => $field[2],
                    'backgroundColor' => $field[2].'20',
                ])->all(),
                'point_count' => $relevant->count(),
            ];
        })->filter(fn (array $chart) => $chart['point_count'] > 0)->values()->all();
    }
}
