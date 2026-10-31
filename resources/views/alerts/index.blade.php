<x-app-layout>
    <x-slot name="title">Clinical alerts</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-xs font-black uppercase tracking-[0.18em] text-rose-700">Review queue</p><h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $isPatient ? 'Your health alerts' : 'Clinical alerts' }}</h1><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $isPatient ? 'Measurements flagged for professional review. An alert is not a diagnosis.' : 'Review measurements detected by approved threshold rules and document the response.' }}</p></div>
            @if (! $isPatient && Auth::user()->hasRole(App\Enums\UserRole::Clinician, App\Enums\UserRole::Administrator))
                <a href="{{ route('threshold-rules.index') }}" class="button-secondary min-h-12">Threshold registry</a>
            @endif
        </div>

        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-950"><strong>Alerts require human review.</strong> They do not diagnose a condition, replace clinical judgment or confirm that an emergency action has occurred.</div>

        <form method="GET" action="{{ route('alerts.index') }}" class="mt-6 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_1fr_auto] sm:p-5">
            <select name="status" class="min-h-12 rounded-2xl border-slate-300"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>@endforeach</select>
            <select name="severity" class="min-h-12 rounded-2xl border-slate-300"><option value="">All priorities</option>@foreach($severities as $severity)<option value="{{ $severity->value }}" @selected(request('severity') === $severity->value)>{{ $severity->label() }}</option>@endforeach</select>
            <button class="button-secondary min-h-12" type="submit">Apply filters</button>
        </form>

        <div class="mt-6 space-y-4">
            @forelse ($alerts as $alert)
                <a href="{{ route('alerts.show', $alert) }}" class="group block rounded-3xl border {{ $alert->status === App\Enums\AlertStatus::Resolved ? 'border-slate-200' : 'border-rose-200' }} bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200 sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4"><span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl {{ $alert->severity->badgeClasses() }}"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Zm-8 10h4"/></svg></span><div><div class="flex flex-wrap items-center gap-2"><h2 class="font-black text-slate-950">{{ $alert->metric->label() }}</h2><span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $alert->severity->badgeClasses() }}">{{ $alert->severity->label() }}</span><span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $alert->status->badgeClasses() }}">{{ $alert->status->label() }}</span></div><p class="mt-2 text-sm text-slate-600">{{ $isPatient ? $alert->message : $alert->patientProfile->user->name.' · '.$alert->patientProfile->patient_number }}</p><p class="mt-1 text-xs font-bold text-slate-500">{{ $alert->displayValue() }} · {{ $alert->detected_at->format('j M Y, g:i a') }}</p></div></div>
                        <span class="text-xs font-black text-rose-700">Review alert →</span>
                    </div>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-100 text-emerald-700">✓</div><h2 class="mt-4 font-black text-slate-900">No alerts found</h2><p class="mt-2 text-sm text-slate-500">There are no alerts matching the selected filters.</p></div>
            @endforelse
        </div>
        @if ($alerts->hasPages())<div class="mt-6">{{ $alerts->links() }}</div>@endif
    </div>
</x-app-layout>
