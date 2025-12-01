// Payment Management System
class PaymentManager {
    constructor() {
        this.customers = [];
        this.searchResults = [];
        this.selectedCustomer = null;
        this.currentInvoice = null;
        this.isInitialized = false;
    }

    loadCustomerData(customerData) {
        this.customers = customerData || [];
        console.log('PaymentManager: Loaded', this.customers.length, 'customers');
    }

    searchCustomers(query) {
        if (!query || query.length < 2) {
            this.searchResults = [];
            return [];
        }

        this.searchResults = this.customers.filter(c => {
            const fullName = `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase();
            const meterNo = c.meter_no || '';
            const accountNo = c.account_no || '';
            return fullName.includes(query) ||
                   c.address.toLowerCase().includes(query) ||
                   meterNo.toLowerCase().includes(query) ||
                   accountNo.toLowerCase().includes(query) ||
                   c.customer_code.toLowerCase().includes(query);
        });

        return this.searchResults.slice(0, 10);
    }

    selectCustomer(customerCode) {
        this.selectedCustomer = this.customers.find(c => c.customer_code === customerCode);
        if (this.selectedCustomer) {
            this.currentInvoice = this.generateInvoice(this.selectedCustomer);
            console.log('PaymentManager: Selected customer', customerCode);
        }
        return this.selectedCustomer;
    }

    generateInvoice(customer) {
        const invoiceId = `INV-${new Date().getFullYear()}-${Math.floor(Math.random() * 9000) + 1000}`;
        const charges = [
            { description: 'Connection Fee', amount: 1500 },
            { description: 'Service Deposit', amount: 1000 },
            { description: 'Meter Installation', amount: 800 },
            { description: 'Processing Fee', amount: 200 }
        ];
        const totalAmount = charges.reduce((sum, item) => sum + item.amount, 0);

        return {
            invoice_id: invoiceId,
            customer_code: customer.customer_code,
            invoice_date: new Date().toISOString(),
            due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(),
            items: charges,
            total_amount: totalAmount,
            paid_amount: 0,
            payment_status: 'PENDING',
            paid_at: null
        };
    }

    processPayment(paymentData) {
        if (!this.selectedCustomer || !this.currentInvoice) {
            throw new Error('No customer or invoice selected');
        }

        const { amountReceived, paymentMethod, referenceNumber } = paymentData;

        if (amountReceived < this.currentInvoice.total_amount) {
            throw new Error(`Insufficient amount. Required: ₱${this.currentInvoice.total_amount.toLocaleString()}`);
        }

        // Create payment transaction
        const transaction = {
            transaction_id: `TXN-${Date.now()}`,
            invoice_id: this.currentInvoice.invoice_id,
            customer_code: this.selectedCustomer.customer_code,
            payment_method: paymentMethod,
            amount_received: amountReceived,
            reference_number: referenceNumber,
            transaction_date: new Date().toISOString()
        };

        // Update invoice
        this.currentInvoice.payment_status = 'PAID';
        this.currentInvoice.paid_amount = this.currentInvoice.total_amount;
        this.currentInvoice.paid_at = new Date().toISOString();

        // Update customer workflow in the global data
        if (window.customerAllData) {
            const globalCustomer = window.customerAllData.find(c => c.customer_code === this.selectedCustomer.customer_code);
            if (globalCustomer) {
                globalCustomer.workflow_status = 'PAYMENT_COMPLETED';
                globalCustomer.payment_completed_at = new Date().toISOString();
            }
        }

        return {
            transaction,
            invoice: this.currentInvoice,
            customer: this.selectedCustomer
        };
    }

    getSelectedCustomer() {
        return this.selectedCustomer;
    }

    getCurrentInvoice() {
        return this.currentInvoice;
    }
}

// Global payment manager instance
let paymentManager;
let paymentInitialized = false;

// Initialize payment system
function initializePaymentSystem() {
    if (paymentInitialized) return;
    
    console.log('PaymentSystem: Initializing...');
    paymentManager = new PaymentManager();
    
    // Wait for customer data to be available
    function waitForCustomerData() {
        if (window.customerAllData && window.customerAllData.length > 0) {
            console.log('PaymentSystem: Customer data found, loading...');
            paymentManager.loadCustomerData(window.customerAllData);
            
            // Check sessionStorage first
            const storedCustomer = sessionStorage.getItem('selectedCustomer');
            const storedCode = sessionStorage.getItem('paymentCustomerCode');
            
            if (storedCustomer && storedCode) {
                console.log('PaymentSystem: Loading from sessionStorage:', storedCode);
                loadCustomerFromCode(storedCode);
            } else {
                // Check if we have a customer code in the URL
                const customerCode = extractCustomerCodeFromURL();
                console.log('PaymentSystem: Extracted customer code:', customerCode);
                
                if (customerCode) {
                    loadCustomerFromCode(customerCode);
                } else {
                    showSearchPanel();
                }
            }
            
            paymentInitialized = true;
        } else {
            console.log('PaymentSystem: Waiting for customer data...');
            setTimeout(waitForCustomerData, 100);
        }
    }
    
    waitForCustomerData();
}

// Extract customer code from URL
function extractCustomerCodeFromURL() {
    const path = window.location.pathname;
    console.log('PaymentSystem: Current path:', path);
    
    // Handle /customer/payment/CUST-2024-001 format
    if (path.includes('/customer/payment/')) {
        const parts = path.split('/customer/payment/');
        if (parts.length > 1 && parts[1] !== 'payment-management') {
            return decodeURIComponent(parts[1]);
        }
    }
    
    return null;
}

// Load customer from code with fallbacks
function loadCustomerFromCode(customerCode) {
    console.log('PaymentSystem: Loading customer:', customerCode);
    
    let customer = paymentManager.selectCustomer(customerCode);
    
    // Fallback 1: Try sessionStorage
    if (!customer) {
        console.log('PaymentSystem: Customer not found in data, checking sessionStorage...');
        const storedCustomer = sessionStorage.getItem('selectedCustomer');
        if (storedCustomer) {
            try {
                customer = JSON.parse(storedCustomer);
                paymentManager.selectedCustomer = customer;
                paymentManager.currentInvoice = paymentManager.generateInvoice(customer);
                console.log('PaymentSystem: Customer loaded from sessionStorage');
            } catch (e) {
                console.error('PaymentSystem: Error parsing stored customer:', e);
            }
        }
    }
    
    if (customer) {
        loadCustomerPayment(customer);
        // Clear sessionStorage after successful load
        sessionStorage.removeItem('selectedCustomer');
        sessionStorage.removeItem('paymentCustomerCode');
    } else {
        console.error('PaymentSystem: Customer not found:', customerCode);
        showAlert('Customer not found: ' + customerCode, 'error');
        setTimeout(() => {
            window.location.href = '/customer/list';
        }, 2000);
    }
}

// Show search panel
function showSearchPanel() {
    console.log('PaymentSystem: Showing search panel');
    
    const searchPanel = document.getElementById('searchPanel');
    const paymentContainer = document.getElementById('paymentContainer');
    
    if (searchPanel && paymentContainer) {
        searchPanel.style.display = 'block';
        paymentContainer.style.display = 'none';
        
        setupSearchFunctionality();
    }
}

// Setup search functionality
function setupSearchFunctionality() {
    const searchInput = document.getElementById('customerSearchInput');
    if (!searchInput) return;
    
    // Remove existing listeners
    const newSearchInput = searchInput.cloneNode(true);
    searchInput.parentNode.replaceChild(newSearchInput, searchInput);
    
    // Add new listener
    newSearchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        
        if (query.length < 2) {
            document.getElementById('searchResults').classList.add('hidden');
            return;
        }
        
        const results = paymentManager.searchCustomers(query);
        displaySearchResults(results);
    });
}

// Display search results
function displaySearchResults(results) {
    const tbody = document.getElementById('searchResultsBody');
    const container = document.getElementById('searchResults');
    
    if (!tbody || !container) return;
    
    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No customers found</td></tr>';
        container.classList.remove('hidden');
        return;
    }
    
    tbody.innerHTML = results.map(c => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900 dark:text-white">${c.cust_first_name} ${c.cust_last_name}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">${c.customer_code}</div>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${c.address}</td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${c.workflow_status}</td>
            <td class="px-4 py-3 text-center">
                <button onclick="selectCustomerForPayment('${c.customer_code}')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                    Select
                </button>
            </td>
        </tr>
    `).join('');
    container.classList.remove('hidden');
}

// Select customer for payment (global function)
window.selectCustomerForPayment = function(customerCode) {
    console.log('PaymentSystem: Selecting customer for payment:', customerCode);
    const customer = paymentManager.selectCustomer(customerCode);
    if (customer) {
        loadCustomerPayment(customer);
    }
};

// Load customer payment UI
function loadCustomerPayment(customer) {
    console.log('PaymentSystem: Loading payment UI for customer:', customer.customer_code);
    
    const searchPanel = document.getElementById('searchPanel');
    const paymentContainer = document.getElementById('paymentContainer');
    
    if (searchPanel && paymentContainer) {
        searchPanel.style.display = 'none';
        paymentContainer.style.display = 'block';
    }
    
    // Populate customer information
    populateCustomerInfo(customer);
    
    // Generate and display invoice
    const invoice = paymentManager.getCurrentInvoice();
    if (invoice) {
        displayInvoice(invoice);
    }
    
    // Setup payment button
    setupPaymentButton();
}

// Populate customer information
function populateCustomerInfo(customer) {
    const fields = {
        'displayCustomerCode': customer.customer_code,
        'displayCustomerName': `${customer.cust_first_name} ${customer.cust_last_name}`,
        'displayAddress': customer.address,
        'displayStatus': customer.workflow_status
    };
    
    Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = value;
        }
    });
}

