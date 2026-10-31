<x-app-layout>
    <x-slot name="title">Schedule appointment</x-slot>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('appointments.index') }}" class="text-sm font-bold text-teal-700 hover:text-teal-900">← Appointment register</a>
        <p class="mt-6 text-xs font-black uppercase tracking-[0.18em] text-teal-700">Continuity of care</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Schedule an appointment</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Create a traceable visit plan, optionally connect it to the encounter that prompted follow-up, and provide clear patient instructions.</p>
        @include('appointments.partials.form')
    </div>
</x-app-layout>
