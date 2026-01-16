// Application Form Print Utility - Standalone Function
function printServiceApplicationForm(customer) {
    const w = window.open('', '_blank', 'width=842,height=1191');
    if (!w) {
        alert('Pop-up blocked. Please allow pop-ups for this site.');
        return;
    }
    const today = new Date().toLocaleDateString();
    
    w.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Service Application Form - ${customer.CustomerName || 'Customer'}</title>
            <style>
                @page { 
                    size: A4; 
                    margin: 15mm;
                }
                @media print {
                    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
                    body { margin: 0; padding: 15mm; background: white; }
                    @page { 
                        margin: 15mm;
                        size: A4;
                    }
                    html, body { height: 100%; }
                    .no-print { display: none !important; }
                }
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: Arial, sans-serif; font-size: 11px; padding: 20px; color: #000; line-height: 1.4; background: white; }
                .header { text-align: center; margin-bottom: 12px; }
                .header img { height: 60px; display: block; margin: 0 auto 6px; }
                .title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
                .subtitle { font-size: 12px; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
                hr { border: none; border-top: 1px solid #000; margin: 10px 0; }
                .info-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 6px 0; }
                .info-field { display: flex; align-items: baseline; gap: 4px; }
                .info-label { font-weight: bold; white-space: nowrap; }
                .info-underline { border-bottom: 1px solid #000; flex: 1; min-height: 16px; }
                .section-title { font-size: 11px; font-weight: bold; margin: 12px 0 8px; text-transform: uppercase; letter-spacing: 0.2px; }
                .subsection-title { font-size: 10px; font-weight: bold; margin: 8px 0 4px; }
                .plain-text { font-size: 10px; line-height: 1.5; margin: 8px 0; }
                .container { border: 1px solid #000; padding: 10px; margin: 8px 0; }
                .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
                .column-title { font-size: 10px; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; }
                .charges-item { font-size: 10px; line-height: 1.6; margin: 2px 0; }
                .charges-row { display: flex; gap: 4px; align-items: baseline; }
                .charges-label { flex: 1; }
                .charges-dots { flex: 0; }
                .charges-amount { white-space: nowrap; }
                .material-item { font-size: 10px; line-height: 1.6; margin: 2px 0; white-space: nowrap; overflow: hidden; }
                .divider-thin { border: none; border-top: 1px solid #000; margin: 10px 0; }
                .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 12px 0; }
                .signature-block { }
                .signature-label { font-weight: bold; font-size: 10px; margin-bottom: 24px; }
                .signature-line { border-top: 1px solid #000; text-align: center; font-size: 9px; padding-top: 2px; }
                .footer-note { font-size: 9px; color: #000; margin-top: 10px; line-height: 1.4; text-align: justify; }
                .checkbox-row { font-size: 10px; margin: 4px 0; letter-spacing: 3px; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="/images/logo.png" alt="Logo" onerror="this.style.display='none'">
                <div class="title">Service Application And Construction Order</div>
                <div class="subtitle">INITAO MUNICIPAL WATER WORKS ORDER</div>
            </div>
            
            <hr>
            
            <div class="info-row">
                <div class="info-field">
                    <span class="info-label">Application #:</span>
                    <span class="info-underline">${customer.customer_code || '-'}</span>
                </div>
                <div class="info-field">
                    <span class="info-label">Date Applied:</span>
                    <span class="info-underline">${today}</span>
                </div>
            </div>

            <div class="section-title">CUSTOMER INFORMATION</div>
            
            <div class="info-row">
                <div class="info-field">
                    <span class="info-label">Name:</span>
                    <span class="info-underline">${customer.CustomerName || '-'}</span>
                </div>
                <div class="info-field">
                    <span class="info-label">Phone:</span>
                    <span class="info-underline">${customer.Phone || '-'}</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-field" style="grid-column: span 2;">
                    <span class="info-label">Address:</span>
                    <span class="info-underline">${customer.Address || '-'}</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-field">
                    <span class="info-label">Email:</span>
                    <span class="info-underline">${customer.Email || '-'}</span>
                </div>
                <div class="info-field">
                    <span class="info-label">Area Code:</span>
                    <span class="info-underline">${customer.AreaCode || '-'}</span>
                </div>
            </div>

            <div class="section-title">SERVICE DETAILS</div>

            <div class="container">
                <div class="subsection-title">Fixed Charges</div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">Water Meter Installation</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 500.00</span>
                    </div>
                </div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">Connection Fee</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 300.00</span>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="subsection-title">Rate Classification (Residential)</div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">First 10 m³ (Minimum)</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 100.00</span>
                    </div>
                </div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">11-20 m³</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 11.00/m³</span>
                    </div>
                </div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">21-30 m³</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 12.00/m³</span>
                    </div>
                </div>
                <div class="charges-item">
                    <div class="charges-row">
                        <span class="charges-label">31+ m³</span>
                        <span class="charges-dots">........................</span>
                        <span class="charges-amount">₱ 13.00/m³</span>
                    </div>
                </div>
            </div>

            <div class="divider-thin"></div>

            <div class="section-title">AUTHORIZATION</div>
            
            <div class="signature-grid">
                <div class="signature-block">
                    <div class="signature-label">Customer Signature</div>
                    <div class="signature-line"></div>
                </div>
                <div class="signature-block">
                    <div class="signature-label">Authorized Representative</div>
                    <div class="signature-line"></div>
                </div>
            </div>

            <div class="footer-note">
                <p><strong>NOTES:</strong> This document is generated from the Service Application system. All information provided is accurate as of the application date. Please ensure all details are correct before submission.</p>
                <p>For inquiries, please contact the Initao Municipal Water Works Order office.</p>
                <p style="margin-top: 12px; text-align: center; font-size: 8px;">Generated: ${new Date().toLocaleString()}</p>
            </div>

        </body>
        </html>
    `);

    w.document.close();
}

// Export to global scope
window.printServiceApplicationForm = printServiceApplicationForm;

export { printServiceApplicationForm };
