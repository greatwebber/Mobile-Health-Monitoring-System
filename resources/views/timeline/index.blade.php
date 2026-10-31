@php
    $isOwner = Auth::id() === $profile->user_id;
    $typeStyles = [
        'encounter' => ['Visit', 'bg-teal-100 text-teal-800', 'M8 3h8v4H8V3ZM6 5H5a2 2 0 0 0-2 2v13h18V7a2 2 0 0 0-2-2h-1M8 12h8m-8 4h5'],
        'symptom' => ['Symptom', 'bg-rose-100 text-rose-700', 'M12 3v18M3 12h18'],
        'vitals' => ['Vitals', 'bg-sky-100 text-sky-700', 'M4 13h4l2.1-5 3.7 10 2.1-5H20'],
        'result' => ['Result', 'bg-violet-100 text-violet-700', 'M8 3h8v4H8V3ZM6 5H5a2 2 0 0 0-2 2v13h18V7a2 2 0 0 0-2-2h-1M8 12h8m-8 4h5'],
    ];
@endphp

<x-app-layout>
    <x-slot name="title">Health timeline</x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        @unless ($isOwner)
            <a href="{{ route('encounters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-900">← Encounter register</a>
        @endunless

        <div class="{{ $isOwner ? '' : 'mt-5' }} flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Longitudinal health record</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $isOwner ? 'Your health timeline' : $profile->user->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Verified visits, symptoms, vital signs and clinical results arranged by when they occurred.</p>
                @unless ($isOwner)<p class="mt-2 text-xs font-bold text-teal-700">{{ $profile->patient_number }}</p>@endunless
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900 lg:max-w-md"><strong>Trend charts support review, not diagnosis.</strong> They do not apply clinical thresholds or generate alerts.</div>
        </div>

        <section class="mt-7 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Timeline summary">
            @foreach ([['Visits', $summary['encounters'], 'teal'], ['Symptoms', $summary['symptoms'], 'rose'], ['Vital snapshots', $summary['vitals'], 'sky'], ['Clinical results', $summary['results'], 'violet']] as [$label, $value, $tone])
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-[0.68rem] font-black uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-3xl font-black tracking-tight text-slate-950">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-7" aria-labelledby="trend-heading">
            <div class="flex items-end justify-between gap-4 px-1">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Verified measurements</p>
                    <h2 id="trend-heading" class="mt-1 text-xl font-black tracking-tight text-slate-950">Health trends</h2>
                </div>
                <span class="hidden text-xs font-semibold text-slate-500 sm:block">Up to 30 recent snapshots per chart</span>
            </div>

            @if (count($trendCharts))
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    @foreach ($trendCharts as $chart)
                        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-health-chart>
                            <div class="flex items-center justify-between gap-3">
                                <div><h3 class="font-black text-slate-950">{{ $chart['title'] }}</h3><p class="mt-1 text-xs text-slate-500">{{ $chart['point_count'] }} verified {{ Str::plural('reading', $chart['point_count']) }} · {{ $chart['unit'] }}</p></div>
                                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider text-teal-700">Trend</span>
                            </div>
                            <div class="relative mt-5 h-64 w-full">
                                <canvas role="img" aria-label="{{ $chart['title'] }} trend chart">{{ $chart['title'] }} trend: {{ implode(', ', $chart['labels']) }}</canvas>
                            </div>
                            <script type="application/json" data-chart-config>@json($chart)</script>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sky-700 shadow-sm ring-1 ring-slate-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 18 9 11l4 3 7-9M4 21h16"/></svg>
                    </div>
                    <h3 class="mt-4 font-black text-slate-900">No verified measurements yet</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Trend charts will appear after authorized staff record vital-sign snapshots during care.</p>
                </div>
            @endif
        </section>

        <section class="mt-9" aria-labelledby="history-heading">
            <div class="px-1">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Clinical history</p>
                <h2 id="history-heading" class="mt-1 text-xl font-black tracking-tight text-slate-950">Timeline</h2>
            </div>

            <div class="relative mt-5">
                @if ($timeline->isNotEmpty())
                    <div class="absolute bottom-5 left-[1.35rem] top-5 w-px bg-gradient-to-b from-teal-300 via-slate-200 to-transparent sm:left-[1.6rem]"></div>
                @endif
                <div class="space-y-4">
                    @forelse ($timeline as $item)
                        @php [$typeLabel, $typeClasses, $iconPath] = $typeStyles[$item['type']]; @endphp
                        <article class="relative grid grid-cols-[2.75rem_1fr] gap-3 sm:grid-cols-[3.25rem_1fr] sm:gap-4">
                            <div class="relative z-10 flex h-11 w-11 items-center justify-center rounded-2xl border-4 border-slate-50 {{ $typeClasses }} shadow-sm sm:h-12 sm:w-12">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $iconPath }}"/></svg>
                            </div>
                            <a href="{{ $item['href'] }}" class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-[0.65rem] font-black uppercase tracking-wider text-teal-700">{{ $typeLabel }}</span>
                                            <span class="rounded-full px-2.5 py-1 text-[0.62rem] font-black uppercase tracking-wider {{ $item['badge_classes'] }}">{{ $item['meta'] }}</span>
                                        </div>
                                        <h3 class="mt-2 font-black text-slate-950 group-hover:text-teal-800">{{ $item['title'] }}</h3>
                                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $item['description'] }}</p>
                                    </div>
                                    <time datetime="{{ $item['occurred_at']->toIso8601String() }}" class="shrink-0 text-xs font-bold text-slate-500 sm:text-right">{{ $item['occurred_at']->format('j M Y') }}<span class="mt-1 block font-medium text-slate-400">{{ $item['occurred_at']->format('g:i a') }}</span></time>
                                </div>
                            </a>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                            <h3 class="font-black text-slate-900">No health activity recorded yet</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Encounters and verified clinical observations will appear here in chronological order.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
