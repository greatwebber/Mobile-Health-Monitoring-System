@php
    $editing = isset($encounter);
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_.85fr]">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-100 text-teal-800">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 3h8v4H8V3ZM6 5H5a2 2 0 0 0-2 2v13h18V7a2 2 0 0 0-2-2h-1M8 12h8m-8 4h5"/></svg>
            </span>
            <div>
                <h2 class="font-black text-slate-950">Encounter details</h2>
                <p class="text-xs text-slate-500">Identify the visit and the reason for assessment.</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            @if (! $editing)
                <div>
                    <label for="patient_profile_id" class="block text-sm font-black text-slate-800">Patient</label>
                    <select id="patient_profile_id" name="patient_profile_id" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                        <option value="">Select a registered patient</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected((string) old('patient_profile_id') === (string) $patient->id)>{{ $patient->patient_number }} — {{ $patient->user->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('patient_profile_id')" class="mt-2" />
                </div>
            @else
                <div class="rounded-2xl border border-teal-200 bg-teal-50 p-4">
                    <p class="text-xs font-black uppercase tracking-wider text-teal-700">Patient</p>
                    <p class="mt-1 font-black text-slate-950">{{ $encounter->patientProfile->user->name }}</p>
                    <p class="mt-1 text-xs font-semibold text-slate-600">{{ $encounter->patientProfile->patient_number }}</p>
                </div>
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="type" class="block text-sm font-black text-slate-800">Encounter type</label>
                    <select id="type" name="type" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(old('type', $encounter->type->value ?? 'triage') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <label for="occurred_at" class="block text-sm font-black text-slate-800">Date and time</label>
                    <input id="occurred_at" name="occurred_at" type="datetime-local" required value="{{ old('occurred_at', isset($encounter) ? $encounter->occurred_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" max="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                    <x-input-error :messages="$errors->get('occurred_at')" class="mt-2" />
                </div>
            </div>

            @if ($editing)
                <div>
                    <label for="status" class="block text-sm font-black text-slate-800">Workflow status</label>
                    <select id="status" name="status" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $encounter->status->value) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs leading-5 text-slate-500">Completing or cancelling locks the encounter against later editing.</p>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            @endif

            <div>
                <label for="chief_complaint" class="block text-sm font-black text-slate-800">Chief complaint or reason for visit</label>
                <textarea id="chief_complaint" name="chief_complaint" rows="5" required class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Record the patient's main concern in clear clinical language.">{{ old('chief_complaint', $encounter->chief_complaint ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('chief_complaint')" class="mt-2" />
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-sky-800">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4L16.5 3.5Z"/></svg>
            </span>
            <div>
                <h2 class="font-black text-slate-950">Clinical documentation</h2>
                <p class="text-xs text-slate-500">Document assessment context and next steps.</p>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            <div>
                <label for="assigned_clinician_id" class="block text-sm font-black text-slate-800">Assigned clinician</label>
                <select id="assigned_clinician_id" name="assigned_clinician_id" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                    <option value="">Not assigned</option>
                    @foreach ($clinicians as $clinician)
                        <option value="{{ $clinician->id }}" @selected((string) old('assigned_clinician_id', $encounter->assigned_clinician_id ?? '') === (string) $clinician->id)>{{ $clinician->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('assigned_clinician_id')" class="mt-2" />
            </div>

            <div>
                <label for="clinical_notes" class="block text-sm font-black text-slate-800">Clinical notes</label>
                <textarea id="clinical_notes" name="clinical_notes" rows="7" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Assessment observations, relevant findings and clinical context.">{{ old('clinical_notes', $encounter->clinical_notes ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('clinical_notes')" class="mt-2" />
            </div>

            <div>
                <label for="disposition" class="block text-sm font-black text-slate-800">Disposition and follow-up</label>
                <textarea id="disposition" name="disposition" rows="4" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Document referral, follow-up or discharge guidance.">{{ old('disposition', $encounter->disposition ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('disposition')" class="mt-2" />
            </div>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="max-w-2xl text-xs leading-5 text-slate-500">Clinical information is visible only to the patient and authorized care personnel. Do not use this workflow for an emergency response.</p>
    <button type="submit" class="button-primary min-h-12 px-7">{{ $editing ? 'Save encounter' : 'Open encounter' }}</button>
</div>
