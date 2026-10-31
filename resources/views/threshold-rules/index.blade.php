<x-app-layout>
    <x-slot name="title">Threshold governance</x-slot>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Clinical governance</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Threshold rule registry</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Drafts are created by administrators but cannot generate alerts until an authorized clinician reviews and approves them. Approved rules are immutable, apply only to measurements recorded after activation and may only be deactivated.</p>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">
                {{ match (session('status')) { 'threshold-created' => 'The draft threshold was created for clinical review.', 'threshold-approved' => 'The threshold was approved and activated.', 'threshold-deactivated' => 'The threshold was deactivated. Existing alerts remain in the audit trail.', default => 'The threshold registry was updated.' } }}
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm leading-6 text-rose-950"><strong>No rule is a universal medical standard.</strong> Values, severity and guidance must be approved for the Medical Centre’s intended population and protocol before activation.</div>

        @can('create', App\Models\ThresholdRule::class)
            <details class="mt-6 rounded-3xl border border-slate-200 bg-white shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between p-6 font-black text-slate-950 sm:p-7">
                    <span class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-2xl bg-teal-100 text-xl text-teal-800">+</span> Create draft rule</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Administrator</span>
                </summary>
                <form method="POST" action="{{ route('threshold-rules.store') }}" class="border-t border-slate-200 p-6 sm:p-7">
                    @csrf
                    <x-input-error :messages="$errors->get('threshold')" class="mb-4 rounded-xl bg-rose-50 p-3" />
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="sm:col-span-2">
                            <label for="rule_name" class="block text-sm font-black text-slate-800">Rule name</label>
                            <input id="rule_name" name="name" value="{{ old('name') }}" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Protocol-specific review rule">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="rule_version" class="block text-sm font-black text-slate-800">Version</label>
                            <input id="rule_version" name="version" type="number" min="1" max="9999" value="{{ old('version', 1) }}" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        </div>
                        <div>
                            <label for="metric" class="block text-sm font-black text-slate-800">Measurement</label>
                            <select id="metric" name="metric" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                                @foreach ($metrics as $metric)
                                    <option value="{{ $metric->value }}">{{ $metric->label() }} ({{ $metric->unit() }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="minimum_value" class="block text-sm font-black text-slate-800">Minimum allowed value</label>
                            <input id="minimum_value" name="minimum_value" type="number" step="0.0001" value="{{ old('minimum_value') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Optional">
                        </div>
                        <div>
                            <label for="maximum_value" class="block text-sm font-black text-slate-800">Maximum allowed value</label>
                            <input id="maximum_value" name="maximum_value" type="number" step="0.0001" value="{{ old('maximum_value') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Optional">
                            <x-input-error :messages="$errors->get('maximum_value')" class="mt-2" />
                        </div>
                        <div>
                            <label for="severity" class="block text-sm font-black text-slate-800">Review priority</label>
                            <select id="severity" name="severity" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                                @foreach ($severities as $severity)
                                    <option value="{{ $severity->value }}">{{ $severity->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="guidance" class="block text-sm font-black text-slate-800">Approved review guidance</label>
                            <textarea id="guidance" name="guidance" rows="4" required class="mt-2 block w-full rounded-2xl border-slate-300" placeholder="Describe the clinical review action without presenting an automated diagnosis.">{{ old('guidance') }}</textarea>
                            <x-input-error :messages="$errors->get('guidance')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end"><button class="button-primary min-h-12 px-7" type="submit">Save draft for review</button></div>
                </form>
            </details>
        @endcan

        <section class="mt-8">
            <div class="flex items-center justify-between px-1"><h2 class="text-xl font-black text-slate-950">Rule history</h2><span class="text-xs font-bold text-slate-500">{{ $rules->total() }} versions</span></div>
            <div class="mt-4 space-y-4">
                @forelse ($rules as $rule)
                    <article class="rounded-3xl border {{ $rule->is_active ? 'border-emerald-200' : 'border-slate-200' }} bg-white p-6 shadow-sm sm:p-7">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-black text-slate-950">{{ $rule->name }}</h3>
                                    <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : ($rule->approved_at ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-900') }}">{{ $rule->is_active ? 'Active' : ($rule->approved_at ? 'Deactivated' : 'Awaiting approval') }}</span>
                                    <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $rule->severity->badgeClasses() }}">{{ $rule->severity->label() }}</span>
                                </div>
                                <p class="mt-2 text-sm font-bold text-teal-700">{{ $rule->metric->label() }} · outside {{ $rule->rangeLabel() }} · version {{ $rule->version }}</p>
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">{{ $rule->guidance }}</p>
                                <p class="mt-3 text-xs text-slate-500">Created by {{ $rule->createdBy->name }} on {{ $rule->created_at->format('j M Y') }}@if($rule->approvedBy) · Approved by {{ $rule->approvedBy->name }}@endif</p>
                            </div>
                            <div class="shrink-0">
                                @can('approve', $rule)
                                    <form method="POST" action="{{ route('threshold-rules.approve', $rule) }}">@csrf<button class="button-primary" type="submit">Approve & activate</button></form>
                                @endcan
                                @can('deactivate', $rule)
                                    <form method="POST" action="{{ route('threshold-rules.deactivate', $rule) }}">@csrf<button class="button-secondary" type="submit">Deactivate</button></form>
                                @endcan
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center"><h3 class="font-black text-slate-900">No threshold rules configured</h3><p class="mt-2 text-sm text-slate-500">No automated alert can be generated until a draft is created and clinically approved.</p></div>
                @endforelse
            </div>
            @if ($rules->hasPages())<div class="mt-6">{{ $rules->links() }}</div>@endif
        </section>
    </div>
</x-app-layout>
