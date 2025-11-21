// Service Application Management
class ServiceManager {
    constructor() {
        this.serviceApplications = [
            { 
                customer_code: 'CUST-2024-012', 
                customer_name: 'Alice Johnson', 
                application_date: '2024-01-15', 
                requirements_status: 'COMPLETE',
                documents: ['Valid ID', 'Proof of Residence', 'Application Form']
            },
            { 
                customer_code: 'CUST-2024-013', 
                customer_name: 'Bob Smith', 
                application_date: '2024-01-14', 
                requirements_status: 'COMPLETE',
                documents: ['Valid ID', 'Proof of Residence', 'Application Form']
            },
            { 
                customer_code: 'CUST-2024-014', 
                customer_name: 'Carol Davis', 
                application_date: '2024-01-13', 
                requirements_status: 'PENDING',
                documents: ['Valid ID', 'Application Form']
            }
        ];

        this.init();
    }

    init() {
        this.renderApplicationTable();
    }

    renderApplicationTable() {
        const tableBody = document.getElementById('applicationTable');

        if (this.serviceApplications.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No applications found</td></tr>';
            return;
        }

        tableBody.innerHTML = this.serviceApplications.map(app => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${app.customer_name}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">${app.customer_code}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${new Date(app.application_date).toLocaleDateString()}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                        app.requirements_status === 'COMPLETE' 
                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' 
                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200'
                    }">
                        ${app.requirements_status}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="window.serviceManager.viewDocuments('${app.customer_code}')" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm" title="View Documents">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${app.requirements_status === 'COMPLETE' ? `
                            <button onclick="window.serviceManager.openVerifiedModal('${app.customer_code}')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                <i class="fas fa-certificate mr-1"></i>Verified by Official
                            </button>
                        ` : `
                            <span class="px-3 py-1 bg-gray-300 text-gray-500 rounded text-sm cursor-not-allowed">
                                Requirements Pending
                            </span>
                        `}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    viewDocuments(customerCode) {
        const app = this.serviceApplications.find(a => a.customer_code === customerCode);
        if (!app) return;

        alert(`Documents for ${app.customer_name}:\n\n${app.documents.join('\n')}`);
    }

    openVerifiedModal(customerCode) {
        const app = this.serviceApplications.find(a => a.customer_code === customerCode);
        if (!app) return;

        document.getElementById('verifiedCustomerName').textContent = app.customer_name;
        document.getElementById('verificationDate').textContent = new Date().toLocaleDateString();
        document.getElementById('verifiedModal').classList.remove('hidden');
    }

    closeVerifiedModal() {
        document.getElementById('verifiedModal').classList.add('hidden');
    }

    showAlert(message, type) {
        const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
        const alert = document.createElement('div');
        alert.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
        alert.textContent = message;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('applicationTable')) {
        window.serviceManager = new ServiceManager();
    }
});

// Make available globally
if (typeof window !== 'undefined') {
    window.ServiceManager = ServiceManager;
}