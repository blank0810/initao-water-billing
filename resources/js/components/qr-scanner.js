import jsQR from "jsqr";
import QRCode from "qrcode";
import { parsePhilSysQR } from "../parsers/philsys-parser.js";

// ─── Image Processing (kept from upload implementation) ─────────

function getImageData(source, crop = null) {
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const sw = source.naturalWidth || source.videoWidth || source.width;
    const sh = source.naturalHeight || source.videoHeight || source.height;
    if (crop) {
        canvas.width = crop.w;
        canvas.height = crop.h;
        ctx.drawImage(
            source,
            crop.x,
            crop.y,
            crop.w,
            crop.h,
            0,
            0,
            crop.w,
            crop.h,
        );
    } else {
        canvas.width = sw;
        canvas.height = sh;
        ctx.drawImage(source, 0, 0);
    }
    return ctx.getImageData(0, 0, canvas.width, canvas.height);
}

function binarize(imageData) {
    const data = imageData.data;
    const len = data.length / 4;
    const gray = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
        const idx = i * 4;
        gray[i] = Math.round(
            0.299 * data[idx] + 0.587 * data[idx + 1] + 0.114 * data[idx + 2],
        );
    }
    const histogram = new Array(256).fill(0);
    for (let i = 0; i < len; i++) histogram[gray[i]]++;
    let sum = 0;
    for (let i = 0; i < 256; i++) sum += i * histogram[i];
    let sumB = 0,
        wB = 0,
        maxVar = 0,
        threshold = 128;
    for (let t = 0; t < 256; t++) {
        wB += histogram[t];
        if (wB === 0) continue;
        const wF = len - wB;
        if (wF === 0) break;
        sumB += t * histogram[t];
        const mB = sumB / wB;
        const mF = (sum - sumB) / wF;
        const variance = wB * wF * (mB - mF) * (mB - mF);
        if (variance > maxVar) {
            maxVar = variance;
            threshold = t;
        }
    }
    for (let i = 0; i < len; i++) {
        const val = gray[i] > threshold ? 255 : 0;
        const idx = i * 4;
        data[idx] = data[idx + 1] = data[idx + 2] = val;
    }
    return imageData;
}

function tryJsQR(imageData) {
    const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: "attemptBoth",
    });
    return code ? code.data : null;
}

function loadImage(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            resolve(img);
        };
        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error("Failed to load image"));
        };
        img.src = url;
    });
}

// ─── Alpine Component ───────────────────────────────────────────