// Display invoice information
function displayInvoice(invoice) {
    // Invoice header
    const invoiceNumber = document.getElementById('invoiceNumber');
    const invoiceStatus = document.getElementById('invoiceStatus');
    
    if (invoiceNumber) invoiceNumber.textContent = invoice.invoice_id;
    if (invoiceStatus) invoiceStatus.textContent = invoice.payment_status;
    
    // Charges breakdown
    const chargesBreakdown = document.getElementById('chargesBreakdown');
    if (chargesBreakdown) {
        const breakdown = invoice.items.map(item => `
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">${item.description}</span>
                <span class="text-gray-900 dark:text-white">₱${item.amount.toLocaleString()}</span>
            </div>
        `).join('');
        chargesBreakdown.innerHTML = breakdown;
    }
    
    // Total amount
    const totalAmount = document.getElementById('totalAmount');
    const amountReceived = document.getElementById('amountReceived');
    
    if (totalAmount) totalAmount.textContent = `₱${invoice.total_amount.toLocaleString()}`;
    if (amountReceived) amountReceived.value = invoice.total_amount;
}

// Setup payment button
function setupPaymentButton() {
    const paymentBtn = document.getElementById('processPaymentBtn');
    if (!paymentBtn) return;
    
    // Remove existing listeners by cloning
    const newPaymentBtn = paymentBtn.cloneNode(true);
    paymentBtn.parentNode.replaceChild(newPaymentBtn, paymentBtn);
    
    // Add new listener
    newPaymentBtn.addEventListener('click', handlePaymentSubmit);
}

