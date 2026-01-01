import { enhancedCustomerData } from './enhanced-customer-data.js';

function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
}

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        el.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    document.getElementById(tab + '-content').classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
}

document.addEventListener('DOMContentLoaded', () => {
    // Get customer code from URL
    const pathParts = window.location.pathname.split('/');
    const customerCode = pathParts[pathParts.length - 1];
    
    // Find customer in enhanced data
    const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);
    
    if (customer) {
        // Populate customer info
        const fullName = `${customer.cust_first_name} ${customer.cust_middle_name} ${customer.cust_last_name}`.replace(/\s+/g, ' ');
        document.getElementById('consumer-id').textContent = customer.customer_code;
        document.getElementById('consumer-name').textContent = fullName;
        document.getElementById('consumer-address').textContent = customer.address;
        document.getElementById('consumer-meter').textContent = customer.meter_no || 'Not Assigned';
        
        // Documents & History
        const documentsData = [
            { type: 'Document', details: `${customer.id_type} - Uploaded`, date: customer.create_date.split(' ')[0], status: '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Verified</span>', actions: '<button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded"><i class="fas fa-eye"></i></button>' },
            { type: 'History', details: 'Application Submitted', date: customer.create_date.split(' ')[0], status: '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Completed</span>', actions: '-' },
        ];
        
        if (customer.documents_printed_at) {
            documentsData.push({ type: 'History', details: 'Documents Printed', date: customer.documents_printed_at.split(' ')[0], status: '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Completed</span>', actions: '-' });
        }
        
        if (customer.requirements_verified_at) {
            documentsData.push({ type: 'History', details: 'Requirements Verified', date: customer.requirements_verified_at.split(' ')[0], status: '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>', actions: '-' });
        }
        
        if (customer.approved_at) {
            documentsData.push({ type: 'History', details: 'Application Approved', date: customer.approved_at.split(' ')[0], status: '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>', actions: '-' });
        }
        
        const docsTbody = document.getElementById('documents-tbody');
        if (docsTbody) {
            docsTbody.innerHTML = documentsData.map(item => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${item.type}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${item.details}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${item.date}</td>
                    <td class="px-4 py-3 text-sm">${item.status}</td>
                    <td class="px-4 py-3 text-center text-sm">${item.actions}</td>
                </tr>
            `).join('');
        }
        
        // Service Connections
        const connTbody = document.getElementById('connections-tbody');
        if (connTbody && customer.account_no) {
            const statusColor = customer.workflow_status === 'ACTIVE_CONSUMER' 
                ? 'bg-green-100 text-green-800' 
                : customer.workflow_status === 'METER_ASSIGNED'
                ? 'bg-blue-100 text-blue-800'
                : 'bg-yellow-100 text-yellow-800';
            
            connTbody.innerHTML = `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">${customer.account_no}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${customer.customer_type || 'Residential'}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">${customer.meterReader}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${customer.area}</div>
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">${customer.meter_no || 'Not Assigned'}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${customer.meter_installed_date ? new Date(customer.meter_installed_date).toLocaleDateString() : 'Not Installed'}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 text-xs rounded-full ${statusColor}">${customer.workflow_status.replace(/_/g, ' ')}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="openConnectionDetailsModal({account_no: '${customer.account_no}', connection_id: '${customer.connection_id || 'N/A'}', customer_type: '${customer.customer_type || 'Residential'}', connection_status: '${customer.workflow_status}', customer_name: '${fullName}', customer_code: '${customer.customer_code}', address: '${customer.address}', meter_no: '${customer.meter_no || 'N/A'}', date_installed: '${customer.meter_installed_date || ''}', meterReader: '${customer.meterReader}', area: '${customer.area}'})" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        } else if (connTbody) {
            connTbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-plug text-3xl mb-2 opacity-50"></i>
                        <p>No service connection yet</p>
                    </td>
                </tr>
            `;
        }
    }
});

window.switchTab = switchTab;
