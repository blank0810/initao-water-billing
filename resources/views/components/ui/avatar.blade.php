<!-- Standardized Avatar Component -->
@php
$colors = [
    'user' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
    'consumer' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
    'meter' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    'billing' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
    'connection' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
];

$icons = [
    'user' => 'fa-user',
    'consumer' => 'fa-user',
    'meter' => 'fa-tachometer-alt',
    'billing' => 'fa-file-invoice-dollar',
    'connection' => 'fa-plug',
];

$type = $type ?? 'user';
$colorClass = $colors[$type] ?? $colors['user'];
$icon = $icon ?? $icons[$type] ?? 'fa-user';
$size = $size ?? 'md';
$sizeClasses = [
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-12 h-12 text-lg',
];
@endphp

<div class="flex items-center gap-3">
    <div class="flex-shrink-0 {{ $sizeClasses[$size] }} rounded-full {{ $colorClass }} flex items-center justify-center">
        <i class="fas {{ $icon }}"></i>
    </div>
    <div class="min-w-0 flex-1">
        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $name }}</div>
        @if(isset($subtitle))
        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $subtitle }}</div>
        @endif
    </div>
</div>
