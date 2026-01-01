/**
 * ========================================
 * CUSTOMER WORKFLOW CONFIGURATION
 * 3-Phase System: Application → Payment → Approval
 * ========================================
 */

// ============================================
// PHASE 1: APPLICATION & DOCUMENTATION
// ============================================
export const PHASE1_STATUSES = {
    NEW_APPLICATION: {
        label: 'New Application',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
        icon: 'fa-file-alt',
        phase: 1,
        description: 'Customer has submitted application',
        nextActions: ['Print Documents', 'Edit Info']
    },
    DOCS_PRINTED: {
        label: 'Documents Printed',
        color: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200',
        icon: 'fa-print',
        phase: 1,
        description: 'Requirements form printed',
        nextActions: ['Mark Requirements Submitted']
    },
    REQUIREMENTS_SUBMITTED: {
        label: 'Requirements Submitted',
        color: 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200',
        icon: 'fa-folder-open',
        phase: 1,
        description: 'Customer has submitted documents',
        nextActions: ['Verify Documents']
    },
    REQUIREMENTS_VERIFIED: {
        label: 'Requirements Verified',
        color: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        icon: 'fa-check-double',
        phase: 1,
        description: 'All documents verified and complete',
        nextActions: ['Generate Invoice']
    }
};

// ============================================
// PHASE 2: BILLING & PAYMENT
// ============================================
export const PHASE2_STATUSES = {
    READY_FOR_PAYMENT: {
        label: 'Ready for Payment',
        color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
        icon: 'fa-file-invoice-dollar',
        phase: 2,
        description: 'Invoice generated, awaiting payment',
        nextActions: ['Process Payment']
    },
    PAYMENT_PENDING: {
        label: 'Payment Pending',
        color: 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',
        icon: 'fa-clock',
        phase: 2,
        description: 'Payment in process',
        nextActions: ['Verify Payment']
    },
    PAYMENT_VERIFIED: {
        label: 'Payment Verified',
        color: 'bg-teal-100 text-teal-800 dark:bg-teal-800 dark:text-teal-200',
        icon: 'fa-check-circle',
        phase: 2,
        description: 'Payment confirmed and verified',
        nextActions: ['Send to Approval']
    }
};

// ============================================
// PHASE 3: APPROVAL & CONNECTION
// ============================================
export const PHASE3_STATUSES = {
    READY_FOR_APPROVAL: {
        label: 'Ready for Approval',
        color: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-800 dark:text-cyan-200',
        icon: 'fa-clipboard-check',
        phase: 3,
        description: 'In approval queue',
        nextActions: ['Approve', 'Decline']
    },
    APPROVED: {
        label: 'Approved',
        color: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200',
        icon: 'fa-thumbs-up',
        phase: 3,
        description: 'Application approved by supervisor',
        nextActions: ['Schedule Connection']
    },
    CONNECTION_SCHEDULED: {
        label: 'Connection Scheduled',
        color: 'bg-sky-100 text-sky-800 dark:bg-sky-800 dark:text-sky-200',
        icon: 'fa-calendar-check',
        phase: 3,
        description: 'Installation date scheduled',
        nextActions: ['Assign Meter']
    },
    METER_ASSIGNED: {
        label: 'Meter Assigned',
        color: 'bg-violet-100 text-violet-800 dark:bg-violet-800 dark:text-violet-200',
        icon: 'fa-tachometer-alt',
        phase: 3,
        description: 'Meter assigned, ready for installation',
        nextActions: ['Complete Installation']
    },
    ACTIVE_CONSUMER: {
        label: 'Active Consumer',
        color: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        icon: 'fa-user-check',
        phase: 3,
        description: 'Meter installed, customer is active',
        nextActions: []
    }
};

// ============================================
// SPECIAL STATUSES
// ============================================
export const SPECIAL_STATUSES = {
    DECLINED: {
        label: 'Declined',
        color: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
        icon: 'fa-times-circle',
        phase: 0,
        description: 'Application declined',
        nextActions: ['Restore Application']
    },
    ON_HOLD: {
        label: 'On Hold',
        color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
        icon: 'fa-pause-circle',
        phase: 0,
        description: 'Application temporarily on hold',
        nextActions: ['Resume', 'Cancel']
    },
    CANCELLED: {
        label: 'Cancelled',
        color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
        icon: 'fa-ban',
        phase: 0,
        description: 'Application cancelled',
        nextActions: []
    }
};

// ============================================
// COMBINED STATUS REGISTRY
// ============================================
export const ALL_STATUSES = {
    ...PHASE1_STATUSES,
    ...PHASE2_STATUSES,
    ...PHASE3_STATUSES,
    ...SPECIAL_STATUSES
};

