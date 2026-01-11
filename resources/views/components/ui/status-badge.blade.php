<!-- Standardized Status Badge Component -->
@php
$colors = [
    // Base statuses
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
    // Workflow statuses
    'verified' => 'ui-badge-primary',
    'scheduled' => 'ui-badge-primary',
    'suspended' => 'ui-badge-warning',
    'cancelled' => 'ui-badge-default',
];

$icons = [
    // Base statuses
    'active' => 'fa-check-circle',
    'inactive' => 'fa-times-circle',
    'paid' => 'fa-money-bill-wave',
    'unpaid' => 'fa-exclamation-circle',
    'overdue' => 'fa-exclamation-triangle',
    'pending' => 'fa-clock',
    'approved' => 'fa-check-circle',
    'rejected' => 'fa-times-circle',
    'connected' => 'fa-plug',
    'disconnected' => 'fa-unlink',
    // Workflow statuses
    'verified' => 'fa-clipboard-check',
    'scheduled' => 'fa-calendar-check',
    'suspended' => 'fa-pause-circle',
    'cancelled' => 'fa-ban',
];

$status = strtolower($status ?? 'active');
$colorClass = $colors[$status] ?? 'ui-badge-default';
$icon = $icons[$status] ?? 'fa-circle';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ui-badge {{ $colorClass }}">
    <i class="fas {{ $icon }} mr-1"></i>
    {{ $label ?? ucfirst($status) }}
</span>
