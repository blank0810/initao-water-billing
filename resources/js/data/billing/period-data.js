// ============================================
// BILLING PERIOD MANAGEMENT - MASTER DATA
// ============================================

console.log('Loading billing period module...');

// ============================================
// PERIOD STATUS CONSTANTS
// ============================================

const PERIOD_STATUSES = {
    ACTIVE: 1,      // Current billing period
    CLOSED: 2,      // Completed and closed
    PENDING: 3,     // Future period
    CANCELLED: 0    // Cancelled period
};

// ============================================
// BILLING PERIODS DATA
// ============================================

const periods = [
    { 
        per_id: 1, 
        per_start_date: '2024-01-01 00:00:00', 
        per_end_date: '2024-01-31 23:59:59',
        create_date: '2023-12-15 00:00:00',
        stat_id: PERIOD_STATUSES.CLOSED,
        period_name: 'January 2024',
        reading_deadline: '2024-02-05',
        billing_deadline: '2024-02-10',
        payment_deadline: '2024-02-15'
    },
    { 
        per_id: 2, 
        per_start_date: '2024-02-01 00:00:00', 
        per_end_date: '2024-02-29 23:59:59',
        create_date: '2024-01-15 00:00:00',
        stat_id: PERIOD_STATUSES.CLOSED,
        period_name: 'February 2024',
        reading_deadline: '2024-03-05',
        billing_deadline: '2024-03-10',
        payment_deadline: '2024-03-15'
    },
    { 
        per_id: 3, 
        per_start_date: '2024-03-01 00:00:00', 
        per_end_date: '2024-03-31 23:59:59',
        create_date: '2024-02-15 00:00:00',
        stat_id: PERIOD_STATUSES.CLOSED,
        period_name: 'March 2024',
        reading_deadline: '2024-04-05',
        billing_deadline: '2024-04-10',
        payment_deadline: '2024-04-15'
    },
    { 
        per_id: 4, 
        per_start_date: '2024-04-01 00:00:00', 
        per_end_date: '2024-04-30 23:59:59',
        create_date: '2024-03-15 00:00:00',
        stat_id: PERIOD_STATUSES.CLOSED,
        period_name: 'April 2024',
        reading_deadline: '2024-05-05',
        billing_deadline: '2024-05-10',
        payment_deadline: '2024-05-15'
    },
    { 
        per_id: 5, 
        per_start_date: '2024-05-01 00:00:00', 
        per_end_date: '2024-05-31 23:59:59',
        create_date: '2024-04-15 00:00:00',
        stat_id: PERIOD_STATUSES.CLOSED,
        period_name: 'May 2024',
        reading_deadline: '2024-06-05',
        billing_deadline: '2024-06-10',
        payment_deadline: '2024-06-15'
    },
    { 
        per_id: 6, 
        per_start_date: '2024-06-01 00:00:00', 
        per_end_date: '2024-06-30 23:59:59',
        create_date: '2024-05-15 00:00:00',
        stat_id: PERIOD_STATUSES.ACTIVE,
        period_name: 'June 2024',
        reading_deadline: '2024-07-05',
        billing_deadline: '2024-07-10',
        payment_deadline: '2024-07-15'
    },
    { 
        per_id: 7, 
        per_start_date: '2024-07-01 00:00:00', 
        per_end_date: '2024-07-31 23:59:59',
        create_date: '2024-06-15 00:00:00',
        stat_id: PERIOD_STATUSES.PENDING,
        period_name: 'July 2024',
        reading_deadline: '2024-08-05',
        billing_deadline: '2024-08-10',
        payment_deadline: '2024-08-15'
    }
];

// ============================================
// HELPER FUNCTIONS
// ============================================

function getCurrentPeriod() {
    return periods.find(p => p.stat_id === PERIOD_STATUSES.ACTIVE);
}

function getPeriodById(periodId) {
    return periods.find(p => p.per_id === periodId);
}

function getPeriodsByStatus(statusId) {
    return periods.filter(p => p.stat_id === statusId);
}

function getClosedPeriods() {
    return periods.filter(p => p.stat_id === PERIOD_STATUSES.CLOSED);
}

function getPeriodName(periodId) {
    const period = getPeriodById(periodId);
    return period ? period.period_name : 'Unknown Period';
}

function isPeriodActive(periodId) {
    const period = getPeriodById(periodId);
    return period && period.stat_id === PERIOD_STATUSES.ACTIVE;
}

function getStatusName(statusId) {
    const statusNames = {
        [PERIOD_STATUSES.ACTIVE]: 'Active',
        [PERIOD_STATUSES.CLOSED]: 'Closed',
        [PERIOD_STATUSES.PENDING]: 'Pending',
        [PERIOD_STATUSES.CANCELLED]: 'Cancelled'
    };
    return statusNames[statusId] || 'Unknown';
}

function getStatusColor(statusId) {
    const colors = {
        [PERIOD_STATUSES.ACTIVE]: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        [PERIOD_STATUSES.CLOSED]: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        [PERIOD_STATUSES.PENDING]: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        [PERIOD_STATUSES.CANCELLED]: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    return colors[statusId] || 'bg-gray-100 text-gray-800';
}

// ============================================
// EXPORT DATA
// ============================================

window.periodData = {
    periods,
    PERIOD_STATUSES,
    getCurrentPeriod,
    getPeriodById,
    getPeriodsByStatus,
    getClosedPeriods,
    getPeriodName,
    isPeriodActive,
    getStatusName,
    getStatusColor
};

console.log('Billing period module loaded successfully');
console.log(`Total periods: ${periods.length}`);
console.log(`Current period: ${getCurrentPeriod()?.period_name || 'None'}`);
