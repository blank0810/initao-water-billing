/**
 * Application Process Manager
 */

import { ALL_STATUSES, calculateProgress } from './workflow-config.js';
import { enhancedCustomerData } from './enhanced-customer-data.js';

class ApplicationProcessManager {
    constructor() {
        this.allCustomers = [...enhancedCustomerData];
        this.applications = [];
        this.filteredApplications = [];
        this.currentPage = 1;
        this.rowsPerPage = 10;
        
        this.init();
    }

    init() {
        if (!document.getElementById('applicationTable')) return;
        
        this.loadApplications();
        this.bindEvents();
        this.renderApplicationList();
    }

    loadApplications() {
        // Only customers with printed docs
        this.applications = this.allCustomers.filter(c => c.documents_printed_at);
        this.filteredApplications = [...this.applications];
    }

    bindEvents() {
        // Search
        document.getElementById('searchInputApp')?.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            this.filteredApplications = this.applications.filter(c => {
                const fullName = `${c.cust_first_name} ${c.cust_last_name}`.toLowerCase();
                return fullName.includes(query) ||
                       c.customer_code.toLowerCase().includes(query) ||
                       c.address.toLowerCase().includes(query);
            });
            this.currentPage = 1;
            this.renderApplicationList();
        });

        // Page size
        document.getElementById('appPageSize')?.addEventListener('change', (e) => {
            this.rowsPerPage = parseInt(e.target.value) || 10;
            this.currentPage = 1;
            this.renderApplicationList();
        });

        // Pagination
        document.getElementById('prevPageApp')?.addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.renderApplicationList();
            }
        });

        document.getElementById('nextPageApp')?.addEventListener('click', () => {
            const totalPages = Math.ceil(this.filteredApplications.length / this.rowsPerPage);
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.renderApplicationList();
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

    renderApplicationList() {
        const tableBody = document.getElementById('applicationTable');
        if (!tableBody) return;

        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        const pageApplications = this.filteredApplications.slice(start, end);

        if (pageApplications.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No applications found</td></tr>';
            this.updatePagination(this.filteredApplications.length);
            return;
        }

        tableBody.innerHTML = pageApplications.map(customer => {
            const statusConfig = ALL_STATUSES[customer.workflow_status];
            const progress = calculateProgress(customer.workflow_status);

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
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customer.address}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-${customer.registration_type === 'RESIDENTIAL' ? 'home' : 'building'} mr-1"></i>
                            ${customer.registration_type}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            <i class="fas fa-user-tie mr-1 text-blue-600"></i>${customer.meterReader || 'N/A'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>${customer.area || 'N/A'}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full ${statusConfig.color}">
                                <i class="fas ${statusConfig.icon}"></i>
                                ${statusConfig.label}
                            </span>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: ${progress}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">${progress}% Complete</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="window.applicationManager.processPayment('${customer.customer_code}')" 
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
                            <i class="fas fa-credit-card mr-1"></i>Payment
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="window.applicationManager.viewCustomer('${customer.customer_code}')" 
                                    class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                    title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="window.applicationManager.editCustomer('${customer.customer_code}')" 
                                    class="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="window.applicationManager.deleteCustomer('${customer.customer_code}')" 
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        this.updatePagination(this.filteredApplications.length);
    }

    updatePagination(total) {
        const totalPages = Math.ceil(total / this.rowsPerPage) || 1;
        const start = Math.min((this.currentPage - 1) * this.rowsPerPage + 1, total);
        const end = Math.min(this.currentPage * this.rowsPerPage, total);

        document.getElementById('currentPageApp').textContent = this.currentPage;
        document.getElementById('totalPagesApp').textContent = totalPages;
        document.getElementById('startRecordApp').textContent = total === 0 ? 0 : start;
        document.getElementById('endRecordApp').textContent = end;
        document.getElementById('totalRecordsApp').textContent = total;

        const prevBtn = document.getElementById('prevPageApp');
        const nextBtn = document.getElementById('nextPageApp');
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage >= totalPages;
    }

    processPayment(customerCode) {
        const customer = this.allCustomers.find(c => c.customer_code === customerCode);
        if (!customer) return;
        
        sessionStorage.setItem('selectedCustomer', JSON.stringify(customer));
        sessionStorage.setItem('paymentCustomerCode', customerCode);
        window.location.href = `/customer/payment/${encodeURIComponent(customerCode)}`;
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

        this.loadApplications();
        this.renderApplicationList();
        
        window.dispatchEvent(new CustomEvent('close-modal'));
        this.showAlert('Customer updated successfully', 'success');
    }

    handleDeleteCustomer(customerCode) {
        const index = this.allCustomers.findIndex(c => c.customer_code === customerCode);
        if (index === -1) return;

        this.allCustomers.splice(index, 1);
        this.loadApplications();
        this.renderApplicationList();
        
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
    window.applicationManager = new ApplicationProcessManager();
    window.customerAllData = window.applicationManager.allCustomers;
});

export default ApplicationProcessManager;
