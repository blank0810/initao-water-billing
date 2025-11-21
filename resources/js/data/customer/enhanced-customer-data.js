/**
 * ========================================
 * ENHANCED CUSTOMER DATA STRUCTURE
 * With 3-Phase Workflow Support
 * ========================================
 */

import { ALL_STATUSES, calculateProgress } from './workflow-config.js';

// ============================================
// ENHANCED CUSTOMER DATA
// ============================================
export const enhancedCustomerData = [
    // PHASE 1: APPLICATION & DOCUMENTATION
    {
        cust_id: 1,
        customer_code: 'CUST-2024-001',
        create_date: '2024-01-20 09:00:00',
        cust_last_name: 'Dela Cruz',
        cust_first_name: 'Juan',
        cust_middle_name: 'Santos',
        email: 'juan.delacruz@email.com',
        phone: '09171234567',
        address: 'Purok 1, Poblacion, Barangay Central',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'National ID',
        id_number: 'NID-1234-5678-9012',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 2,
        customer_code: 'CUST-2024-002',
        create_date: '2024-01-19 14:30:00',
        cust_last_name: 'Santos',
        cust_first_name: 'Maria',
        cust_middle_name: 'Garcia',
        email: 'maria.santos@email.com',
        phone: '09181234567',
        address: 'Purok 2, San Jose, Barangay East',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        readingSchedule: '2024-02-10',
        id_type: 'Driver\'s License',
        id_number: 'DL-A12-34-567890',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'DOCS_PRINTED',
        requirements_complete: 0,
        documents_printed_at: '2024-01-20 10:15:00',
        documents_printed_count: 1,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 3,
        customer_code: 'CUST-2024-003',
        create_date: '2024-01-18 11:00:00',
        cust_last_name: 'Garcia',
        cust_first_name: 'Pedro',
        cust_middle_name: 'Lopez',
        email: 'pedro.garcia@email.com',
        phone: '09191234567',
        address: 'Purok 3, Maligaya, Barangay West',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        readingSchedule: '2024-02-15',
        id_type: 'Passport',
        id_number: 'P1234567',
        registration_type: 'COMMERCIAL',
        workflow_status: 'REQUIREMENTS_SUBMITTED',
        requirements_complete: 0,
        documents_printed_at: '2024-01-19 09:00:00',
        documents_printed_count: 2,
        requirements_submitted_at: '2024-01-20 14:00:00',
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 4,
        customer_code: 'CUST-2024-004',
        create_date: '2024-01-17 08:30:00',
        cust_last_name: 'Rodriguez',
        cust_first_name: 'Ana',
        cust_middle_name: 'Martinez',
        email: 'ana.rodriguez@email.com',
        phone: '09201234567',
        address: 'Purok 4, San Roque, Barangay North',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'National ID',
        id_number: 'NID-9876-5432-1098',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'REQUIREMENTS_VERIFIED',
        requirements_complete: 1,
        documents_printed_at: '2024-01-18 10:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-19 11:00:00',
        requirements_verified_at: '2024-01-20 09:30:00',
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },

    // PHASE 2: BILLING & PAYMENT
    {
        cust_id: 5,
        customer_code: 'CUST-2024-005',
        create_date: '2024-01-16 13:00:00',
        cust_last_name: 'Lopez',
        cust_first_name: 'Carlos',
        cust_middle_name: 'Reyes',
        email: 'carlos.lopez@email.com',
        phone: '09211234567',
        address: 'Purok 5, Dampol, Barangay South',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        readingSchedule: '2024-02-10',
        id_type: 'Driver\'s License',
        id_number: 'DL-B98-76-543210',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'READY_FOR_PAYMENT',
        requirements_complete: 1,
        documents_printed_at: '2024-01-17 09:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-18 10:00:00',
        requirements_verified_at: '2024-01-19 14:00:00',
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 6,
        customer_code: 'CUST-2024-006',
        create_date: '2024-01-15 10:00:00',
        cust_last_name: 'Chen',
        cust_first_name: 'Lisa',
        cust_middle_name: 'Wang',
        email: 'lisa.chen@email.com',
        phone: '09221234567',
        address: 'Purok 6, Central Plaza, Barangay Centro',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        readingSchedule: '2024-02-15',
        id_type: 'National ID',
        id_number: 'NID-5555-6666-7777',
        registration_type: 'COMMERCIAL',
        workflow_status: 'PAYMENT_PENDING',
        requirements_complete: 1,
        documents_printed_at: '2024-01-16 11:00:00',
        documents_printed_count: 2,
        requirements_submitted_at: '2024-01-17 09:00:00',
        requirements_verified_at: '2024-01-18 10:00:00',
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 7,
        customer_code: 'CUST-2024-007',
        create_date: '2024-01-14 15:00:00',
        cust_last_name: 'Johnson',
        cust_first_name: 'Mark',
        cust_middle_name: 'David',
        email: 'mark.johnson@email.com',
        phone: '09231234567',
        address: 'Purok 7, Riverside, Barangay East',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'Passport',
        id_number: 'P9876543',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'PAYMENT_VERIFIED',
        requirements_complete: 1,
        documents_printed_at: '2024-01-15 08:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-16 10:00:00',
        requirements_verified_at: '2024-01-17 11:00:00',
        payment_completed_at: '2024-01-20 14:30:00',
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },

    // PHASE 3: APPROVAL & CONNECTION
    {
        cust_id: 8,
        customer_code: 'CUST-2024-008',
        create_date: '2024-01-13 09:00:00',
        cust_last_name: 'Martinez',
        cust_first_name: 'Sofia',
        cust_middle_name: 'Cruz',
        email: 'sofia.martinez@email.com',
        phone: '09241234567',
        address: 'Purok 8, Hillside, Barangay West',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        readingSchedule: '2024-02-10',
        id_type: 'National ID',
        id_number: 'NID-1111-2222-3333',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'READY_FOR_APPROVAL',
        requirements_complete: 1,
        documents_printed_at: '2024-01-14 10:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-15 11:00:00',
        requirements_verified_at: '2024-01-16 09:00:00',
        payment_completed_at: '2024-01-19 15:00:00',
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 9,
        customer_code: 'CUST-2024-009',
        create_date: '2024-01-12 11:30:00',
        cust_last_name: 'Wilson',
        cust_first_name: 'David',
        cust_middle_name: 'Lee',
        email: 'david.wilson@email.com',
        phone: '09251234567',
        address: 'Purok 9, Lakeside, Barangay North',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        readingSchedule: '2024-02-15',
        id_type: 'Driver\'s License',
        id_number: 'DL-C11-22-334455',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'APPROVED',
        requirements_complete: 1,
        documents_printed_at: '2024-01-13 09:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-14 10:00:00',
        requirements_verified_at: '2024-01-15 11:00:00',
        payment_completed_at: '2024-01-18 14:00:00',
        approved_at: '2024-01-20 10:00:00',
        connected_at: null,
        account_no: 'ACC-2024-5001',
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 10,
        customer_code: 'CUST-2024-010',
        create_date: '2024-01-11 14:00:00',
        cust_last_name: 'Brown',
        cust_first_name: 'Emma',
        cust_middle_name: 'Rose',
        email: 'emma.brown@email.com',
        phone: '09261234567',
        address: 'Purok 10, Mountain View, Barangay South',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'National ID',
        id_number: 'NID-4444-5555-6666',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'CONNECTION_SCHEDULED',
        requirements_complete: 1,
        documents_printed_at: '2024-01-12 10:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-13 11:00:00',
        requirements_verified_at: '2024-01-14 09:00:00',
        payment_completed_at: '2024-01-17 15:00:00',
        approved_at: '2024-01-19 11:00:00',
        connected_at: null,
        account_no: 'ACC-2024-5002',
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 11,
        customer_code: 'CUST-2024-011',
        create_date: '2024-01-10 08:00:00',
        cust_last_name: 'Taylor',
        cust_first_name: 'Robert',
        cust_middle_name: 'James',
        email: 'robert.taylor@email.com',
        phone: '09271234567',
        address: 'Purok 11, Seaside, Barangay Centro',
        area: 'Zone D',
        meterReader: 'Sarah Williams',
        readingSchedule: '2024-02-20',
        id_type: 'Passport',
        id_number: 'P5555555',
        registration_type: 'COMMERCIAL',
        workflow_status: 'METER_ASSIGNED',
        requirements_complete: 1,
        documents_printed_at: '2024-01-11 09:00:00',
        documents_printed_count: 2,
        requirements_submitted_at: '2024-01-12 10:00:00',
        requirements_verified_at: '2024-01-13 11:00:00',
        payment_completed_at: '2024-01-16 14:00:00',
        approved_at: '2024-01-18 10:00:00',
        connected_at: null,
        account_no: 'ACC-2024-5003',
        meter_no: 'MTR-1001',
        stat_id: 1
    },
    {
        cust_id: 12,
        customer_code: 'CUST-2024-012',
        create_date: '2024-01-09 10:30:00',
        cust_last_name: 'Davis',
        cust_first_name: 'Jennifer',
        cust_middle_name: 'Anne',
        email: 'jennifer.davis@email.com',
        phone: '09281234567',
        address: 'Purok 12, Valley View, Barangay East',
        area: 'Zone E',
        meterReader: 'Tom Brown',
        readingSchedule: '2024-02-25',
        id_type: 'National ID',
        id_number: 'NID-7777-8888-9999',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'ACTIVE_CONSUMER',
        requirements_complete: 1,
        documents_printed_at: '2024-01-10 09:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: '2024-01-11 10:00:00',
        requirements_verified_at: '2024-01-12 11:00:00',
        payment_completed_at: '2024-01-15 14:00:00',
        approved_at: '2024-01-17 10:00:00',
        connected_at: '2024-01-20 09:00:00',
        account_no: 'ACC-2024-5004',
        meter_no: 'MTR-1002',
        stat_id: 1
    },

    // SPECIAL CASES
    {
        cust_id: 13,
        customer_code: 'CUST-2024-013',
        create_date: '2024-01-08 13:00:00',
        cust_last_name: 'Anderson',
        cust_first_name: 'Michael',
        cust_middle_name: 'Paul',
        email: 'michael.anderson@email.com',
        phone: '09291234567',
        address: 'Purok 13, Garden Hills, Barangay West',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        readingSchedule: '2024-02-10',
        id_type: 'Driver\'s License',
        id_number: 'DL-D55-66-778899',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'ON_HOLD',
        requirements_complete: 0,
        documents_printed_at: '2024-01-09 10:00:00',
        documents_printed_count: 1,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 2
    },

    // MORE NEW APPLICATIONS
    {
        cust_id: 14,
        customer_code: 'CUST-2024-014',
        create_date: '2024-01-21 08:00:00',
        cust_last_name: 'Reyes',
        cust_first_name: 'Isabella',
        cust_middle_name: 'Marie',
        email: 'isabella.reyes@email.com',
        phone: '09301234567',
        address: 'Purok 1, Riverside, Barangay North',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'National ID',
        id_number: 'NID-2222-3333-4444',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 15,
        customer_code: 'CUST-2024-015',
        create_date: '2024-01-21 09:30:00',
        cust_last_name: 'Fernandez',
        cust_first_name: 'Gabriel',
        cust_middle_name: 'Luis',
        email: 'gabriel.fernandez@email.com',
        phone: '09311234567',
        address: 'Purok 2, Sunset View, Barangay South',
        area: 'Zone B',
        meterReader: 'Jane Doe',
        readingSchedule: '2024-02-10',
        id_type: 'SSS ID',
        id_number: 'SSS-12-3456789-0',
        registration_type: 'COMMERCIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 16,
        customer_code: 'CUST-2024-016',
        create_date: '2024-01-21 10:15:00',
        cust_last_name: 'Villanueva',
        cust_first_name: 'Sophia',
        cust_middle_name: 'Grace',
        email: 'sophia.villanueva@email.com',
        phone: '09321234567',
        address: 'Purok 3, Palm Street, Barangay East',
        area: 'Zone C',
        meterReader: 'Mike Johnson',
        readingSchedule: '2024-02-15',
        id_type: 'PhilHealth ID',
        id_number: 'PH-1234567890123',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 17,
        customer_code: 'CUST-2024-017',
        create_date: '2024-01-21 11:00:00',
        cust_last_name: 'Ramos',
        cust_first_name: 'Daniel',
        cust_middle_name: 'Jose',
        email: 'daniel.ramos@email.com',
        phone: '09331234567',
        address: 'Purok 4, Mango Avenue, Barangay Centro',
        area: 'Zone D',
        meterReader: 'Sarah Williams',
        readingSchedule: '2024-02-20',
        id_type: 'Driver\'s License',
        id_number: 'DL-E12-34-567890',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 18,
        customer_code: 'CUST-2024-018',
        create_date: '2024-01-21 13:30:00',
        cust_last_name: 'Torres',
        cust_first_name: 'Olivia',
        cust_middle_name: 'Rose',
        email: 'olivia.torres@email.com',
        phone: '09341234567',
        address: 'Purok 5, Coconut Lane, Barangay West',
        area: 'Zone E',
        meterReader: 'Tom Brown',
        readingSchedule: '2024-02-25',
        id_type: 'Passport',
        id_number: 'P7654321',
        registration_type: 'COMMERCIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    },
    {
        cust_id: 19,
        customer_code: 'CUST-2024-019',
        create_date: '2024-01-21 14:45:00',
        cust_last_name: 'Mendoza',
        cust_first_name: 'Lucas',
        cust_middle_name: 'Antonio',
        email: 'lucas.mendoza@email.com',
        phone: '09351234567',
        address: 'Purok 6, Bamboo Street, Barangay North',
        area: 'Zone A',
        meterReader: 'John Smith',
        readingSchedule: '2024-02-05',
        id_type: 'National ID',
        id_number: 'NID-8888-9999-0000',
        registration_type: 'RESIDENTIAL',
        workflow_status: 'NEW_APPLICATION',
        requirements_complete: 0,
        documents_printed_at: null,
        documents_printed_count: 0,
        requirements_submitted_at: null,
        requirements_verified_at: null,
        payment_completed_at: null,
        approved_at: null,
        connected_at: null,
        account_no: null,
        meter_no: null,
        stat_id: 1
    }
];

// ============================================
// CUSTOMER DOCUMENTS
// ============================================
export const customerDocuments = [
    { document_id: 1, cust_id: 3, document_type: 'VALID_ID', file_name: 'passport.pdf', file_path: '/uploads/docs/passport.pdf', uploaded_at: '2024-01-20 13:00:00', verified: 1, verified_by: 1, verified_at: '2024-01-20 14:00:00' },
    { document_id: 2, cust_id: 3, document_type: 'BARANGAY_CLEARANCE', file_name: 'brgy_clearance.pdf', file_path: '/uploads/docs/brgy_clearance.pdf', uploaded_at: '2024-01-20 13:05:00', verified: 1, verified_by: 1, verified_at: '2024-01-20 14:00:00' },
    { document_id: 3, cust_id: 3, document_type: 'BUSINESS_PERMIT', file_name: 'business_permit.pdf', file_path: '/uploads/docs/business_permit.pdf', uploaded_at: '2024-01-20 13:10:00', verified: 1, verified_by: 1, verified_at: '2024-01-20 14:00:00' },
    { document_id: 4, cust_id: 4, document_type: 'VALID_ID', file_name: 'national_id.pdf', file_path: '/uploads/docs/national_id.pdf', uploaded_at: '2024-01-19 10:00:00', verified: 1, verified_by: 1, verified_at: '2024-01-20 09:30:00' },
    { document_id: 5, cust_id: 4, document_type: 'PROOF_OF_RESIDENCE', file_name: 'proof_residence.pdf', file_path: '/uploads/docs/proof_residence.pdf', uploaded_at: '2024-01-19 10:05:00', verified: 1, verified_by: 1, verified_at: '2024-01-20 09:30:00' }
];

// ============================================
// WORKFLOW HISTORY
// ============================================
export const workflowHistory = [
    { history_id: 1, cust_id: 2, from_status: 'NEW_APPLICATION', to_status: 'DOCS_PRINTED', action: 'Documents printed', performed_by: 1, performed_at: '2024-01-20 10:15:00', notes: 'Requirements form printed', ip_address: '192.168.1.100' },
    { history_id: 2, cust_id: 3, from_status: 'DOCS_PRINTED', to_status: 'REQUIREMENTS_SUBMITTED', action: 'Requirements submitted', performed_by: 2, performed_at: '2024-01-20 14:00:00', notes: 'All documents submitted', ip_address: '192.168.1.101' },
    { history_id: 3, cust_id: 4, from_status: 'REQUIREMENTS_SUBMITTED', to_status: 'REQUIREMENTS_VERIFIED', action: 'Documents verified', performed_by: 1, performed_at: '2024-01-20 09:30:00', notes: 'All documents complete and verified', ip_address: '192.168.1.100' },
    { history_id: 4, cust_id: 7, from_status: 'PAYMENT_PENDING', to_status: 'PAYMENT_VERIFIED', action: 'Payment verified', performed_by: 1, performed_at: '2024-01-20 14:30:00', notes: 'Payment of ₱3,500 verified', ip_address: '192.168.1.100' },
    { history_id: 5, cust_id: 9, from_status: 'READY_FOR_APPROVAL', to_status: 'APPROVED', action: 'Application approved', performed_by: 3, performed_at: '2024-01-20 10:00:00', notes: 'Approved by supervisor', ip_address: '192.168.1.102' }
];

// ============================================
// INVOICES
// ============================================
export const invoices = [
    { invoice_id: 1, invoice_number: 'INV-2024-1001', cust_id: 5, customer_code: 'CUST-2024-005', invoice_date: '2024-01-19', due_date: '2024-01-26', amount: 3500, invoice_type: 'REGISTRATION', items: JSON.stringify([{description: 'Connection Fee', amount: 1500}, {description: 'Service Deposit', amount: 1000}, {description: 'Meter Installation', amount: 800}, {description: 'Processing Fee', amount: 200}]), payment_status: 'PENDING', paid_amount: 0, paid_at: null, created_by: 1 },
    { invoice_id: 2, invoice_number: 'INV-2024-1002', cust_id: 6, customer_code: 'CUST-2024-006', invoice_date: '2024-01-18', due_date: '2024-01-25', amount: 3800, invoice_type: 'REGISTRATION', items: JSON.stringify([{description: 'Connection Fee', amount: 1500}, {description: 'Service Deposit', amount: 1000}, {description: 'Meter Installation', amount: 800}, {description: 'Processing Fee', amount: 200}, {description: 'Inspection Fee', amount: 300}]), payment_status: 'PENDING', paid_amount: 0, paid_at: null, created_by: 1 },
    { invoice_id: 3, invoice_number: 'INV-2024-1003', cust_id: 7, customer_code: 'CUST-2024-007', invoice_date: '2024-01-17', due_date: '2024-01-24', amount: 3500, invoice_type: 'REGISTRATION', items: JSON.stringify([{description: 'Connection Fee', amount: 1500}, {description: 'Service Deposit', amount: 1000}, {description: 'Meter Installation', amount: 800}, {description: 'Processing Fee', amount: 200}]), payment_status: 'PAID', paid_amount: 3500, paid_at: '2024-01-20 14:30:00', created_by: 1 },
    { invoice_id: 4, invoice_number: 'INV-2024-1004', cust_id: 8, customer_code: 'CUST-2024-008', invoice_date: '2024-01-16', due_date: '2024-01-23', amount: 3500, invoice_type: 'REGISTRATION', items: JSON.stringify([{description: 'Connection Fee', amount: 1500}, {description: 'Service Deposit', amount: 1000}, {description: 'Meter Installation', amount: 800}, {description: 'Processing Fee', amount: 200}]), payment_status: 'PAID', paid_amount: 3500, paid_at: '2024-01-19 15:00:00', created_by: 1 }
];

// ============================================
// PAYMENT TRANSACTIONS
// ============================================
export const paymentTransactions = [
    { payment_id: 1, invoice_id: 3, cust_id: 7, receipt_no: 'RCP-2024-0001', payment_date: '2024-01-20', payment_method: 'CASH', amount_received: 3500, change_amount: 0, received_by: 1, payment_verified: 1, stat_id: 1 },
    { payment_id: 2, invoice_id: 4, cust_id: 8, receipt_no: 'RCP-2024-0002', payment_date: '2024-01-19', payment_method: 'GCASH', amount_received: 3500, change_amount: 0, received_by: 1, payment_verified: 1, stat_id: 1 }
];

// ============================================
// APPROVAL QUEUE
// ============================================
export const approvalQueue = [
    { approval_id: 1, cust_id: 8, customer_code: 'CUST-2024-008', invoice_id: 4, submitted_at: '2024-01-19 15:30:00', approval_status: 'PENDING', approved_by: null, approved_at: null, decline_reason: null, priority: 'MEDIUM', required_actions: JSON.stringify(['Verify payment', 'Check documents']) }
];

// ============================================
// SERVICE CONNECTIONS
// ============================================
export const serviceConnections = [
    { connection_id: 1, cust_id: 9, account_no: 'ACC-2024-5001', approved_by: 3, approved_at: '2024-01-20 10:00:00', connection_type: 'NEW', requested_date: '2024-01-20', scheduled_date: '2024-01-22', completed_date: null, installation_crew: 'Team A', connection_status: 'SCHEDULED', meter_required: 1 },
    { connection_id: 2, cust_id: 10, account_no: 'ACC-2024-5002', approved_by: 3, approved_at: '2024-01-19 11:00:00', connection_type: 'NEW', requested_date: '2024-01-19', scheduled_date: '2024-01-21', completed_date: null, installation_crew: 'Team B', connection_status: 'SCHEDULED', meter_required: 1 },
    { connection_id: 3, cust_id: 11, account_no: 'ACC-2024-5003', approved_by: 3, approved_at: '2024-01-18 10:00:00', connection_type: 'NEW', requested_date: '2024-01-18', scheduled_date: '2024-01-20', completed_date: null, installation_crew: 'Team A', connection_status: 'IN_PROGRESS', meter_required: 1 },
    { connection_id: 4, cust_id: 12, account_no: 'ACC-2024-5004', approved_by: 3, approved_at: '2024-01-17 10:00:00', connection_type: 'NEW', requested_date: '2024-01-17', scheduled_date: '2024-01-19', completed_date: '2024-01-20', installation_crew: 'Team B', connection_status: 'COMPLETED', meter_required: 1 }
];

// ============================================
// METER ASSIGNMENTS
// ============================================
export const meterAssignments = [
    { assignment_id: 1, connection_id: 3, meter_id: 1001, assigned_by: 1, assigned_at: '2024-01-20 08:00:00', installed_by: null, installed_at: null, initial_reading: null, installation_notes: null },
    { assignment_id: 2, connection_id: 4, meter_id: 1002, assigned_by: 1, assigned_at: '2024-01-19 08:00:00', installed_by: 4, installed_at: '2024-01-20 09:00:00', initial_reading: 0, installation_notes: 'Installation completed successfully' }
];

// ============================================
// HELPER FUNCTIONS
// ============================================
export function getCustomersByPhase(phase) {
    return enhancedCustomerData.filter(customer => {
        const statusConfig = ALL_STATUSES[customer.workflow_status];
        return statusConfig && statusConfig.phase === phase;
    });
}

export function getCustomersByStatus(status) {
    return enhancedCustomerData.filter(customer => customer.workflow_status === status);
}

export function getCustomerProgress(customerCode) {
    const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
    if (!customer) return 0;
    return calculateProgress(customer.workflow_status);
}

export function getCustomerDocuments(custId) {
    return customerDocuments.filter(doc => doc.cust_id === custId);
}

export function getCustomerHistory(custId) {
    return workflowHistory.filter(h => h.cust_id === custId).sort((a, b) => 
        new Date(b.performed_at) - new Date(a.performed_at)
    );
}

export function getCustomerInvoice(custId) {
    return invoices.find(inv => inv.cust_id === custId);
}

// Make available globally
if (typeof window !== 'undefined') {
    window.enhancedCustomerData = enhancedCustomerData;
    window.customerDocuments = customerDocuments;
    window.workflowHistory = workflowHistory;
    window.invoices = invoices;
    window.paymentTransactions = paymentTransactions;
    window.approvalQueue = approvalQueue;
    window.serviceConnections = serviceConnections;
    window.meterAssignments = meterAssignments;
    
    window.CustomerDataHelpers = {
        getCustomersByPhase,
        getCustomersByStatus,
        getCustomerProgress,
        getCustomerDocuments,
        getCustomerHistory,
        getCustomerInvoice
    };
}
