<x-app-layout>
    <x-slot name="title">Update encounter</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('encounters.show', $encounter) }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-900">← Encounter {{ $encounter->encounter_number }}</a>
        <div class="mt-5">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Active care record</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Update encounter</h1>
            <p class="mt-2 text-sm text-slate-600">Record the current assessment and advance the workflow when appropriate.</p>
        </div>

        <form method="POST" action="{{ route('encounters.update', $encounter) }}" class="mt-8">
            @csrf
            @method('PATCH')
            @include('encounters.partials.form')
        </form>
    </div>
</x-app-layout>
