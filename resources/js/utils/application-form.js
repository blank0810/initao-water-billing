class UnifiedPrintSystem {
    static printServiceApplicationForm(customer) {
        const w = window.open('', '_blank', 'width=842,height=1191');
        const today = new Date().toLocaleDateString();
        
        // Generate filename from customer name
        const fileName = `Application_${(customer.CustomerName || 'Customer').replace(/\s+/g, '_')}.pdf`;
        
        w.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title></title>
                <style>
                    @page { 
                        size: A4; 
                        margin: 15mm;
                    }
                    @media print {
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                        body { margin: 0; padding: 15mm; }
                        @page { 
                            margin: 15mm;
                            size: A4;
                        }
                        html, body { height: 100%; }
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
                    @media print {
                        body { padding: 15mm; margin: 0; }
                    }
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
                        <span class="info-label">Applicant Name:</span>
                        <span class="info-underline">${customer.CustomerName || ''}</span>
                    </div>
                    <div class="info-field">
                        <span class="info-label">Date:</span>
                        <span class="info-underline">${today}</span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-field">
                        <span class="info-label">Address:</span>
                        <span class="info-underline">${customer.AreaCode || ''}</span>
                    </div>
                    <div class="info-field">
                        <span class="info-label">App. No.:</span>
                        <span class="info-underline">${customer.id || customer.customer_code || ''}</span>
                    </div>
                </div>
                
                <hr class="divider-thin">
                
                <div class="plain-text">
                    I hereby apply for a water service-connection size no. _____________ located at ________________________________.
                </div>
                
                <div class="plain-text">
                    I acknowledge that the water service connection will only be installed upon approval of this application and full payment of all basic charges. I assume full responsibility for the maintenance of the water meter and the service connection line. I further agree to comply with and abide by all rules, regulations, and policies of the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong>.
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 16px 0;">
                    <div>
                        <div style="border-bottom: 1px solid #000; min-height: 20px; margin-bottom: 2px;"></div>
                        <div style="font-size: 9px; font-weight: bold;">Applicant's Signature</div>
                    </div>
                    <div>
                        <div style="border-bottom: 1px solid #000; min-height: 20px; margin-bottom: 2px;"></div>
                        <div style="font-size: 9px; font-weight: bold;">Cell No.</div>
                    </div>
                </div>
                
                <div class="container">
                    <div class="grid-2">
                        <div>
                            <div class="column-title">Investigation of Applicant's System</div>
                            <div class="subsection-title" style="margin-top: 6px;">Plumbing Installation:</div>
                            <div class="checkbox-row">[ ] ADEQUATE</div>
                            <div class="checkbox-row">[ ] NOT ADEQUATE</div>
                            <div style="margin-top: 10px;">
                                <div class="subsection-title">Investigated By:</div>
                                <div style="border-bottom: 1px solid #000; min-height: 14px; margin-bottom: 2px;"></div>
                                <div class="subsection-title" style="margin-top: 8px;">Date:</div>
                                <div style="border-bottom: 1px solid #000; min-height: 14px;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="column-title">Availability of Applicant</div>
                            <div style="margin-top: 20px;"></div>
                            <div class="checkbox-row">[ ] AVAILABLE</div>
                            <div class="checkbox-row">[ ] NOT AVAILABLE</div>
                            <div style="margin-top: 12px;">
                                <div class="subsection-title">Verified By:</div>
                                <div style="border-bottom: 1px solid #000; min-height: 14px; margin-bottom: 2px;"></div>
                                <div class="subsection-title" style="margin-top: 8px;">Date:</div>
                                <div style="border-bottom: 1px solid #000; min-height: 14px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="container">
                    <div class="grid-2">
                        <div>
                            <div class="column-title">Amount of Charges Due</div>
                            <div class="charges-item">1. Registration Fee ..................... ₱ 50.00</div>
                            <div class="charges-item">2. Installation Fee ..................... ₱ 200.00</div>
                            <div class="charges-item">3. Other Charges:</div>
                            <div class="charges-item" style="margin-left: 16px;">Tapping Fee .......................... ₱ 50.00</div>
                            <div class="charges-item" style="margin-left: 16px;">Excavation Fee ....................... ₱ 50.00</div>
                            <div class="charges-item">4. Labor</div>
                            
                            <div style="margin-top: 12px; border-top: 1px solid #000; padding-top: 10px;">
                                <div class="subsection-title">Official Receipt:</div>
                                <div style="margin-top: 6px;">
                                    <div class="charges-item">Amount .......................... ₱ 350.00</div>
                                    <div style="display: flex; gap: 4px; margin: 4px 0; align-items: baseline;">
                                        <span style="font-weight: bold;">Date:</span>
                                        <span style="border-bottom: 1px solid #000; flex: 1; min-height: 14px;"></span>
                                    </div>
                                    <div style="display: flex; gap: 4px; margin: 4px 0; align-items: baseline;">
                                        <span style="font-weight: bold;">Balance Due:</span>
                                        <span style="border-bottom: 1px solid #000; flex: 1; min-height: 14px;">₱ </span>
                                    </div>
                                    <div style="display: flex; gap: 4px; margin: 4px 0; align-items: baseline;">
                                        <span style="font-weight: bold;">Term of Payment:</span>
                                        <span style="border-bottom: 1px solid #000; flex: 1; min-height: 14px;">₱ </span>
                                        <span style="white-space: nowrap;">/ Month</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="column-title">Materials</div>
                            <div class="material-item">1. _________________________________________________________</div>
                            <div class="material-item">2. _________________________________________________________</div>
                            <div class="material-item">3. _________________________________________________________</div>
                            <div class="material-item">4. _________________________________________________________</div>
                            <div class="material-item">5. _________________________________________________________</div>
                            <div class="material-item">6. _________________________________________________________</div>
                            <div class="material-item">7. _________________________________________________________</div>
                            <div class="material-item">8. _________________________________________________________</div>
                            <div class="material-item">9. _________________________________________________________</div>
                            <div class="material-item">10. ________________________________________________________</div>
                            <div class="material-item">11. ________________________________________________________</div>
                            <div class="material-item">12. ________________________________________________________</div>
                            <div class="material-item">13. ________________________________________________________</div>
                            <div class="material-item">14. ________________________________________________________</div>
                            <div class="material-item">15. ________________________________________________________</div>
                        </div>
                    </div>
                </div>
                
                <hr class="divider-thin">
                
                <div class="section-title">Service Connection Record</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div style="font-size: 10px; line-height: 1.8;">
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 4px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">S.C. NO.:</span>
                            <span style="border-bottom: 1px solid #000; min-width: 80px;"></span>
                        </div>
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 4px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">METER NO.:</span>
                            <span style="border-bottom: 1px solid #000; min-width: 80px;"></span>
                        </div>
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 4px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">ACCOUNT NO.:</span>
                            <span style="border-bottom: 1px solid #000; min-width: 80px;"></span>
                        </div>
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 4px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">INITIAL READING:</span>
                            <span style="border-bottom: 1px solid #000; min-width: 80px;"></span>
                        </div>
                    </div>
                    <div style="font-size: 10px;">
                        <div style="font-weight: bold; margin-bottom: 8px;">INSTALLED BY:</div>
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 6px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">Plumber:</span>
                            <span style="border-bottom: 1px solid #000; flex: 1; min-height: 14px;"></span>
                        </div>
                        <div style="display: flex; gap: 4px; align-items: baseline; margin: 6px 0;">
                            <span style="font-weight: bold; white-space: nowrap;">DATE:</span>
                            <span style="border-bottom: 1px solid #000; flex: 1; min-height: 14px;"></span>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 14px; text-align: center; font-size: 10px; font-weight: bold;">(SKETCH LOCATION OF PROPOSED SERVICE AT THE BACK)</div>
                
                <div style="margin-top: 10px;">
                    <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px;">MATERIALS USED:</div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
                        <tr>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 50%;">Description</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 25%;">Unit</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 25%;">Quantity</th>
                        </tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                        <tr><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td><td style="border: 1px solid #000; padding: 8px;"></td></tr>
                    </table>
                </div>
                
                <div style="margin-top: 12px;">
                    <div class="signature-grid">
                        <div class="signature-block">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 12px;">RECOMMENDING FOR APPROVAL:</div>
                        </div>
                        <div class="signature-block">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 12px;">VERIFIED AND REVIEWED BY:</div>
                            <div style="font-weight: bold; font-size: 10px;">DANTE AMPER / JONATHAN L. BAGARES</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 12px; text-align: center;">
                    <div style="margin-bottom: 6px; font-size: 10px; font-weight: bold;">Approved by:</div>
                    <div style="text-align: center; font-weight: bold; font-size: 10px;">ENGR. HERMAN L. BALABAT</div>
                    <div style="text-align: center; font-size: 10px; margin-top: 1px;">MEEDO</div>
                </div>
                
                <div class="footer-note">
                    The application shall be submitted during office hours by the applicant, either personally or through a representative. It shall be the duty of the MEEDO personnel to report the application received to the MEEDO Officer, who shall acknowledge receipt.
                </div>

                <!-- PAGE BREAK FOR MEEDO CONTRACT -->
                <div style="page-break-after: always;"></div>
                
                <!-- MEEDO WATER SERVICE CONTRACT -->
                <div style="font-family: Arial, sans-serif; font-size: 11px;">
                    <div class="header">
                        <img src="/images/logo.png" alt="Logo" onerror="this.style.display='none'" style="height: 60px; display: block; margin: 0 auto 6px;">
                        <div style="text-align: center; margin-bottom: 8px;">
                            <div style="font-size: 10px;">Republic of the Philippines</div>
                            <div style="font-size: 10px;">Province of Misamis Oriental</div>
                            <div style="font-size: 10px;">MUNICIPALITY OF INITAO</div>
                            <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">INITAO MUNICIPAL WATER WORKS SYSTEM</div>
                            <div style="font-size: 12px; font-weight: bold; margin-top: 4px; text-transform: uppercase;">WATER SERVICE CONTRACT</div>
                        </div>
                    </div>
                    
                    <hr style="border: none; border-top: 1px solid #000; margin: 10px 0;">
                    
                    <!-- Row 1: Concessioner & Address -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 6px 0; font-size: 10px;">
                        <div>
                            <span style="font-weight: bold;">CONCESSIONER:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 200px;">${customer.CustomerName || ''}</span>
                        </div>
                        <div>
                            <span style="font-weight: bold;">ADDRESS:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 200px;">${customer.AreaCode || ''}</span>
                        </div>
                    </div>
                    
                    <!-- Row 2: Classification & Meter Serial No. -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 6px 0; font-size: 10px;">
                        <div>
                            <span style="font-weight: bold;">Classification:</span>
                            <span style="margin-left: 40px; font-weight: bold;">METER SERIAL NO.:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 120px;"></span>
                        </div>
                        <div></div>
                    </div>
                    
                    <!-- Row 3: Classification Options & Kind of Service -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 6px 0; font-size: 10px;">
                        <div>
                            <div style="margin: 4px 0;">_____ Residential</div>
                            <div style="margin: 4px 0;">_____ Commercial</div>
                            <div style="margin: 4px 0;">_____ Industrial</div>
                        </div>
                        <div>
                            <div style="font-weight: bold; margin-bottom: 4px;">Kind of Service:</div>
                            <div style="margin: 4px 0;">_____ New Connection</div>
                            <div style="margin: 4px 0;">_____ Temporary Close</div>
                            <div style="margin: 4px 0;">_____ Transfer</div>
                        </div>
                    </div>
                    
                    <!-- Registration & Fees Section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 10px 0; font-size: 10px;">
                        <div>
                            <div style="font-weight: bold; margin-bottom: 4px;">Registration and other fees:</div>
                            <div>
                                <span style="font-weight: bold;">O.R. No.:</span>
                                <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 100px;"></span>
                                <span style="margin-left: 20px; font-weight: bold;">Amount: ₱ 350.00</span>
                            </div>
                        </div>
                        <div style="padding-top: 24px;">
                            <span style="font-weight: bold;">Date:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 150px;"></span>
                        </div>
                    </div>
                    
                    <hr style="border: none; border-top: 1px solid #000; margin: 10px 0;">
                    
                    <!-- Contract Introduction -->
                    <div style="margin: 10px 0; text-align: justify; font-size: 10px; line-height: 1.6;">
                        This CONTRACT is made and entered into this <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 30px;"></span> day of <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 80px;"></span>, 20<span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 30px;"></span> at Initao, Misamis Oriental, Philippines, by and between the Initao Municipal Waterworks System (IMWS), represented by the MEEDO, and the CUSTOMER <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 150px;">${customer.CustomerName || ''}</span>, with residence at <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 200px;">${customer.AreaCode || ''}</span>, Initao, Misamis Oriental.
                    </div>
                    
                    <div style="font-size: 10px; font-weight: bold; margin: 10px 0; text-align: center;">THE PARTIES AGREE THAT:</div>
                    
                    <!-- Clauses 1-15 -->
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>1.</strong> The Initao Waterworks System shall furnish water service in the customer's installation at the address given on the above record during the period of this contract at the rate stipulated and under the conditions provided in the schedule of rates.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>2.</strong> The IMWS shall not be responsible for the interruption of the service for causes beyond its control, not liable to the customer for damages caused by defective connection.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>3.</strong> The IMWS reserve the right to cut off the water supply or disconnect services for any day without prior notice for the following reasons:
                        <div style="margin-left: 20px; margin-top: 4px;">
                            <div>(a) For Repair</div>
                            <div>(b) For non-payment of bills within stipulated period</div>
                            <div>(c) For fraudulent practice in relation to the use of water sewer</div>
                        </div>
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>4.</strong> The CUSTOMER shall conform to and abide with all IMWS policies, resolutions, rules, and regulations pertaining to the operation of the water district.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>5.</strong> The CUSTOMER shall pay his/her bill. The CUSTOMER shall be considered delinquent immediately after the reading of the water meter. His/Her due date is thirty (30) days from billing date within which time he/she pays without penalty. All water bills unpaid after due date shall be considered delinquent and subject to 10% surcharge as penalty. DISCONNECTION shall be made if the customer shall not settle his/her account within 48 hours penalty period after due date.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>6.</strong> The CUSTOMER shall pay to IMWS the water bill on or before the due date and upon presentation of water bills.
                    </div>
                    
                    <div style="margin: 6px 0 6px 40px; font-size: 10px;">
                        <div style="margin: 4px 0;">
                            <span style="font-weight: bold;">Type:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 150px;"></span>
                            <span style="margin-left: 30px; font-weight: bold;">Service Line Size:</span>
                            <span style="border-bottom: 1px solid #000; padding: 0 2px; display: inline-block; min-width: 50px;"></span> Ø
                        </div>
                    </div>
                    
                    <div style="margin: 6px 0 6px 40px; font-size: 10px;">
                        <div style="font-weight: bold; margin-bottom: 4px;">6.1) For RESIDENTIAL</div>
                        <div style="margin-left: 20px; line-height: 1.6;">
                            <div>a. 1st to 10 m³ minimum &nbsp;&nbsp;&nbsp;&nbsp; ₱100.00 &nbsp;&nbsp; (1–10 m³)</div>
                            <div>b. 2nd 10 m³ (11–20) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱11.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                            <div>c. 3rd 10 m³ (21–30) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱12.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                            <div>d. Excess (31 up) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱13.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                        </div>
                        <div style="font-weight: bold; margin: 6px 0 4px;">6.2) For COMMERCIAL</div>
                        <div style="margin-left: 20px; line-height: 1.6;">
                            <div>a. 1st to 10 m³ minimum &nbsp;&nbsp;&nbsp;&nbsp; ₱200.00 &nbsp; (1–10 m³)</div>
                            <div>b. 2nd 10 m³ (11–20) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱22.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                            <div>c. 3rd 10 m³ (21–30) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱24.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                            <div>d. Excess (31 up) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱26.00 &nbsp;&nbsp;&nbsp; per cubic meter</div>
                        </div>
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>7.</strong> The customer shall protect the meter from any damage. The customer shall allow the IMWS personnel to install the water meter within the perimeter of the building and construct a concrete box visible for the water meter reader. The customer shall replace the water meter or apply for replacement of new water meter if defective.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>8.</strong> Should any water meter become unserviceable or condemned for one reason or another, the average consumption of the customer for three months period previous to the date when the meter becomes unserviceable or was condemned shall be the basis for the subsequent bills or flat rate shall be charged whichever is greater.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>9.</strong> The CUSTOMER shall make no changes in his/her approved connection without previously signing a revision of contract for that purpose in the Office of the Initao Municipal Waterworks System. The Customer shall notify the Initao Waterworks System in the case of transfer of ownership or the tenant leaves the premises.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>10.</strong> Representative of the Initao Municipal Waterworks System shall have access to the premises at all hours without the permission from the household owner for the purpose of inspection, testing, repair and DISCONNECTION and that no one shall be permitted to remove, change tamper with the installation unless authorized by the Initao Municipal Waterworks System.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>11.</strong> The CUSTOMER shall maintain the installation in proper condition while it is connected with the main line of the Initao Municipal Waterworks System and guarantee that no tapping will be made on the service pipes nor allow sub connection without prior authority from the IMWS that he/she agrees to maintain the expenses of the same.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>12.</strong> Service pipes and accessories installed within the road right of way shall be automatically donated the IMWS and become property of the INITAO Municipality.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>13.</strong> The CUSTOMER is not allowed to tap motor pump regardless of any horsepower or any accessories direct to waterline of the IMWS and to illegally tap/connect in the waterline. Should this prohibition not be observed he/she shall be subject to the following penalties to wit:
                        <div style="margin-left: 20px; margin-top: 4px;">
                            <div>1. First offense – disconnection and administrative fine of ₱500.00</div>
                            <div>2. Second offense – disconnection and administrative fine of ₱1,000.00</div>
                            <div>3. Third offense – automatic disconnection and no reconnection, ₱2,000.00</div>
                        </div>
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>14.</strong> No installation or re-installation of water meter is done without CLEARANCE from the MUNICIPAL TREASURER/REPRESENTATIVE.
                    </div>
                    
                    <div style="margin: 6px 0 6px 20px; font-size: 10px; text-align: justify; line-height: 1.6;">
                        <strong>15.</strong> The customer shall adhere to the policy that only IMWS technical personnel will conduct the installation of all water connections from the distribution lines to the building. In such cases where the service of other technical personnel is needed, he should be a registered plumber and duly signed by the MEED Officer. However, after such installation, the plumber foreman IMWS shall inspect the installation based on the specification and shall submit the technical to the MEEDO.
                    </div>
                    
                    <div style="margin-top: 16px; font-size: 10px; font-weight: bold;">IN WITNESS WHEREOF, we hereby affix our signatures on the date and place first above written.</div>
                    
                    <!-- Signature Section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; font-size: 10px;">
                        <div>
                            <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 4px; text-align: center;">Customer's Signature</div>
                        </div>
                        <div>
                            <div style="font-weight: bold; margin-bottom: 4px;">Recommending Approval:</div>
                            <div style="border-top: 1px solid #000; margin-top: 30px; padding-top: 4px;"></div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 10px; font-size: 10px;">
                        <div>
                            <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 4px; text-align: center;">Address</div>
                        </div>
                        <div>
                            <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 4px;"></div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; font-size: 10px; font-weight: bold; text-align: center;">
                        <div>INITAO MUNICIPAL WATERWORKS SYSTEM</div>
                        <div style="margin-top: 8px;">ENGR. HERMAN L. BALABAT</div>
                        <div>MEEDO</div>
                    </div>
                    
                    <!-- Addendum Section -->
                    <div style="margin-top: 20px;">
                        <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px;">ADDENDUM</div>
                        <div style="font-size: 10px; font-weight: bold; margin-bottom: 6px;">THE PROPERTY OWNER AGREES</div>
                        <div style="font-size: 10px; text-align: justify; line-height: 1.6;">
                            To guarantee the responsibility of payment of all water bills and of all other accounts incurred in connection with said water service upon default of payment of the customer.
                        </div>
                        
                        <!-- Final Signatures -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; font-size: 10px;">
                            <div>
                                <div style="border-top: 1px solid #000; margin-top: 30px; padding-top: 4px; text-align: center;">Witnesses</div>
                            </div>
                            <div>
                                <div style="border-top: 1px solid #000; margin-top: 30px; padding-top: 4px; text-align: center;">Property Owner</div>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 10px; font-size: 10px;">
                            <div>
                                <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 4px;"></div>
                            </div>
                            <div></div>
                        </div>
                    </div>
                </div>
                
                <script>
                    // Disable browser headers/footers by setting empty title
                    document.title = '';
                    
                    window.addEventListener('beforeprint', function() {
                        document.title = '';
                    });
                    
                    window.addEventListener('afterprint', function() {
                        // Set filename for download
                        document.title = '${fileName.replace('.pdf', '')}';
                    });
                    
                    window.onload = function() {
                        // Ensure title is empty for print
                        document.title = '';
                        setTimeout(function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        }, 250);
                    };
                </script>
            </body>
            </html>
        `);
        w.document.close();
    }
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
                    @page { size: A4; margin: 15mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #1a1a1a; line-height: 1.6; background: white; }
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
                        @page { margin: 15mm; }
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
                        setTimeout(function() { window.print(); }, 250);
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
                    @page { size: A4; margin: 15mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 40px; color: #1a1a1a; background: white; }
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
                    @media print { 
                        body { padding: 20px; }
                        @page { margin: 15mm; }
                    }
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
                        setTimeout(function() { window.print(); }, 250);
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
                    @page { size: A4; margin: 15mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 30px; background: white; }
                    h1 { color: #1e293b; margin-bottom: 10px; font-size: 24px; }
                    .meta { color: #64748b; font-size: 13px; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; font-size: 13px; }
                    th { background: #f1f5f9; font-weight: 600; color: #1e293b; }
                    tr:nth-child(even) { background: #f8fafc; }
                    @media print { 
                        body { padding: 15px; }
                        @page { margin: 15mm; }
                    }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                <div class="meta">Generated on ${new Date().toLocaleString()}</div>
                ${table.outerHTML}
                <script>window.onload = function() { setTimeout(function() { window.print(); }, 250); };</script>
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
                    @page { size: A4; margin: 15mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 30px; text-align: center; background: white; }
                    h1 { color: #1e293b; margin-bottom: 10px; font-size: 24px; }
                    .meta { color: #64748b; font-size: 13px; margin-bottom: 20px; }
                    img { max-width: 100%; height: auto; border: 1px solid #e2e8f0; }
                    @media print { 
                        body { padding: 15px; }
                        @page { margin: 15mm; }
                    }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                <div class="meta">Generated on ${new Date().toLocaleString()}</div>
                <img src="${imgData}" alt="${title}">
                <script>window.onload = function() { setTimeout(function() { window.print(); }, 250); };</script>
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
