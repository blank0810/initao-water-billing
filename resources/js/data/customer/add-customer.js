/**
 * PHASE 1: Customer Application Management
 * Auto-generates: Customer Code, Invoice, Workflow Status
 */

import { ALL_STATUSES, CHARGE_TYPES } from './workflow-config.js';

class AddCustomerManager {
    constructor() {
        this.uploadedFiles = [];
        this.formData = {};
        this.lastCustomerId = 1000; // Start from 1000
    }

    // ============================================
    // PHASE 1: AUTO-GENERATE CUSTOMER CODE
    // ============================================
    generateCustomerCode() {
        const year = new Date().getFullYear();
        this.lastCustomerId++;
        const id = String(this.lastCustomerId).padStart(4, '0');
        return `CUST-${year}-${id}`;
    }

    // ============================================
    // PHASE 1: AUTO-GENERATE INVOICE
    // ============================================
    generateInvoice(customerCode, registrationType) {
        const year = new Date().getFullYear();
        const invoiceId = String(Math.floor(Math.random() * 9000) + 1000);
        const invoiceNumber = `INV-${year}-${invoiceId}`;
        
        // Get charges based on registration type
        const charges = [
            { description: 'Connection Fee', amount: CHARGE_TYPES.CONNECTION_FEE.amount },
            { description: 'Service Deposit', amount: CHARGE_TYPES.SERVICE_DEPOSIT.amount },
            { description: 'Meter Installation', amount: CHARGE_TYPES.METER_INSTALLATION.amount },
            { description: 'Processing Fee', amount: CHARGE_TYPES.PROCESSING_FEE.amount }
        ];

        // Add inspection fee for commercial/industrial
        if (registrationType === 'COMMERCIAL' || registrationType === 'INDUSTRIAL') {
            charges.push({ 
                description: 'Inspection Fee', 
                amount: CHARGE_TYPES.INSPECTION_FEE.amount 
            });
        }

        const totalAmount = charges.reduce((sum, c) => sum + c.amount, 0);
        const today = new Date();
        const dueDate = new Date(today);
        dueDate.setDate(dueDate.getDate() + 10); // 10 days to pay

        return {
            invoice_number: invoiceNumber,
            customer_code: customerCode,
            invoice_date: today.toISOString().split('T')[0],
            due_date: dueDate.toISOString().split('T')[0],
            total_amount: totalAmount,
            payment_status: 'PENDING',
            items: charges,
            created_at: new Date().toISOString()
        };
    }

    // ============================================
    // PHASE 1: CREATE AUDIT LOG ENTRY
    // ============================================
    createAuditLog(customerCode, action, fromStatus, toStatus, notes = '') {
        return {
            customer_code: customerCode,
            action: action,
            from_status: fromStatus || null,
            to_status: toStatus,
            performed_by: 'System', // Will be replaced with actual user
            notes: notes,
            timestamp: new Date().toISOString()
        };
    }

    // ============================================
    // PHASE 1: DUPLICATE DETECTION
    // ============================================
    checkDuplicates(formData, existingCustomers = []) {
        const duplicates = [];

        // Check phone number
        const phoneExists = existingCustomers.find(c => c.phone === formData.phone);
        if (phoneExists) {
            duplicates.push({
                type: 'phone',
                message: `Phone number ${formData.phone} already registered`,
                customer: phoneExists
            });
        }

        // Check ID number
        const idExists = existingCustomers.find(c => 
            c.id_type === formData.id_type && c.id_number === formData.id_number
        );
        if (idExists) {
            duplicates.push({
                type: 'id',
                message: `${formData.id_type} ${formData.id_number} already registered`,
                customer: idExists
            });
        }

        // Check name + address combination
        const nameAddressExists = existingCustomers.find(c => 
            c.cust_first_name.toLowerCase() === formData.cust_first_name.toLowerCase() &&
            c.cust_last_name.toLowerCase() === formData.cust_last_name.toLowerCase() &&
            c.address.toLowerCase().includes(formData.barangay.toLowerCase())
        );
        if (nameAddressExists) {
            duplicates.push({
                type: 'name_address',
                message: `Customer with same name and address already exists`,
                customer: nameAddressExists
            });
        }

        return duplicates;
    }

