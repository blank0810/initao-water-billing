@props([
    'title' => '',
    'value' => '',
    'icon' => null,
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => 'up'
])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
    'green' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
    'red' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
    'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400',
    'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
    'gray' => 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
];

$valueColorClasses = [
    'blue' => 'text-blue-600 dark:text-blue-400',
    'green' => 'text-green-600 dark:text-green-400',
    'red' => 'text-red-600 dark:text-red-400',
    'yellow' => 'text-yellow-600 dark:text-yellow-400',
    'purple' => 'text-purple-600 dark:text-purple-400',
    'gray' => 'text-gray-900 dark:text-gray-100'
];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-200">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ $title }}</p>
            <p class="text-2xl font-bold {{ $valueColorClasses[$color] }}">{{ $value }}</p>
            
            @if($trend)
                <div class="flex items-center mt-2">
                    <i class="fas fa-arrow-{{ $trendDirection === 'up' ? 'up' : 'down' }} text-xs {{ $trendDirection === 'up' ? 'text-green-500' : 'text-red-500' }} mr-1"></i>
                    <span class="text-xs {{ $trendDirection === 'up' ? 'text-green-600' : 'text-red-600' }}">{{ $trend }}</span>
                </div>
            @endif
        </div>
        
        @if($icon)
            <div class="flex-shrink-0 ml-4">
                <div class="w-12 h-12 rounded-lg {{ $colorClasses[$color] }} flex items-center justify-center">
                    <i class="{{ $icon }} text-lg"></i>
                </div>
            </div>
        @endif
    </div>
</div>