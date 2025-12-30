@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
    'icon' => null,
    'placeholder' => '',
    'required' => false
])

@php
$inputClasses = $error ? 'ui-input-error' : 'ui-input';
if ($icon) { $inputClasses .= ' pl-10'; }
@endphp

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="{{ $icon }} text-gray-400"></i>
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => $inputClasses]) }}
        >
    </div>
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