// ============================================
// STATUS TRANSITION RULES
// ============================================
export const STATUS_TRANSITIONS = {
    NEW_APPLICATION: ['DOCS_PRINTED', 'ON_HOLD', 'CANCELLED'],
    DOCS_PRINTED: ['REQUIREMENTS_SUBMITTED', 'ON_HOLD'],
    REQUIREMENTS_SUBMITTED: ['REQUIREMENTS_VERIFIED', 'NEW_APPLICATION', 'ON_HOLD'],
    REQUIREMENTS_VERIFIED: ['READY_FOR_PAYMENT'],
    READY_FOR_PAYMENT: ['PAYMENT_PENDING', 'ON_HOLD'],
    PAYMENT_PENDING: ['PAYMENT_VERIFIED', 'READY_FOR_PAYMENT'],
    PAYMENT_VERIFIED: ['READY_FOR_APPROVAL'],
    READY_FOR_APPROVAL: ['APPROVED', 'DECLINED', 'ON_HOLD'],
    APPROVED: ['CONNECTION_SCHEDULED'],
    CONNECTION_SCHEDULED: ['METER_ASSIGNED', 'APPROVED'],
    METER_ASSIGNED: ['ACTIVE_CONSUMER'],
    ACTIVE_CONSUMER: [],
    DECLINED: ['NEW_APPLICATION'],
    ON_HOLD: ['NEW_APPLICATION', 'DOCS_PRINTED', 'REQUIREMENTS_SUBMITTED', 'READY_FOR_PAYMENT'],
    CANCELLED: []
};

// ============================================
// PHASE HELPERS
// ============================================
export function getPhaseByStatus(status) {
    const statusConfig = ALL_STATUSES[status];
    return statusConfig ? statusConfig.phase : 0;
}

export function getPhaseStatuses(phase) {
    return Object.entries(ALL_STATUSES)
        .filter(([_, config]) => config.phase === phase)
        .reduce((acc, [key, config]) => ({ ...acc, [key]: config }), {});
}

export function canTransition(fromStatus, toStatus) {
    const allowedTransitions = STATUS_TRANSITIONS[fromStatus] || [];
    return allowedTransitions.includes(toStatus);
}

export function getNextActions(status) {
    const statusConfig = ALL_STATUSES[status];
    return statusConfig ? statusConfig.nextActions : [];
}

// ============================================
// PHASE PROGRESS CALCULATOR
// ============================================
export function calculateProgress(status) {
    const phase = getPhaseByStatus(status);
    const statusOrder = [
        'NEW_APPLICATION', 'DOCS_PRINTED', 'REQUIREMENTS_SUBMITTED', 'REQUIREMENTS_VERIFIED',
        'READY_FOR_PAYMENT', 'PAYMENT_PENDING', 'PAYMENT_VERIFIED',
        'READY_FOR_APPROVAL', 'APPROVED', 'CONNECTION_SCHEDULED', 'METER_ASSIGNED', 'ACTIVE_CONSUMER'
    ];
    
    const currentIndex = statusOrder.indexOf(status);
    if (currentIndex === -1) return 0;
    
    return Math.round((currentIndex / (statusOrder.length - 1)) * 100);
}

// ============================================
// REGISTRATION TYPES
// ============================================
export const REGISTRATION_TYPES = {
    RESIDENTIAL: { label: 'Residential', icon: 'fa-home', color: 'text-blue-600' },
    COMMERCIAL: { label: 'Commercial', icon: 'fa-building', color: 'text-purple-600' },
    INDUSTRIAL: { label: 'Industrial', icon: 'fa-industry', color: 'text-orange-600' },
    GOVERNMENT: { label: 'Government', icon: 'fa-landmark', color: 'text-green-600' }
};

// ============================================
// DOCUMENT TYPES
// ============================================
export const DOCUMENT_TYPES = {
    VALID_ID: 'Valid ID',
    BARANGAY_CLEARANCE: 'Barangay Clearance',
    PROOF_OF_RESIDENCE: 'Proof of Residence',
    TAX_DECLARATION: 'Tax Declaration',
    BUSINESS_PERMIT: 'Business Permit',
    DTI_REGISTRATION: 'DTI Registration',
    OTHER: 'Other Documents'
};

// ============================================
// PAYMENT METHODS
// ============================================
export const PAYMENT_METHODS = {
    CASH: { label: 'Cash', icon: 'fa-money-bill-wave' },
    CARD: { label: 'Debit/Credit Card', icon: 'fa-credit-card' },
    BANK_TRANSFER: { label: 'Bank Transfer', icon: 'fa-university' },
    GCASH: { label: 'GCash', icon: 'fa-mobile-alt' },
    PAYMAYA: { label: 'PayMaya', icon: 'fa-mobile-alt' },
    CHECK: { label: 'Check', icon: 'fa-money-check' }
};

// ============================================
// CHARGE TYPES
// ============================================
export const CHARGE_TYPES = {
    CONNECTION_FEE: { label: 'Connection Fee', amount: 1500, required: true },
    SERVICE_DEPOSIT: { label: 'Service Deposit', amount: 1000, required: true },
    METER_INSTALLATION: { label: 'Meter Installation', amount: 800, required: true },
    PROCESSING_FEE: { label: 'Processing Fee', amount: 200, required: true },
    INSPECTION_FEE: { label: 'Inspection Fee', amount: 300, required: false }
};

// Make available globally
if (typeof window !== 'undefined') {
    window.WorkflowConfig = {
        PHASE1_STATUSES,
        PHASE2_STATUSES,
        PHASE3_STATUSES,
        SPECIAL_STATUSES,
        ALL_STATUSES,
        STATUS_TRANSITIONS,
        REGISTRATION_TYPES,
        DOCUMENT_TYPES,
        PAYMENT_METHODS,
        CHARGE_TYPES,
        getPhaseByStatus,
        getPhaseStatuses,
        canTransition,
        getNextActions,
        calculateProgress
    };
}
