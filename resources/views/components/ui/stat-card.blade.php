@props([
    'title' => '',
    'value' => '0',
    'icon' => 'chart-line'
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
            <i class="fas fa-{{ $icon }} text-gray-600 dark:text-gray-400 text-xl"></i>
        </div>
    </div>
</div>
