import { Html5Qrcode } from 'html5-qrcode';

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

            // Wait for modal DOM to render, then start camera
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
            this.scanner = new Html5Qrcode(readerId);
            this.isScanning = true;

            try {
                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                    },
                    (decodedText) => this.onScanSuccess(decodedText),
                    () => {} // Ignore scan errors (no QR found yet)
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
                const html5Qrcode = new Html5Qrcode('qr-upload-region');
                const result = await html5Qrcode.scanFile(file, true);
                html5Qrcode.clear();
                this.onScanSuccess(result);
            } catch (err) {
                console.error('[QR Scanner] File scan failed:', err);
                this.error = 'Could not read QR code from image. Please try a clearer photo.';
            } finally {
                this.isProcessingFile = false;
                event.target.value = '';
            }
        },

        onScanSuccess(decodedText) {
            console.log('[QR Scanner] Raw result:', decodedText);

            // For now, just display the raw result — no parsing yet
            this.rawResult = decodedText;

            // Stop camera after successful scan so it doesn't keep scanning
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
