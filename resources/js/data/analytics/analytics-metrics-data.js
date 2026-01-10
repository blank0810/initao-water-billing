// Analytics & Dashboard Metrics Data
const analyticsMetricsData = {
    period: 'January 2024',
    generated_date: new Date().toISOString(),
    
    billing_metrics: {
        total_consumers: 302,
        active_consumers: 298,
        inactive_consumers: 4,
        new_consumers_this_month: 5,
        total_bills_issued: 298,
        bills_paid: 287,
        bills_overdue: 8,
        bills_unpaid: 3,
        total_billed_amount: '₱185,450.50',
        total_collected: '₱178,923.00',
        collection_rate_percent: 96.48,
    },

    consumption_metrics: {
        total_consumption_cubic_meters: 7245,
        average_consumption_per_consumer: '23.97 m³',
        highest_consumption: '250 m³',
        lowest_consumption: '5 m³',
        peak_consumption_day: 'January 30, 2024',
        consumption_trend: 'stable',
    },

    payment_metrics: {
        total_payments_received: '₱178,923.00',
        total_payment_transactions: 287,
        average_payment_amount: '₱623.27',
        payment_methods: {
            bank_transfer: {
                count: 145,
                amount: '₱92,350.00',
                percent: 51.6,
            },
            over_the_counter: {
                count: 98,
                amount: '₱61,150.50',
                percent: 34.2,
            },
            check: {
                count: 44,
                amount: '₱25,422.50',
                percent: 14.2,
            },
        },
        average_days_to_pay: 12,
        on_time_payment_percent: 96.5,
        late_payment_percent: 3.5,
    },

    ledger_metrics: {
        total_entries_posted: 875,
        total_debits: '₱185,450.50',
        total_credits: '₱178,923.00',
        net_balance: '₱6,527.50',
        entry_types: {
            bills: 298,
            payments: 287,
            adjustments: 145,
            penalties: 89,
            refunds: 12,
            others: 44,
        },
    },

    rate_metrics: {
        active_rates: 3,
        total_rate_tiers: 9,
        residential_consumers: 245,
        commercial_consumers: 45,
        institutional_consumers: 12,
        average_residential_bill: '₱605.50',
        average_commercial_bill: '₱1,850.75',
        average_institutional_bill: '₱2,325.00',
    },

    revenue_metrics: {
        total_revenue: '₱178,923.00',
        revenue_by_category: {
            residential: {
                amount: '₱148,197.50',
                percent: 82.8,
            },
            commercial: {
                amount: '₱23,287.50',
                percent: 13.0,
            },
            institutional: {
                amount: '₱7,438.00',
                percent: 4.2,
            },
        },
        revenue_by_month: [
            { month: 'January', amount: 178923.00 },
        ],
    },

    delinquency_metrics: {
        total_delinquent_accounts: 8,
        delinquency_rate_percent: 2.68,
        total_arrears: '₱6,527.50',
        accounts_overdue_30_days: 3,
        accounts_overdue_60_days: 2,
        accounts_overdue_90_days: 2,
        accounts_overdue_180_days: 1,
    },

    operational_metrics: {
        meter_readings_completed: 298,
        meter_readings_completion_rate: 100,
        average_reading_per_day: 10.6,
        billing_process_time_hours: 2.5,
        peak_system_usage: '2024-01-30 08:30 AM',
        system_uptime_percent: 99.95,
    },
};

// Analytics Trends Data (Last 12 Months)
const analyticsTrendsData = {
    monthly_trends: [
        { month: 'February 2023', consumption: 6890, revenue: 168450, payments: 162340 },
        { month: 'March 2023', consumption: 7120, revenue: 175230, payments: 169450 },
        { month: 'April 2023', consumption: 6950, revenue: 170890, payments: 164560 },
        { month: 'May 2023', consumption: 7340, revenue: 182450, payments: 176890 },
        { month: 'June 2023', consumption: 7890, revenue: 198760, payments: 192340 },
        { month: 'July 2023', consumption: 8120, revenue: 205670, payments: 199880 },
        { month: 'August 2023', consumption: 7980, revenue: 199450, payments: 193230 },
        { month: 'September 2023', consumption: 7340, revenue: 181230, payments: 175680 },
        { month: 'October 2023', consumption: 6890, revenue: 168900, payments: 163450 },
        { month: 'November 2023', consumption: 7120, revenue: 176540, payments: 170890 },
        { month: 'December 2023', consumption: 7450, revenue: 183670, payments: 177880 },
        { month: 'January 2024', consumption: 7245, revenue: 178923, payments: 178923 },
    ],

    top_consumers: [
        { rank: 1, name: 'City Hospital', consumption: '250 m³', amount: '₱2,150.00' },
        { rank: 2, name: 'Angel Construction Inc.', consumption: '85 m³', amount: '₱1,250.00' },
        { rank: 3, name: 'Pedro Reyes', consumption: '68 m³', amount: '₱680.75' },
        { rank: 4, name: 'Juan Dela Cruz', consumption: '25 m³', amount: '₱739.20' },
        { rank: 5, name: 'Maria Santos', consumption: '22 m³', amount: '₱520.50' },
    ],

    collection_efficiency: {
        current_month: 96.48,
        previous_month: 94.23,
        three_months_avg: 95.24,
        six_months_avg: 94.87,
        year_to_date: 96.48,
        trend: 'up',
    },
};

// Dashboard Summary Widget Data
const dashboardSummaryData = {
    total_revenue_this_month: {
        amount: '₱178,923.00',
        change_percent: 0.45,
        trend: 'up',
        previous_month: '₱178,140.00',
    },
    collection_rate: {
        percent: 96.48,
        change_percent: 2.25,
        trend: 'up',
        target: 95,
    },
    active_consumers: {
        count: 298,
        change: 5,
        trend: 'up',
        growth_percent: 1.71,
    },
    total_consumption: {
        amount: '7,245 m³',
        change_percent: -2.05,
        trend: 'down',
        average_per_consumer: '23.97 m³',
    },
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics and metrics data loaded');
});
