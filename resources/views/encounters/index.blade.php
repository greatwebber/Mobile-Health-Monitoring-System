<x-app-layout>
    <x-slot name="title">{{ $isPatient ? 'Visit history' : 'Encounter register' }}</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">{{ $isPatient ? 'Continuity of care' : 'Clinical workspace' }}</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $isPatient ? 'Your visit history' : 'Clinical encounters' }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $isPatient ? 'Review visit records documented by the Medical Centre.' : 'Find active visits, review documentation and continue authorized care workflows.' }}</p>
            </div>
            @can('create', App\Models\ClinicalEncounter::class)
                <a href="{{ route('encounters.create') }}" class="button-primary inline-flex min-h-12 items-center justify-center gap-2 px-5">
                    <span class="text-lg leading-none">+</span> Open encounter
                </a>
            @endcan
        </div>

        @unless ($isPatient)
            <form method="GET" action="{{ route('encounters.index') }}" class="mt-7 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_13rem_auto] sm:p-5">
                <div>
                    <label for="search" class="sr-only">Search encounters</label>
                    <input id="search" name="search" value="{{ request('search') }}" class="block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Patient name, number or encounter ID">
                </div>
                <div>
                    <label for="status" class="sr-only">Filter by status</label>
                    <select id="status" name="status" class="block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="button-secondary min-h-12 px-5" type="submit">Apply filter</button>
            </form>
        @endunless

        <div class="mt-7 space-y-4">
            @forelse ($encounters as $encounter)
                <a href="{{ route('encounters.show', $encounter) }}" class="group block rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200 sm:p-6">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-800 transition group-hover:bg-teal-700 group-hover:text-white">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 3h8v4H8V3ZM6 5H5a2 2 0 0 0-2 2v13h18V7a2 2 0 0 0-2-2h-1M8 12h8m-8 4h5"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="font-black text-slate-950">{{ $isPatient ? $encounter->type->label() : $encounter->patientProfile->user->name }}</span>
                                    <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $encounter->status->badgeClasses() }}">{{ $encounter->status->label() }}</span>
                                </span>
                                @unless ($isPatient)
                                    <span class="mt-1 block text-xs font-bold text-teal-700">{{ $encounter->patientProfile->patient_number }} · {{ $encounter->type->label() }}</span>
                                @endunless
                                <span class="mt-2 line-clamp-2 block text-sm leading-6 text-slate-600">{{ $encounter->chief_complaint }}</span>
                            </span>
                        </div>
                        <span class="shrink-0 border-t border-slate-100 pt-4 text-left sm:border-0 sm:pt-0 sm:text-right">
                            <span class="block text-sm font-black text-slate-800">{{ $encounter->occurred_at->format('j M Y') }}</span>
                            <span class="mt-1 block text-xs text-slate-500">{{ $encounter->occurred_at->format('g:i a') }} · {{ $encounter->encounter_number }}</span>
                            <span class="mt-2 block text-xs font-bold text-teal-700">View record →</span>
                        </span>
                    </div>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-teal-800 shadow-sm ring-1 ring-slate-200">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 3h8v4H8V3ZM6 5H5a2 2 0 0 0-2 2v13h18V7a2 2 0 0 0-2-2h-1M8 12h8m-8 4h5"/></svg>
                    </div>
                    <h2 class="mt-4 font-black text-slate-900">No encounters found</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $isPatient ? 'Your documented Medical Centre visits will appear here.' : 'Open an encounter when a registered patient begins an authorized visit.' }}</p>
                </div>
            @endforelse
        </div>

        @if ($encounters->hasPages())
            <div class="mt-7">{{ $encounters->links() }}</div>
        @endif
    </div>
</x-app-layout>
