import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

// Support multiple barcode formats — PhilSys ID may use QR, PDF417, or Data Matrix
const SUPPORTED_FORMATS = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.PDF_417,
    Html5QrcodeSupportedFormats.DATA_MATRIX,
    Html5QrcodeSupportedFormats.AZTEC,
];

/**
 * Alpine.js QR Scanner Component
 *
 * Dispatches 'qr-scanned' event with parsed data on success.
 * For now, displays raw QR output for debugging/testing.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('qrScanner', () => ({
        isOpen: false,
        isScanning: false,
        isProcessingFile: false,
        scanner: null,
        error: '',
        mode: 'camera', // 'camera' or 'upload'
        rawResult: '',   // Raw QR code output for display

        openScanner() {
            this.isOpen = true;
            this.error = '';
            this.rawResult = '';
            this.mode = 'camera';

            this.$nextTick(() => {
                this.startScanning();
            });
        },

        async switchMode(newMode) {
            if (this.mode === newMode) return;
            this.error = '';
            this.rawResult = '';

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
                verbose: false
            });
            this.isScanning = true;

            try {
                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 15,
                        // Scan 80% of the viewfinder — no tiny box, much more forgiving
                        qrbox: (viewfinderWidth, viewfinderHeight) => ({
                            width: Math.floor(viewfinderWidth * 0.8),
                            height: Math.floor(viewfinderHeight * 0.8),
                        }),
                        aspectRatio: 1.0,
                    },
                    (decodedText, decodedResult) => this.onScanSuccess(decodedText, decodedResult),
                    () => {} // Ignore per-frame scan misses
                );
            } catch (err) {
                console.error('[QR Scanner] Failed to start camera:', err);
                this.error = 'Could not access camera. Please check browser permissions.';
                this.isScanning = false;
            }
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.error = '';
            this.rawResult = '';
            this.isProcessingFile = true;

            try {
                const html5Qrcode = new Html5Qrcode('qr-upload-region', {
                    formatsToSupport: SUPPORTED_FORMATS,
                    verbose: false
                });
                const result = await html5Qrcode.scanFile(file, /* showImage= */ false);
                html5Qrcode.clear();
                this.onScanSuccess(result);
            } catch (err) {
                console.error('[QR Scanner] File scan failed:', err);
                this.error = 'Could not detect a barcode in the image. Make sure the QR code is clear and well-lit, then try again.';
            } finally {
                this.isProcessingFile = false;
                event.target.value = '';
            }
        },

        onScanSuccess(decodedText, decodedResult) {
            const format = decodedResult?.result?.format?.formatName || 'unknown';
            console.log(`[QR Scanner] Detected format: ${format}`);
            console.log('[QR Scanner] Raw result:', decodedText);

            this.rawResult = decodedText;

            // Stop camera after successful scan
            if (this.scanner && this.isScanning) {
                this.scanner.stop().then(() => {
                    this.isScanning = false;
                }).catch(() => {
                    this.isScanning = false;
                });
            }
        },

        scanAgain() {
            this.rawResult = '';
            this.error = '';

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
            this.isOpen = false;
            this.error = '';
            this.rawResult = '';
            this.mode = 'camera';
        }
    }));
});
