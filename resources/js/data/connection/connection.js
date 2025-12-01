// Service Connection Management System
class ServiceConnectionManager {
    constructor() {
        this.connections = [
            { connection_id: 'CONN-1001', customer_code: 'CUST-2024-009', customer_name: 'David Wilson', account_no: 'ACC-2024-5001', area: 'Zone C', meterReader: 'Mike Johnson', readingSchedule: '2024-02-15', connection_status: 'SCHEDULED', scheduled_date: '2024-01-20', created_at: '2024-01-10', payment_status: 'UNPAID', invoice_id: 'INV-2024-1008', amount: 3500 },
            { connection_id: 'CONN-1002', customer_code: 'CUST-2024-010', customer_name: 'Emma Brown', account_no: 'ACC-2024-5002', area: 'Zone A', meterReader: 'John Smith', readingSchedule: '2024-02-05', connection_status: 'SCHEDULED', scheduled_date: '2024-01-21', created_at: '2024-01-09', payment_status: 'UNPAID', invoice_id: 'INV-2024-1009', amount: 3500 },
            { connection_id: 'CONN-1003', customer_code: 'CUST-2024-011', customer_name: 'Robert Taylor', account_no: 'ACC-2024-5003', area: 'Zone B', meterReader: 'Jane Doe', readingSchedule: '2024-02-10', connection_status: 'COMPLETED', scheduled_date: '2024-01-08', completed_date: '2024-01-08', created_at: '2024-01-05', payment_status: 'UNPAID', invoice_id: 'INV-2024-1010', amount: 3500 },
            { connection_id: 'CONN-1004', customer_code: 'CUST-2024-012', customer_name: 'Sarah Johnson', account_no: 'ACC-2024-5004', area: 'Zone A', meterReader: 'John Smith', readingSchedule: '2024-02-05', connection_status: 'COMPLETED', scheduled_date: '2024-01-05', completed_date: '2024-01-05', created_at: '2024-01-02', payment_status: 'PAID', paid_date: '2024-01-06', invoice_id: 'INV-2024-1011', amount: 3500 }
        ];
        this.currentFilter = 'ALL';
        this.selectedConnection = null;
    }

    filterConnections(status) {
        this.currentFilter = status;
        return status === 'ALL'
            ? this.connections
            : this.connections.filter(c => c.connection_status === status);
    }

    getFilteredConnections() {
        return this.filterConnections(this.currentFilter);
    }

    getConnectionById(connectionId) {
        return this.connections.find(c => c.connection_id === connectionId);
    }

    selectConnection(connectionId) {
        this.selectedConnection = this.getConnectionById(connectionId);
        return this.selectedConnection;
    }

    completeConnection(meterData) {
        if (!this.selectedConnection) {
            throw new Error('No connection selected');
        }

        const { meterId, installedBy, initialReading, installationNotes } = meterData;

        // Create meter assignment
        const meterAssignment = {
            assignment_id: `MA-${Date.now()}`,
            customer_code: this.selectedConnection.customer_code,
            meter_id: meterId,
            installed_by: installedBy,
            initial_reading: parseFloat(initialReading),
            installation_date: new Date().toISOString(),
            installation_notes: installationNotes
        };

        // Update service connection
        this.selectedConnection.connection_status = 'COMPLETED';
        this.selectedConnection.completed_date = new Date().toISOString();

        return {
            meterAssignment,
            connection: this.selectedConnection
        };
    }

