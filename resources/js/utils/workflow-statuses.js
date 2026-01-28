// Workflow status configuration
const workflowStatuses = {
    'PENDING_DOCS': { label: 'Pending Documents', color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200', icon: 'fa-clock' },
    'DOCS_PRINTED': { label: 'Documents Printed', color: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200', icon: 'fa-print' },
    'PAYMENT_PENDING': { label: 'Payment Pending', color: 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200', icon: 'fa-credit-card' },
    'PAYMENT_COMPLETED': { label: 'Payment Completed', color: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200', icon: 'fa-check-circle' },
    'APPROVED': { label: 'Approved', color: 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200', icon: 'fa-thumbs-up' },
    'CONNECTED': { label: 'Connected', color: 'bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-200', icon: 'fa-plug' }
};

// Make it globally accessible
window.WorkflowStatuses = workflowStatuses;

// Export for module usage
export default workflowStatuses;
