// Re-export print functions from unified-print.js
import './unified-print.js';

// Export printCustomerForm for compatibility
export function printCustomerForm(customer) {
    if (window.printCustomerForm) {
        window.printCustomerForm(customer);
    }
}
