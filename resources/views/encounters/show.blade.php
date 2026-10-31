@php
    $history = $encounter->patientProfile->medicalHistory;
    $historyItems = collect([
        'Allergies' => $history?->allergies,
        'Chronic conditions' => $history?->chronic_conditions,
        'Current medications' => $history?->current_medications,
    ])->filter(fn ($items) => filled($items));
    $timelineRoute = Auth::user()->hasRole(App\Enums\UserRole::Patient)
        ? route('timeline.index')
        : route('timeline.patient', $encounter->patientProfile);
@endphp

<x-app-layout>
    <x-slot name="title">Encounter {{ $encounter->encounter_number }}</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('encounters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-900">← {{ Auth::user()->hasRole(App\Enums\UserRole::Patient) ? 'Visit history' : 'Encounter register' }}</a>

        @if (session('status') === 'encounter-created')
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">The clinical encounter was opened successfully.</div>
        @elseif (session('status') === 'encounter-updated')
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">The encounter record was updated successfully.</div>
        @elseif (session('status') === 'symptom-recorded')
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">The symptom was recorded and verified.</div>
        @elseif (session('status') === 'vitals-recorded')
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">The vital-sign snapshot was recorded and verified.</div>
        @elseif (session('status') === 'clinical-result-recorded')
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">The clinical result was recorded and verified.</div>
        @endif

        <section class="relative mt-6 overflow-hidden rounded-[2rem] bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10 sm:p-8">
            <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-teal-300/15"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-teal-200">{{ $encounter->type->label() }}</span>
                        <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $encounter->status->badgeClasses() }}">{{ $encounter->status->label() }}</span>
                    </div>
                    <h1 class="mt-5 text-2xl font-black tracking-tight sm:text-3xl">{{ $encounter->patientProfile->user->name }}</h1>
                    <p class="mt-2 text-sm font-semibold text-slate-300">{{ $encounter->patientProfile->patient_number }} · {{ $encounter->encounter_number }}</p>
                </div>
                <div class="text-sm sm:text-right">
                    <p class="font-black">{{ $encounter->occurred_at->format('j F Y') }}</p>
                    <p class="mt-1 text-slate-400">{{ $encounter->occurred_at->format('g:i a') }}</p>
                    @can('update', $encounter)
                        <a href="{{ route('encounters.edit', $encounter) }}" class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-teal-400 px-4 font-black text-slate-950 transition hover:bg-teal-300">Continue documentation</a>
                    @endcan
                    <a href="{{ $timelineRoute }}" class="mt-2 inline-flex min-h-11 items-center rounded-xl border border-white/20 px-4 font-black text-white transition hover:bg-white/10">View health timeline</a>
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.25fr_.75fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Reason for visit</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $encounter->chief_complaint }}</p>
                </section>

                @include('encounters.partials.observations')

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h2 class="text-lg font-black text-slate-950">Clinical documentation</h2>
                    <div class="mt-6 grid gap-6 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Assessment notes</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $encounter->clinical_notes ?: 'No clinical notes recorded yet.' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Disposition and follow-up</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $encounter->disposition ?: 'No disposition recorded yet.' }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-black text-slate-950">Care team</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Opened by</dt><dd class="mt-1 font-semibold text-slate-800">{{ $encounter->createdBy->name }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Assigned clinician</dt><dd class="mt-1 font-semibold text-slate-800">{{ $encounter->assignedClinician?->name ?? 'Not assigned' }}</dd></div>
                        @if ($encounter->completed_at)
                            <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Completed</dt><dd class="mt-1 font-semibold text-slate-800">{{ $encounter->completed_at->format('j M Y, g:i a') }}</dd></div>
                        @endif
                    </dl>
                </section>

                @unless (Auth::user()->hasRole(App\Enums\UserRole::Patient))
                    <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
                        <p class="text-xs font-black uppercase tracking-[0.15em] text-amber-800">Safety context</p>
                        <h2 class="mt-2 font-black text-amber-950">Known medical history</h2>
                        @forelse ($historyItems as $label => $items)
                            <div class="mt-4 border-t border-amber-200 pt-4 first:border-0 first:pt-0">
                                <p class="text-xs font-black text-amber-900">{{ $label }}</p>
                                <p class="mt-1 text-sm leading-6 text-amber-950">{{ implode(', ', $items) }}</p>
                            </div>
                        @empty
                            <p class="mt-3 text-sm leading-6 text-amber-900">No allergies, conditions or medications are recorded.</p>
                        @endforelse
                    </section>
                @endunless
            </aside>
        </div>
    </div>
</x-app-layout>
