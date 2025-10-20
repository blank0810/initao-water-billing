export function printCustomerForm(customer) {
    const printWindow = window.open('', '_blank', 'width=842,height=1191'); // A4 size
    const logoUrl = '/images/logo.png'; // Replace with your logo

    const htmlContent = `
    <html>
    <head>
        <title>Customer Print</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; color: #000; }
            .header { text-align: center; }
            .header img { max-height: 80px; margin-bottom: 10px; }
            .muni-info { text-align: center; margin-bottom: 30px; font-size: 14px; }
            .customer-info { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; }
            .divider { border-bottom: 2px solid #000; margin: 10px 0 20px 0; }
            .requirements { margin-bottom: 50px; font-size: 13px; }
            .requirements li { margin-bottom: 5px; }
            .signature { display: flex; justify-content: space-between; margin-top: 60px; font-size: 14px; }
            .signature div { text-align: center; }
        </style>
    </head>
    <body>
        <div class="header">
            <img src="${logoUrl}" alt="Logo">
            <h2>Municipality of XYZ</h2>
        </div>
        <div class="muni-info">
            ZIP Code: 12345<br>
            Contact: (123) 456-7890
        </div>

        <div class="customer-info">
            <div>
                <strong>Customer Name:</strong> ${customer.CustomerName}<br>
                <strong>Customer ID:</strong> ${customer.id}<br>
                <strong>Status:</strong> ${customer.Status}
            </div>
            <div>
                <strong>Address:</strong> ${customer.AreaCode}<br>
                <strong>Email:</strong> ${customer.Email || '-'}<br>
                <strong>Date Applied:</strong> ${new Date(customer.DateApplied).toLocaleDateString()}
            </div>
        </div>

        <div class="divider"></div>

        <div class="requirements">
            <h4>Requirements for Water Billing:</h4>
            <ol>
                <li>Proof of Identification</li>
                <li>Barangay Clearance</li>
                <li>Filled Application Form</li>
                <li>Payment of Connection Fee</li>
                <li>Other Supporting Documents</li>
            </ol>
        </div>

        <div class="signature">
            <div>
                ___________________<br>
                Printed by
            </div>
            <div>
                ___________________<br>
                Customer Acknowledgement
            </div>
        </div>
    </body>
    </html>
    `;

    printWindow.document.open();
    printWindow.document.write(htmlContent);
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}
