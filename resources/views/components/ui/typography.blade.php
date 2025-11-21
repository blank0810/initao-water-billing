@props([
    'variant' => 'body',
    'size' => null,
    'weight' => null,
    'color' => null
])

@php
$variants = [
    'h1' => 'text-3xl font-bold text-gray-900 dark:text-white',
    'h2' => 'text-2xl font-bold text-gray-900 dark:text-white',
    'h3' => 'text-xl font-semibold text-gray-900 dark:text-white',
    'h4' => 'text-lg font-semibold text-gray-900 dark:text-white',
    'h5' => 'text-base font-semibold text-gray-900 dark:text-white',
    'h6' => 'text-sm font-semibold text-gray-900 dark:text-white',
    'body' => 'text-sm text-gray-700 dark:text-gray-300',
    'caption' => 'text-xs text-gray-500 dark:text-gray-400',
    'label' => 'text-sm font-medium text-gray-700 dark:text-gray-300',
    'muted' => 'text-sm text-gray-500 dark:text-gray-400'
];

$tag = match($variant) {
    'h1' => 'h1',
    'h2' => 'h2', 
    'h3' => 'h3',
    'h4' => 'h4',
    'h5' => 'h5',
    'h6' => 'h6',
    default => 'p'
};

$classes = $variants[$variant];

if ($size) $classes .= " text-{$size}";
if ($weight) $classes .= " font-{$weight}";
if ($color) $classes .= " text-{$color}";
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>