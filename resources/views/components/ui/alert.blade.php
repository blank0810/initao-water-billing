@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null
])

@php
$types = [
    'success' => 'ui-alert ui-alert-success',
    'error' => 'ui-alert ui-alert-error',
    'warning' => 'ui-alert ui-alert-warning',
    'info' => 'ui-alert ui-alert-info'
];
$icons = [
    'success' => 'fas fa-check-circle',
    'error' => 'fas fa-exclamation-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle'
];
$alertIcon = $icon ?? $icons[$type];
$alertClasses = $types[$type];
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
