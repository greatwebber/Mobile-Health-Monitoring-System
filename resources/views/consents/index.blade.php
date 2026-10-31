<x-app-layout>
    <x-slot name="title">Consent and privacy</x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Control and transparency</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Consent & privacy</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review how different parts of EdeHealth may use your information. Every change creates a dated record instead of overwriting your previous decision.</p>
        </div>

        @include('patient-profile.sub-navigation')

        @if (session('status') === 'consent-updated')
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">Your consent preference was recorded.</div>
        @elseif (session('status') === 'consent-unchanged')
            <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sm font-semibold text-sky-900" role="status">That preference is already current; no duplicate record was created.</div>
        @endif

        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-950">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-amber-200 font-black">!</span>
            <p><strong>Draft institutional wording.</strong> These statements support development and testing. The Medical Centre must approve the final consent language and document version before a live clinical pilot.</p>
        </div>

        <div class="mt-6 space-y-4">
            @foreach ($consentTypes as $type)
                @php $current = $currentDecisions->get($type->value); @endphp
                <article class="overflow-hidden rounded-3xl border {{ $current?->granted ? 'border-emerald-200' : 'border-slate-200' }} bg-white shadow-sm">
                    <div class="p-6 sm:p-7">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                            <div class="max-w-2xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-black text-slate-950">{{ $type->label() }}</h2>
                                    <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wider {{ $type->isRequiredForCoreCare() ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $type->isRequiredForCoreCare() ? 'Core care' : 'Optional' }}
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $type->description() }}</p>
                            </div>
                            <div class="shrink-0">
                                @if ($current?->granted)
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-800"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Allowed</span>
                                @elseif ($current)
                                    <span class="inline-flex items-center gap-2 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-black text-rose-800"><span class="h-2 w-2 rounded-full bg-rose-500"></span> Not allowed</span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-600"><span class="h-2 w-2 rounded-full bg-slate-400"></span> No decision</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-slate-500">
                                @if ($current)
                                    Last changed {{ $current->decided_at->timezone(config('app.timezone'))->format('j M Y, g:i a') }} · document {{ $current->document_version }}
                                @else
                                    No preference has been recorded · document {{ $documentVersion }}
                                @endif
                            </p>
                            <div class="flex gap-2">
                                @if (! $current?->granted)
                                    <form method="POST" action="{{ route('consents.store', $type->value) }}">
                                        @csrf
                                        <input type="hidden" name="granted" value="1">
                                        <button type="submit" class="button-primary">Allow</button>
                                    </form>
                                @endif
                                @if ($current?->granted || ! $current)
                                    <form method="POST" action="{{ route('consents.store', $type->value) }}">
                                        @csrf
                                        <input type="hidden" name="granted" value="0">
                                        <button type="submit" class="button-secondary">{{ $current?->granted ? 'Withdraw' : 'Decline' }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-app-layout>