    getStatusColor(status) {
        const colors = {
            'COMPLETED': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
            'SCHEDULED': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200'
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    getPaymentStatusColor(status) {
        const colors = {
            'PAID': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
            'UNPAID': 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200'
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }
}

        let connectionManager;

        document.addEventListener('DOMContentLoaded', function() {
            connectionManager = new ServiceConnectionManager();
            initializeServiceConnections();
        });

        function initializeServiceConnections() {
            renderTable();
        }

        function renderTable() {
            const tableBody = document.getElementById('connectionTable');
            const filtered = connectionManager.getFilteredConnections();

            if (filtered.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No connections found</td></tr>';
                return;
            }

            tableBody.innerHTML = filtered.map(conn => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">${conn.account_no}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${conn.customer_name}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">${conn.customer_code}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${new Date(conn.scheduled_date).toLocaleDateString()}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${connectionManager.getStatusColor(conn.connection_status)}">
                            ${conn.connection_status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        ${conn.connection_status === 'SCHEDULED' ? `
                            <button onclick="openMeterModal('${conn.connection_id}')" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-tools mr-2"></i>Complete Connection
                            </button>
                        ` : `
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Completed ${new Date(conn.completed_date).toLocaleDateString()}
                            </span>
                        `}
                    </td>
                </tr>
            `).join('');
        }

        function filterConnections(status) {
            connectionManager.filterConnections(status);

            // Update button styles
            document.querySelectorAll('[id^="btn"]').forEach(btn => {
                btn.className = 'px-4 py-2 bg-gray-300 text-gray-700 rounded-lg';
            });
            
            // Fix button ID mapping
            let btnId;
            if (status === 'ALL') {
                btnId = 'btnAll';
            } else if (status === 'SCHEDULED') {
                btnId = 'btnScheduled';
            } else if (status === 'COMPLETED') {
                btnId = 'btnCompleted';
            }
            
            const activeBtn = document.getElementById(btnId);
            if (activeBtn) {
                activeBtn.className = 'px-4 py-2 bg-blue-600 text-white rounded-lg';
            }

            renderTable();
        }

        function openMeterModal(connectionId) {
            const connection = connectionManager.selectConnection(connectionId);
            if (!connection) return;

            // Use the unified meter assignment modal
            if (window.openMeterAssignmentModal) {
                // Set the selected customer for the modal
                window.selectedServiceConnection = connection;
                window.openMeterAssignmentModal('new', 'service');
            } else {
                console.error('Meter assignment modal not available');
            }
        }

        function closeMeterModal() {
            if (window.closeMeterAssignmentModal) {
                window.closeMeterAssignmentModal();
            }
        }

        function completeMeterInstallation() {
            // This function is now handled by the unified modal
            // The modal will call back to update the service connection
            if (window.selectedServiceConnection) {
                window.selectedServiceConnection.connection_status = 'COMPLETED';
                window.selectedServiceConnection.completed_date = new Date().toISOString();
                
                showAlert(`Service connection completed! ${window.selectedServiceConnection.customer_name} is now CONNECTED.`, 'success');
                renderTable();
                
                // Clear selection
                window.selectedServiceConnection = null;
            }
        }

        function openInvoiceModal(connectionId) {
            const connection = connectionManager.getConnectionById(connectionId);
            if (!connection) return;

            const modal = document.getElementById('invoiceModal');
            if (!modal) return;

            document.getElementById('invoiceCustomerName').textContent = connection.customer_name;
            document.getElementById('invoiceAccountNo').textContent = connection.account_no;
            document.getElementById('invoiceNumber').textContent = connection.invoice_id;
            document.getElementById('invoiceDate').textContent = connection.paid_date ? new Date(connection.paid_date).toLocaleDateString() : new Date().toLocaleDateString();
            document.getElementById('invoiceAmount').textContent = `₱${connection.amount.toLocaleString()}`;
            document.getElementById('invoiceTotal').textContent = `₱${connection.amount.toLocaleString()}`;

            // Update status display in modal
            const statusElement = document.querySelector('#invoiceModal #invoiceStatus');
            if (statusElement) {
                statusElement.className = `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${connectionManager.getPaymentStatusColor(connection.payment_status)}`;
                statusElement.textContent = connection.payment_status;
            }

            modal.classList.remove('hidden');
        }

        function closeInvoiceModal() {
            document.getElementById('invoiceModal').classList.add('hidden');
        }

        function printInvoice() {
            window.print();
        }

        function showAlert(message, type) {
            const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
            const alert = document.createElement('div');
            alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            alert.textContent = message;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
        }

// Export for global access
window.ServiceConnectionManager = ServiceConnectionManager;
window.filterConnections = filterConnections;
window.openMeterModal = openMeterModal;
window.closeMeterModal = closeMeterModal;
window.completeMeterInstallation = completeMeterInstallation;
window.openInvoiceModal = openInvoiceModal;
window.closeInvoiceModal = closeInvoiceModal;
window.printInvoice = printInvoice;
