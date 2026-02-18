@props([
    'name' => 'signature',
    'existingUrl' => null,
    'label' => 'Digital Signature',
    'removeName' => 'remove_signature',
])

<div x-data="signaturePadComponent({{ $existingUrl ? '\'' . $existingUrl . '\'' : 'null' }})"
     x-init="$nextTick(() => { if (mode === 'draw') initPad(); })"
     class="w-full">

    {{-- Label --}}
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }} <span class="text-gray-400 font-normal">(Optional)</span>
    </label>

    {{-- Existing Signature Preview --}}
    <div x-show="hasExisting" class="mb-3">
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
            <img :src="existingUrl" alt="Current signature" class="h-12 bg-white rounded border p-1">
            <span class="text-sm text-gray-600 dark:text-gray-400">Current signature</span>
            <button type="button" @click="removeExisting()"
                    class="ml-auto text-xs text-red-600 hover:text-red-800 dark:text-red-400">
                <i class="fas fa-trash-alt mr-1"></i> Remove
            </button>
        </div>
    </div>

    {{-- Mode Tabs --}}
    <div class="flex border-b border-gray-200 dark:border-gray-600 mb-3">
        <button type="button" @click="switchMode('draw')"
                :class="mode === 'draw'
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
            <i class="fas fa-pen mr-1.5"></i> Draw
        </button>
        <button type="button" @click="switchMode('upload')"
                :class="mode === 'upload'
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
            <i class="fas fa-upload mr-1.5"></i> Upload
        </button>
    </div>

    {{-- Draw Mode --}}
    <div x-show="mode === 'draw'" x-transition>
        <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
            <canvas x-ref="signatureCanvas"
                    class="w-full cursor-crosshair"
                    style="height: 150px; touch-action: none;"></canvas>

            {{-- Placeholder --}}
            <div x-show="isEmpty && !signatureData"
                 class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span class="text-gray-400 dark:text-gray-500 text-sm">
                    <i class="fas fa-pen-fancy mr-1"></i> Sign here
                </span>
            </div>
        </div>

        {{-- Draw Controls --}}
        <div class="flex items-center justify-between mt-2">
            <div class="flex gap-2">
                <button type="button" @click="undoStroke()" :disabled="isEmpty"
                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-undo mr-1"></i> Undo
                </button>
                <button type="button" @click="clearPad()" :disabled="isEmpty"
                        class="px-3 py-1.5 text-xs font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 border border-red-300 dark:border-red-600 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-eraser mr-1"></i> Clear
                </button>
            </div>
            <span x-show="!isEmpty" x-transition class="text-xs text-green-600 dark:text-green-400">
                <i class="fas fa-check-circle mr-1"></i> Signature captured
            </span>
        </div>
    </div>

    {{-- Upload Mode --}}
    <div x-show="mode === 'upload'" x-transition>
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center bg-white dark:bg-gray-800">
            {{-- Preview uploaded image --}}
            <template x-if="signatureData && mode === 'upload'">
                <div class="mb-3">
                    <img :src="signatureData" alt="Uploaded signature" class="h-16 mx-auto bg-white rounded border p-1">
                </div>
            </template>

            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-image mr-2"></i>
                <span x-text="signatureData && mode === 'upload' ? 'Change Image' : 'Choose Image'"></span>
                <input type="file" accept="image/*" @change="handleUpload($event)" class="hidden">
            </label>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, or WEBP. Transparent background recommended.</p>
        </div>
    </div>

    {{-- Hidden inputs for form submission --}}
    <input type="hidden" :name="'{{ $name }}'" :value="getSignatureValue()">
    <input type="hidden" :name="'{{ $removeName }}'" :value="removeSignature ? '1' : ''">
</div>
