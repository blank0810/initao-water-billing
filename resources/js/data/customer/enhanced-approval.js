/**
 * ========================================
 * ENHANCED APPROVAL MANAGEMENT
 * Phase 3: Approval Queue & Connection Management
 * ========================================
 */

import { ALL_STATUSES } from './workflow-config.js';
import { enhancedCustomerData, approvalQueue, invoices } from './enhanced-customer-data.js';
import { declinedCustomersData } from './declined-customers.js';

class EnhancedApprovalManager {
    constructor() {
        this.approvalQueue = [];
        this.invoiceList = [...invoices];
        this.declinedCustomers = [...declinedCustomersData];
        this.filteredQueue = [];
        this.filteredInvoices = [];
        this.filteredDeclined = [];
        this.selectedApproval = null;
        this.activeTab = 'approval';
        this.currentPage = 1;
        this.pageSize = 10;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        
        this.init();
    }

    init() {
        this.loadApprovalQueue();
        this.filteredInvoices = [...this.invoiceList];
        this.filteredDeclined = [...this.declinedCustomers];
        this.renderApprovalTable();
        this.renderInvoiceTable();
        this.renderDeclinedTable();
        this.bindEvents();
        this.updatePagination();
    }

    loadApprovalQueue() {
        // Load customers who are READY_FOR_APPROVAL or PAYMENT_VERIFIED
        this.approvalQueue = enhancedCustomerData
            .filter(c => ['READY_FOR_APPROVAL', 'PAYMENT_VERIFIED'].includes(c.workflow_status))
            .map(customer => {
                const invoice = this.invoiceList.find(inv => inv.customer_code === customer.customer_code);
                return {
                    approval_id: `APR-${customer.cust_id}`,
                    cust_id: customer.cust_id,
                    customer_code: customer.customer_code,
                    customer_name: `${customer.cust_first_name} ${customer.cust_last_name}`,
                    customer_email: customer.email,
                    customer_phone: customer.phone,
                    address: customer.address,
                    area: customer.area,
                    meterReader: customer.meterReader,
                    readingSchedule: customer.readingSchedule,
                    registration_type: customer.registration_type,
                    invoice_id: invoice ? invoice.invoice_number : 'N/A',
                    paid_amount: invoice ? invoice.paid_amount : 0,
                    submitted_at: customer.payment_completed_at || customer.create_date,
                    approval_status: 'PENDING',
                    priority: this.calculatePriority(customer),
                    documents_verified: customer.requirements_complete,
                    payment_verified: customer.workflow_status === 'PAYMENT_VERIFIED' || customer.workflow_status === 'READY_FOR_APPROVAL'
                };
            });

        this.filteredQueue = [...this.approvalQueue];
    }

