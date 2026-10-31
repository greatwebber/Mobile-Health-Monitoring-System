<x-app-layout>
    <x-slot name="title">Reschedule appointment</x-slot>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('appointments.show', $appointment) }}" class="text-sm font-bold text-teal-700 hover:text-teal-900">← Appointment details</a>
        <p class="mt-6 text-xs font-black uppercase tracking-[0.18em] text-teal-700">{{ $appointment->appointment_number }}</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Update the schedule</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Only scheduled appointments can be changed. Final attendance outcomes remain preserved.</p>
        @include('appointments.partials.form')
    </div>
</x-app-layout>