// Handle payment submission
function handlePaymentSubmit() {
    console.log('PaymentSystem: Processing payment...');
    
    const form = document.getElementById('paymentForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const paymentData = {
        amountReceived: parseFloat(document.getElementById('amountReceived').value),
        paymentMethod: document.getElementById('paymentMethod').value,
        referenceNumber: document.getElementById('referenceNumber').value
    };
    
    const btn = document.getElementById('processPaymentBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    }
    
    try {
        setTimeout(() => {
            const result = paymentManager.processPayment(paymentData);
            console.log('PaymentSystem: Payment processed successfully:', result);
            showAlert('Payment processed successfully! Customer moved to approval queue.', 'success');
            
            setTimeout(() => {
                window.location.href = '/customer/list';
            }, 2000);
        }, 1500);
    } catch (error) {
        console.error('PaymentSystem: Payment error:', error);
        showAlert(error.message, 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Process Payment';
        }
    }
}

// Show alert notification
function showAlert(message, type = 'info') {
    const colors = { 
        success: 'bg-green-500', 
        error: 'bg-red-500', 
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
    alert.textContent = message;
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 3000);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePaymentSystem);
} else {
    initializePaymentSystem();
}

// Export for global access
window.PaymentManager = PaymentManager;
window.paymentManager = paymentManager;
