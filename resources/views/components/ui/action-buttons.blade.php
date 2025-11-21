<!-- Standardized Action Buttons Component -->
<div class="flex items-center gap-2">
    @if($view ?? true)
    <button onclick="{{ $viewClick }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors" title="View">
        <i class="fas fa-eye"></i>
    </button>
    @endif
    
    @if($edit ?? true)
    <button onclick="{{ $editClick }}" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded transition-colors" title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    @endif
    
    @if($delete ?? false)
    <button onclick="{{ $deleteClick }}" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
    @endif
    
    @if(isset($custom))
    {!! $custom !!}
    @endif
</div>
