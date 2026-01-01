/**
 * Simple Customer List with Tabs
 */

import { ALL_STATUSES, calculateProgress } from './workflow-config.js';
import { enhancedCustomerData } from './enhanced-customer-data.js';

class SimpleCustomerList {
    constructor() {
        this.allCustomers = [...enhancedCustomerData];
        this.customers = []; // Only customers without printed docs
        this.filteredCustomers = [];
        this.currentPage = 1;
        this.rowsPerPage = 10;
        
        this.init();
    }

    init() {
        if (!document.getElementById('customerTable')) return;
        
        this.loadCustomers();
        this.bindEvents();
        this.renderCustomerList();
    }

    loadCustomers() {
        // Only customers without printed docs
        this.customers = this.allCustomers.filter(c => !c.documents_printed_at);
        this.filteredCustomers = [...this.customers];
    }

    bindEvents() {
        // Search
        document.getElementById('searchInput')?.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            this.filteredCustomers = this.customers.filter(c => {
                const fullName = `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase();
                return fullName.includes(query) ||
                       c.customer_code.toLowerCase().includes(query) ||
                       c.address.toLowerCase().includes(query);
            });
            this.currentPage = 1;
            this.renderCustomerList();
        });

        // Page size
        document.getElementById('customerPageSize')?.addEventListener('change', (e) => {
            this.rowsPerPage = parseInt(e.target.value) || 10;
            this.currentPage = 1;
            this.renderCustomerList();
        });

        // Pagination
        document.getElementById('prevPage')?.addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.renderCustomerList();
            }
        });

        document.getElementById('nextPage')?.addEventListener('click', () => {
            const totalPages = Math.ceil(this.filteredCustomers.length / this.rowsPerPage);
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.renderCustomerList();
            }
        });

        // Modal events
        window.addEventListener('save-customer', (e) => {
            this.handleSaveCustomer(e.detail);
        });

        window.addEventListener('confirm-delete-customer', (e) => {
            this.handleDeleteCustomer(e.detail);
        });
    }



    renderCustomerList() {
        const tableBody = document.getElementById('customerTable');
        if (!tableBody) return;

        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        const pageCustomers = this.filteredCustomers.slice(start, end);

        if (pageCustomers.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No customers found</td></tr>';
            this.updatePagination(this.filteredCustomers.length);
            return;
        }

        tableBody.innerHTML = pageCustomers.map(customer => {
            const statusConfig = ALL_STATUSES[customer.workflow_status] || ALL_STATUSES['NEW_APPLICATION'];

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                ${customer.cust_first_name.charAt(0)}${customer.cust_last_name.charAt(0)}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    ${customer.cust_first_name} ${customer.cust_last_name}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customer.id_type || 'N/A'}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${customer.id_number || 'N/A'}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customer.address}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ${customer.registration_type}
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${customer.meterReader || 'N/A'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ${customer.area || 'N/A'}
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-gray-100">
                        ${new Date(customer.create_date).toLocaleDateString()}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="window.customerList.printDocuments('${customer.customer_code}')" 
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors"
                                title="Print Documents">
                            <i class="fas fa-print mr-1"></i>Print
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="window.customerList.viewCustomer('${customer.customer_code}')" 
                                    class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                    title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="window.customerList.editCustomer('${customer.customer_code}')" 
                                    class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="window.location.href='/customer/list'" 
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors"
                                    title="Return to Customer List">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updatePagination(this.filteredCustomers.length);
    }



    updatePagination(total) {
        const totalPages = Math.ceil(total / this.rowsPerPage) || 1;
        const start = Math.min((this.currentPage - 1) * this.rowsPerPage + 1, total);
        const end = Math.min(this.currentPage * this.rowsPerPage, total);

        document.getElementById('currentPage').textContent = this.currentPage;
        document.getElementById('totalPages').textContent = totalPages;
        document.getElementById('startRecord').textContent = total === 0 ? 0 : start;
        document.getElementById('endRecord').textContent = end;
        document.getElementById('totalRecords').textContent = total;

        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage >= totalPages;
    }



    processPayment(customerCode) {
        const customer = this.allCustomers.find(c => c.customer_code === customerCode);
        if (!customer) return;
        
        // Store customer data for payment page
        sessionStorage.setItem('selectedCustomer', JSON.stringify(customer));
        sessionStorage.setItem('paymentCustomerCode', customerCode);
        
        // Navigate to payment management with customer code in URL
        window.location.href = `/customer/payment/${encodeURIComponent(customerCode)}`;
    }

    printDocuments(customerCode) {
        const customer = this.allCustomers.find(c => c.customer_code === customerCode);
        if (!customer) return;

        if (window.UnifiedPrintSystem) {
            window.UnifiedPrintSystem.printCustomerForm({
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                id: customer.customer_code,
                Email: customer.email,
                Phone: customer.phone,
                AreaCode: customer.address,
                DateApplied: customer.create_date,
                Status: customer.workflow_status,
                registration_type: customer.registration_type
            });
        }

        // Mark as printed
        customer.documents_printed_at = new Date().toISOString();
        customer.documents_printed_count++;
        customer.workflow_status = 'DOCS_PRINTED';

        // Reload customers
        this.loadCustomers();
        this.renderCustomerList();

        this.showAlert('Documents printed! Customer moved to Application Process.', 'success');
    }



    viewCustomer(customerCode) {
        const customer = this.allCustomers.find(c => c.customer_code === customerCode);
        if (!customer) return;

        window.dispatchEvent(new CustomEvent('view-customer', { 
            detail: {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                Email: customer.email || 'N/A',
                AreaCode: customer.address,
                DateApplied: customer.create_date,
                Status: ALL_STATUSES[customer.workflow_status].label
            }
        }));
    }

    editCustomer(customerCode) {
        const customer = this.allCustomers.find(c => c.customer_code === customerCode);
        if (!customer) return;

        window.dispatchEvent(new CustomEvent('edit-customer', { 
            detail: {
                id: customer.customer_code,
                CustomerName: `${customer.cust_first_name} ${customer.cust_last_name}`,
                Email: customer.email || '',
                AreaCode: customer.address
            }
        }));
    }

    deleteCustomer(customerCode) {
        window.dispatchEvent(new CustomEvent('delete-customer', { detail: customerCode }));
    }

    handleSaveCustomer(data) {
        const customer = this.allCustomers.find(c => c.customer_code === data.id);
        if (!customer) return;

        const [firstName, ...lastNameParts] = data.CustomerName.split(' ');
        customer.cust_first_name = firstName;
        customer.cust_last_name = lastNameParts.join(' ');
        customer.email = data.Email;
        customer.address = data.AreaCode;

        this.loadCustomers();
        this.renderCustomerList();
        
        window.dispatchEvent(new CustomEvent('close-modal'));
        this.showAlert('Customer updated successfully', 'success');
    }

    handleDeleteCustomer(customerCode) {
        const index = this.allCustomers.findIndex(c => c.customer_code === customerCode);
        if (index === -1) return;

        this.allCustomers.splice(index, 1);
        this.loadCustomers();
        this.renderCustomerList();
        
        window.dispatchEvent(new CustomEvent('close-modal'));
        this.showAlert('Customer deleted successfully', 'success');
    }

    showAlert(message, type = 'info') {
        window.dispatchEvent(new CustomEvent('show-alert', {
            detail: { message, type, duration: 3000 }
        }));
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.customerList = new SimpleCustomerList();
    // Export customer data globally for payment system
    window.customerAllData = window.customerList.allCustomers;
});

export default SimpleCustomerList;
