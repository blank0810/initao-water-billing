// User management list script. Initializes when #userTable exists on the page.
(function () {
    if (!document.getElementById('userTable')) return;

    // Import or define dummy user data
    const userAllData = [
        {
            id: 'USR001',
            UserName: 'John Administrator',
            Email: 'john.admin@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-01-15'
        },
        {
            id: 'USR002',
            UserName: 'Maria Rodriguez',
            Email: 'maria.rodriguez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-02-10'
        },
        {
            id: 'USR003',
            UserName: 'Carlos Santos',
            Email: 'carlos.santos@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-03-05'
        },
        {
            id: 'USR004',
            UserName: 'Anna Thompson',
            Email: 'anna.thompson@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-01-20'
        },
        {
            id: 'USR005',
            UserName: 'David Garcia',
            Email: 'david.garcia@example.com',
            Role: 'User',
            Status: 'Inactive',
            DateCreated: '2024-04-12'
        },
        {
            id: 'USR006',
            UserName: 'Sofia Martinez',
            Email: 'sofia.martinez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-05-08'
        },
        {
            id: 'USR007',
            UserName: 'Robert Lee',
            Email: 'robert.lee@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-02-25'
        },
        {
            id: 'USR008',
            UserName: 'Elena Vasquez',
            Email: 'elena.vasquez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-06-01'
        },
        {
            id: 'USR009',
            UserName: 'Michael Chen',
            Email: 'michael.chen@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-03-18'
        },
        {
            id: 'USR010',
            UserName: 'Patricia Johnson',
            Email: 'patricia.johnson@example.com',
            Role: 'User',
            Status: 'Inactive',
            DateCreated: '2024-04-30'
        },
        {
            id: 'USR011',
            UserName: 'James Wilson',
            Email: 'james.wilson@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-05-22'
        },
        {
            id: 'USR012',
            UserName: 'Linda Brown',
            Email: 'linda.brown@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-06-15'
        }
    ];
    
    // Make data globally available
    window.userAllData = userAllData;
    
    const userData = [
        {
            id: 'USR001',
            UserName: 'John Administrator',
            Email: 'john.admin@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-01-15'
        },
        {
            id: 'USR002',
            UserName: 'Maria Rodriguez',
            Email: 'maria.rodriguez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-02-10'
        },
        {
            id: 'USR003',
            UserName: 'Carlos Santos',
            Email: 'carlos.santos@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-03-05'
        },
        {
            id: 'USR004',
            UserName: 'Anna Thompson',
            Email: 'anna.thompson@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-01-20'
        },
        {
            id: 'USR005',
            UserName: 'David Garcia',
            Email: 'david.garcia@example.com',
            Role: 'User',
            Status: 'Inactive',
            DateCreated: '2024-04-12'
        },
        {
            id: 'USR006',
            UserName: 'Sofia Martinez',
            Email: 'sofia.martinez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-05-08'
        },
        {
            id: 'USR007',
            UserName: 'Robert Lee',
            Email: 'robert.lee@example.com',
            Role: 'Admin',
            Status: 'Active',
            DateCreated: '2024-02-25'
        },
        {
            id: 'USR008',
            UserName: 'Elena Vasquez',
            Email: 'elena.vasquez@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-06-01'
        },
        {
            id: 'USR009',
            UserName: 'Michael Chen',
            Email: 'michael.chen@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-03-18'
        },
        {
            id: 'USR010',
            UserName: 'Patricia Johnson',
            Email: 'patricia.johnson@example.com',
            Role: 'User',
            Status: 'Inactive',
            DateCreated: '2024-04-30'
        },
        {
            id: 'USR011',
            UserName: 'James Wilson',
            Email: 'james.wilson@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-05-22'
        },
        {
            id: 'USR012',
            UserName: 'Linda Brown',
            Email: 'linda.brown@example.com',
            Role: 'User',
            Status: 'Active',
            DateCreated: '2024-06-15'
        }
    ];

    const tableBody = document.getElementById('userTable');
    let rowsPerPage = 10;
    let currentPage = 1;
    let filteredUsers = [...userAllData];

    const searchInput = document.getElementById('searchInput');
    const pageSizeSelect = document.getElementById('userPageSize');
    const prevPageBtn = document.getElementById('userPrevBtn');
    const nextPageBtn = document.getElementById('userNextBtn');
    const currentPageSpan = document.getElementById('userCurrentPage');
    const totalPagesSpan = document.getElementById('userTotalPages');
    const totalRecordsSpan = document.getElementById('userTotalRecords');

    function renderTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageUsers = filteredUsers.slice(start, end);

        const template = document.getElementById('user-row-template');
        pageUsers.forEach(user => {
            const clone = template.content.firstElementChild.cloneNode(true);

            clone.querySelector('[data-col="id"]').textContent = user.id;
            
            // Name with avatar
            const nameCell = clone.querySelector('[data-col="name"]');
            nameCell.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${user.UserName || user.name || 'N/A'}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${user.Email || 'N/A'}</div>
                    </div>
                </div>
            `;
            
            clone.querySelector('[data-col="email"]').textContent = user.Email || 'N/A';
            // Role badge with icon
            const roleCell = clone.querySelector('[data-col="role"]');
            const roleIcon = user.Role === 'Admin' ? 'user-shield' : 'user';
            roleCell.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    <i class="fas fa-${roleIcon} mr-1"></i>
                    ${user.Role}
                </span>
            `;
            clone.querySelector('[data-col="date"]').textContent = new Date(user.DateCreated).toLocaleDateString();

            // Status badge with icon
            const statusCell = clone.querySelector('[data-col="status"]');
            const statusIcon = user.Status === 'Active' ? 'check-circle' : 'times-circle';
            const statusClass = user.Status === 'Active'
                ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
            statusCell.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                    <i class="fas fa-${statusIcon} mr-1"></i>
                    ${user.Status}
                </span>
            `;

            // Actions
            const actionsCell = clone.querySelector('[data-col="actions"]');
            actionsCell.innerHTML = '';

            const editBtn = document.createElement('button');
            editBtn.className = 'text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded transition-colors';
            editBtn.title = 'Edit';
            editBtn.innerHTML = '<i class="fas fa-edit"></i>';
            editBtn.addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('show-edit-user', { detail: user }));
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-user' }));
            });

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'text-red-600 hover:text-red-900 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded transition-colors ml-1';
            deleteBtn.title = 'Delete';
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            deleteBtn.addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('show-delete-user', { detail: user.id }));
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-user' }));
            });

            const viewBtn = document.createElement('button');
            viewBtn.className = 'text-gray-600 hover:text-gray-900 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/20 p-2 rounded transition-colors ml-1';
            viewBtn.title = 'View Details';
            viewBtn.innerHTML = '<i class="fas fa-eye"></i>';
            viewBtn.addEventListener('click', () => {
                window.dispatchEvent(new CustomEvent('show-view-user', { detail: user }));
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'view-user' }));
            });

            actionsCell.appendChild(editBtn);
            actionsCell.appendChild(deleteBtn);
            actionsCell.appendChild(viewBtn);

            tableBody.appendChild(clone);
        });

        updatePagination();
    }

    // --- Search ---
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            const query = e.target.value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter')?.value || '';
            filteredUsers = userAllData.filter(u => {
                const matchesSearch = u.id.toLowerCase().includes(query) ||
                    u.UserName.toLowerCase().includes(query) ||
                    u.Email.toLowerCase().includes(query) ||
                    u.Role.toLowerCase().includes(query);
                const matchesRole = !roleFilter || u.Role === roleFilter;
                return matchesSearch && matchesRole;
            });
            currentPage = 1;
            renderTable();
        });
    }
    
    // --- Role Filter ---
    window.filterUsers = function() {
        const query = searchInput?.value.toLowerCase() || '';
        const roleFilter = document.getElementById('roleFilter')?.value || '';
        filteredUsers = userAllData.filter(u => {
            const matchesSearch = u.id.toLowerCase().includes(query) ||
                u.UserName.toLowerCase().includes(query) ||
                u.Email.toLowerCase().includes(query) ||
                u.Role.toLowerCase().includes(query);
            const matchesRole = !roleFilter || u.Role === roleFilter;
            return matchesSearch && matchesRole;
        });
        currentPage = 1;
        renderTable();
    };
    
    // --- Export Functions ---
    window.exportPDF = function() {
        console.log('Exporting to PDF...');
        alert('PDF export functionality - Coming soon!');
    };
    
    window.exportExcel = function() {
        console.log('Exporting to Excel...');
        alert('Excel export functionality - Coming soon!');
    };

    function updatePagination() {
        const totalPages = Math.ceil(filteredUsers.length / rowsPerPage) || 1;
        const start = Math.min((currentPage - 1) * rowsPerPage + 1, filteredUsers.length);
        const end = Math.min(currentPage * rowsPerPage, filteredUsers.length);
        
        if (currentPageSpan) currentPageSpan.textContent = currentPage;
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
        if (totalRecordsSpan) totalRecordsSpan.textContent = `${start} to ${end} of ${filteredUsers.length}`;
        
        if (prevPageBtn) {
            prevPageBtn.disabled = currentPage === 1;
        }
        if (nextPageBtn) {
            nextPageBtn.disabled = currentPage >= totalPages;
        }
    }
    
    // Create pagination object
    window.userPagination = {
        prevPage: function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        },
        nextPage: function() {
            const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        },
        updatePageSize: function(newSize) {
            rowsPerPage = parseInt(newSize) || 10;
            currentPage = 1;
            renderTable();
        }
    };

    // --- Initial render ---
    renderTable();

    // --- Global event handlers for save/delete ---
    window.addEventListener('save-user', e => {
        const updated = e.detail;
        if (!updated || !updated.id) return;
        const idx = userAllData.findIndex(u => u.id === updated.id);
        if (idx > -1) {
            userAllData[idx] = { ...userAllData[idx], ...updated };
        } else {
            userAllData.unshift(updated);
        }
        // refresh table
        try { renderTable(); } catch (err) { /* ignore */ }
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-user' }));
        window.dispatchEvent(new CustomEvent('show-alert', { detail: { type: 'success', message: 'User updated successfully' } }));
    });

    window.addEventListener('confirm-delete-user', e => {
        const id = e.detail;
        if (!id) return;
        const idx = userAllData.findIndex(u => u.id === id);
        if (idx > -1) userAllData.splice(idx, 1);
        try { renderTable(); } catch (err) { /* ignore */ }
        window.dispatchEvent(new CustomEvent('close-modal', { detail: 'delete-user' }));
        window.dispatchEvent(new CustomEvent('show-alert', { detail: { type: 'success', message: 'User deleted successfully' } }));
    });

})();
