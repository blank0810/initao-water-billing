@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'href' => null,
    'icon' => null
])

@php
$baseClasses = 'ui-btn';
$variants = [
    'primary' => 'ui-btn-primary',
    'secondary' => 'ui-btn-secondary',
    'success' => 'ui-btn-success',
    'danger' => 'ui-btn-danger',
    'warning' => 'ui-btn-warning',
    'outline' => 'ui-btn-outline',
    'ghost' => 'ui-btn-ghost'
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base'
];
$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
