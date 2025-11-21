<!-- Standardized Status Badge Component -->
@php
$colors = [
    'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'unpaid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    'connected' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'disconnected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
];

$icons = [
    'active' => 'fa-check-circle',
    'inactive' => 'fa-times-circle',
    'paid' => 'fa-check-circle',
    'unpaid' => 'fa-exclamation-circle',
    'overdue' => 'fa-exclamation-triangle',
    'pending' => 'fa-clock',
    'approved' => 'fa-check-circle',
    'rejected' => 'fa-times-circle',
    'connected' => 'fa-check-circle',
    'disconnected' => 'fa-times-circle',
];

$status = strtolower($status ?? 'active');
$colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
$icon = $icons[$status] ?? 'fa-circle';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
    <i class="fas {{ $icon }} mr-1"></i>
    {{ $label ?? ucfirst($status) }}
</span>
