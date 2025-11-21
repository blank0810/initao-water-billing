@props([
    'variant' => 'default',
    'size' => 'md'
])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'primary' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
    'danger' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200'
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
    'lg' => 'px-3 py-1.5 text-base'
];

$classes = 'inline-flex items-center font-medium rounded-full ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>