    calculatePriority(customer) {
        const daysSinceApplication = Math.floor(
            (new Date() - new Date(customer.create_date)) / (1000 * 60 * 60 * 24)
        );
        
        if (daysSinceApplication > 7) return 'URGENT';
        if (daysSinceApplication > 5) return 'HIGH';
        if (daysSinceApplication > 3) return 'MEDIUM';
        return 'LOW';
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value.toLowerCase());
            });
        }

        const pageSizeSelect = document.getElementById('approvalPageSize');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', (e) => {
                this.pageSize = parseInt(e.target.value);
                this.currentPage = 1;
                this.renderApprovalTable();
                this.updatePagination();
            });
        }

        const prevBtn = document.getElementById('approvalPrevBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.prevPage());
        }

        const nextBtn = document.getElementById('approvalNextBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        window.addEventListener('confirm-restore', (e) => {
            this.handleRestore(e.detail);
        });
    }

    switchTab(tab) {
        this.activeTab = tab;
        
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.className = 'tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 transition-colors';
        });
        
        // Hide all content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

        // Show active tab
        if (tab === 'approval') {
            document.getElementById('tabApproval').className = 'tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400';
            document.getElementById('approvalContent').classList.remove('hidden');
            document.getElementById('searchInput').placeholder = 'Search by customer name, code, or invoice...';
        } else if (tab === 'invoice') {
            document.getElementById('tabInvoice').className = 'tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400';
            document.getElementById('invoiceContent').classList.remove('hidden');
            document.getElementById('searchInput').placeholder = 'Search invoices...';
        } else if (tab === 'declined') {
            document.getElementById('tabDeclined').className = 'tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400';
            document.getElementById('declinedContent').classList.remove('hidden');
            document.getElementById('searchInput').placeholder = 'Search declined customers...';
        }
    }

    handleSearch(query) {
        this.filteredQueue = this.approvalQueue.filter(a =>
            a.customer_name.toLowerCase().includes(query) ||
            a.customer_code.toLowerCase().includes(query) ||
            a.invoice_id.toLowerCase().includes(query) ||
            a.address.toLowerCase().includes(query)
        );
        this.currentPage = 1;
        this.renderApprovalTable();
        this.updatePagination();
    }

    sortBy(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        this.filteredQueue.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];

            if (column === 'submitted_at') {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
            }

            if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });

        this.renderApprovalTable();
    }

    get paginatedQueue() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredQueue.slice(start, start + this.pageSize);
    }

    get totalPages() {
        return Math.ceil(this.filteredQueue.length / this.pageSize) || 1;
    }

    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderApprovalTable();
            this.updatePagination();
        }
    }

    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.renderApprovalTable();
            this.updatePagination();
        }
    }

    updatePagination() {
        const start = this.filteredQueue.length === 0 ? 0 : ((this.currentPage - 1) * this.pageSize) + 1;
        const end = Math.min(this.currentPage * this.pageSize, this.filteredQueue.length);

        const currentPageEl = document.getElementById('approvalCurrentPage');
        const totalPagesEl = document.getElementById('approvalTotalPages');
        const startRecordEl = document.getElementById('approvalStartRecord');
        const endRecordEl = document.getElementById('approvalEndRecord');
        const totalRecordsEl = document.getElementById('approvalTotalRecords');

        if (currentPageEl) currentPageEl.textContent = this.currentPage;
        if (totalPagesEl) totalPagesEl.textContent = this.totalPages;
        if (startRecordEl) startRecordEl.textContent = start;
        if (endRecordEl) endRecordEl.textContent = end;
        if (totalRecordsEl) totalRecordsEl.textContent = this.filteredQueue.length;

        const prevBtn = document.getElementById('approvalPrevBtn');
        const nextBtn = document.getElementById('approvalNextBtn');
        
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === this.totalPages;
    }

    renderApprovalTable() {
        const tableBody = document.getElementById('approvalTable');
        if (!tableBody) return;

        const pending = this.paginatedQueue.filter(a => a.approval_status === 'PENDING');

        if (pending.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p class="text-lg font-medium">No pending approvals</p>
                        <p class="text-sm">All applications have been processed</p>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = pending.map(approval => {
            const priorityConfig = {
                URGENT: { color: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200', icon: 'fa-exclamation-circle' },
                HIGH: { color: 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200', icon: 'fa-arrow-up' },
                MEDIUM: { color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200', icon: 'fa-minus' },
                LOW: { color: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200', icon: 'fa-arrow-down' }
            };
            const priority = priorityConfig[approval.priority] || priorityConfig.MEDIUM;

            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold">
                                ${approval.customer_name.split(' ').map(n => n[0]).join('')}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${approval.customer_name}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${approval.customer_code}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-phone text-xs mr-1"></i>${approval.customer_phone}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${approval.address}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-${approval.registration_type === 'RESIDENTIAL' ? 'home' : 'building'} mr-1"></i>
                            ${approval.registration_type}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            <i class="fas fa-user-tie mr-1 text-blue-600"></i>${approval.meterReader || 'N/A'}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>${approval.area || 'N/A'}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${approval.readingSchedule ? new Date(approval.readingSchedule).toLocaleDateString() : 'N/A'}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-mono text-gray-900 dark:text-gray-100">${approval.invoice_id}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ${approval.payment_verified ? '<i class="fas fa-check-circle text-green-500 mr-1"></i>Verified' : '<i class="fas fa-clock text-orange-500 mr-1"></i>Pending'}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">₱${approval.paid_amount.toLocaleString()}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${new Date(approval.submitted_at).toLocaleDateString()}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${new Date(approval.submitted_at).toLocaleTimeString()}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full ${priority.color}">
                            <i class="fas ${priority.icon}"></i>
                            ${approval.priority}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="window.approvalManager.approveApplication('${approval.approval_id}')" 
                                    class="w-9 h-9 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center justify-center"
                                    title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="window.approvalManager.openDeclineModal('${approval.approval_id}')" 
                                    class="w-9 h-9 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center justify-center"
                                    title="Decline">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderInvoiceTable() {
        const tableBody = document.getElementById('invoiceTable');
        if (!tableBody) return;

        if (this.filteredInvoices.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No invoices found</td></tr>';
            return;
        }

        tableBody.innerHTML = this.filteredInvoices.map(invoice => {
            const customer = enhancedCustomerData.find(c => c.customer_code === invoice.customer_code);
            const customerName = customer ? `${customer.cust_first_name} ${customer.cust_last_name}` : 'N/A';
            
            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">${invoice.invoice_number}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-100">${customerName}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${invoice.customer_code}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${new Date(invoice.invoice_date).toLocaleDateString()}</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">₱${invoice.amount.toLocaleString()}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                            invoice.payment_status === 'PAID' 
                                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' 
                                : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200'
                        }">
                            ${invoice.payment_status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="window.approvalManager.viewInvoice('${invoice.invoice_number}')" 
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderDeclinedTable() {
        const tableBody = document.getElementById('declinedTable');
        if (!tableBody) return;

        if (this.filteredDeclined.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No declined customers</td></tr>';
            return;
        }

        tableBody.innerHTML = this.filteredDeclined.map(customer => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${customer.customer_name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${customer.customer_code}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${customer.address}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${new Date(customer.date_declined).toLocaleDateString()}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${customer.reason}</td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">${customer.reference_no}</td>
                <td class="px-4 py-3 text-center">
                    <button onclick="window.approvalManager.restoreApplication('${customer.customer_code}')" 
                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                        <i class="fas fa-undo mr-1"></i>Restore
                    </button>
                </td>
            </tr>
        `).join('');
    }

    viewDetails(approvalId) {
        const approval = this.approvalQueue.find(a => a.approval_id === approvalId);
        if (!approval) return;

        // TODO: Open detailed view modal
        this.showAlert(`Viewing details for ${approval.customer_name}`, 'info');
    }

    approveApplication(approvalId) {
        const approval = this.approvalQueue.find(a => a.approval_id === approvalId);
        if (!approval) return;

        this.selectedApproval = approvalId;
        const customer = enhancedCustomerData.find(c => c.customer_code === approval.customer_code);
        
        document.getElementById('approveCustomerName').textContent = approval.customer_name;
        document.getElementById('approveCustomerCode').textContent = approval.customer_code;
        document.getElementById('approveIdInfo').textContent = customer ? `${customer.id_type} - ${customer.id_number}` : '-';
        document.getElementById('approveRegType').textContent = approval.registration_type;
        document.getElementById('approveAddress').textContent = approval.address;
        document.getElementById('approveArea').textContent = approval.area || '-';
        document.getElementById('approveMeterReader').textContent = approval.meterReader || '-';
        document.getElementById('approveReadingSchedule').textContent = approval.readingSchedule ? new Date(approval.readingSchedule).toLocaleDateString() : '-';
        document.getElementById('approveInvoiceId').textContent = approval.invoice_id;
        document.getElementById('approveAmount').textContent = `₱${approval.paid_amount.toLocaleString()}`;
        document.getElementById('approveAppDate').textContent = new Date(approval.submitted_at).toLocaleDateString();
        
        document.getElementById('approveModal').classList.remove('hidden');
    }

    closeApproveModal() {
        this.selectedApproval = null;
        const modal = document.getElementById('approveModal');
        if (modal) modal.classList.add('hidden');
    }

    confirmApprove() {
        const approval = this.approvalQueue.find(a => a.approval_id === this.selectedApproval);
        if (!approval) return;

        // Update customer status in main data
        const customer = enhancedCustomerData.find(c => c.customer_code === approval.customer_code);
        if (customer) {
            customer.workflow_status = 'APPROVED';
            customer.approved_at = new Date().toISOString();
            customer.account_no = `ACC-${new Date().getFullYear()}-${5000 + customer.cust_id}`;
        }

        approval.approval_status = 'APPROVED';
        this.showAlert(`${approval.customer_name} approved! Redirecting to Service Connection...`, 'success');
        this.closeApproveModal();
        this.loadApprovalQueue();
        this.renderApprovalTable();

        setTimeout(() => {
            window.location.href = `/customer/service-connection?customer_id=${approval.customer_code}&approved=true`;
        }, 1500);
    }

    openDeclineModal(approvalId) {
        this.selectedApproval = approvalId;
        const approval = this.approvalQueue.find(a => a.approval_id === approvalId);
        if (!approval) return;
        
        const customer = enhancedCustomerData.find(c => c.customer_code === approval.customer_code);
        
        const modal = document.getElementById('declineModal');
        if (!modal) {
            console.error('Decline modal not found');
            return;
        }
        
        setTimeout(() => {
            document.getElementById('declineCustomerName').textContent = approval.customer_name;
            document.getElementById('declineCustomerCode').textContent = approval.customer_code;
            document.getElementById('declineIdInfo').textContent = customer ? `${customer.id_type} - ${customer.id_number}` : '-';
            document.getElementById('declineArea').textContent = approval.area || '-';
            document.getElementById('declineMeterReader').textContent = approval.meterReader || '-';
            document.getElementById('declineAddress').textContent = approval.address;
        }, 50);
        
        modal.classList.remove('hidden');
    }

    closeDeclineModal() {
        this.selectedApproval = null;
        const reasonField = document.getElementById('declineReason');
        const modal = document.getElementById('declineModal');
        if (reasonField) reasonField.value = '';
        if (modal) modal.classList.add('hidden');
    }

    confirmDecline() {
        const reason = document.getElementById('declineReason').value.trim();
        if (!reason) {
            this.showAlert('Please provide a decline reason', 'error');
            return;
        }

        const approval = this.approvalQueue.find(a => a.approval_id === this.selectedApproval);
        if (!approval) return;

        // Update customer status
        const customer = enhancedCustomerData.find(c => c.customer_code === approval.customer_code);
        if (customer) {
            customer.workflow_status = 'DECLINED';
        }

        // Add to declined list
        this.declinedCustomers.push({
            customer_code: approval.customer_code,
            customer_name: approval.customer_name,
            address: approval.address,
            date_declined: new Date().toISOString().split('T')[0],
            reason: reason,
            reference_no: `REF-${Date.now()}`
        });

        approval.approval_status = 'DECLINED';
        this.filteredDeclined = [...this.declinedCustomers];
        
        this.showAlert('Application declined', 'info');
        this.closeDeclineModal();
        this.loadApprovalQueue();
        this.renderApprovalTable();
        this.renderDeclinedTable();
    }

    viewInvoice(invoiceNumber) {
        const invoice = this.invoiceList.find(i => i.invoice_number === invoiceNumber);
        if (!invoice) return;

        const customer = enhancedCustomerData.find(c => c.customer_code === invoice.customer_code);
        const items = JSON.parse(invoice.items);

        window.dispatchEvent(new CustomEvent('show-invoice', { 
            detail: {
                invoice_id: invoice.invoice_number,
                customer_name: customer ? `${customer.cust_first_name} ${customer.cust_last_name}` : 'N/A',
                customer_code: invoice.customer_code,
                amount: invoice.amount,
                status: invoice.payment_status,
                PaymentMethod: 'CASH',
                DateApplied: invoice.invoice_date,
                items: items
            }
        }));
    }

    restoreApplication(customerCode) {
        window.dispatchEvent(new CustomEvent('restore-application', { detail: customerCode }));
    }

    handleRestore(detail) {
        const { customerCode, notes } = detail;
        const declinedIndex = this.declinedCustomers.findIndex(c => c.customer_code === customerCode);
        if (declinedIndex === -1) return;

        const declined = this.declinedCustomers[declinedIndex];
        
        // Update customer status
        const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
        if (customer) {
            customer.workflow_status = 'NEW_APPLICATION';
        }

        this.declinedCustomers.splice(declinedIndex, 1);
        this.filteredDeclined = [...this.declinedCustomers];

        this.showAlert(`${declined.customer_name}'s application restored`, 'success');
        this.renderDeclinedTable();
    }

    showAlert(message, type = 'info') {
        const colors = { 
            success: 'bg-green-500', 
            error: 'bg-red-500', 
            info: 'bg-blue-500' 
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
}

// Global functions
window.switchTab = (tab) => window.approvalManager?.switchTab(tab);

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.approvalManager = new EnhancedApprovalManager();
});

export default EnhancedApprovalManager;
