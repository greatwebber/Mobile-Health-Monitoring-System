@php
    $isPatient = Auth::user()->hasRole(\App\Enums\UserRole::Patient);
    $isAdministrator = Auth::user()->hasRole(\App\Enums\UserRole::Administrator);
    $patientProfile = $isPatient ? Auth::user()->patientProfile : null;
    $profileCompletion = $patientProfile?->completionPercentage() ?? 0;
    $firstName = str(Auth::user()->name)->before(' ');
@endphp

<x-app-layout>
    <x-slot name="title">Home</x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-9 lg:px-8 lg:py-12">
        <section class="relative overflow-hidden rounded-[2rem] bg-slate-950 px-6 py-7 text-white shadow-2xl shadow-slate-950/15 sm:px-9 sm:py-10 lg:px-12">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full border border-teal-300/15"></div>
            <div class="pointer-events-none absolute -right-6 top-2 h-52 w-52 rounded-full border border-teal-300/10"></div>
            <svg class="pointer-events-none absolute bottom-0 right-0 hidden h-full w-2/5 text-teal-400/10 lg:block" viewBox="0 0 420 220" fill="none" aria-hidden="true">
                <path d="M0 124h70l26-53 44 104 40-92 28 41h212" stroke="currentColor" stroke-width="9" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <div class="relative z-10 grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-teal-300/20 bg-teal-300/10 px-3 py-1.5 text-[0.68rem] font-black uppercase tracking-[0.16em] text-teal-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-300"></span>
                        {{ $isPatient ? 'Personal health space' : Auth::user()->role->label().' workspace' }}
                    </div>
                    <h1 class="mt-5 max-w-2xl text-3xl font-black tracking-[-0.035em] sm:text-4xl lg:text-5xl">
                        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $firstName }}.
                    </h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300 sm:text-base sm:leading-7">
                        {{ $isPatient
                            ? 'Your secure health overview brings records, trends and Medical Centre follow-up into one calm workspace.'
                            : 'Your clinical workspace is being prepared around safe triage, patient continuity and accountable decision support.' }}
                    </p>
                </div>

                @if ($isPatient)
                    <a href="{{ route('patient-profile.edit') }}" class="group flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur transition hover:bg-white/[0.12] focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-300/40 lg:w-72">
                        <div class="relative grid h-16 w-16 shrink-0 place-items-center rounded-full" style="background: conic-gradient(#2dd4bf {{ $profileCompletion }}%, rgba(255,255,255,.12) 0)">
                            <div class="grid h-12 w-12 place-items-center rounded-full bg-slate-950 text-sm font-black">{{ $profileCompletion }}%</div>
                        </div>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-teal-200">Health profile</span>
                            <span class="mt-1 block text-sm font-semibold text-white">{{ $profileCompletion === 100 ? 'Details are complete' : 'Continue your setup' }}</span>
                            <span class="mt-1 inline-flex items-center gap-1 text-xs text-slate-400 transition group-hover:text-teal-200">Review details <span aria-hidden="true">→</span></span>
                        </span>
                    </a>
                @elseif ($isAdministrator)
                    <a href="{{ route('threshold-rules.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 6h10m4 0h2M4 12h3m4 0h9M4 18h8m4 0h4M14 3v6M7 9v6m5 0v6"/></svg>
                        </span>
                        <span class="mt-3 block text-sm font-black text-slate-900">Threshold rules</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">Prepare rules for clinical approval</span>
                    </a>
                @else
                    <div class="flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur lg:w-72">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-teal-400/15 text-teal-200">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m8 12 2.5 2.5L16.5 8.5M12 3l8 3v5c0 5.2-3.2 8.5-8 10-4.8-1.5-8-4.8-8-10V6l8-3Z"/></svg>
                        </span>
                        <span>
                            <span class="block text-xs font-bold uppercase tracking-wider text-teal-200">Access level</span>
                            <span class="mt-1 block text-sm font-semibold">{{ Auth::user()->role->label() }}</span>
                            <span class="mt-1 block text-xs text-slate-400">Verified demo workspace</span>
                        </span>
                    </div>
                @endif
            </div>
        </section>

        <section class="mt-5" aria-labelledby="quick-actions-title">
            <div class="mb-3 flex items-center justify-between px-1">
                <h2 id="quick-actions-title" class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Quick actions</h2>
                <span class="text-[0.68rem] font-semibold text-slate-400">More tools arrive with each phase</span>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @if ($isPatient)
                    <a href="{{ route('patient-profile.edit') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-800 transition group-hover:bg-teal-700 group-hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8c.7-3.4 3.3-5.5 7-5.5s6.3 2.1 7 5.5"/></svg>
                        </span>
                        <span class="mt-3 block text-sm font-black text-slate-900">Update profile</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">Keep personal details current</span>
                    </a>
                @elseif ($isAdministrator)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm opacity-70">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8c.7-3.4 3.3-5.5 7-5.5s6.3 2.1 7 5.5"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">User accounts</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Administration module is upcoming</span>
                </div>
                @else
                    <a href="{{ route('encounters.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7-1a2.75 2.75 0 1 0 0-5.5M3.5 20c.4-3.8 2.5-6 6-6s5.6 2.2 6 6m1-6c2.5.2 3.8 2 4 4.5"/></svg>
                        </span>
                        <span class="mt-3 block text-sm font-black text-slate-900">Find patient</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">Review active and recent encounters</span>
                    </a>
                @endif

                @if ($isPatient)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm opacity-70">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 13h4l2.1-5 3.7 10 2.1-5H20"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Record vitals</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Coming with health monitoring</span>
                </div>
                @elseif ($isAdministrator)
                <a href="{{ route('audit-logs.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-lg hover:shadow-rose-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 transition group-hover:bg-rose-700 group-hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M6 3h9l3 3v15H6V3Zm3 7h6m-6 4h6m-6 4h4"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Audit trail</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Review sensitive access and changes</span>
                </a>
                @else
                <a href="{{ route('encounters.create') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-lg hover:shadow-rose-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 transition group-hover:bg-rose-700 group-hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 13h4l2.1-5 3.7 10 2.1-5H20"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Start triage</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Open a traceable patient encounter</span>
                </a>
                @endif

                @if ($canViewAppointments)
                <a href="{{ route('appointments.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-200">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 3v3m10-3v3M4.5 9.5h15M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Appointments</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">{{ $isPatient ? 'Review confirmed visits' : 'Coordinate follow-up care' }}</span>
                </a>
                @else
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm opacity-70">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 3v3m10-3v3M4.5 9.5h15M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Appointments</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Clinical access is separated</span>
                </div>
                @endif

                <a href="{{ route('profile.edit') }}" class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-lg hover:shadow-sky-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-sky-200">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-800 transition group-hover:bg-sky-700 group-hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm8-3.5 2-1.2-2-3.5-2.3.7a8 8 0 0 0-1.8-1L15.4 4h-4L11 7a8 8 0 0 0-1.8 1L7 7.3 5 10.8 7 12a8 8 0 0 0 0 2l-2 1.2L7 18.7l2.3-.7a8 8 0 0 0 1.8 1l.4 3h4l.4-3a8 8 0 0 0 1.8-1l2.3.7 2-3.5-2-1.2a8 8 0 0 0 0-2Z"/></svg>
                    </span>
                    <span class="mt-3 block text-sm font-black text-slate-900">Account</span>
                    <span class="mt-1 block text-xs leading-5 text-slate-500">Security and sign-in details</span>
                </a>
            </div>
        </section>

        <section class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Health summary">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $isPatient ? 'Recent vitals' : 'Patients today' }}</p>
                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                </div>
                <p class="mt-5 text-3xl font-black tracking-tight text-slate-950">—</p>
                <p class="mt-1 text-xs font-medium text-slate-500">No records available yet</p>
            </article>
            <{{ $canViewAlerts ? 'a' : 'article' }} @if ($canViewAlerts) href="{{ route('alerts.index') }}" @endif class="rounded-2xl border {{ $activeAlerts > 0 ? 'border-rose-200 bg-gradient-to-br from-rose-50 to-white' : 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-white' }} p-5 shadow-sm transition {{ $canViewAlerts ? 'hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200' : '' }}">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $activeAlerts > 0 ? 'text-rose-800' : 'text-emerald-800' }}">Active alerts</p>
                    <span class="h-2 w-2 rounded-full {{ $activeAlerts > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                </div>
                <p class="mt-5 text-3xl font-black tracking-tight {{ $activeAlerts > 0 ? 'text-rose-950' : 'text-emerald-950' }}">{{ $canViewAlerts ? $activeAlerts : '—' }}</p>
                <p class="mt-1 text-xs font-semibold {{ $activeAlerts > 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ ! $canViewAlerts ? 'Clinical access is separated' : ($activeAlerts > 0 ? 'Review required' : 'Nothing needs attention') }}</p>
            </{{ $canViewAlerts ? 'a' : 'article' }}>
            <{{ $canViewAppointments ? 'a' : 'article' }} @if ($canViewAppointments) href="{{ route('appointments.index') }}" @endif class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition {{ $canViewAppointments ? 'hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md focus:outline-none focus-visible:ring-4 focus-visible:ring-amber-200' : '' }}">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Appointments</p>
                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                </div>
                <p class="mt-5 text-3xl font-black tracking-tight text-slate-950">{{ $canViewAppointments ? $upcomingAppointments : '—' }}</p>
                <p class="mt-1 text-xs font-medium text-slate-500">{{ $nextAppointment ? 'Next: '.$nextAppointment->scheduled_at->format('j M, g:i a') : ($canViewAppointments ? 'No upcoming visit' : 'Clinical access is separated') }}</p>
            </{{ $canViewAppointments ? 'a' : 'article' }}>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Risk reviews</p>
                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                </div>
                <p class="mt-5 text-3xl font-black tracking-tight text-slate-950">—</p>
                <p class="mt-1 text-xs font-medium text-slate-500">Model not configured</p>
            </article>
        </section>

        <div class="mt-6 grid gap-5 lg:grid-cols-[1.35fr_.65fr]">
            <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Longitudinal record</p>
                        <h2 class="mt-2 text-xl font-black tracking-tight text-slate-950">{{ $isPatient ? 'Your health timeline' : 'Recent clinical activity' }}</h2>
                    </div>
                    <span class="hidden rounded-full bg-slate-100 px-3 py-1.5 text-[0.68rem] font-bold text-slate-600 sm:inline-flex">Clinical MVP in progress</span>
                </div>
                <div class="mt-7 rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 px-5 py-10 text-center sm:px-8">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-teal-800 shadow-sm ring-1 ring-slate-200">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 5v14m0-10h6m-6 7h10M11 6l3 3-3 3m4 1 3 3-3 3"/></svg>
                    </div>
                    <h3 class="mt-4 font-black text-slate-900">{{ $isPatient ? 'Your timeline is ready' : 'Clinical activity is available' }}</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Verified vital signs, symptoms, consultations and clinical results are arranged in chronological order.</p>
                    @if ($isAdministrator)
                        <a href="{{ route('threshold-rules.index') }}" class="button-secondary mt-5">Open threshold registry</a>
                    @else
                        <a href="{{ $isPatient ? route('timeline.index') : route('encounters.index') }}" class="button-secondary mt-5">{{ $isPatient ? 'Open health timeline' : 'Open encounter register' }}</a>
                    @endif
                </div>
            </section>

            <aside class="relative overflow-hidden rounded-[1.75rem] border border-amber-200 bg-amber-50 p-6 sm:p-8">
                <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-amber-200/40"></div>
                <div class="relative">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-200 font-black text-amber-950">!</div>
                    <h2 class="mt-5 text-lg font-black text-amber-950">Safety comes first</h2>
                    <p class="mt-3 text-sm leading-6 text-amber-900">EdeHealth Sentinel supports monitoring and clinical review. It does not provide emergency care or replace a qualified professional.</p>
                    <div class="mt-6 border-t border-amber-200 pt-5 text-xs font-semibold leading-5 text-amber-800">For urgent help, contact the Medical Centre or an appropriate emergency service directly.</div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
