@php
    $editing = isset($appointment);
    $patientValue = old('patient_profile_id', $appointment->patient_profile_id ?? $selectedPatientId ?? '');
    $encounterValue = old('clinical_encounter_id', $appointment->clinical_encounter_id ?? $selectedEncounterId ?? '');
@endphp

<form method="POST" action="{{ $editing ? route('appointments.update', $appointment) : route('appointments.store') }}" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="patient_profile_id" class="block text-sm font-black text-slate-800">Patient</label>
            <select id="patient_profile_id" name="patient_profile_id" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                <option value="">Select a registered patient</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}" @selected((string) $patientValue === (string) $patient->id)>{{ $patient->user->name }} · {{ $patient->patient_number }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('patient_profile_id')" class="mt-2" />
        </div>

        <div>
            <label for="assigned_clinician_id" class="block text-sm font-black text-slate-800">Assigned clinician</label>
            <select id="assigned_clinician_id" name="assigned_clinician_id" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                <option value="">Not assigned yet</option>
                @foreach ($clinicians as $clinician)
                    <option value="{{ $clinician->id }}" @selected((string) old('assigned_clinician_id', $appointment->assigned_clinician_id ?? '') === (string) $clinician->id)>{{ $clinician->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('assigned_clinician_id')" class="mt-2" />
        </div>

        <div>
            <label for="scheduled_at" class="block text-sm font-black text-slate-800">Date and time</label>
            <input id="scheduled_at" name="scheduled_at" type="datetime-local" required value="{{ old('scheduled_at', isset($appointment) ? $appointment->scheduled_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i')) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600">
            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
        </div>

        <div>
            <label for="duration_minutes" class="block text-sm font-black text-slate-800">Expected duration</label>
            <select id="duration_minutes" name="duration_minutes" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                @foreach ([15, 20, 30, 45, 60, 90, 120] as $minutes)
                    <option value="{{ $minutes }}" @selected((int) old('duration_minutes', $appointment->duration_minutes ?? 30) === $minutes)>{{ $minutes }} minutes</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label for="reason" class="block text-sm font-black text-slate-800">Reason for appointment</label>
            <input id="reason" name="reason" required maxlength="500" value="{{ old('reason', $appointment->reason ?? '') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600" placeholder="For example: follow-up review after consultation">
            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
        </div>

        <div class="sm:col-span-2">
            <label for="clinical_encounter_id" class="block text-sm font-black text-slate-800">Related encounter <span class="font-semibold text-slate-400">(optional)</span></label>
            <select id="clinical_encounter_id" name="clinical_encounter_id" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                <option value="">No linked encounter</option>
                @foreach ($encounters as $encounter)
                    <option value="{{ $encounter->id }}" @selected((string) $encounterValue === (string) $encounter->id)>{{ $encounter->patientProfile->user->name }} · {{ $encounter->encounter_number }} · {{ $encounter->occurred_at->format('j M Y') }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('clinical_encounter_id')" class="mt-2" />
            <p class="mt-2 text-xs leading-5 text-slate-500">The system verifies that the encounter belongs to the selected patient.</p>
        </div>

        <div class="sm:col-span-2">
            <label for="patient_instructions" class="block text-sm font-black text-slate-800">Patient instructions <span class="font-semibold text-slate-400">(optional)</span></label>
            <textarea id="patient_instructions" name="patient_instructions" rows="4" maxlength="2000" class="mt-2 block w-full rounded-2xl border-slate-300 focus:border-teal-600 focus:ring-teal-600" placeholder="Preparation, arrival or follow-up instructions visible to the patient.">{{ old('patient_instructions', $appointment->patient_instructions ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('patient_instructions')" class="mt-2" />
        </div>
    </div>

    <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ $editing ? route('appointments.show', $appointment) : route('appointments.index') }}" class="button-secondary min-h-12 justify-center">Cancel</a>
        <button type="submit" class="button-primary min-h-12 px-7">{{ $editing ? 'Save schedule changes' : 'Schedule appointment' }}</button>
    </div>
</form>
