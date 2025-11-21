@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
    'icon' => null,
    'placeholder' => '',
    'required' => false
])

@php
$inputClasses = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors';

if ($error) {
    $inputClasses = 'w-full px-3 py-2 border border-red-300 dark:border-red-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors';
}

if ($icon) {
    $inputClasses .= ' pl-10';
}
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