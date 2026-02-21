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
        <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900">
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
        <!-- Image Preview (shown when uploaded) -->
        <div x-show="signatureData" x-cloak>
            <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800 mb-3">
                <img :src="signatureData" alt="Uploaded signature" class="w-full h-auto max-h-40 object-contain mx-auto rounded">
            </div>
            <button type="button" @click="$refs.fileInput.click()" 
                    class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-sync-alt"></i>
                <span>Replace Image</span>
            </button>
        </div>

        <!-- Upload Area (shown when no image) -->
        <div x-show="!signatureData" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-7 lg:p-10 text-center bg-gray-50 dark:bg-gray-900 transition-colors cursor-pointer"
             @drop.prevent="handleUpload({ dataTransfer: { files: $event.dataTransfer.files } })"
             @dragover.prevent="$el.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20')"
             @dragleave.prevent="$el.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20')"
             @click="$refs.fileInput.click()">
            <div class="mb-5 flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                    <svg class="fill-current" width="24" height="24" viewBox="0 0 29 28" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5019 3.91699C14.2852 3.91699 14.0899 4.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z" />
                    </svg>
                </div>
            </div>
            <h4 class="mb-3 font-semibold text-gray-800 dark:text-white/90 text-base">Drag & Drop Image Here</h4>
            <span class="text-center mb-4 block w-full text-sm text-gray-700 dark:text-gray-400">Drag and drop your PNG, JPG, or WebP image here or browse</span>
            <span class="font-medium underline text-sm text-blue-600 dark:text-blue-400">Browse File</span>
        </div>
        <input type="file" x-ref="fileInput" accept="image/png,image/jpeg,image/webp" @change="handleUpload($event)" class="hidden">
    </div>

    {{-- Hidden inputs for form submission --}}
    <input type="hidden" :name="'{{ $name }}'" :value="getSignatureValue()">
    <input type="hidden" :name="'{{ $removeName }}'" :value="removeSignature ? '1' : ''">
</div>
