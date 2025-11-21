@props(['name', 'title' => null, 'maxWidth' => 'md', 'closeable' => true])

<!-- Use the new standardized modal component -->
<x-ui.modal 
    :name="$name" 
    :title="$title"
    :maxWidth="$maxWidth"
    :closeable="$closeable"
    {{ $attributes }}
>
    {{ $slot }}
</x-ui.modal>