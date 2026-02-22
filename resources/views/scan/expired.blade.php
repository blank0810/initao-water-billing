<x-guest-layout>
    <div class="text-center px-6 py-12">
        <div class="w-20 h-20 mx-auto rounded-full bg-amber-500/20 flex items-center justify-center mb-4">
            <i class="fas fa-clock text-amber-400 text-3xl"></i>
        </div>
        <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            @if($reason === 'used')
                Link Already Used
            @else
                Link Expired
            @endif
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if($reason === 'used')
                This scan link has already been used. Please generate a new one from your computer.
            @else
                This scan link has expired. Please generate a new one from your computer.
            @endif
        </p>
    </div>
</x-guest-layout>
