@props([
    'href' => null,
    'label',
    'active' => false,
    'disabled' => false,
    'prominent' => false,
])

@php
    $itemClasses = 'app-dock-item '.($active ? 'is-active ' : '').($disabled ? 'is-disabled ' : '').($prominent ? 'is-prominent' : '');
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" class="{{ $itemClasses }}" data-nav-label="{{ $label }}" @if ($active) aria-current="page" @endif>
        <span class="app-dock-icon" aria-hidden="true">{{ $icon }}</span>
        <span class="app-dock-label">{{ $label }}</span>
    </a>
@else
    <button type="button" class="{{ $itemClasses }}" data-nav-label="{{ $label }}" disabled aria-disabled="true" title="{{ $label }} — coming soon">
        <span class="app-dock-icon" aria-hidden="true">{{ $icon }}</span>
        <span class="app-dock-label">{{ $label }}</span>
    </button>
@endif
