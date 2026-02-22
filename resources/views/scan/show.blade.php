<x-guest-layout>
    @push('styles')
    <style>
        /* Override guest layout constraints for full-viewport scanner */
        body { overflow: auto !important; }
        body > div { height: auto !important; }
        body > div > div:first-child { align-items: stretch !important; }
        .scan-page { min-height: 100dvh; display: flex; flex-direction: column; }
        /* Ensure html5-qrcode video fills container */
        #phone-qr-reader { width: 100%; }
        #phone-qr-reader video { width: 100% !important; object-fit: cover; }
    </style>
    @endpush

    <div x-data="phoneScanApp('{{ $token }}')" class="scan-page bg-gray-900 w-full">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 px-4 py-3 shadow-sm flex items-center gap-3 shrink-0">
            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <i class="fas fa-qrcode text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-gray-900 dark:text-white">Scan National ID</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Point camera at the QR code</p>
            </div>
        </div>

        {{-- Scanner state --}}
        <div x-show="state === 'scanning'" class="flex-1 relative">
            <div id="phone-qr-reader" class="w-full h-full"></div>

            {{-- Scanning indicator overlay --}}
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-sm text-white font-medium">Scanning...</span>
                </div>
            </div>
        </div>

        {{-- Submitting state --}}
        <div x-show="state === 'submitting'" class="flex-1 flex items-center justify-center bg-gray-900">
            <div class="text-center px-6">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-400 mb-4"></i>
                <p class="text-lg font-semibold text-white">Sending to computer...</p>
                <p class="text-sm text-gray-400 mt-1">Please wait</p>
            </div>
        </div>

        {{-- Success state --}}
        <div x-show="state === 'success'" class="flex-1 flex items-center justify-center bg-gray-900">
            <div class="text-center px-6">
                <div class="w-20 h-20 mx-auto rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                    <i class="fas fa-check text-green-400 text-4xl"></i>
                </div>
                <p class="text-xl font-bold text-white">Scanned Successfully!</p>
                <p class="text-sm text-gray-400 mt-2">You can return to your computer.</p>
            </div>
        </div>

        {{-- Error state --}}
        <div x-show="state === 'error'" class="flex-1 flex items-center justify-center bg-gray-900 px-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-red-500/20 flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>
                <p class="text-lg font-bold text-white mb-2">Something went wrong</p>
                <p class="text-sm text-gray-400 mb-4" x-text="errorMessage"></p>
                <button @click="retry()" type="button"
                        class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium text-base">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('phoneScanApp', (token) => ({
                state: 'scanning',
                errorMessage: '',
                scanner: null,

                init() {
                    this.$nextTick(() => this.startScanning());
                },

                async startScanning() {
                    const { Html5Qrcode } = await import('html5-qrcode');
                    this.scanner = new Html5Qrcode('phone-qr-reader', {
                        verbose: false,
                        experimentalFeatures: { useBarCodeDetectorIfSupported: true }
                    });

                    try {
                        await this.scanner.start(
                            { facingMode: 'environment' },
                            { fps: 15, disableFlip: false },
                            (decodedText, decodedResult) => this.onScanSuccess(decodedText, decodedResult),
                            () => {}
                        );
                    } catch (err) {
                        // Try front camera as fallback
                        try {
                            await this.scanner.start(
                                { facingMode: 'user' },
                                { fps: 15, disableFlip: false },
                                (decodedText, decodedResult) => this.onScanSuccess(decodedText, decodedResult),
                                () => {}
                            );
                        } catch (err2) {
                            this.state = 'error';
                            this.errorMessage = 'Could not access camera. Please check browser permissions and try again.';
                        }
                    }
                },

                async onScanSuccess(decodedText, decodedResult) {
                    if (this.state !== 'scanning') return;

                    const format = decodedResult?.result?.format?.formatName || 'unknown';

                    // Stop camera
                    if (this.scanner) {
                        try { await this.scanner.stop(); } catch (e) {}
                    }

                    this.state = 'submitting';
                    await this.submitResult(decodedText, format);
                },

                async submitResult(rawData, format) {
                    const maxRetries = 3;
                    for (let attempt = 1; attempt <= maxRetries; attempt++) {
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                            const response = await fetch(`/api/scan/${token}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken || '',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: JSON.stringify({ raw_data: rawData, format }),
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.state = 'success';
                                return;
                            } else {
                                throw new Error(data.message || 'Submission failed');
                            }
                        } catch (err) {
                            if (attempt === maxRetries) {
                                this.state = 'error';
                                this.errorMessage = err.message || 'Failed to send scan result. Please check your connection.';
                                return;
                            }
                            await new Promise(r => setTimeout(r, 1000));
                        }
                    }
                },

                async retry() {
                    this.state = 'scanning';
                    this.errorMessage = '';
                    // Clean up old scanner
                    if (this.scanner) {
                        try { this.scanner.clear(); } catch (e) {}
                        this.scanner = null;
                    }
                    this.$nextTick(() => this.startScanning());
                },
            }));
        });
    </script>
    @endpush
</x-guest-layout>
