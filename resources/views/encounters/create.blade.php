<x-app-layout>
    <x-slot name="title">Open clinical encounter</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('encounters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-teal-700 hover:text-teal-900">← Encounter register</a>
        <div class="mt-5">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Clinical workspace</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Open a clinical encounter</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Start a traceable visit record before documenting symptoms, measurements and clinical actions.</p>
        </div>

        @if ($patients->isEmpty())
            <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50 p-6 text-sm leading-6 text-amber-950">No patient profiles are available. Register a patient before opening an encounter.</div>
        @else
            <form method="POST" action="{{ route('encounters.store') }}" class="mt-8">
                @csrf
                @include('encounters.partials.form')
            </form>
        @endif
    </div>
</x-app-layout>
