// Application Configuration & Settings Data
const applicationSettingsData = {
    app_name: 'Initao Water Billing System',
    app_version: '2.0.0',
    app_status: 'Production',
    environment: 'Production',
    database_status: 'Connected',
    
    system_settings: {
        organization_name: 'Initao Water District',
        organization_code: 'IWD',
        organization_address: 'Initao, Misamis Oriental',
        phone: '+63 88 123 4567',
        email: 'info@initaowaterdistrict.gov.ph',
        website: 'www.initaowaterdistrict.gov.ph',
        timezone: 'UTC+8 (Philippines)',
    },

    billing_settings: {
        billing_cycle: 'Monthly',
        billing_day: 'End of Month',
        due_days: 10,
        late_penalty_percent: 2.5,
        grace_period_days: 3,
        minimum_bill: 50.00,
        currency: 'PHP (₱)',
    },

    rate_settings: {
        active_rate_period: 'BP-2024-01 (January 2024)',
        rate_update_frequency: 'Annually',
        last_rate_update: '2024-01-01',
        tiered_rate_enabled: true,
        rate_tiers: 3,
    },

    ledger_settings: {
        ledger_type: 'Double-Entry Accounting',
        immutable_entries: true,
        auto_posting: true,
        audit_trail_enabled: true,
    },

    payment_settings: {
        payment_methods: ['Bank Transfer', 'Over-the-Counter', 'Check'],
        payment_reconciliation: 'Daily',
        auto_reconcile: true,
        payment_timeout_days: 30,
    },

    security_settings: {
        two_factor_auth_enabled: true,
        session_timeout_minutes: 30,
        password_expiry_days: 90,
        login_attempts_limit: 5,
        ip_whitelist_enabled: false,
    },

    backup_settings: {
        auto_backup_enabled: true,
        backup_frequency: 'Daily',
        backup_time: '02:00 AM',
        retention_days: 30,
        last_backup: '2024-01-20 02:15:00',
    },

    notification_settings: {
        bill_due_notifications: true,
        payment_confirmation_email: true,
        system_alerts_enabled: true,
        email_provider: 'SMTP',
        sms_enabled: true,
    },

    audit_log_settings: {
        log_retention_days: 365,
        log_level: 'Info',
        include_data_changes: true,
        include_user_activities: true,
    },

    feature_flags: {
        new_ui_enabled: true,
        bulk_operations_enabled: true,
        advanced_analytics_enabled: true,
        api_access_enabled: true,
        mobile_app_enabled: false,
        offline_mode_enabled: false,
    }
};

// Application Modules Data
const applicationModulesData = [
    {
        id: 'MOD-001',
        module_name: 'Billing Module',
        module_code: 'BILLING',
        status: 'Active',
        version: '2.0.0',
        enabled: true,
        features_count: 12,
        users_count: 45,
    },
    {
        id: 'MOD-002',
        module_name: 'Ledger Module',
        module_code: 'LEDGER',
        status: 'Active',
        version: '2.0.0',
        enabled: true,
        features_count: 8,
        users_count: 20,
    },
    {
        id: 'MOD-003',
        module_name: 'Rate Module',
        module_code: 'RATE',
        status: 'Active',
        version: '2.0.0',
        enabled: true,
        features_count: 6,
        users_count: 15,
    },
    {
        id: 'MOD-004',
        module_name: 'Payment Module',
        module_code: 'PAYMENT',
        status: 'Active',
        version: '1.5.0',
        enabled: true,
        features_count: 10,
        users_count: 30,
    },
    {
        id: 'MOD-005',
        module_name: 'Customer Module',
        module_code: 'CUSTOMER',
        status: 'Active',
        version: '2.0.0',
        enabled: true,
        features_count: 9,
        users_count: 25,
    },
];

// Application Health Check Data
const applicationHealthData = {
    status: 'Healthy',
    uptime_percentage: 99.95,
    last_check: new Date().toISOString(),
    checks: {
        database: {
            status: 'Connected',
            response_time: '5ms',
            last_check: new Date().toISOString(),
        },
        api: {
            status: 'Running',
            response_time: '45ms',
            last_check: new Date().toISOString(),
        },
        storage: {
            status: 'Available',
            used_gb: 45.2,
            total_gb: 500,
            usage_percent: 9.04,
            last_check: new Date().toISOString(),
        },
        cache: {
            status: 'Connected',
            memory_used_mb: 256,
            response_time: '2ms',
            last_check: new Date().toISOString(),
        },
        email: {
            status: 'Configured',
            last_sent: new Date().toISOString(),
            pending_queue: 0,
        },
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application settings and configuration data loaded');
});
