import SignaturePad from 'signature_pad';

window.SignaturePad = SignaturePad;

window.signaturePadComponent = function (existingUrl = null) {
    return {
        mode: 'draw',
        signatureData: '',
        pad: null,
        isEmpty: true,
        hasExisting: !!existingUrl,
        existingUrl: existingUrl,
        removeSignature: false,

        initPad() {
            this.$nextTick(() => {
                const canvas = this.$refs.signatureCanvas;
                if (!canvas) return;

                this.resizeCanvas(canvas);

                this.pad = new SignaturePad(canvas, {
                    minWidth: 0.5,
                    maxWidth: 2.5,
                    penColor: '#000000',
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                });

                canvas.addEventListener('endStroke', () => {
                    this.isEmpty = this.pad.isEmpty();
                    if (!this.isEmpty) {
                        this.signatureData = this.pad.toDataURL('image/png');
                        this.removeSignature = false;
                    }
                });

                window.addEventListener('resize', () => {
                    if (this.mode === 'draw') {
                        const data = this.pad.toData();
                        this.resizeCanvas(canvas);
                        if (data.length > 0) {
                            this.pad.fromData(data);
                        }
                    }
                });
            });
        },

        resizeCanvas(canvas) {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            if (this.pad) {
                this.pad.clear();
            }
        },

        switchMode(newMode) {
            this.mode = newMode;
            if (newMode === 'draw') {
                this.signatureData = '';
                this.isEmpty = true;
                this.$nextTick(() => {
                    const canvas = this.$refs.signatureCanvas;
                    if (canvas && this.pad) {
                        this.resizeCanvas(canvas);
                    } else if (canvas) {
                        this.initPad();
                    }
                });
            } else {
                this.signatureData = '';
                if (this.pad) {
                    this.pad.clear();
                    this.isEmpty = true;
                }
            }
        },

        clearPad() {
            if (this.pad) {
                this.pad.clear();
                this.isEmpty = true;
                this.signatureData = '';
            }
        },

        undoStroke() {
            if (this.pad) {
                const data = this.pad.toData();
                if (data.length > 0) {
                    data.pop();
                    this.pad.fromData(data);
                    this.isEmpty = this.pad.isEmpty();
                    if (this.isEmpty) {
                        this.signatureData = '';
                    } else {
                        this.signatureData = this.pad.toDataURL('image/png');
                    }
                }
            }
        },

        handleUpload(event) {
            let file;
            if (event.dataTransfer && event.dataTransfer.files) {
                file = event.dataTransfer.files[0];
            } else if (event.target && event.target.files) {
                file = event.target.files[0];
            }
            
            if (!file) return;

            const validTypes = ['image/png', 'image/jpeg', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                console.warn('Invalid file type. Please use PNG, JPG, or WebP.');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.signatureData = e.target.result;
                this.hasExisting = false;
                this.removeSignature = false;
            };
            reader.readAsDataURL(file);
        },

        removeExisting() {
            this.hasExisting = false;
            this.existingUrl = null;
            this.removeSignature = true;
            this.signatureData = '';
        },

        getSignatureValue() {
            if (this.removeSignature) return '';
            return this.signatureData;
        },
    };
};
