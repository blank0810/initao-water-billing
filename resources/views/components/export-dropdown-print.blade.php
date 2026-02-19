{{--
    Reusable Export Dropdown Button for Printable Layout Pages (Standalone HTML/CSS)

    Usage: @include('components.export-dropdown-print', ['exportFilename' => 'billing-summary', 'exportSelector' => '.document'])

    Props:
    - exportFilename: Base filename for downloaded files (e.g. 'billing-summary')
    - exportSelector: CSS selector for the DOM element to export (default: '.document')
--}}
@php
    $exportFilename = $exportFilename ?? 'report';
    $exportSelector = $exportSelector ?? '.document';
    $dropdownId = 'exportDropdown_' . uniqid();
@endphp

<div style="position: relative; display: inline-block;">
    <button onclick="document.getElementById('{{ $dropdownId }}').classList.toggle('export-dropdown-hidden')" class="btn btn-secondary" type="button" style="display: inline-flex; align-items: center; gap: 8px; position: relative;">
        <i class="fas fa-download"></i> Export
        <i class="fas fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
    </button>
    <div id="{{ $dropdownId }}" class="export-dropdown-hidden" style="position: absolute; right: 0; bottom: 100%; margin-bottom: 6px; width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1); z-index: 50; overflow: hidden;">
        <button onclick="ReportExporter.printToExcel('{{ $exportSelector }}', '{{ $exportFilename }}'); document.getElementById('{{ $dropdownId }}').classList.add('export-dropdown-hidden')" type="button" style="display: flex; align-items: center; padding: 10px 16px; width: 100%; border: none; background: transparent; cursor: pointer; color: #374151; font-size: 13px; text-align: left; transition: background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-file-excel" style="color: #16a34a; margin-right: 12px; width: 16px; text-align: center;"></i>
            Export to Excel
        </button>
        <button onclick="ReportExporter.printToPdf('{{ $exportSelector }}', '{{ $exportFilename }}'); document.getElementById('{{ $dropdownId }}').classList.add('export-dropdown-hidden')" type="button" style="display: flex; align-items: center; padding: 10px 16px; width: 100%; border: none; background: transparent; cursor: pointer; color: #374151; font-size: 13px; border-top: 1px solid #f3f4f6; text-align: left; transition: background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-file-pdf" style="color: #dc2626; margin-right: 12px; width: 16px; text-align: center;"></i>
            Export to PDF
        </button>
        <button onclick="ReportExporter.printToWord('{{ $exportSelector }}', '{{ $exportFilename }}'); document.getElementById('{{ $dropdownId }}').classList.add('export-dropdown-hidden')" type="button" style="display: flex; align-items: center; padding: 10px 16px; width: 100%; border: none; background: transparent; cursor: pointer; color: #374151; font-size: 13px; border-top: 1px solid #f3f4f6; text-align: left; transition: background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-file-word" style="color: #2563eb; margin-right: 12px; width: 16px; text-align: center;"></i>
            Export to Word
        </button>
    </div>
</div>

<style>
    .export-dropdown-hidden { display: none !important; }
</style>

{{-- Inline ReportExporter for standalone print pages (loaded once per page) --}}
@once
<script>
    if (typeof window.ReportExporter === 'undefined') {
        window.ReportExporter = {
            printToExcel(selector, filename) {
                if (typeof XLSX === 'undefined') { alert('Excel export library not loaded.'); return; }
                const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
                if (!el) { console.error('ReportExporter: Element not found:', selector); return; }
                const tables = el.querySelectorAll('table.data-table');
                const wb = XLSX.utils.book_new();
                if (tables.length === 0) {
                    const anyTable = el.querySelector('table');
                    if (!anyTable) { alert('No table found to export.'); return; }
                    XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(anyTable), 'Report');
                } else {
                    tables.forEach((table, i) => {
                        XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(table), tables.length > 1 ? 'Sheet' + (i + 1) : 'Report');
                    });
                }
                XLSX.writeFile(wb, filename + '.xlsx');
            },
            printToPdf(selector, filename, options) {
                if (typeof html2pdf === 'undefined') { alert('PDF export library not loaded.'); return; }
                const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
                if (!el) { console.error('ReportExporter: Element not found:', selector); return; }
                var opts = Object.assign({
                    margin: 5, filename: filename + '.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, logging: false },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                }, options || {});
                html2pdf().set(opts).from(el).save();
            },
            printToWord(selector, filename) {
                const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
                if (!el) { console.error('ReportExporter: Element not found:', selector); return; }
                const clone = el.cloneNode(true);
                clone.querySelectorAll('img').forEach(function(img) { img.remove(); });
                clone.querySelectorAll('.header-logo, .logo-container, .report-logo').forEach(function(el) { el.remove(); });
                const wordHtml = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"><!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View></w:WordDocument></xml><![endif]--><style>@page{size:A4 landscape;margin:1cm 1.5cm}body{font-family:Arial,Helvetica,sans-serif;font-size:10pt;color:#111827;margin:0;padding:0}h1,h2,h3,h4,h5,h6{font-family:Arial,Helvetica,sans-serif;color:#111827;margin:4pt 0}p{margin:2pt 0}table{border-collapse:collapse;width:100%;font-size:9pt}th{border:1px solid #d1d5db;padding:6px 8px;background-color:#f3f4f6;font-weight:600;text-transform:uppercase;font-size:8pt;letter-spacing:.03em;color:#374151}td{border:1px solid #d1d5db;padding:5px 8px;color:#111827}.text-right,td[align=right],th[align=right]{text-align:right}.text-center,td[align=center],th[align=center]{text-align:center}.text-left,td[align=left],th[align=left]{text-align:left}.font-bold,strong{font-weight:bold}.header-section{text-align:center;margin-bottom:12pt}img{display:none!important}</style></head><body>' + clone.innerHTML + '</body></html>';
                const blob = new Blob(['\ufeff', wordHtml], { type: 'application/msword' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = filename + '.doc';
                document.body.appendChild(a); a.click();
                document.body.removeChild(a); URL.revokeObjectURL(url);
            }
        };
    }
</script>
@endonce