document.addEventListener("alpine:init", () => {
    Alpine.data("qrScanner", () => ({
        isOpen: false,
        isCaptured: false,
        isProcessingFile: false,
        error: "",
        mode: "phone", // 'phone' or 'upload'
        rawResult: "",

        // Phone scan state
        scanToken: "",
        scanUrl: "",
        qrCodeDataUrl: "",
        expiresAt: null,
        countdown: "",
        isExpired: false,
        isWaiting: false,
        _countdownTimer: null,
        _echoChannel: null,

        openScanner() {
            this.isOpen = true;
            this.error = "";
            this.rawResult = "";
            this.isCaptured = false;
            this.mode = "phone";

            this.$nextTick(() => this.createScanSession());
        },

        async switchMode(newMode) {
            if (this.mode === newMode) return;
            this.error = "";
            this.rawResult = "";
            this.isCaptured = false;

            if (this.mode === "phone") {
                this._cleanupPhoneSession();
            }

            this.mode = newMode;

            if (newMode === "phone") {
                this.$nextTick(() => this.createScanSession());
            }
        },

        // ─── Phone Scan Mode ──────────────────────────────────

        async createScanSession() {
            this.isWaiting = true;
            this.isExpired = false;
            this.error = "";
            this.scanToken = "";
            this.qrCodeDataUrl = "";
            this.countdown = "";

            try {
                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]',
                )?.content;
                if (!csrfToken) throw new Error("CSRF token not found");

                const response = await fetch("/api/scan-sessions", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    console.error(
                        "[QR Scanner] Session create failed:",
                        response.status,
                        response.statusText,
                    );
                    throw new Error(`Server error (${response.status})`);
                }

                const data = await response.json();
                if (!data.success)
                    throw new Error(data.message || "Failed to create session");

                this.scanToken = data.token;
                this.scanUrl = `${window.location.origin}/scan/${data.token}`;
                this.expiresAt = new Date(data.expires_at);

                // Generate QR code image from the scan URL
                this.qrCodeDataUrl = await QRCode.toDataURL(this.scanUrl, {
                    width: 280,
                    margin: 2,
                    color: { dark: "#1f2937", light: "#ffffff" },
                });

                // Start countdown timer
                this._startCountdown();

                // Listen for scan result via Echo
                this._listenForResult();
            } catch (err) {
                console.error("[QR Scanner] Failed to create session:", err);
                this.error = "Failed to create scan session. Please try again.";
                this.isWaiting = false;
            }
        },

        _startCountdown() {
            this._countdownTimer = setInterval(() => {
                const now = new Date();
                const diff = this.expiresAt - now;

                if (diff <= 0) {
                    this.isExpired = true;
                    this.countdown = "Expired";
                    this._cleanupPhoneSession();
                    return;
                }

                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                this.countdown = `${mins}:${secs.toString().padStart(2, "0")}`;
            }, 1000);
        },

        _listenForResult() {
            if (!window.Echo) {
                console.error("[QR Scanner] Echo not available");
                return;
            }

            this._echoChannel = window.Echo.private(
                `scan-session.${this.scanToken}`,
            ).listen(".scan.completed", (data) => {
                console.log(
                    "[QR Scanner] Received scan result via Reverb:",
                    data,
                );
                this._cleanupPhoneSession();
                this.isWaiting = false;
                this.showCapturedResult(data.raw_data);
            });
        },

        regenerateSession() {
            this._cleanupPhoneSession();
            this.createScanSession();
        },

        _cleanupPhoneSession() {
            if (this._countdownTimer) {
                clearInterval(this._countdownTimer);
                this._countdownTimer = null;
            }
            if (this._echoChannel && window.Echo) {
                window.Echo.leave(`scan-session.${this.scanToken}`);
                this._echoChannel = null;
            }
        },

        // ─── Upload Mode ──────────────────────────────────────

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.error = "";
            this.rawResult = "";
            this.isCaptured = false;
            this.isProcessingFile = true;

            try {
                const img = await loadImage(file);
                const iw = img.naturalWidth;
                const ih = img.naturalHeight;

                // Attempt 1: jsQR on full image
                let result = tryJsQR(getImageData(img));

                // Attempt 2: jsQR on binarized full image
                if (!result) result = tryJsQR(binarize(getImageData(img)));

                // Attempt 3: jsQR on right half (binarized) — PhilSys QR is on the right
                if (!result) {
                    const crop = {
                        x: Math.floor(iw * 0.4),
                        y: 0,
                        w: Math.ceil(iw * 0.6),
                        h: ih,
                    };
                    result = tryJsQR(binarize(getImageData(img, crop)));
                }

                // Attempt 4: Native BarcodeDetector
                if (!result && "BarcodeDetector" in window) {
                    try {
                        const detector = new BarcodeDetector({
                            formats: [
                                "qr_code",
                                "pdf417",
                                "data_matrix",
                                "aztec",
                            ],
                        });
                        const barcodes = await detector.detect(img);
                        if (barcodes.length > 0) result = barcodes[0].rawValue;
                    } catch (e) {}
                }

                if (result) {
                    this.showCapturedResult(result);
                } else {
                    this.error =
                        "Could not detect a barcode. Make sure the QR code is clearly visible.";
                }
            } catch (err) {
                this.error = "Failed to load the image file.";
            } finally {
                this.isProcessingFile = false;
                event.target.value = "";
            }
        },

        // ─── Shared ──────────────────────────────────────────

        showCapturedResult(text) {
            this.isCaptured = true;
            setTimeout(() => {
                this.rawResult = text;
                this.isCaptured = false;

                // Parse the raw QR data and dispatch parsed fields
                const parsed = parsePhilSysQR(text);
                if (parsed) {
                    this.$dispatch("qr-scanned", parsed);
                } else {
                    this.$dispatch("qr-scanned", { rawData: text });
                }

                // Auto-close the scanner modal after dispatching
                this.closeScanner();
            }, 800);
        },

        scanAgain() {
            this.rawResult = "";
            this.error = "";
            this.isCaptured = false;

            if (this.mode === "phone") {
                this.createScanSession();
            }
        },

        async closeScanner() {
            this._cleanupPhoneSession();
            this.isOpen = false;
            this.isWaiting = false;
            this.isProcessingFile = false;
            this.isCaptured = false;
            this.error = "";
            this.rawResult = "";
            this.mode = "phone";
            this.qrCodeDataUrl = "";
            this.scanToken = "";
        },
    }));
});
