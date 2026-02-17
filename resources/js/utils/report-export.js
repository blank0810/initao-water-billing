/**
 * ReportExporter - Client-side export utility for Initao Water Billing reports.
 *
 * Depends on (loaded via CDN):
 *   - XLSX (SheetJS) for Excel exports
 *   - html2pdf.js for PDF exports
 *
 * Two export contexts:
 *   1. Table exports (data-driven) - from Alpine.js filteredData arrays
 *   2. Print exports (DOM-capture) - from rendered report layout pages
 */
const ReportExporter = {

    // =========================================================================
    // TABLE EXPORTS — from Alpine.js data arrays
    // =========================================================================

    /**
     * Export data array to Excel (.xlsx).
     * @param {Object[]} data     - Array of row objects (e.g. filteredData)
     * @param {Object[]} columns  - Column definitions [{key, label, format?}]
     * @param {string}   filename - Base filename without extension
     * @param {string}   [sheetName='Report'] - Worksheet name
     */
    tableToExcel(data, columns, filename, sheetName = 'Report') {
        if (typeof XLSX === 'undefined') {
            alert('Excel export library not loaded. Please try again.');
            return;
        }

        const header = columns.map(c => c.label);
        const rows = data.map(row =>
            columns.map(c => this._rawValue(row[c.key], c.format))
        );

        const ws = XLSX.utils.aoa_to_sheet([header, ...rows]);

        // Set column widths
        ws['!cols'] = columns.map(c => ({
            wch: Math.max(c.label.length + 4, 15)
        }));

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, `${filename}.xlsx`);
    },

    /**
     * Export data array to PDF by building an HTML table and converting via html2pdf.
     * @param {Object[]} data     - Array of row objects
     * @param {Object[]} columns  - Column definitions [{key, label, format?}]
     * @param {string}   filename - Base filename without extension
     * @param {Object}   [options={}] - html2pdf options override
     */
    tableToPdf(data, columns, filename, options = {}) {
        if (typeof html2pdf === 'undefined') {
            alert('PDF export library not loaded. Please try again.');
            return;
        }

        const html = this._buildHtmlTable(data, columns, this._titleFromFilename(filename));

        // Create element for html2pdf — do NOT manually append to DOM.
        // html2pdf.from(element) handles DOM placement internally via its
        // own overlay, so manual appending causes double-nesting and blank output.
        const container = document.createElement('div');
        container.style.cssText = 'padding:10mm; background:#fff;';
        container.innerHTML = html;

        // Show a loading overlay so the user does not see html2pdf's
        // internal rendering container flash on screen.
        const loadingOverlay = document.createElement('div');
        loadingOverlay.style.cssText = 'position:fixed; inset:0; z-index:999999; background:rgba(255,255,255,0.95); display:flex; align-items:center; justify-content:center;';
        loadingOverlay.innerHTML = '<div style="text-align:center"><div style="border:3px solid #e5e7eb; border-top-color:#3D90D7; border-radius:50%; width:32px; height:32px; animation:_pdfSpin 0.8s linear infinite; margin:0 auto"></div><p style="margin-top:10px; color:#6b7280; font-size:13px;">Generating PDF\u2026</p></div>';
        const spinStyle = document.createElement('style');
        spinStyle.textContent = '@keyframes _pdfSpin{to{transform:rotate(360deg)}}';
        document.head.appendChild(spinStyle);
        document.body.appendChild(loadingOverlay);

        const defaultOpts = {
            margin: 10,
            filename: `${filename}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false, backgroundColor: '#ffffff' },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
        };

        html2pdf()
            .set({ ...defaultOpts, ...options })
            .from(container)
            .save()
            .then(() => { loadingOverlay.remove(); spinStyle.remove(); })
            .catch(() => { loadingOverlay.remove(); spinStyle.remove(); });
    },

    /**
     * Export data array to Word (.doc) by building an HTML table.
     * @param {Object[]} data     - Array of row objects
     * @param {Object[]} columns  - Column definitions [{key, label, format?}]
     * @param {string}   filename - Base filename without extension
     */
    tableToWord(data, columns, filename) {
        const tableHtml = this._buildHtmlTable(data, columns, this._titleFromFilename(filename));
        const wordHtml = this._wrapWordHtml(tableHtml);
        const blob = new Blob(['\ufeff', wordHtml], { type: 'application/msword' });
        this._downloadBlob(blob, `${filename}.doc`);
    },

    // =========================================================================
    // PRINT PAGE EXPORTS — from DOM capture
    // =========================================================================

    /**
     * Export DOM table(s) to Excel (.xlsx).
     * Finds all <table class="data-table"> elements inside the selector.
     * @param {string|HTMLElement} selector - CSS selector or DOM element
     * @param {string} filename - Base filename without extension
     */
    printToExcel(selector, filename) {
        if (typeof XLSX === 'undefined') {
            alert('Excel export library not loaded. Please try again.');
            return;
        }

        const el = this._resolveElement(selector);
        if (!el) return;

        const tables = el.querySelectorAll('table.data-table');
        if (tables.length === 0) {
            const anyTable = el.querySelector('table');
            if (!anyTable) {
                alert('No table found to export.');
                return;
            }
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(anyTable);
            XLSX.utils.book_append_sheet(wb, ws, 'Report');
            XLSX.writeFile(wb, `${filename}.xlsx`);
            return;
        }

        const wb = XLSX.utils.book_new();
        tables.forEach((table, i) => {
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, tables.length > 1 ? `Sheet${i + 1}` : 'Report');
        });
        XLSX.writeFile(wb, `${filename}.xlsx`);
    },

    /**
     * Export DOM element to PDF via html2pdf.
     * @param {string|HTMLElement} selector - CSS selector or DOM element
     * @param {string} filename - Base filename without extension
     * @param {Object} [options={}] - html2pdf options override
     */
    printToPdf(selector, filename, options = {}) {
        if (typeof html2pdf === 'undefined') {
            alert('PDF export library not loaded. Please try again.');
            return;
        }

        const el = this._resolveElement(selector);
        if (!el) return;

        const defaultOpts = {
            margin: 5,
            filename: `${filename}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        };

        html2pdf()
            .set({ ...defaultOpts, ...options })
            .from(el)
            .save();
    },

    /**
     * Export DOM element to Word (.doc).
     * @param {string|HTMLElement} selector - CSS selector or DOM element
     * @param {string} filename - Base filename without extension
     */
    printToWord(selector, filename) {
        const el = this._resolveElement(selector);
        if (!el) return;

        const clone = el.cloneNode(true);
        // Remove logos and images for clean, content-focused export
        clone.querySelectorAll('img').forEach(img => img.remove());
        // Remove any empty header containers left after image removal
        clone.querySelectorAll('.header-logo, .logo-container, .report-logo').forEach(el => el.remove());
        const wordHtml = this._wrapWordHtml(clone.innerHTML);
        const blob = new Blob(['\ufeff', wordHtml], { type: 'application/msword' });
        this._downloadBlob(blob, `${filename}.doc`);
    },

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    _resolveElement(selector) {
        if (typeof selector === 'string') {
            const el = document.querySelector(selector);
            if (!el) {
                console.error('ReportExporter: Element not found for selector:', selector);
                return null;
            }
            return el;
        }
        return selector;
    },

    _rawValue(value, format) {
        if (value === null || value === undefined) return '';
        if ((format === 'currency' || format === 'number') && typeof value === 'number') return value;
        return value;
    },

    _formatValue(value, format) {
        if (value === null || value === undefined) return '';
        if (format === 'currency' && typeof value === 'number') {
            return '\u20B1' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        }
        if (format === 'number' && typeof value === 'number') {
            return value.toLocaleString('en-PH');
        }
        return String(value);
    },

    _buildHtmlTable(data, columns, title) {
        let html = '';

        if (title) {
            html += `<div style="text-align:center; margin-bottom:16px;">
                <h2 style="font-size:16px; font-weight:bold; margin:0 0 4px 0;">${title}</h2>
                <p style="font-size:11px; color:#666; margin:0;">Generated on ${new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
            </div>`;
        }

        html += '<table style="border-collapse:collapse; width:100%; font-family:Arial,sans-serif;">';
        html += '<thead><tr>';
        columns.forEach(c => {
            const align = (c.format === 'currency' || c.format === 'number') ? 'right' : 'left';
            html += `<th style="border:1px solid #d1d5db; padding:8px 10px; background:#f3f4f6; font-size:10px; font-weight:600; text-align:${align}; text-transform:uppercase; letter-spacing:0.03em; color:#374151;">${c.label}</th>`;
        });
        html += '</tr></thead>';

        html += '<tbody>';
        data.forEach((row, idx) => {
            const bg = idx % 2 === 0 ? '#ffffff' : '#f9fafb';
            html += `<tr style="background:${bg};">`;
            columns.forEach(c => {
                const align = (c.format === 'currency' || c.format === 'number') ? 'right' : 'left';
                html += `<td style="border:1px solid #d1d5db; padding:6px 10px; font-size:10px; text-align:${align}; color:#111827;">${this._formatValue(row[c.key], c.format)}</td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        html += `<p style="font-size:9px; color:#6b7280; margin-top:8px; text-align:right;">Total Records: ${data.length}</p>`;

        return html;
    },

    _wrapWordHtml(innerHtml) {
        return `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="utf-8">
<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View></w:WordDocument></xml><![endif]-->
<style>
    @page { size: A4 landscape; margin: 1cm 1.5cm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #111827; margin: 0; padding: 0; }
    h1, h2, h3, h4, h5, h6 { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 4pt 0; }
    p { margin: 2pt 0; }
    table { border-collapse: collapse; width: 100%; font-size: 9pt; }
    th { border: 1px solid #d1d5db; padding: 6px 8px; background-color: #f3f4f6; font-weight: 600; text-transform: uppercase; font-size: 8pt; letter-spacing: 0.03em; color: #374151; }
    td { border: 1px solid #d1d5db; padding: 5px 8px; color: #111827; }
    tr:nth-child(even) td { background-color: #f9fafb; }
    .text-right, td[align="right"], th[align="right"] { text-align: right; }
    .text-center, td[align="center"], th[align="center"] { text-align: center; }
    .text-left, td[align="left"], th[align="left"] { text-align: left; }
    .font-bold, strong { font-weight: bold; }
    .font-mono { font-family: 'Courier New', monospace; }
    .header-section { text-align: center; margin-bottom: 12pt; }
    .header-section h2 { font-size: 14pt; margin-bottom: 2pt; }
    .header-section p { font-size: 9pt; color: #6b7280; }
    .summary-row td { background-color: #f3f4f6 !important; font-weight: bold; }
    img { display: none !important; }
</style>
</head><body>${innerHtml}</body></html>`;
    },

    _downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    },

    _titleFromFilename(filename) {
        return filename
            .replace(/[-_]/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
    },
};

window.ReportExporter = ReportExporter;
