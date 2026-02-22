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
        scanner: null,
        error: '',

        openScanner() {
            this.isOpen = true;
            this.error = '';

            // Wait for modal DOM to render, then start camera
            this.$nextTick(() => {
                this.startScanning();
            });
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
            this.isOpen = false;
            this.error = '';
        }
    }));
});