    // ============================================
    // VALIDATION
    // ============================================
    validateForm(formData) {
        const required = [
            'cust_first_name', 'cust_last_name', 'phone', 
            'id_type', 'id_number', 'registration_type',
            'barangay', 'purok', 'landmark', 'area'
        ];

        for (const field of required) {
            if (!formData[field] || String(formData[field]).trim() === '') {
                return { 
                    valid: false, 
                    message: `${field.replace(/_/g, ' ').toUpperCase()} is required` 
                };
            }
        }

        // Phone validation (11 digits, starts with 09)
        const phoneRegex = /^09\d{9}$/;
        if (!phoneRegex.test(formData.phone)) {
            return { 
                valid: false, 
                message: 'Phone number must be 11 digits starting with 09' 
            };
        }

        return { valid: true };
    }

    // ============================================
    // PHASE 1: SUBMIT APPLICATION (ENHANCED)
    // ============================================
    submitApplication(formData, existingCustomers = []) {
        // Step 1: Validate form
        const validation = this.validateForm(formData);
        if (!validation.valid) {
            throw new Error(validation.message);
        }

        // Step 2: Check for duplicates
        const duplicates = this.checkDuplicates(formData, existingCustomers);
        if (duplicates.length > 0) {
            const warningMsg = duplicates.map(d => d.message).join('\n');
            if (!confirm(`⚠️ DUPLICATE DETECTED:\n\n${warningMsg}\n\nDo you want to continue anyway?`)) {
                throw new Error('Application cancelled due to duplicate detection');
            }
        }

        // Step 3: Generate customer code
        const customerCode = this.generateCustomerCode();

        // Step 4: Build full address
        const fullAddress = `${formData.purok}, ${formData.landmark}, Barangay ${formData.barangay}`;

        // Step 5: Create customer record
        const customer = {
            customer_code: customerCode,
            cust_id: this.lastCustomerId,
            cust_first_name: formData.cust_first_name.trim(),
            cust_middle_name: formData.cust_middle_name?.trim() || '',
            cust_last_name: formData.cust_last_name.trim(),
            phone: formData.phone.trim(),
            id_type: formData.id_type,
            id_number: formData.id_number.trim(),
            address: fullAddress,
            barangay: formData.barangay,
            purok: formData.purok,
            landmark: formData.landmark,
            area: formData.area,
            meterReader: formData.meterReader,
            readingSchedule: formData.readingSchedule,
            registration_type: formData.registration_type,
            workflow_status: 'NEW_APPLICATION',
            requirements_complete: false,
            documents_printed_at: null,
            payment_verified_at: null,
            approved_at: null,
            create_date: new Date().toISOString(),
            submitted_at: new Date().toISOString()
        };

        // Step 6: Generate invoice
        const invoice = this.generateInvoice(customerCode, formData.registration_type);

        // Step 7: Create audit log
        const auditLog = this.createAuditLog(
            customerCode,
            'Application Submitted',
            null,
            'NEW_APPLICATION',
            'Customer application form submitted'
        );

        // Step 8: Reset form
        this.uploadedFiles = [];
        this.formData = {};

        return {
            success: true,
            customer: customer,
            invoice: invoice,
            auditLog: auditLog,
            message: `✅ Application Submitted Successfully!\n\nCustomer Code: ${customerCode}\nInvoice: ${invoice.invoice_number}\nTotal Amount: ₱${invoice.total_amount.toLocaleString()}\nDue Date: ${invoice.due_date}\n\nStatus: NEW_APPLICATION`
        };
    }

    // ============================================
    // FILE HANDLING
    // ============================================
    handleFileUpload(files) {
        this.uploadedFiles = Array.from(files);
        return this.uploadedFiles;
    }

    removeFile(index) {
        this.uploadedFiles.splice(index, 1);
        return this.uploadedFiles;
    }

