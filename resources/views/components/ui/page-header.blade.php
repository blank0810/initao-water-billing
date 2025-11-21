@props([
    'title' => '',
    'subtitle' => '',
    'backUrl' => null,
    'backText' => 'Back',
    'actions' => null
])

<div class="flex items-center justify-between mb-8">
    <div class="flex items-center space-x-4">
        @if($backUrl)
            <x-ui.button variant="outline" size="sm" href="{{ $backUrl }}" icon="fas fa-arrow-left">
                {{ $backText }}
            </x-ui.button>
        @endif
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    
    @if($actions)
        <div class="flex items-center space-x-3">
            {{ $actions }}
        </div>
    @endif
</div>