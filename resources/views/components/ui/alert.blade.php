@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null
])

@php
$types = [
    'success' => 'bg-green-50 dark:bg-green-900 border-green-200 dark:border-green-700 text-green-700 dark:text-green-300',
    'error' => 'bg-red-50 dark:bg-red-900 border-red-200 dark:border-red-700 text-red-700 dark:text-red-300',
    'warning' => 'bg-yellow-50 dark:bg-yellow-900 border-yellow-200 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300',
    'info' => 'bg-blue-50 dark:bg-blue-900 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300'
];

$icons = [
    'success' => 'fas fa-check-circle',
    'error' => 'fas fa-exclamation-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle'
];

$alertIcon = $icon ?? $icons[$type];
$alertClasses = $types[$type] . ' border px-4 py-3 rounded-lg mb-4';
@endphp

<div {{ $attributes->merge(['class' => $alertClasses]) }} @if($dismissible) x-data="{ show: true }" x-show="show" @endif>
    <div class="flex items-start">
        @if($alertIcon)
            <i class="{{ $alertIcon }} mr-3 mt-0.5 flex-shrink-0"></i>
        @endif
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="ml-3 flex-shrink-0">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>