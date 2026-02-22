import { Html5Qrcode } from 'html5-qrcode';
import { parsePhilSysQR } from '../parsers/philsys-parser.js';

/**
 * Alpine.js QR Scanner Component
 *
 * Dispatches 'qr-scanned' event with parsed data on success.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('qrScanner', () => ({
        isOpen: false,
        isScanning: false,
        isProcessingFile: false,
        scanner: null,
        error: '',
        mode: 'camera', // 'camera' or 'upload'

        openScanner() {
            this.isOpen = true;
            this.error = '';
            this.mode = 'camera';

            // Wait for modal DOM to render, then start camera
            this.$nextTick(() => {
                this.startScanning();
            });
        },

        async switchMode(newMode) {
            if (this.mode === newMode) return;
            this.error = '';

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
                // Reset file input so the same file can be re-selected
                event.target.value = '';
            }
        },

        onScanSuccess(decodedText) {
            const parsed = parsePhilSysQR(decodedText);

            if (parsed) {
                // Dispatch event with parsed data for the parent form to consume
                this.$dispatch('qr-scanned', parsed);
                this.closeScanner();
            } else {
                this.error = 'QR code detected but could not read ID data. Please try again.';
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
            this.mode = 'camera';
        }
    }));
});
