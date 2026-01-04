class MEEDOContractPrint {
    static printWaterServiceContract(customer) {
        const w = window.open('', '_blank', 'width=842,height=1191');
        const today = new Date();
        const days = ['', '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th', '21st', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th', '31st'];
        const months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        w.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Water Service Contract - ${customer.CustomerName || 'Customer'}</title>
                <style>
                    @page { size: A4; margin: 10mm; }
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Courier New', monospace; font-size: 10px; padding: 20px; color: #000; line-height: 1.4; background: white; }
                    .header { text-align: center; margin-bottom: 8px; }
                    .header-line { font-size: 10px; font-weight: normal; line-height: 1.3; }
                    .divider { border: none; border-top: 1px solid #000; margin: 6px 0; }
                    .section-title { font-weight: bold; margin: 8px 0 4px; font-size: 10px; }
                    .row { display: flex; gap: 12px; margin: 4px 0; }
                    .col { flex: 1; }
                    .field-group { margin: 4px 0; }
                    .field-label { display: inline; font-weight: bold; }
                    .field-value { display: inline; border-bottom: 1px solid #000; min-width: 80px; padding: 0 2px; }
                    .checkbox { display: inline; margin-right: 12px; }
                    .contract-body { margin: 6px 0; text-align: justify; line-height: 1.5; }
                    .contract-text { font-size: 9px; margin: 4px 0; line-height: 1.6; text-align: justify; }
                    .contract-item { margin: 4px 0 4px 20px; text-align: justify; font-size: 9px; line-height: 1.5; }
                    .rate-table { margin: 6px 0; font-size: 9px; }
                    .rate-row { display: flex; gap: 10px; margin: 2px 0; }
                    .signature-section { display: flex; gap: 20px; margin-top: 20px; font-size: 9px; }
                    .signature-block { flex: 1; }
                    .signature-line { border-top: 1px solid #000; margin-top: 30px; text-align: center; }
                    .addendum { margin-top: 16px; }
                    @media print {
                        body { padding: 10px; }
                        @page { margin: 10mm; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="header-line">Republic of the Philippines</div>
                    <div class="header-line">Province of Misamis Oriental</div>
                    <div class="header-line">MUNICIPALITY OF INITAO</div>
                    <div class="header-line"><strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong></div>
                    <div class="header-line" style="margin-top: 4px;"><strong>WATER SERVICE CONTRACT</strong></div>
                </div>
                
                <div class="divider"></div>
                
                <div class="field-group">
                    <span class="field-label">CONCESSIONER:</span>
                    <span class="field-value">${customer.CustomerName || '_____________________________'}</span>
                    <span class="field-label" style="margin-left: 20px;">ADDRESS:</span>
                    <span class="field-value">${customer.AreaCode || '_____________________________'}</span>
                </div>
                
                <div class="row" style="font-size: 9px;">
                    <div class="col">
                        <div style="margin-bottom: 4px;"><strong>Classification:</strong></div>
                        <div class="checkbox">[ ] Residential</div>
                        <div class="checkbox">[ ] Commercial</div>
                        <div class="checkbox">[ ] Industrial</div>
                        <div style="margin-top: 8px;"><strong>METER SERIAL NO.:</strong> <span class="field-value" style="min-width: 120px;">_____________________</span></div>
                        <div style="margin-top: 4px;"><strong>Kind of Service:</strong></div>
                        <div class="checkbox">[ ] New Connection</div>
                        <div class="checkbox">[ ] Temporary Close</div>
                        <div class="checkbox">[ ] Transfer</div>
                    </div>
                </div>
                
                <div style="margin-top: 8px; font-size: 9px;">
                    <div class="row" style="align-items: flex-start;">
                        <div class="col">
                            <span class="field-label">O.R. No.:</span>
                            <span class="field-value" style="min-width: 100px;">_________________</span>
                            <span class="field-label" style="margin-left: 10px;">Amount:</span>
                            <span>₱ 350.00</span>
                        </div>
                        <div class="col" style="text-align: right;">
                            <span class="field-label">Date:</span>
                            <span class="field-value" style="min-width: 100px;">________________</span>
                        </div>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <div class="contract-body">
                    This <strong>CONTRACT</strong> made and entered into this <span class="field-value" style="min-width: 30px;">_____</span> day of <span class="field-value" style="min-width: 60px;">__________</span>, 20<span class="field-value" style="min-width: 20px;">___</span> at Initao, Misamis Oriental, Philippines, by and between the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong>, Initao, Misamis Oriental, represented by <strong>MEEDO</strong> and <strong>CUSTOMER</strong> <span class="field-value">${customer.CustomerName || '__________________________'}</span> with postal address and residence at <span class="field-value">${customer.AreaCode || '__________________________'}</span>, Initao, Mis. Or.
                </div>
                
                <div class="section-title">THE PARTIES AGREE THAT:</div>
                
                <div class="contract-item">
                    <strong>1.</strong> The <strong>INITAO WATERWORKS SYSTEM</strong> shall furnish water service in the <strong>CUSTOMER's</strong> installation at the address given on the above record during the period of this contract at the rate stipulated and under the conditions provided in the schedule of rates.
                </div>
                
                <div class="contract-item">
                    <strong>2.</strong> The <strong>IMWS</strong> shall not be responsible for the interruption of the service for causes beyond its control, not liable to the <strong>CUSTOMER</strong> for damages caused by defective connection.
                </div>
                
                <div class="contract-item">
                    <strong>3.</strong> The <strong>IMWS</strong> reserve the right to cut off the water supply or disconnect services for any day without prior notice for the following reasons:
                    <div style="margin-left: 20px; margin-top: 2px;">
                        <div>(a) For <strong>REPAIR</strong></div>
                        <div>(b) For non-payment of bills within stipulated period</div>
                        <div>(c) For fraudulent practice in relation to the use of water sewer</div>
                    </div>
                </div>
                
                <div class="contract-item">
                    <strong>4.</strong> The <strong>CUSTOMER</strong> shall conform to and abide with all <strong>IMWS</strong> policies, resolutions, rules, and regulations pertaining to the operation of the water district.
                </div>
                
                <div class="contract-item">
                    <strong>5.</strong> The <strong>CUSTOMER</strong> shall pay his/her bill. The <strong>CUSTOMER</strong> shall be considered delinquent immediately after the reading of the water meter. His/Her due date is thirty (30) days from billing date within which time he/she pays without penalty. All water bills unpaid after due date shall be considered delinquent and subject to 10% surcharge as penalty. <strong>DISCONNECTION</strong> shall be made if the customer shall not settle his/her account within 48 hours penalty period after due date.
                </div>
                
                <div class="contract-item">
                    <strong>6.</strong> The <strong>CUSTOMER</strong> shall pay to <strong>IMWS</strong> the water bill on or before the due date and upon presentation of water bills.
                </div>
                
                <div style="margin: 4px 0 4px 40px; font-size: 9px;">
                    <div class="row">
                        <div class="col">
                            Type: <span class="field-value" style="min-width: 120px;">___________________</span>
                        </div>
                        <div class="col" style="text-align: right;">
                            Service Line Size: <span class="field-value" style="min-width: 80px;">_____________</span>Ø
                        </div>
                    </div>
                </div>
                
                <div style="margin: 4px 0 4px 40px; font-size: 9px;">
                    <div style="font-weight: bold; margin-bottom: 2px;">6.1) For <strong>RESIDENTIAL</strong></div>
                    <div style="margin-left: 10px; line-height: 1.5;">
                        <div>a. 1st to 10 m³ minimum - ₱100.00 ..............(1–10 m³)</div>
                        <div>b. 2nd 10 m³ (11–20) - ₱11.00 ..................per cubic meter</div>
                        <div>c. 3rd 10 m³ (21–30) - ₱12.00 ..................per cubic meter</div>
                        <div>d. Excess (31 up) - ₱13.00 .....................per cubic meter</div>
                    </div>
                    <div style="font-weight: bold; margin: 4px 0 2px;">6.2) For <strong>COMMERCIAL</strong></div>
                    <div style="margin-left: 10px; line-height: 1.5;">
                        <div>a. 1st to 10 m³ minimum - ₱200.00 ..............(1–10 m³)</div>
                        <div>b. 2nd 10 m³ (11–20) - ₱22.00 ..................per cubic meter</div>
                        <div>c. 3rd 10 m³ (21–30) - ₱24.00 ..................per cubic meter</div>
                        <div>d. Excess (31 up) - ₱26.00 .....................per cubic meter</div>
                    </div>
                </div>
                
                <div class="contract-item">
                    <strong>7.</strong> The <strong>CUSTOMER</strong> shall protect the meter from any damage. The <strong>CUSTOMER</strong> shall allow the <strong>IMWS</strong> personnel to install the water meter within the perimeter of the building and construct a concrete box visible for the water meter reader. The <strong>CUSTOMER</strong> shall replace the water meter or apply for replacement of new water meter if defective.
                </div>
                
                <div class="contract-item">
                    <strong>8.</strong> Should any water meter become unserviceable or condemned for one reason or another, the average consumption of the customer for three months period previous to the date when the meter becomes unserviceable or was condemned shall be the basis for the subsequent bills or flat rate shall be charged whichever is greater.
                </div>
                
                <div class="contract-item">
                    <strong>9.</strong> The <strong>CUSTOMER</strong> shall make no changes in his/her approved connection without previously signing a revision of contract for that purpose in the Office of the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong>. The <strong>CUSTOMER</strong> shall notify the <strong>INITAO WATERWORKS SYSTEM</strong> in the case of transfer of ownership or the tenant leaves the premises.
                </div>
                
                <div class="contract-item">
                    <strong>10.</strong> Representative of the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong> shall have access to the premises at all hours without the permission from the household owner for the purpose of inspection, testing, repair and <strong>DISCONNECTION</strong> and that no one shall be permitted to remove, change tamper with the installation unless authorized by the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong>.
                </div>
                
                <div class="contract-item">
                    <strong>11.</strong> The <strong>CUSTOMER</strong> shall maintain the installation in proper condition while it is connected with the main line of the <strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong> and guarantee that no tapping will be made on the service pipes nor allow sub connection without prior authority from the <strong>IMWS</strong> that he/she agrees to maintain the expenses of the same.
                </div>
                
                <div class="contract-item">
                    <strong>12.</strong> Service pipes and accessories installed within the road right of way shall be automatically donated the <strong>IMWS</strong> and become property of the <strong>INITAO MUNICIPALITY</strong>.
                </div>
                
                <div class="contract-item">
                    <strong>13.</strong> The <strong>CUSTOMER</strong> is not allowed to tap motor pump regardless of any horsepower or any accessories direct to waterline of the <strong>IMWS</strong> and to illegally tap/connect in the waterline. Should this prohibition not be observed he/she shall be subject to the following penalties to wit:
                    <div style="margin-left: 20px; margin-top: 2px;">
                        <div>1. First offense – disconnection and administrative fine of ₱500.00</div>
                        <div>2. Second offense – disconnection and administrative fine of ₱1,000.00</div>
                        <div>3. Third offense – automatic disconnection and no reconnection, ₱2,000.00</div>
                    </div>
                </div>
                
                <div class="contract-item">
                    <strong>14.</strong> No installation or re-installation of water meter is done without <strong>CLEARANCE</strong> from the <strong>MUNICIPAL TREASURER/REPRESENTATIVE</strong>.
                </div>
                
                <div class="contract-item">
                    <strong>15.</strong> The <strong>CUSTOMER</strong> shall adhere to the policy that only <strong>IMWS</strong> technical personnel will conduct the installation of all water connections from the distribution lines to the building. In such cases where the service of other technical personnel is needed, he should be a registered plumber and duly signed by the <strong>MEED OFFICER</strong>. However, after such installation, the plumber foreman <strong>IMWS</strong> shall inspect the installation based on the specification and shall submit the technical to the <strong>MEEDO</strong>.
                </div>
                
                <div style="margin-top: 12px; font-size: 9px; font-weight: bold;">IN WITNESS WHEREOF, we hereby affix our signature on the date and place first above written.</div>
                
                <div class="signature-section">
                    <div class="signature-block">
                        <div class="signature-line">Customer's Signature</div>
                        <div style="margin-top: 4px;">_________________________</div>
                        <div style="font-size: 8px;">Address</div>
                    </div>
                    <div class="signature-block">
                        <div style="margin-top: 30px; text-align: center;">_________________________</div>
                        <div style="font-size: 8px; text-align: center;">Recommending Approval</div>
                        <div style="font-size: 8px; text-align: center;"><strong>MEEDO</strong> Representative</div>
                    </div>
                </div>
                
                <div style="margin-top: 12px; font-size: 9px; font-weight: bold;">
                    <div><strong>INITAO MUNICIPAL WATERWORKS SYSTEM</strong></div>
                    <div style="margin-top: 8px;">ENGR. HERMAN L. BALABAT</div>
                    <div><strong>MEEDO</strong></div>
                </div>
                
                <div class="addendum">
                    <div style="font-size: 9px; font-weight: bold; margin-bottom: 6px;">ADDENDUM</div>
                    <div style="font-size: 9px; font-weight: bold; margin-bottom: 4px;">THE PROPERTY OWNER AGREES</div>
                    <div class="contract-text">
                        To guarantee the responsibility of payment of all water bills and of all other accounts incurred in connection with said water service upon default of payment of the customer.
                    </div>
                    <div style="display: flex; gap: 20px; margin-top: 20px; font-size: 9px;">
                        <div style="flex: 1;">
                            <div style="margin-top: 30px;">_________________________</div>
                            <div style="padding-top: 4px;">Witness</div>
                        </div>
                        <div style="flex: 1; text-align: center;">
                            <div style="margin-top: 30px;">_________________________</div>
                            <div style="padding-top: 4px;">Property Owner</div>
                        </div>
                    </div>
                </div>
                
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 250);
                    };
                </script>
            </body>
            </html>
        `);
        w.document.close();
    }
}

// Export to global scope
window.MEEDOContractPrint = MEEDOContractPrint;

export { MEEDOContractPrint };
