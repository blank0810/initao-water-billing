import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

const SUPPORTED_FORMATS = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.PDF_417,
    Html5QrcodeSupportedFormats.DATA_MATRIX,
    Html5QrcodeSupportedFormats.AZTEC,
];

/**
 * Alpine.js QR Scanner Component
 *
 * Works like a phone scanner — point the camera at a QR code,
 * it auto-detects with a visual capture flash, then displays the result.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('qrScanner', () => ({
        isOpen: false,
        isScanning: false,
        isCaptured: false,      // Brief "captured!" flash state
        isProcessingFile: false,
        scanner: null,
        error: '',
        mode: 'camera',
        rawResult: '',

        openScanner() {
            this.isOpen = true;
            this.error = '';
            this.rawResult = '';
            this.isCaptured = false;
            this.mode = 'camera';

            this.$nextTick(() => {
                this.startScanning();
            });
        },

        async switchMode(newMode) {
            if (this.mode === newMode) return;
            this.error = '';
            this.rawResult = '';
            this.isCaptured = false;

            if (this.mode === 'camera' && this.scanner) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                } catch (err) {
                    console.warn('[QR Scanner] Error stopping camera:', err);
                }
                this.scanner = null;
                this.isScanning = false;
            }

            this.mode = newMode;

            if (newMode === 'camera') {
                this.$nextTick(() => this.startScanning());
            }
        },

        async startScanning() {
            const readerId = 'qr-reader';
            this.scanner = new Html5Qrcode(readerId, {
                formatsToSupport: SUPPORTED_FORMATS,
                verbose: false,
                // Use browser's native BarcodeDetector if available (much better detection)
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            });
            this.isScanning = true;

            try {
                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 15,
                        // No qrbox — scan the ENTIRE camera frame, no positioning needed
                        disableFlip: false,
                    },
                    (decodedText, decodedResult) => this.onScanSuccess(decodedText, decodedResult),
                    () => {}
                );
            } catch (err) {
                console.error('[QR Scanner] Failed to start camera:', err);

                // If rear camera fails, try any available camera
                try {
                    await this.scanner.start(
                        { facingMode: 'user' },
                        { fps: 15, disableFlip: false },
                        (decodedText, decodedResult) => this.onScanSuccess(decodedText, decodedResult),
                        () => {}
                    );
                } catch (fallbackErr) {
                    console.error('[QR Scanner] Fallback camera also failed:', fallbackErr);
                    this.error = 'Could not access camera. Please check browser permissions.';
                    this.isScanning = false;
                }
            }
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.error = '';
            this.rawResult = '';
            this.isCaptured = false;
            this.isProcessingFile = true;

            try {
                const html5Qrcode = new Html5Qrcode('qr-upload-region', {
                    formatsToSupport: SUPPORTED_FORMATS,
                    verbose: false,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                });
                const result = await html5Qrcode.scanFile(file, false);
                html5Qrcode.clear();
                this.showCapturedResult(result);
            } catch (err) {
                console.error('[QR Scanner] File scan failed:', err);
                this.error = 'Could not detect a barcode in the image. Make sure the QR code is clear and well-lit, then try again.';
            } finally {
                this.isProcessingFile = false;
                event.target.value = '';
            }
        },

        onScanSuccess(decodedText, decodedResult) {
            // Prevent duplicate captures
            if (this.isCaptured || this.rawResult) return;

            const format = decodedResult?.result?.format?.formatName || 'unknown';
            console.log(`[QR Scanner] Detected format: ${format}`);
            console.log('[QR Scanner] Raw result:', decodedText);

            // Stop the camera immediately
            if (this.scanner && this.isScanning) {
                this.scanner.stop().then(() => {
                    this.isScanning = false;
                }).catch(() => {
                    this.isScanning = false;
                });
            }

            this.showCapturedResult(decodedText);
        },

        showCapturedResult(text) {
            // Show the green "Captured!" flash
            this.isCaptured = true;

            // After a brief flash, show the result
            setTimeout(() => {
                this.rawResult = text;
                this.isCaptured = false;
            }, 800);
        },

        scanAgain() {
            this.rawResult = '';
            this.error = '';
            this.isCaptured = false;

            if (this.mode === 'camera') {
                this.$nextTick(() => this.startScanning());
            }
        },

        async closeScanner() {
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                } catch (err) {
                    console.warn('[QR Scanner] Error stopping scanner:', err);
                }
                this.scanner = null;
            }
            this.isScanning = false;
            this.isProcessingFile = false;
            this.isCaptured = false;
            this.isOpen = false;
            this.error = '';
            this.rawResult = '';
            this.mode = 'camera';
        }
    }));
});
