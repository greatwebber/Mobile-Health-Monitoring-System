<x-app-layout>
    <x-slot name="title">Audit trail</x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Security and accountability</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Immutable audit trail</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Review authentication, sensitive record access and clinical workflow changes. Audit entries identify actions and changed field names without duplicating clinical values.</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold leading-5 text-amber-900">Administrator-only · Entries cannot be edited or deleted</div>
        </div>

        <form method="GET" action="{{ route('audit-logs.index') }}" class="mt-7 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[1fr_1fr_12rem_auto] sm:p-5">
            <div>
                <label for="event" class="sr-only">Event</label>
                <input id="event" name="event" value="{{ request('event') }}" maxlength="120" class="block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Event, e.g. appointment">
            </div>
            <div>
                <label for="actor" class="sr-only">Actor</label>
                <input id="actor" name="actor" value="{{ request('actor') }}" maxlength="160" class="block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Actor name or email">
            </div>
            <div>
                <label for="date" class="sr-only">Date</label>
                <input id="date" name="date" type="date" value="{{ request('date') }}" class="block min-h-12 w-full rounded-2xl border-slate-300">
            </div>
            <button class="button-secondary min-h-12 justify-center" type="submit">Filter trail</button>
        </form>

        <section class="mt-7" aria-labelledby="audit-events-title">
            <div class="flex items-center justify-between px-1">
                <h2 id="audit-events-title" class="text-lg font-black text-slate-950">Recorded events</h2>
                <span class="text-xs font-bold text-slate-500">{{ $logs->total() }} entries</span>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($logs as $log)
                    @php
                        $changedFields = collect($log->metadata['changed_fields'] ?? [])->map(fn ($field) => str($field)->replace('_', ' ')->title());
                    @endphp
                    <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-slate-950 px-3 py-1.5 text-[0.66rem] font-black uppercase tracking-wider text-teal-200">{{ str($log->event)->replace(['.', '_'], ' ')->title() }}</span>
                                    @if ($log->request_method)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[0.64rem] font-black text-slate-600">{{ $log->request_method }}</span>
                                    @endif
                                </div>
                                <p class="mt-3 font-black text-slate-950">{{ $log->actor?->name ?? 'Unauthenticated attempt' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $log->actor?->email ?? 'No authenticated account' }}</p>

                                <dl class="mt-4 grid gap-3 text-xs sm:grid-cols-2 lg:grid-cols-3">
                                    <div><dt class="font-bold uppercase tracking-wider text-slate-400">Resource</dt><dd class="mt-1 font-semibold text-slate-700">{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : 'Route-level event' }}</dd></div>
                                    <div><dt class="font-bold uppercase tracking-wider text-slate-400">Route</dt><dd class="mt-1 break-all font-semibold text-slate-700">{{ $log->route_name ?? 'System event' }}</dd></div>
                                    <div><dt class="font-bold uppercase tracking-wider text-slate-400">Network address</dt><dd class="mt-1 font-semibold text-slate-700">{{ $log->ip_address ?? 'Not available' }}</dd></div>
                                </dl>

                                @if ($changedFields->isNotEmpty())
                                    <div class="mt-4 flex flex-wrap gap-1.5" aria-label="Changed fields">
                                        @foreach ($changedFields as $field)
                                            <span class="rounded-lg bg-teal-50 px-2 py-1 text-[0.68rem] font-bold text-teal-800">{{ $field }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="shrink-0 lg:text-right">
                                <time datetime="{{ $log->occurred_at->toIso8601String() }}" class="text-sm font-black text-slate-800">{{ $log->occurred_at->format('j M Y, g:i:s a') }}</time>
                                <p class="mt-1 font-mono text-[0.65rem] text-slate-400">{{ $log->request_id }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                        <h2 class="font-black text-slate-900">No audit events match</h2>
                        <p class="mt-2 text-sm text-slate-500">Try clearing the filters to review the full security trail.</p>
                    </div>
                @endforelse
            </div>

            @if ($logs->hasPages())
                <div class="mt-7">{{ $logs->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
