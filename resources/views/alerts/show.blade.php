<x-app-layout>
    <x-slot name="title">Alert review</x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('alerts.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-900">← Alert queue</a>

        @if (session('status'))
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">{{ session('status') === 'alert-resolved' ? 'The alert was resolved with a documented clinical response.' : 'The alert was acknowledged for clinical review.' }}</div>
        @endif

        <section class="relative mt-6 overflow-hidden rounded-[2rem] bg-slate-950 p-6 text-white shadow-xl sm:p-8">
            <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-rose-300/15"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div><div class="flex flex-wrap gap-2"><span class="rounded-full px-3 py-1.5 text-xs font-black {{ $alert->severity->badgeClasses() }}">{{ $alert->severity->label() }}</span><span class="rounded-full px-3 py-1.5 text-xs font-black {{ $alert->status->badgeClasses() }}">{{ $alert->status->label() }}</span></div><p class="mt-5 text-xs font-bold uppercase tracking-wider text-rose-200">{{ $alert->metric->label() }}</p><h1 class="mt-2 text-4xl font-black tracking-tight">{{ $alert->displayValue() }}</h1><p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">{{ $alert->message }}</p></div>
                <div class="text-sm sm:text-right"><p class="font-black">Detected {{ $alert->detected_at->format('j M Y') }}</p><p class="mt-1 text-slate-400">{{ $alert->detected_at->format('g:i a') }}</p></div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Patient and encounter</p>
                    <h2 class="mt-2 text-xl font-black text-slate-950">{{ $alert->patientProfile->user->name }}</h2>
                    <p class="mt-1 text-sm font-semibold text-slate-500">{{ $alert->patientProfile->patient_number }} · {{ $alert->clinicalEncounter->encounter_number }}</p>
                    <div class="mt-5 flex flex-wrap gap-3"><a href="{{ route('encounters.show', $alert->clinicalEncounter) }}" class="button-secondary">Open encounter</a>@unless(Auth::user()->hasRole(App\Enums\UserRole::Patient))<a href="{{ route('timeline.patient', $alert->patientProfile) }}" class="button-secondary">View timeline</a>@endunless</div>
                </section>

                <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6 sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-amber-800">Approved review guidance</p>
                    <p class="mt-3 text-sm leading-7 text-amber-950">{{ $alert->thresholdRule->guidance }}</p>
                    <p class="mt-4 border-t border-amber-200 pt-4 text-xs leading-5 text-amber-800">Rule: {{ $alert->rule_name }} · version {{ $alert->rule_version }} · approved by {{ $alert->thresholdRule->approvedBy?->name ?? 'Unavailable' }}</p>
                </section>

                @if ($alert->resolution_notes)
                    <section class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 sm:p-8"><p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-800">Resolution record</p><p class="mt-3 whitespace-pre-line text-sm leading-7 text-emerald-950">{{ $alert->resolution_notes }}</p><p class="mt-4 text-xs font-semibold text-emerald-800">Resolved by {{ $alert->resolvedBy?->name }} on {{ $alert->resolved_at?->format('j M Y, g:i a') }}</p></section>
                @endif
            </div>

            <aside class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-black text-slate-950">Review trail</h2>
                    <dl class="mt-5 space-y-4 text-sm"><div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Rule range</dt><dd class="mt-1 font-semibold text-slate-800">{{ $alert->thresholdRule->rangeLabel() }}</dd></div><div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Acknowledged</dt><dd class="mt-1 font-semibold text-slate-800">{{ $alert->acknowledgedBy ? $alert->acknowledgedBy->name.' · '.$alert->acknowledged_at->format('j M Y, g:i a') : 'Not yet' }}</dd></div><div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Resolved</dt><dd class="mt-1 font-semibold text-slate-800">{{ $alert->resolvedBy ? $alert->resolvedBy->name.' · '.$alert->resolved_at->format('j M Y, g:i a') : 'Not yet' }}</dd></div></dl>
                </section>

                @can('acknowledge', $alert)
                    <form method="POST" action="{{ route('alerts.acknowledge', $alert) }}" class="rounded-3xl border border-amber-200 bg-white p-6 shadow-sm">@csrf<h2 class="font-black text-slate-950">Acknowledge review</h2><p class="mt-2 text-xs leading-5 text-slate-500">This records that a care team member has seen the alert. It does not resolve it.</p><button class="button-primary mt-5 w-full" type="submit">Acknowledge alert</button></form>
                @endcan

                @can('resolve', $alert)
                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="rounded-3xl border border-emerald-200 bg-white p-6 shadow-sm">@csrf<h2 class="font-black text-slate-950">Resolve alert</h2><label for="resolution_notes" class="mt-4 block text-sm font-black text-slate-800">Clinical response and outcome</label><textarea id="resolution_notes" name="resolution_notes" rows="5" required minlength="10" class="mt-2 block w-full rounded-2xl border-slate-300" placeholder="Document assessment and action taken.">{{ old('resolution_notes') }}</textarea><x-input-error :messages="$errors->get('resolution_notes')" class="mt-2"/><button class="button-primary mt-5 w-full" type="submit">Resolve with documentation</button></form>
                @endcan
            </aside>
        </div>
    </div>
</x-app-layout>
