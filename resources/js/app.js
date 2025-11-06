import "./bootstrap";
import Chart from "chart.js/auto";
import { customerAllData } from "./data/all-dummy.js";
import Alpine from "alpinejs";
import { printCustomerForm } from "./print.js";
import "flowbite";
import { Modal } from "flowbite";

window.Alpine = Alpine;
Alpine.start();

// Make Modal available globally
window.Modal = Modal;

// Initialize charts
document.addEventListener("DOMContentLoaded", () => {
    initializeCharts();
    // renderCustomerTable(); // <-- COMMENTED OUT: Now using DataTables instead of custom table
});

// --- Chart initialization (unchanged) ---
function initializeCharts() {
    // ... your chart code here ...
}

// --- Customer Table ---
// DEPRECATED: This custom table implementation has been replaced with DataTables
// See resources/views/pages/customer/customer-list.blade.php for the new implementation
/*
window.renderCustomerTable = function() {
    const tableBody = document.getElementById('customerTable');
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredCustomers = [...customerAllData];

    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const searchInput = document.getElementById('searchInput');

    let printHistory = JSON.parse(localStorage.getItem('customerPrintHistory') || '{}');

    function renderTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageCustomers = filteredCustomers.slice(start, end);

        pageCustomers.forEach(customer => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150';

            const count = printHistory[customer.id] || 0;
            const printLabel = count === 0 ? 'Print' : count === 1 ? 'Printed' : `Printed(${count})`;
            const printClasses = count === 0
                ? 'px-3 py-1.5 border border-blue-500 text-blue-500 rounded-full text-xs hover:bg-blue-50 dark:hover:bg-blue-700'
                : 'px-3 py-1.5 bg-green-200 dark:bg-green-600 text-green-800 dark:text-white rounded-full text-xs';

            tr.innerHTML = `
                <td class="px-6 py-4 font-mono text-gray-900 dark:text-white">${customer.id}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            ${customer.CustomerName.slice(0,2)}
                        </div>
                        <div>
                            <div class="text-gray-900 dark:text-white">${customer.CustomerName}</div>
                            <div class="text-gray-500 dark:text-gray-400 text-sm">${customer.Email || ''}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-900 dark:text-white">${customer.AreaCode}</td>
                <td class="px-6 py-4 text-gray-900 dark:text-white">${new Date(customer.DateApplied).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-center"><span class="px-3 py-1 bg-orange-200 dark:bg-orange-600 text-orange-800 dark:text-white rounded-full text-xs font-semibold">${customer.Status}</span></td>
                <td class="px-6 py-4 text-center">
                    <button onclick="handlePrint('${customer.id}', this)" class="${printClasses}">
                        ${printLabel}
                    </button>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="goToPayment('${customer.id}', '${customer.CustomerName.replace(/'/g,"\\'")}')" class="px-3 py-1.5 border border-green-500 text-green-500 rounded-full text-xs hover:bg-green-50 dark:hover:bg-green-700">Payment</button>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center items-center gap-2">
                        <button onclick="alert('Edit ${customer.id}')" class="text-blue-600 dark:text-blue-400 hover:text-blue-900">Edit</button>
                        <button onclick="alert('Delete ${customer.id}')" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                        <button onclick="alert('View ${customer.id}')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900">View</button>
                    </div>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        paginationInfo.textContent = `Showing ${Math.min((currentPage-1)*rowsPerPage+1, filteredCustomers.length)} to ${Math.min(currentPage*rowsPerPage, filteredCustomers.length)} of ${filteredCustomers.length} results`;
    }

    // --- Search ---
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            const query = e.target.value.toLowerCase();
            filteredCustomers = customerAllData.filter(c =>
                c.id.toLowerCase().includes(query) ||
                c.CustomerName.toLowerCase().includes(query) ||
                c.AreaCode.toLowerCase().includes(query)
            );
            currentPage = 1;
            renderTable();
        });
    }

    // --- Pagination ---
    prevPageBtn?.addEventListener('click', () => { if(currentPage>1) currentPage--; renderTable(); });
    nextPageBtn?.addEventListener('click', () => { if(currentPage*rowsPerPage<filteredCustomers.length) currentPage++; renderTable(); });

    // --- Print function ---
    window.handlePrint = function(customerId, buttonEl) {
        const customer = customerAllData.find(c => c.id === customerId);
        if(!customer) return alert('Customer not found!');

        printHistory[customerId] = (printHistory[customerId] || 0) + 1;
        localStorage.setItem('customerPrintHistory', JSON.stringify(printHistory));

        const count = printHistory[customerId];
        buttonEl.textContent = count === 1 ? 'Printed' : `Printed(${count})`;
        buttonEl.className = 'px-3 py-1.5 bg-green-200 dark:bg-green-600 text-green-800 dark:text-white rounded-full text-xs';

        printCustomerForm(customer);
    };

    // --- Payment button navigation ---
    window.goToPayment = function(customerId, customerName) {
        const url = `/payment-management?customerId=${encodeURIComponent(customerId)}&customerName=${encodeURIComponent(customerName)}`;
        window.location.href = url;
    };

window.goToPayment = function(customerId, customerName) {
    const url = `/customer/payment-management?customerId=${encodeURIComponent(customerId)}&customerName=${encodeURIComponent(customerName)}`;
    window.location.href = url;
};


    // --- Initial render ---
    renderTable();
};
*/
