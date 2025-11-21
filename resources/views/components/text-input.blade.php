@props(['disabled' => false, 'label' => null, 'error' => null, 'icon' => null])

<x-ui.input 
    :label="$label" 
    :error="$error" 
    :icon="$icon"
    :disabled="$disabled"
    {{ $attributes }}
/>
