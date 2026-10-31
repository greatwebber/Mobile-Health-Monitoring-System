<section id="observations" class="scroll-mt-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-700">Structured observations</p>
            <h2 class="mt-2 text-xl font-black tracking-tight text-slate-950">Symptoms, vital signs & results</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Observations are stored with their time, source and verification record.</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-800"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Clinical entries verified</span>
    </div>

    <div class="mt-7 grid gap-6 xl:grid-cols-2">
        <div>
            <div class="flex items-center justify-between">
                <h3 class="font-black text-slate-900">Vital-sign snapshots</h3>
                <span class="text-xs font-bold text-slate-400">{{ $encounter->vitalSignObservations->count() }} recorded</span>
            </div>
            <div class="mt-3 space-y-3">
                @forelse ($encounter->vitalSignObservations as $observation)
                    <article class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs font-black text-sky-900">{{ $observation->measured_at->format('j M Y, g:i a') }}</p>
                            <span class="text-[0.65rem] font-bold uppercase tracking-wider text-emerald-700">{{ $observation->source->label() }}</span>
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($observation->formattedReadings() as $label => $value)
                                <div class="rounded-xl bg-white p-3 ring-1 ring-sky-100">
                                    <dt class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</dt>
                                    <dd class="mt-1 text-sm font-black text-slate-950">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        @if ($observation->notes)
                            <p class="mt-3 text-xs leading-5 text-slate-600">{{ $observation->notes }}</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">No vital signs have been recorded for this encounter.</div>
                @endforelse
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <h3 class="font-black text-slate-900">Reported symptoms</h3>
                <span class="text-xs font-bold text-slate-400">{{ $encounter->symptoms->count() }} recorded</span>
            </div>
            <div class="mt-3 space-y-3">
                @forelse ($encounter->symptoms as $symptom)
                    <article class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h4 class="font-black text-slate-950">{{ $symptom->name }}</h4>
                            <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $symptom->severity->badgeClasses() }}">{{ $symptom->severity->label() }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                            @if ($symptom->started_at)<span>Started {{ $symptom->started_at->format('j M Y, g:i a') }}</span>@endif
                            @if ($symptom->duration)<span>Duration: {{ $symptom->duration }}</span>@endif
                            <span>{{ $symptom->source->label() }}</span>
                        </div>
                        @if ($symptom->notes)
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $symptom->notes }}</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">No structured symptoms have been recorded for this encounter.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-7 border-t border-slate-200 pt-7">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-slate-900">Laboratory and clinical results</h3>
                <p class="mt-1 text-xs leading-5 text-slate-500">Flags reproduce the recorded result context; they are not calculated by this application.</p>
            </div>
            <span class="text-xs font-bold text-slate-400">{{ $encounter->clinicalResults->count() }} recorded</span>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            @forelse ($encounter->clinicalResults as $result)
                <article class="rounded-2xl border border-violet-100 bg-violet-50/50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-[0.65rem] font-black uppercase tracking-wider text-violet-700">{{ $result->category->label() }}{{ $result->test_code ? ' · '.$result->test_code : '' }}</p>
                            <h4 class="mt-1 font-black text-slate-950">{{ $result->test_name }}</h4>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $result->flag->badgeClasses() }}">{{ $result->flag->label() }}</span>
                    </div>
                    <p class="mt-4 text-2xl font-black tracking-tight text-slate-950">{{ $result->displayValue() }}</p>
                    <div class="mt-3 grid gap-2 text-xs text-slate-500 sm:grid-cols-2">
                        @if ($result->reference_range)<p><strong class="text-slate-700">Reference:</strong> {{ $result->reference_range }}</p>@endif
                        @if ($result->specimen)<p><strong class="text-slate-700">Specimen:</strong> {{ $result->specimen }}</p>@endif
                        <p><strong class="text-slate-700">Resulted:</strong> {{ $result->resulted_at->format('j M Y, g:i a') }}</p>
                        <p><strong class="text-slate-700">Source:</strong> {{ $result->source->label() }}</p>
                    </div>
                    @if ($result->notes)<p class="mt-3 border-t border-violet-100 pt-3 text-xs leading-5 text-slate-600">{{ $result->notes }}</p>@endif
                </article>
            @empty
                <div class="sm:col-span-2 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">No laboratory or clinical results have been recorded for this encounter.</div>
            @endforelse
        </div>
    </div>

    @can('update', $encounter)
        <div class="mt-7 border-t border-slate-200 pt-7">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900"><strong>Clinical ranges are validation boundaries, not diagnostic thresholds.</strong> Abnormal-value alerts will be introduced only after clinician-approved rules are configured.</div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2">
                <details class="group rounded-2xl border border-slate-200 bg-slate-50 open:bg-white">
                    <summary class="flex cursor-pointer list-none items-center justify-between p-5 font-black text-slate-900">
                        <span class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-rose-100 text-rose-700">+</span> Record symptom</span>
                        <span class="text-slate-400 transition group-open:rotate-45">+</span>
                    </summary>
                    <form method="POST" action="{{ route('encounters.symptoms.store', $encounter) }}" class="space-y-4 border-t border-slate-200 p-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-black text-slate-800">Symptom</label>
                            <input id="name" name="name" value="{{ old('name') }}" required maxlength="120" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example: Headache">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="severity" class="block text-sm font-black text-slate-800">Severity</label>
                                <select id="severity" name="severity" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                    @foreach (App\Enums\SymptomSeverity::cases() as $severity)
                                        <option value="{{ $severity->value }}" @selected(old('severity') === $severity->value)>{{ $severity->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="started_at" class="block text-sm font-black text-slate-800">Started at</label>
                                <input id="started_at" name="started_at" type="datetime-local" value="{{ old('started_at') }}" max="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <label for="duration" class="block text-sm font-black text-slate-800">Duration</label>
                            <input id="duration" name="duration" value="{{ old('duration') }}" maxlength="120" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Example: Three days">
                        </div>
                        <div>
                            <label for="symptom_notes" class="block text-sm font-black text-slate-800">Context</label>
                            <textarea id="symptom_notes" name="notes" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">{{ old('notes') }}</textarea>
                        </div>
                        <button class="button-primary min-h-11 w-full" type="submit">Save symptom</button>
                    </form>
                </details>

                <details class="group rounded-2xl border border-slate-200 bg-slate-50 open:bg-white">
                    <summary class="flex cursor-pointer list-none items-center justify-between p-5 font-black text-slate-900">
                        <span class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-100 text-sky-700">+</span> Record vital signs</span>
                        <span class="text-slate-400 transition group-open:rotate-45">+</span>
                    </summary>
                    <form method="POST" action="{{ route('encounters.vital-signs.store', $encounter) }}" class="space-y-4 border-t border-slate-200 p-5">
                        @csrf
                        <x-input-error :messages="$errors->get('vitals')" class="rounded-xl bg-rose-50 p-3" />
                        <div>
                            <label for="measured_at" class="block text-sm font-black text-slate-800">Measured at</label>
                            <input id="measured_at" name="measured_at" type="datetime-local" required value="{{ old('measured_at', now()->format('Y-m-d\TH:i')) }}" max="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label for="temperature_c" class="block text-xs font-black text-slate-700">Temperature (°C)</label><input id="temperature_c" name="temperature_c" type="number" step="0.1" value="{{ old('temperature_c') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="heart_rate_bpm" class="block text-xs font-black text-slate-700">Heart rate (bpm)</label><input id="heart_rate_bpm" name="heart_rate_bpm" type="number" value="{{ old('heart_rate_bpm') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="systolic_bp" class="block text-xs font-black text-slate-700">Systolic BP</label><input id="systolic_bp" name="systolic_bp" type="number" value="{{ old('systolic_bp') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="diastolic_bp" class="block text-xs font-black text-slate-700">Diastolic BP</label><input id="diastolic_bp" name="diastolic_bp" type="number" value="{{ old('diastolic_bp') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="respiratory_rate_bpm" class="block text-xs font-black text-slate-700">Respiratory rate</label><input id="respiratory_rate_bpm" name="respiratory_rate_bpm" type="number" value="{{ old('respiratory_rate_bpm') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="oxygen_saturation_percent" class="block text-xs font-black text-slate-700">Oxygen saturation (%)</label><input id="oxygen_saturation_percent" name="oxygen_saturation_percent" type="number" step="0.01" value="{{ old('oxygen_saturation_percent') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="weight_kg" class="block text-xs font-black text-slate-700">Weight (kg)</label><input id="weight_kg" name="weight_kg" type="number" step="0.01" value="{{ old('weight_kg') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div><label for="height_cm" class="block text-xs font-black text-slate-700">Height (cm)</label><input id="height_cm" name="height_cm" type="number" step="0.01" value="{{ old('height_cm') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                            <div class="col-span-2"><label for="blood_glucose_mg_dl" class="block text-xs font-black text-slate-700">Blood glucose (mg/dL)</label><input id="blood_glucose_mg_dl" name="blood_glucose_mg_dl" type="number" step="0.01" value="{{ old('blood_glucose_mg_dl') }}" class="mt-2 block w-full rounded-xl border-slate-300"></div>
                        </div>
                        <div>
                            <label for="vital_notes" class="block text-sm font-black text-slate-800">Measurement notes</label>
                            <textarea id="vital_notes" name="notes" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-teal-600 focus:ring-teal-600"></textarea>
                        </div>
                        <button class="button-primary min-h-11 w-full" type="submit">Save vital signs</button>
                    </form>
                </details>

                <details class="group rounded-2xl border border-slate-200 bg-slate-50 open:bg-white lg:col-span-2">
                    <summary class="flex cursor-pointer list-none items-center justify-between p-5 font-black text-slate-900">
                        <span class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-100 text-violet-700">+</span> Record laboratory or clinical result</span>
                        <span class="text-slate-400 transition group-open:rotate-45">+</span>
                    </summary>
                    <form method="POST" action="{{ route('encounters.clinical-results.store', $encounter) }}" class="space-y-5 border-t border-slate-200 p-5">
                        @csrf
                        <x-input-error :messages="$errors->get('result_value')" class="rounded-xl bg-rose-50 p-3" />
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <label for="result_category" class="block text-xs font-black text-slate-700">Category</label>
                                <select id="result_category" name="category" required class="mt-2 block min-h-11 w-full rounded-xl border-slate-300">
                                    @foreach (App\Enums\ClinicalResultCategory::cases() as $category)
                                        <option value="{{ $category->value }}">{{ $category->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="test_name" class="block text-xs font-black text-slate-700">Test or procedure name</label>
                                <input id="test_name" name="test_name" required value="{{ old('test_name') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Example: Haemoglobin">
                                <x-input-error :messages="$errors->get('test_name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="test_code" class="block text-xs font-black text-slate-700">Test code</label>
                                <input id="test_code" name="test_code" value="{{ old('test_code') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Optional">
                            </div>
                            <div>
                                <label for="value_numeric" class="block text-xs font-black text-slate-700">Numeric value</label>
                                <input id="value_numeric" name="value_numeric" type="number" step="0.0001" value="{{ old('value_numeric') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="For measured results">
                            </div>
                            <div>
                                <label for="value_text" class="block text-xs font-black text-slate-700">Text result</label>
                                <input id="value_text" name="value_text" value="{{ old('value_text') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Example: Negative">
                            </div>
                            <div>
                                <label for="unit" class="block text-xs font-black text-slate-700">Unit</label>
                                <input id="unit" name="unit" value="{{ old('unit') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Example: g/dL">
                            </div>
                            <div>
                                <label for="reference_range" class="block text-xs font-black text-slate-700">Reference range</label>
                                <input id="reference_range" name="reference_range" value="{{ old('reference_range') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="As stated by the laboratory">
                            </div>
                            <div>
                                <label for="result_flag" class="block text-xs font-black text-slate-700">Reported flag</label>
                                <select id="result_flag" name="flag" required class="mt-2 block min-h-11 w-full rounded-xl border-slate-300">
                                    @foreach (App\Enums\ClinicalResultFlag::cases() as $flag)
                                        <option value="{{ $flag->value }}">{{ $flag->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="specimen" class="block text-xs font-black text-slate-700">Specimen or modality</label>
                                <input id="specimen" name="specimen" value="{{ old('specimen') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Example: Whole blood">
                            </div>
                            <div>
                                <label for="collected_at" class="block text-xs font-black text-slate-700">Collected at</label>
                                <input id="collected_at" name="collected_at" type="datetime-local" value="{{ old('collected_at') }}" max="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300">
                            </div>
                            <div>
                                <label for="resulted_at" class="block text-xs font-black text-slate-700">Resulted at</label>
                                <input id="resulted_at" name="resulted_at" type="datetime-local" required value="{{ old('resulted_at', now()->format('Y-m-d\TH:i')) }}" max="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300">
                            </div>
                            <div>
                                <label for="result_notes" class="block text-xs font-black text-slate-700">Result notes</label>
                                <input id="result_notes" name="notes" value="{{ old('notes') }}" class="mt-2 block min-h-11 w-full rounded-xl border-slate-300" placeholder="Optional context">
                            </div>
                        </div>
                        <p class="text-xs leading-5 text-slate-500">Enter either a numeric or text value. Flags and reference ranges must reproduce the source report; EdeHealth does not infer them.</p>
                        <button class="button-primary min-h-11 w-full sm:w-auto sm:px-7" type="submit">Save clinical result</button>
                    </form>
                </details>
            </div>
        </div>
    @endcan
</section>
