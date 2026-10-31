<x-app-layout>
    <x-slot name="title">Appointment {{ $appointment->appointment_number }}</x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('appointments.index') }}" class="text-sm font-bold text-teal-700 hover:text-teal-900">← Appointments</a>

        @if (session('status'))
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">
                {{ match (session('status')) { 'appointment-created' => 'The appointment was scheduled successfully.', 'appointment-updated' => 'The appointment schedule was updated.', 'appointment-status-updated' => 'The attendance outcome was recorded.', default => 'The appointment was updated.' } }}
            </div>
        @endif

        <section class="relative mt-6 overflow-hidden rounded-[2rem] bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10 sm:p-9">
            <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-teal-300/15"></div>
            <div class="relative flex flex-col gap-7 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $appointment->status->badgeClasses() }}">{{ $appointment->status->label() }}</span>
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold text-slate-300">{{ $appointment->duration_minutes }} minutes</span>
                    </div>
                    <p class="mt-6 text-sm font-bold text-teal-200">{{ $appointment->scheduled_at->format('l, j F Y') }}</p>
                    <h1 class="mt-1 text-3xl font-black tracking-tight sm:text-4xl">{{ $appointment->scheduled_at->format('g:i a') }}</h1>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">{{ $appointment->reason }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Appointment reference</p>
                    <p class="mt-1 font-black text-white">{{ $appointment->appointment_number }}</p>
                    @can('update', $appointment)
                        <a href="{{ route('appointments.edit', $appointment) }}" class="mt-5 inline-flex min-h-11 items-center rounded-xl bg-teal-400 px-4 text-sm font-black text-slate-950 transition hover:bg-teal-300">Reschedule or edit</a>
                    @endcan
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Patient instructions</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $appointment->patient_instructions ?: 'No special preparation instructions were recorded.' }}</p>
                </section>

                @if ($appointment->outcome_notes)
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-500">Attendance outcome</p>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $appointment->outcome_notes }}</p>
                        <p class="mt-4 text-xs font-semibold text-slate-500">Recorded {{ $appointment->status_changed_at?->format('j M Y, g:i a') }} by {{ $appointment->statusChangedBy?->name ?? 'authorized staff' }}</p>
                    </section>
                @endif

                @can('transition', $appointment)
                    <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6 sm:p-8">
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-amber-800">Close the follow-up loop</p>
                        <h2 class="mt-2 text-lg font-black text-amber-950">Record attendance outcome</h2>
                        <p class="mt-2 text-sm leading-6 text-amber-900">Final outcomes cannot be edited. Add a meaningful note before completing, cancelling or recording non-attendance.</p>
                        <form method="POST" action="{{ route('appointments.transition', $appointment) }}" class="mt-5">
                            @csrf
                            <label for="outcome_notes" class="block text-sm font-black text-amber-950">Outcome note</label>
                            <textarea id="outcome_notes" name="outcome_notes" required minlength="10" maxlength="2000" rows="4" class="mt-2 block w-full rounded-2xl border-amber-300 bg-white" placeholder="Document the attendance or cancellation context.">{{ old('outcome_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('outcome_notes')" class="mt-2" />
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            <div class="mt-4 flex flex-wrap gap-2">
                                @unless ($appointment->scheduled_at->isFuture())
                                    <button name="status" value="completed" class="button-primary" type="submit">Mark completed</button>
                                    <button name="status" value="no_show" class="button-secondary" type="submit">Did not attend</button>
                                @endunless
                                <button name="status" value="cancelled" class="button-secondary" type="submit">Cancel appointment</button>
                            </div>
                        </form>
                    </section>
                @endcan
            </div>

            <aside class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-black text-slate-950">Visit details</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Patient</dt><dd class="mt-1 font-semibold text-slate-800">{{ $appointment->patientProfile->user->name }}</dd><dd class="text-xs text-slate-500">{{ $appointment->patientProfile->patient_number }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Clinician</dt><dd class="mt-1 font-semibold text-slate-800">{{ $appointment->assignedClinician?->name ?? 'Not assigned yet' }}</dd></div>
                        <div><dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Scheduled by</dt><dd class="mt-1 font-semibold text-slate-800">{{ $appointment->scheduledBy->name }}</dd></div>
                    </dl>
                </section>

                @if ($appointment->clinicalEncounter)
                    <section class="rounded-3xl border border-teal-200 bg-teal-50 p-6">
                        <p class="text-xs font-black uppercase tracking-[0.15em] text-teal-800">Related care record</p>
                        <p class="mt-2 font-black text-teal-950">{{ $appointment->clinicalEncounter->encounter_number }}</p>
                        <a href="{{ route('encounters.show', $appointment->clinicalEncounter) }}" class="mt-4 inline-flex text-sm font-black text-teal-800 hover:text-teal-950">Open encounter →</a>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</x-app-layout>
