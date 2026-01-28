/**
 * Customer Print Utilities
 *
 * Handles all print-related functionality for customer forms and receipts.
 * Extracted from customer.js for better modularity and reusability.
 */

/**
 * Print customer form using UnifiedPrintSystem
 * @param {Object} customer - Customer object with customer data
 */
function printCustomerFormDirect(customer) {
    // Use UnifiedPrintSystem if available, fallback to window function
    if (window.UnifiedPrintSystem) {
        window.UnifiedPrintSystem.printCustomerForm(customer);
    } else if (window.printCustomerForm) {
        window.printCustomerForm(customer);
    }
}

/**
 * Print requirement receipt for customer
 * @param {Object} customer - Customer object containing customer details
 */
function printRequirementReceipt(customer) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Requirement Receipt - ${customer.customer_code}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info { margin: 10px 0; }
                .footer { margin-top: 50px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Water Service Application</h2>
                <h3>Requirement Receipt</h3>
            </div>
            <div class="info"><strong>Customer Code:</strong> ${customer.customer_code}</div>
            <div class="info"><strong>Name:</strong> ${customer.cust_first_name} ${customer.cust_last_name}</div>
            <div class="info"><strong>Address:</strong> ${customer.address}</div>
            <div class="info"><strong>Date Applied:</strong> ${new Date(customer.created_at).toLocaleDateString()}</div>
            <div class="info"><strong>Date Printed:</strong> ${new Date().toLocaleString()}</div>
            <div class="footer">
                <p>Please proceed to payment processing.</p>
                <p>Keep this receipt for your records.</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

/**
 * Get print count for a customer from localStorage
 * @param {string} customerCode - Customer code to lookup
 * @returns {string|number} Print count or empty string if 0
 */
function getPrintCount(customerCode) {
    const counts = JSON.parse(localStorage.getItem('printCounts') || '{}');
    const count = counts[customerCode] || 0;
    return count > 0 ? count : '';
}

/**
 * Increment print count for a customer in localStorage
 * @param {string} customerCode - Customer code to increment
 * @returns {number} New print count
 */
function incrementPrintCount(customerCode) {
    const counts = JSON.parse(localStorage.getItem('printCounts') || '{}');
    counts[customerCode] = (counts[customerCode] || 0) + 1;
    localStorage.setItem('printCounts', JSON.stringify(counts));
    return counts[customerCode];
}

// Make functions globally accessible via window.CustomerPrint namespace
window.CustomerPrint = {
    printForm: printCustomerFormDirect,
    printReceipt: printRequirementReceipt,
    getPrintCount: getPrintCount,
    incrementPrintCount: incrementPrintCount
};

// ES6 module export (for modern import usage)
export default {
    printForm: printCustomerFormDirect,
    printReceipt: printRequirementReceipt,
    getPrintCount,
    incrementPrintCount
};
