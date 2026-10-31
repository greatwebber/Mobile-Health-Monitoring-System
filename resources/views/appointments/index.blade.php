<x-app-layout>
    <x-slot name="title">Appointments</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Care calendar</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $isPatient ? 'Your appointments' : 'Appointment register' }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $isPatient ? 'Review confirmed visit times, care instructions and attendance outcomes.' : 'Coordinate scheduled reviews and preserve a clear follow-up outcome for every appointment.' }}</p>
            </div>
            @can('create', App\Models\Appointment::class)
                <a href="{{ route('appointments.create') }}" class="button-primary min-h-12 justify-center">Schedule appointment</a>
            @endcan
        </div>

        @unless ($isPatient)
            <form method="GET" action="{{ route('appointments.index') }}" class="mt-7 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_13rem_auto] sm:p-5">
                <label class="sr-only" for="appointment_search">Search appointments</label>
                <input id="appointment_search" name="search" value="{{ request('search') }}" class="min-h-12 rounded-2xl border-slate-300" placeholder="Patient or appointment number">
                <label class="sr-only" for="appointment_status">Status</label>
                <select id="appointment_status" name="status" class="min-h-12 rounded-2xl border-slate-300">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button class="button-secondary min-h-12 justify-center" type="submit">Filter</button>
            </form>
        @endunless

        <div class="mt-7 space-y-4">
            @forelse ($appointments as $appointment)
                <a href="{{ route('appointments.show', $appointment) }}" class="group block rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200 sm:p-6">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <div class="grid w-16 shrink-0 overflow-hidden rounded-2xl border border-slate-200 text-center">
                                <span class="bg-slate-950 px-2 py-1.5 text-[0.62rem] font-black uppercase tracking-widest text-teal-200">{{ $appointment->scheduled_at->format('M') }}</span>
                                <span class="px-2 py-2 text-2xl font-black text-slate-950">{{ $appointment->scheduled_at->format('d') }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="truncate font-black text-slate-950">{{ $isPatient ? $appointment->reason : $appointment->patientProfile->user->name }}</h2>
                                    <span class="rounded-full px-2.5 py-1 text-[0.64rem] font-black uppercase tracking-wider {{ $appointment->status->badgeClasses() }}">{{ $appointment->status->label() }}</span>
                                </div>
                                <p class="mt-2 text-sm font-bold text-teal-700">{{ $appointment->scheduled_at->format('l, j F Y · g:i a') }}</p>
                                <p class="mt-1 truncate text-sm text-slate-500">{{ $isPatient ? ($appointment->assignedClinician?->name ?? 'Clinician to be assigned') : $appointment->reason }}</p>
                            </div>
                        </div>
                        <div class="shrink-0 sm:text-right">
                            <p class="text-xs font-bold text-slate-400">{{ $appointment->appointment_number }}</p>
                            <p class="mt-2 text-sm font-black text-slate-700 transition group-hover:text-teal-700">View details →</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-white text-teal-800 shadow-sm ring-1 ring-slate-200">⌁</div>
                    <h2 class="mt-4 font-black text-slate-900">No appointments found</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $isPatient ? 'Your confirmed follow-up visits will appear here.' : 'Adjust the filters or schedule the first appointment.' }}</p>
                </div>
            @endforelse
        </div>

        @if ($appointments->hasPages())
            <div class="mt-7">{{ $appointments->links() }}</div>
        @endif
    </div>
</x-app-layout>
