@props([
    'status' => null,
])

@php
$statusId = is_object($status) ? $status->stat_id : $status;
$statusDesc = is_object($status) ? $status->stat_desc : ($statusId == 1 ? 'ACTIVE' : 'INACTIVE');

$classes = match($statusId) {
    1 => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    2 => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
};
@endphp

<span class="px-2 py-1 text-xs font-medium rounded-full {{ $classes }}">
    {{ $statusDesc }}
</span>
