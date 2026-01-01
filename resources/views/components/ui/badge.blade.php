@props([
    'variant' => 'default',
    'size' => 'md'
])

@php
$variants = [
    'default' => 'ui-badge-default',
    'primary' => 'ui-badge-primary',
    'success' => 'ui-badge-success',
    'danger' => 'ui-badge-danger',
    'warning' => 'ui-badge-warning',
    'info' => 'ui-badge-primary'
];
$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
    'lg' => 'px-3 py-1.5 text-base'
];
$classes = 'ui-badge ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
