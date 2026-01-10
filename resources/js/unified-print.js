/**
 * ========================================
 * UNIFIED PRINT & EXPORT SYSTEM
 * Comprehensive printing and export utilities
 * ========================================
 */

class UnifiedPrintSystem {
    // Print customer application form
    static printCustomerForm(customer) {
        const printWindow = window.open('', '_blank', 'width=842,height=1191');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Customer Application - ${customer.CustomerName || customer.customer_name}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #1a1a1a; line-height: 1.6; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 20px; }
                    .header img { max-height: 80px; margin-bottom: 10px; }
                    .header h1 { font-size: 24px; color: #2563eb; margin: 10px 0; }
                    .header p { font-size: 12px; color: #666; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
                    .info-item { padding: 12px; background: #f8fafc; border-left: 3px solid #2563eb; }
                    .info-item label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 4px; }
                    .info-item value { font-size: 14px; color: #1e293b; font-weight: 500; }
                    .section { margin: 30px 0; }
                    .section-title { font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
                    .requirements { background: #f8fafc; padding: 20px; border-radius: 8px; }
                    .requirements ol { margin-left: 20px; }
                    .requirements li { margin: 8px 0; font-size: 13px; color: #475569; }
                    .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 60px; }
                    .signature-box { text-align: center; }
                    .signature-line { border-top: 2px solid #1e293b; padding-top: 8px; margin-top: 50px; font-size: 12px; color: #64748b; }
                    .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
                    .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; }
                    .status-new { background: #dbeafe; color: #1e40af; }
                    .status-verified { background: #d1fae5; color: #065f46; }
                    .status-pending { background: #fed7aa; color: #92400e; }
                    @media print {
                        body { padding: 20px; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="/images/logo.png" alt="Logo" onerror="this.style.display='none'">
                    <h1>Water Service Application Form</h1>
                    <p>Municipality Water District | Contact: (123) 456-7890 | Email: info@waterdistrict.gov</p>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Customer Name</label>
                        <value>${customer.CustomerName || customer.customer_name || 'N/A'}</value>
                    </div>
                    <div class="info-item">
                        <label>Customer ID</label>
                        <value>${customer.id || customer.customer_code || 'N/A'}</value>
                    </div>
                    <div class="info-item">
                        <label>Email Address</label>
                        <value>${customer.Email || customer.email || 'N/A'}</value>
                    </div>
                    <div class="info-item">
                        <label>Phone Number</label>
                        <value>${customer.Phone || customer.phone || 'N/A'}</value>
                    </div>
                    <div class="info-item">
                        <label>Service Address</label>
                        <value>${customer.AreaCode || customer.address || 'N/A'}</value>
                    </div>
                    <div class="info-item">
                        <label>Application Date</label>
                        <value>${customer.DateApplied ? new Date(customer.DateApplied).toLocaleDateString() : new Date().toLocaleDateString()}</value>
                    </div>
                    <div class="info-item">
                        <label>Registration Type</label>
                        <value>${customer.registration_type || 'RESIDENTIAL'}</value>
                    </div>
                    <div class="info-item">
                        <label>Application Status</label>
                        <value><span class="status-badge status-${(customer.Status || 'NEW').toLowerCase()}">${customer.Status || customer.workflow_status || 'NEW'}</span></value>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Required Documents Checklist</div>
                    <div class="requirements">
                        <ol>
                            <li>Valid Government-Issued ID (Driver's License, Passport, or National ID)</li>
                            <li>Barangay Clearance (issued within the last 6 months)</li>
                            <li>Proof of Property Ownership or Lease Agreement</li>
                            <li>Completed Application Form with Signature</li>
                            <li>Recent Utility Bill (if transferring service)</li>
                            <li>Tax Declaration or Land Title (for property verification)</li>
                            <li>Two (2) Recent Passport-sized Photos</li>
                        </ol>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Terms and Conditions</div>
                    <div class="requirements">
                        <p style="font-size: 12px; color: #475569; margin-bottom: 10px;">
                            By signing this application, I acknowledge and agree to the following:
                        </p>
                        <ul style="margin-left: 20px; font-size: 12px; color: #475569;">
                            <li style="margin: 6px 0;">All information provided is accurate and complete</li>
                            <li style="margin: 6px 0;">I will comply with all water district regulations and policies</li>
                            <li style="margin: 6px 0;">Payment of connection fees and deposits is required before service activation</li>
                            <li style="margin: 6px 0;">I authorize the water district to verify all submitted documents</li>
                        </ul>
                    </div>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">
                            Applicant Signature
                        </div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">
                            Processed By
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <p>This is a computer-generated document. No signature required for initial submission.</p>
                    <p>Generated on ${new Date().toLocaleString()} | Reference: ${customer.id || customer.customer_code || 'N/A'}</p>
                </div>

                <script>
                    window.onload = function() {
                        window.print();
                    };
                </script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    }

    // Print invoice
    static printInvoice(invoice) {
        const printWindow = window.open('', '_blank', 'width=842,height=1191');
        
        const items = Array.isArray(invoice.items) ? invoice.items : 
                     (typeof invoice.items === 'string' ? JSON.parse(invoice.items) : []);
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Invoice - ${invoice.invoice_id}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #1a1a1a; }
                    .invoice-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #2563eb; }
                    .company-info h1 { font-size: 24px; color: #2563eb; margin-bottom: 5px; }
                    .company-info p { font-size: 12px; color: #666; }
                    .invoice-meta { text-align: right; }
                    .invoice-meta h2 { font-size: 28px; color: #1e293b; margin-bottom: 10px; }
                    .invoice-meta p { font-size: 13px; color: #64748b; margin: 4px 0; }
                    .customer-info { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
                    .customer-info h3 { font-size: 14px; color: #64748b; margin-bottom: 10px; }
                    .customer-info p { font-size: 13px; color: #1e293b; margin: 4px 0; }
                    .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                    .items-table thead { background: #2563eb; color: white; }
                    .items-table th { padding: 12px; text-align: left; font-size: 12px; font-weight: 600; }
                    .items-table td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
                    .items-table tbody tr:hover { background: #f8fafc; }
                    .items-table .amount { text-align: right; font-weight: 600; }
                    .totals { margin-top: 20px; text-align: right; }
                    .totals-row { display: flex; justify-content: flex-end; margin: 8px 0; }
                    .totals-label { width: 150px; text-align: right; padding-right: 20px; font-size: 13px; color: #64748b; }
                    .totals-value { width: 120px; text-align: right; font-size: 14px; font-weight: 600; }
                    .total-final { font-size: 18px; color: #2563eb; padding-top: 12px; border-top: 2px solid #2563eb; }
                    .payment-info { background: #f0fdf4; border: 1px solid #86efac; padding: 15px; border-radius: 8px; margin: 20px 0; }
                    .payment-info h4 { font-size: 13px; color: #166534; margin-bottom: 8px; }
                    .payment-info p { font-size: 12px; color: #15803d; margin: 4px 0; }
                    .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
                    @media print { body { padding: 20px; } }
                </style>
            </head>
            <body>
                <div class="invoice-header">
                    <div class="company-info">
                        <h1>Water District</h1>
                        <p>Municipality Water Services</p>
                        <p>123 Main Street, City</p>
                        <p>Phone: (123) 456-7890</p>
                        <p>Email: billing@waterdistrict.gov</p>
                    </div>
                    <div class="invoice-meta">
                        <h2>INVOICE</h2>
                        <p><strong>Invoice #:</strong> ${invoice.invoice_id}</p>
                        <p><strong>Date:</strong> ${new Date(invoice.DateApplied || Date.now()).toLocaleDateString()}</p>
                        <p><strong>Status:</strong> ${invoice.status || 'PENDING'}</p>
                    </div>
                </div>

                <div class="customer-info">
                    <h3>BILL TO:</h3>
                    <p><strong>${invoice.customer_name}</strong></p>
                    <p>Customer Code: ${invoice.customer_code}</p>
                </div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Description</th>
                            <th style="width: 20%;">Quantity</th>
                            <th style="width: 20%;" class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(item => `
                            <tr>
                                <td>${item.description}</td>
                                <td>${item.quantity || 1}</td>
                                <td class="amount">₱${(item.amount || 0).toLocaleString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="totals">
                    <div class="totals-row">
                        <div class="totals-label">Subtotal:</div>
                        <div class="totals-value">₱${(invoice.amount || 0).toLocaleString()}</div>
                    </div>
                    <div class="totals-row">
                        <div class="totals-label">Tax (0%):</div>
                        <div class="totals-value">₱0.00</div>
                    </div>
                    <div class="totals-row total-final">
                        <div class="totals-label">Total Amount:</div>
                        <div class="totals-value">₱${(invoice.amount || 0).toLocaleString()}</div>
                    </div>
                </div>

                ${invoice.status === 'PAID' ? `
                    <div class="payment-info">
                        <h4>✓ Payment Received</h4>
                        <p>Payment Method: ${invoice.PaymentMethod || 'N/A'}</p>
                        <p>Thank you for your payment!</p>
                    </div>
                ` : ''}

                <div class="footer">
                    <p>Thank you for your business!</p>
                    <p>For inquiries, please contact our billing department.</p>
                    <p>Generated on ${new Date().toLocaleString()}</p>
                </div>

                <script>
                    window.onload = function() {
                        window.print();
                    };
                </script>
            </body>
            </html>
        `);
        
        printWindow.document.close();
    }

    // Export table to CSV
    static exportTableToCSV(tableId, filename = 'export.csv') {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = Array.from(table.querySelectorAll('tr'));
        const csv = rows.map(row => {
            const cells = Array.from(row.querySelectorAll('td, th'));
            return cells.map(cell => {
                let text = cell.innerText.replace(/[\r\n]+/g, ' ').trim();
                text = text.replace(/"/g, '""');
                return `"${text}"`;
            }).join(',');
        }).join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    // Print table
    static printTable(tableId, title = 'Table Report') {
        const table = document.getElementById(tableId);
        if (!table) return;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>${title}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 30px; }
                    h1 { color: #1e293b; margin-bottom: 10px; font-size: 24px; }
                    .meta { color: #64748b; font-size: 13px; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 13px; }
                    th { background: #f1f5f9; font-weight: 600; color: #1e293b; }
                    tr:nth-child(even) { background: #f8fafc; }
                    @media print { body { padding: 15px; } }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                <div class="meta">Generated on ${new Date().toLocaleString()}</div>
                ${table.outerHTML}
                <script>window.onload = function() { window.print(); };</script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    // Export chart as image
    static exportChartAsImage(chartId, filename = 'chart.png') {
        const canvas = document.getElementById(chartId);
        if (!canvas) return;

        canvas.toBlob(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        });
    }

    // Print chart
    static printChart(chartId, title = 'Chart Report') {
        const canvas = document.getElementById(chartId);
        if (!canvas) return;

        const printWindow = window.open('', '_blank');
        const imgData = canvas.toDataURL('image/png');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>${title}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 30px; text-align: center; }
                    h1 { color: #1e293b; margin-bottom: 10px; font-size: 24px; }
                    .meta { color: #64748b; font-size: 13px; margin-bottom: 20px; }
                    img { max-width: 100%; height: auto; border: 1px solid #e2e8f0; }
                    @media print { body { padding: 15px; } }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                <div class="meta">Generated on ${new Date().toLocaleString()}</div>
                <img src="${imgData}" alt="${title}">
                <script>window.onload = function() { window.print(); };</script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
}

// Table sorting utility
class TableSorter {
    static makeTableSortable(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const headers = table.querySelectorAll('thead th');
        headers.forEach((header, index) => {
            if (header.classList.contains('no-sort')) return;
            
            header.style.cursor = 'pointer';
            header.classList.add('sortable');
            
            const icon = document.createElement('i');
            icon.className = 'fas fa-sort ml-2 text-gray-400 text-xs';
            header.appendChild(icon);
            
            header.addEventListener('click', () => this.sortTable(table, index, header));
        });
    }

    static sortTable(table, columnIndex, header) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = !header.classList.contains('sort-asc');
        
        table.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
            const icon = th.querySelector('i');
            if (icon) icon.className = 'fas fa-sort ml-2 text-gray-400 text-xs';
        });
        
        header.classList.add(isAscending ? 'sort-asc' : 'sort-desc');
        const icon = header.querySelector('i');
        if (icon) {
            icon.className = `fas fa-sort-${isAscending ? 'up' : 'down'} ml-2 text-blue-600 text-xs`;
        }
        
        rows.sort((a, b) => {
            const aText = a.cells[columnIndex]?.textContent.trim() || '';
            const bText = b.cells[columnIndex]?.textContent.trim() || '';
            
            const aNum = parseFloat(aText.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bText.replace(/[^\d.-]/g, ''));
            
            let comparison = 0;
            if (!isNaN(aNum) && !isNaN(bNum)) {
                comparison = aNum - bNum;
            } else {
                comparison = aText.localeCompare(bText);
            }
            
            return isAscending ? comparison : -comparison;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
}

// Export to global scope
window.UnifiedPrintSystem = UnifiedPrintSystem;
window.TableSorter = TableSorter;

// Backward compatibility
window.printCustomerForm = (customer) => UnifiedPrintSystem.printCustomerForm(customer);
window.ExportPrint = UnifiedPrintSystem;

export { UnifiedPrintSystem, TableSorter };
