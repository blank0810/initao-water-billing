<!-- Standardized Status Badge Component -->
@php
$colors = [
    'active' => 'ui-badge-success',
    'inactive' => 'ui-badge-default',
    'paid' => 'ui-badge-success',
    'unpaid' => 'ui-badge-warning',
    'overdue' => 'ui-badge-danger',
    'pending' => 'ui-badge-warning',
    'approved' => 'ui-badge-success',
    'rejected' => 'ui-badge-danger',
    'connected' => 'ui-badge-success',
    'disconnected' => 'ui-badge-danger',
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
$colorClass = $colors[$status] ?? 'ui-badge-default';
$icon = $icons[$status] ?? 'fa-circle';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ui-badge {{ $colorClass }}">
    <i class="fas {{ $icon }} mr-1"></i>
    {{ $label ?? ucfirst($status) }}
</span>
