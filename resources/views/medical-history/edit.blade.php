<x-app-layout>
    <x-slot name="title">Medical history</x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Care profile</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Medical history</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Add one item per line. Only include information you know to be accurate; authorized clinicians can review it during care.</p>
        </div>

        @include('patient-profile.sub-navigation')

        @if (session('status') === 'medical-history-updated')
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">
                Your medical history was updated successfully.
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 p-5 text-sm leading-6 text-sky-950">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-sky-200 font-black text-sky-900">i</span>
                <p><strong>This is not a diagnosis form.</strong> Use it to record known history. New or severe symptoms should be assessed by the Medical Centre.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('medical-history.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PATCH')

            <section class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-3xl border border-rose-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-100 text-rose-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 3v7m0 4v.01M5.6 20h12.8a2 2 0 0 0 1.7-3L13.7 5.8a2 2 0 0 0-3.4 0L3.9 17a2 2 0 0 0 1.7 3Z"/></svg>
                        </span>
                        <div><h2 class="font-black text-slate-950">Allergies</h2><p class="text-xs text-slate-500">Medicines, foods or other known triggers</p></div>
                    </div>
                    <textarea id="allergies_text" name="allergies_text" rows="5" class="mt-5 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example:&#10;Penicillin&#10;Peanuts">{{ old('allergies_text', implode("\n", $history->allergies ?? [])) }}</textarea>
                    <x-input-error :messages="$errors->get('allergies_text')" class="mt-2" />
                </div>

                <div class="rounded-3xl border border-violet-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 6v12M6 12h12M5 4h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>
                        </span>
                        <div><h2 class="font-black text-slate-950">Chronic conditions</h2><p class="text-xs text-slate-500">Long-term conditions previously identified</p></div>
                    </div>
                    <textarea id="chronic_conditions_text" name="chronic_conditions_text" rows="5" class="mt-5 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example:&#10;Asthma&#10;Hypertension">{{ old('chronic_conditions_text', implode("\n", $history->chronic_conditions ?? [])) }}</textarea>
                    <x-input-error :messages="$errors->get('chronic_conditions_text')" class="mt-2" />
                </div>

                <div class="rounded-3xl border border-emerald-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8.5 4.5a4.95 4.95 0 0 1 7 7l-7 7a4.95 4.95 0 1 1-7-7l7-7Zm-3.5 10 5 5m4-14 5 5"/></svg>
                        </span>
                        <div><h2 class="font-black text-slate-950">Current medications</h2><p class="text-xs text-slate-500">Name and dose, if known</p></div>
                    </div>
                    <textarea id="current_medications_text" name="current_medications_text" rows="5" class="mt-5 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example:&#10;Medication name — dose — frequency">{{ old('current_medications_text', implode("\n", $history->current_medications ?? [])) }}</textarea>
                    <x-input-error :messages="$errors->get('current_medications_text')" class="mt-2" />
                </div>

                <div class="rounded-3xl border border-amber-200 bg-white p-6 shadow-sm sm:p-7">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 20h14M7 20v-5a5 5 0 0 1 10 0v5M9 8a3 3 0 1 1 6 0v2H9V8Z"/></svg>
                        </span>
                        <div><h2 class="font-black text-slate-950">Previous surgeries</h2><p class="text-xs text-slate-500">Procedure and approximate year, if known</p></div>
                    </div>
                    <textarea id="previous_surgeries_text" name="previous_surgeries_text" rows="5" class="mt-5 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example:&#10;Appendectomy — 2019">{{ old('previous_surgeries_text', implode("\n", $history->previous_surgeries ?? [])) }}</textarea>
                    <x-input-error :messages="$errors->get('previous_surgeries_text')" class="mt-2" />
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label for="family_history_text" class="block text-sm font-black text-slate-900">Relevant family history</label>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Conditions known in close biological relatives, one per line.</p>
                        <textarea id="family_history_text" name="family_history_text" rows="6" class="mt-3 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example:&#10;Mother — diabetes&#10;Father — hypertension">{{ old('family_history_text', implode("\n", $history->family_history ?? [])) }}</textarea>
                        <x-input-error :messages="$errors->get('family_history_text')" class="mt-2" />
                    </div>
                    <div>
                        <label for="additional_notes" class="block text-sm font-black text-slate-900">Additional context</label>
                        <p class="mt-1 text-xs leading-5 text-slate-500">Optional information that does not fit above.</p>
                        <textarea id="additional_notes" name="additional_notes" rows="6" class="mt-3 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Add a short note">{{ old('additional_notes', $history->additional_notes) }}</textarea>
                        <x-input-error :messages="$errors->get('additional_notes')" class="mt-2" />
                    </div>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-xl text-xs leading-5 text-slate-500">The date and account responsible for each update are recorded for accountability.</p>
                <button type="submit" class="button-primary min-h-12 px-7">Save medical history</button>
            </div>
        </form>
    </div>
</x-app-layout>
