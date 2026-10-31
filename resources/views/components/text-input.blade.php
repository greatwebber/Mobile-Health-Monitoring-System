@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'min-h-11 rounded-xl border-slate-300 bg-white shadow-sm focus:border-teal-600 focus:ring-teal-600']) }}>