    resetForm() {
        this.uploadedFiles = [];
        this.formData = {};
    }
}

// ============================================
// INITIALIZE
// ============================================
let addCustomerManager;

document.addEventListener('DOMContentLoaded', function() {
    addCustomerManager = new AddCustomerManager();
    initializeForm();
});

function initializeForm() {
    const form = document.getElementById('customerApplicationForm');
    const areaSelect = document.getElementById('areaSelect');
    const meterReaderInput = document.getElementById('meterReaderInput');
    const readingScheduleInput = document.getElementById('readingScheduleInput');

    // Area configuration
    const areaConfig = {
        'Zone A': { meterReader: 'John Smith', readingDay: 5 },
        'Zone B': { meterReader: 'Jane Doe', readingDay: 10 },
        'Zone C': { meterReader: 'Mike Johnson', readingDay: 15 },
        'Zone D': { meterReader: 'Sarah Williams', readingDay: 20 },
        'Zone E': { meterReader: 'Tom Brown', readingDay: 25 }
    };

    // Auto-assign meter reader and schedule based on area
    if (areaSelect) {
        areaSelect.addEventListener('change', function(e) {
            const area = e.target.value;
            if (area && areaConfig[area]) {
                meterReaderInput.value = areaConfig[area].meterReader;
                const today = new Date();
                const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, areaConfig[area].readingDay);
                readingScheduleInput.value = nextMonth.toISOString().split('T')[0];
            } else {
                meterReaderInput.value = '';
                readingScheduleInput.value = '';
            }
        });
    }

    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit();
        });
    }
}

function handleFormSubmit() {
    const form = document.getElementById('customerApplicationForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    const submitButton = form.querySelector('button[type="submit"]');
    const originalHTML = submitButton.innerHTML;

    try {
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        submitButton.disabled = true;

        // Simulate API call
        setTimeout(() => {
            try {
                const result = addCustomerManager.submitApplication(data);
                
                // Log to console for debugging
                console.log('✅ Application Created:', result);
                console.log('📄 Customer:', result.customer);
                console.log('🧾 Invoice:', result.invoice);
                console.log('📝 Audit Log:', result.auditLog);
                
                // Show success modal
                showSuccessModal(result);
                
                // Reset form
                form.reset();
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
                
            } catch (error) {
                showToast(error.message, 'error');
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }
        }, 1000);
        
    } catch (error) {
        showToast(error.message, 'error');
        submitButton.innerHTML = originalHTML;
        submitButton.disabled = false;
    }
}

function showSuccessModal(result) {
    const modal = document.getElementById('successModal');
    document.getElementById('modalCustomerCode').textContent = result.customer.customer_code;
    document.getElementById('modalInvoiceNumber').textContent = result.invoice.invoice_number;
    document.getElementById('modalTotalAmount').textContent = '₱' + result.invoice.total_amount.toLocaleString();
    document.getElementById('modalDueDate').textContent = new Date(result.invoice.due_date).toLocaleDateString();
    modal.classList.remove('hidden');
    
    // Store for printing
    window.currentApplicationData = result;
}

window.printApplicationForm = function() {
    if (!window.currentApplicationData) return;
    
    const { customer, invoice } = window.currentApplicationData;
    
    // Use unified print system
    if (window.UnifiedPrintSystem) {
        const formattedCustomer = {
            CustomerName: `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`,
            id: customer.customer_code,
            customer_code: customer.customer_code,
            Email: customer.email || 'N/A',
            Phone: customer.phone,
            AreaCode: customer.address,
            address: customer.address,
            registration_type: customer.registration_type,
            DateApplied: customer.create_date,
            Status: customer.workflow_status,
            workflow_status: customer.workflow_status
        };
        window.UnifiedPrintSystem.printCustomerForm(formattedCustomer);
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `<i class="fas ${icons[type]}"></i><span>${message}</span>`;
    
    container.appendChild(toast);
    
    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export for global access
window.AddCustomerManager = AddCustomerManager;
window.addCustomerManager = addCustomerManager;

export default AddCustomerManager;
