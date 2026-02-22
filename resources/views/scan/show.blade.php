<x-guest-layout>
    @push('styles')
    <style>
        body { overflow: auto !important; }
        body > div { height: auto !important; }
        body > div > div:first-child { align-items: stretch !important; }
        .scan-page { min-height: 100dvh; display: flex; flex-direction: column; }
    </style>
    @endpush

    <div x-data="phoneScan('{{ $token }}')" class="scan-page bg-gray-900 w-full">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 px-4 py-3 shadow-sm flex items-center gap-3 shrink-0 z-10">
            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <i class="fas fa-qrcode text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-gray-900 dark:text-white">Scan National ID</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="headerHint"></p>
            </div>
        </div>

        {{-- 1. SCANNING — camera with detection overlay --}}
        <div x-show="step === 'scanning' || step === 'detected'" class="flex-1 relative bg-black">
            <video x-ref="video" class="w-full h-full object-cover" autoplay playsinline muted></video>
            <canvas x-ref="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

            {{-- Scanning hint --}}
            <div x-show="step === 'scanning'" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-sm text-white font-medium">Looking for QR code...</span>
                </div>
            </div>

            {{-- Detected overlay — spinner on top of frozen frame --}}
            <div x-show="step === 'detected'" x-cloak
                 class="absolute inset-0 flex items-center justify-center bg-black/40">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-white mb-3"></i>
                    <p class="text-white font-bold text-lg">QR Code Found!</p>
                    <p class="text-white/70 text-sm">Reading data...</p>
                </div>
            </div>
        </div>

        {{-- 2. PREVIEW — show result, user confirms --}}
        <div x-show="step === 'preview'" x-cloak class="flex-1 flex flex-col bg-gray-900 px-5 py-6">
            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                    <i class="fas fa-check text-green-400 text-3xl"></i>
                </div>
                <p class="text-lg font-bold text-white mb-4">QR Code Captured!</p>
                <div class="w-full max-w-sm bg-gray-800 rounded-lg border border-gray-700 p-3 mb-6">
                    <p class="text-xs text-gray-400 mb-1 font-medium">Scanned Data</p>
                    <pre class="text-xs text-gray-200 whitespace-pre-wrap break-all max-h-40 overflow-y-auto font-mono" x-text="scannedData.substring(0, 500)"></pre>
                </div>
            </div>
            <div class="flex gap-3 w-full max-w-sm mx-auto">
                <button @click="rescan()" type="button"
                        class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium text-sm">
                    <i class="fas fa-redo mr-1"></i> Rescan
                </button>
                <button @click="sendToPC()" type="button"
                        class="flex-[2] px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Send to Computer
                </button>
            </div>
        </div>

        {{-- 3. SENDING --}}
        <div x-show="step === 'sending'" x-cloak class="flex-1 flex items-center justify-center bg-gray-900">
            <div class="text-center px-6">
                <i class="fas fa-spinner fa-spin text-4xl text-purple-400 mb-4"></i>
                <p class="text-lg font-semibold text-white">Sending to computer...</p>
            </div>
        </div>

        {{-- 4. SUCCESS --}}
        <div x-show="step === 'success'" x-cloak class="flex-1 flex items-center justify-center bg-gray-900">
            <div class="text-center px-6">
                <div class="w-20 h-20 mx-auto rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                    <i class="fas fa-check text-green-400 text-4xl"></i>
                </div>
                <p class="text-xl font-bold text-white">Sent Successfully!</p>
                <p class="text-sm text-gray-400 mt-2">You can return to your computer.</p>
            </div>
        </div>

        {{-- 5. ERROR --}}
        <div x-show="step === 'error'" x-cloak class="flex-1 flex items-center justify-center bg-gray-900 px-6">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto rounded-full bg-red-500/20 flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>
                <p class="text-lg font-bold text-white mb-2">Something went wrong</p>
                <p class="text-sm text-gray-400 mb-4" x-text="errorMessage"></p>
                <button @click="rescan()" type="button"
                        class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                    Try Again
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('phoneScan', (token) => ({
                step: 'scanning',
                scannedData: '',
                scannedFormat: '',
                errorMessage: '',
                stream: null,
                detector: null,
                captured: false,
                detectCount: 0,
                lastValue: '',

                get headerHint() {
                    return {
                        scanning: 'Point camera at the QR code',
                        detected: 'Reading QR code...',
                        preview: 'Review scanned data',
                        sending: 'Sending...',
                        success: 'Done!',
                        error: 'Failed',
                    }[this.step] || '';
                },

                init() {
                    this.$nextTick(() => this.startCamera());
                },

                // ── Start camera + detection loop ─────────────────
                async startCamera() {
                    const video = this.$refs.video;
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
                        });
                    } catch {
                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia({
                                video: { facingMode: 'user' },
                            });
                        } catch {
                            this.step = 'error';
                            this.errorMessage = 'Could not access camera. Check browser permissions.';
                            return;
                        }
                    }

                    video.srcObject = this.stream;
                    await video.play();

                    // Size overlay canvas to match video
                    const overlay = this.$refs.overlay;
                    overlay.width = video.videoWidth;
                    overlay.height = video.videoHeight;

                    // Use native BarcodeDetector (Android Chrome) for bounding boxes
                    if ('BarcodeDetector' in window) {
                        this.detector = new BarcodeDetector({ formats: ['qr_code'] });
                        this.scanLoop();
                    } else {
                        // Fallback: use Html5Qrcode (no bounding box, but still works)
                        this.fallbackScan();
                    }
                },

                async scanLoop() {
                    if (this.captured) return;

                    try {
                        const codes = await this.detector.detect(this.$refs.video);
                        const ctx = this.$refs.overlay.getContext('2d');
                        ctx.clearRect(0, 0, this.$refs.overlay.width, this.$refs.overlay.height);

                        if (codes.length > 0) {
                            const qr = codes[0];
                            this.drawFrame(ctx, qr.boundingBox);

                            // Require 3 consecutive detections of the same QR
                            if (qr.rawValue === this.lastValue) {
                                this.detectCount++;
                            } else {
                                this.lastValue = qr.rawValue;
                                this.detectCount = 1;
                            }

                            if (this.detectCount >= 3) {
                                this.onCaptured(qr.rawValue, qr.boundingBox);
                                return;
                            }
                        } else {
                            this.detectCount = 0;
                            this.lastValue = '';
                        }
                    } catch {}

                    requestAnimationFrame(() => this.scanLoop());
                },

                // Draw green frame around detected QR
                drawFrame(ctx, box) {
                    const pad = 16;
                    const x = box.x - pad;
                    const y = box.y - pad;
                    const w = box.width + pad * 2;
                    const h = box.height + pad * 2;
                    const corner = Math.min(w, h) * 0.25;

                    ctx.strokeStyle = '#22c55e';
                    ctx.lineWidth = 4;
                    ctx.lineCap = 'round';

                    // Top-left
                    ctx.beginPath();
                    ctx.moveTo(x, y + corner); ctx.lineTo(x, y); ctx.lineTo(x + corner, y);
                    ctx.stroke();
                    // Top-right
                    ctx.beginPath();
                    ctx.moveTo(x + w - corner, y); ctx.lineTo(x + w, y); ctx.lineTo(x + w, y + corner);
                    ctx.stroke();
                    // Bottom-left
                    ctx.beginPath();
                    ctx.moveTo(x, y + h - corner); ctx.lineTo(x, y + h); ctx.lineTo(x + corner, y + h);
                    ctx.stroke();
                    // Bottom-right
                    ctx.beginPath();
                    ctx.moveTo(x + w - corner, y + h); ctx.lineTo(x + w, y + h); ctx.lineTo(x + w, y + h - corner);
                    ctx.stroke();

                    // Subtle fill
                    ctx.fillStyle = 'rgba(34, 197, 94, 0.08)';
                    ctx.fillRect(x, y, w, h);
                },

                // QR captured — freeze, show spinner, then preview
                onCaptured(text, box) {
                    this.captured = true;
                    this.scannedData = text;
                    this.scannedFormat = 'QR_CODE';

                    // Keep the final frame drawn, freeze video
                    this.$refs.video.pause();
                    this.step = 'detected';

                    // Hold for 1.5s so user sees the detection, then go to preview
                    setTimeout(() => {
                        this.stopCamera();
                        this.step = 'preview';
                    }, 1500);
                },

                // Fallback for browsers without BarcodeDetector
                async fallbackScan() {
                    const scanner = new window.Html5Qrcode('phone-qr-reader-fallback', {
                        verbose: false,
                        experimentalFeatures: { useBarCodeDetectorIfSupported: false },
                    });

                    // Create a hidden div for html5-qrcode (it needs a container)
                    let container = document.getElementById('phone-qr-reader-fallback');
                    if (!container) {
                        container = document.createElement('div');
                        container.id = 'phone-qr-reader-fallback';
                        container.style.display = 'none';
                        document.body.appendChild(container);
                    }

                    this._fallbackScanner = scanner;
                    // Stop the native camera (html5-qrcode will open its own)
                    this.stopCamera();

                    try {
                        await scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, disableFlip: false },
                            (text) => {
                                if (this.captured) return;
                                this.captured = true;
                                this.scannedData = text;
                                this.scannedFormat = 'QR_CODE';
                                scanner.stop().catch(() => {});
                                this.step = 'preview';
                            },
                            () => {},
                        );
                    } catch {
                        this.step = 'error';
                        this.errorMessage = 'Could not start QR scanner.';
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                    }
                },

                // ── Send to PC ────────────────────────────────────
                async sendToPC() {
                    this.step = 'sending';
                    try {
                        const response = await fetch(`/api/scan/${token}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({ raw_data: this.scannedData, format: this.scannedFormat }),
                        });
                        if (!response.ok) {
                            const body = await response.text();
                            throw new Error(`HTTP ${response.status}: ${body.substring(0, 150)}`);
                        }
                        this.step = 'success';
                    } catch (err) {
                        this.step = 'error';
                        this.errorMessage = err.message || 'Failed to send.';
                    }
                },

                // ── Rescan ────────────────────────────────────────
                async rescan() {
                    this.captured = false;
                    this.detectCount = 0;
                    this.lastValue = '';
                    this.scannedData = '';
                    this.errorMessage = '';
                    this.step = 'scanning';

                    if (this._fallbackScanner) {
                        try { this._fallbackScanner.clear(); } catch {}
                        this._fallbackScanner = null;
                    }

                    this.$nextTick(() => this.startCamera());
                },
            }));
        });
    </script>
    @endpush
</x-guest-layout>
