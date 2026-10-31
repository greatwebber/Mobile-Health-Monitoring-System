@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-teal-50 px-4 py-3 text-start text-base font-bold text-teal-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200'
            : 'block w-full rounded-xl px-4 py-3 text-start text-base font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
