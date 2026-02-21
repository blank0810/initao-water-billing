/**
 * User Management List
 * Fetches users from API and handles CRUD operations
 */
(function () {
    'use strict';

    if (!document.getElementById('userTable')) return;

    // State
    let users = [];
    let filteredUsers = [];
    let roles = [];
    let rowsPerPage = 10;
    let currentPage = 1;
    let isLoading = false;

    // DOM Elements
    const tableBody = document.getElementById('userTable');
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const prevPageBtn = document.getElementById('userPrevBtn');
    const nextPageBtn = document.getElementById('userNextBtn');
    const currentPageSpan = document.getElementById('userCurrentPage');
    const totalPagesSpan = document.getElementById('userTotalPages');
    const totalRecordsSpan = document.getElementById('userTotalRecords');

    // CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    // Show loading state with skeleton
    function showLoading() {
        isLoading = true;
        const skeletonHTML = `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 animate-pulse">
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-8"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-28"></div></td>
                <td class="px-4 py-3"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-16"></div></td>
                <td class="px-4 py-3 text-center"><div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto"></div></td>
            </tr>
        `;
        tableBody.innerHTML = skeletonHTML;
    }

    // Show error state
    function showError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-exclamation-circle text-3xl text-red-500 mb-3"></i>
                        <span class="text-red-600 dark:text-red-400">${message}</span>
                        <button onclick="window.userManager.refresh()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            <i class="fas fa-redo mr-2"></i>Retry
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    // Show empty state
    function showEmpty() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-users text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <span class="text-gray-500 dark:text-gray-400">No users found</span>
                    </div>
                </td>
            </tr>
        `;
    }

    // Fetch users from API
    async function fetchUsers() {
        showLoading();
        try {
            const response = await fetch('/api/users', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch users');
            }

            const result = await response.json();
            users = result.data || [];
            filteredUsers = [...users];
            filterUsers();
        } catch (error) {
            console.error('Error fetching users:', error);
            showError('Failed to load users. Please try again.');
        } finally {
            isLoading = false;
        }
    }

    // Fetch roles for filter dropdown
    async function fetchRoles() {
        try {
            const response = await fetch('/api/roles/available', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            if (response.ok) {
                const result = await response.json();
                roles = result.data || [];
                populateRoleFilter();
            }
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    }

    // Populate role filter dropdown
    function populateRoleFilter() {
        if (!roleFilter) return;

        // Keep the "All Roles" option
        roleFilter.innerHTML = '<option value="">All Roles</option>';

        roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.role_name;
            option.textContent = role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            roleFilter.appendChild(option);
        });
    }

    // Render table
    function renderTable() {
        if (filteredUsers.length === 0) {
            showEmpty();
            updatePagination();
            return;
        }

        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageUsers = filteredUsers.slice(start, end);

        const template = document.getElementById('user-row-template');

        pageUsers.forEach(user => {
            const clone = template.content.firstElementChild.cloneNode(true);

            // ID
            clone.querySelector('[data-col="id"]').textContent = user.id;

            // Name with avatar
            const nameCell = clone.querySelector('[data-col="name"]');
            nameCell.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                        <img src="${user.photo_url || '/images/logo.png'}" class="w-full h-full object-cover" alt="${user.name || 'User'}">
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${user.name || 'N/A'}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${user.email || 'N/A'}</div>
                    </div>
                </div>
            `;

            // Email
            clone.querySelector('[data-col="email"]').textContent = user.email || 'N/A';

            // Role badge
            const roleCell = clone.querySelector('[data-col="role"]');
            const roleName = user.role?.display_name || user.role?.role_name || 'No Role';
            const isAdmin = user.role?.role_name?.includes('admin');
            const roleIcon = isAdmin ? 'user-shield' : 'user';
            roleCell.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    <i class="fas fa-${roleIcon} mr-1"></i>
                    ${roleName}
                </span>
            `;

            // Date
            clone.querySelector('[data-col="date"]').textContent = user.created_at_formatted || 'N/A';

            // Status badge
            const statusCell = clone.querySelector('[data-col="status"]');
            const isActive = user.status?.toLowerCase() === 'active';
            const statusIcon = isActive ? 'check-circle' : 'times-circle';
            const statusClass = isActive
                ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
            statusCell.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                    <i class="fas fa-${statusIcon} mr-1"></i>
                    ${user.status || 'Unknown'}
                </span>
            `;

            // Actions
            const actionsCell = clone.querySelector('[data-col="actions"]');
            actionsCell.innerHTML = buildActionButtons(user);

            tableBody.appendChild(clone);
        });

        // Attach event listeners to action buttons
        attachActionListeners();
        updatePagination();
    }

    // Build action buttons HTML
    function buildActionButtons(user) {
        return `
            <button class="view-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900/20 p-2 rounded transition-colors"
                title="View Details" data-user-id="${user.id}">
                <i class="fas fa-eye"></i>
            </button>
            <button class="edit-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-2 rounded transition-colors"
                title="Edit" data-user-id="${user.id}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="delete-btn text-red-600 hover:text-red-900 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded transition-colors"
                title="Delete" data-user-id="${user.id}">
                <i class="fas fa-trash"></i>
            </button>
        `;
    }

    // Attach event listeners to action buttons
    function attachActionListeners() {
        // View buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = btn.dataset.userId;
                const user = users.find(u => u.id == userId);
                if (user) {
                    showViewUserModal(user);
                }
            });
        });

        // Edit buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = btn.dataset.userId;
                const user = users.find(u => u.id == userId);
                if (user) {
                    showEditUserModal(user);
                }
            });
        });

        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = btn.dataset.userId;
                const user = users.find(u => u.id == userId);
                if (user) {
                    showDeleteUserModal(user);
                }
            });
        });
    }

    // Filter users
    function filterUsers() {
        const query = searchInput?.value.toLowerCase() || '';
        const roleValue = roleFilter?.value || '';

        filteredUsers = users.filter(u => {
            const matchesSearch =
                String(u.id).toLowerCase().includes(query) ||
                (u.name || '').toLowerCase().includes(query) ||
                (u.email || '').toLowerCase().includes(query) ||
                (u.role?.role_name || '').toLowerCase().includes(query);

            const matchesRole = !roleValue || u.role?.role_name === roleValue;

            return matchesSearch && matchesRole;
        });

        currentPage = 1;
        renderTable();
    }

    // Update pagination
    function updatePagination() {
        const totalPages = Math.ceil(filteredUsers.length / rowsPerPage) || 1;
        const start = filteredUsers.length > 0 ? (currentPage - 1) * rowsPerPage + 1 : 0;
        const end = Math.min(currentPage * rowsPerPage, filteredUsers.length);

        if (currentPageSpan) currentPageSpan.textContent = currentPage;
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
        if (totalRecordsSpan) totalRecordsSpan.textContent = `${start} to ${end} of ${filteredUsers.length}`;

        if (prevPageBtn) prevPageBtn.disabled = currentPage === 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage >= totalPages;
    }

    // Save user (update)
    async function saveUser(userData) {
        try {
            const response = await fetch(`/user/${userData.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify(userData),
            });

            const result = await response.json();

            if (result.success) {
                await fetchUsers();
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-user' }));
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'success', message: result.message || 'User updated successfully' }
                }));
            } else {
                throw new Error(result.message || 'Failed to update user');
            }
        } catch (error) {
            console.error('Error saving user:', error);
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type: 'error', message: error.message || 'Failed to update user' }
            }));
        }
    }

    // Delete user
    async function deleteUser(userId) {
        try {
            const response = await fetch(`/user/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            const result = await response.json();

            if (result.success) {
                await fetchUsers();
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'delete-user' }));
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'success', message: result.message || 'User deleted successfully' }
                }));
            } else {
                throw new Error(result.message || 'Failed to delete user');
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type: 'error', message: error.message || 'Failed to delete user' }
            }));
        }
    }

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', filterUsers);
    }

    // Global event handlers
    window.addEventListener('save-user', e => {
        if (e.detail) saveUser(e.detail);
    });

    window.addEventListener('confirm-delete-user', e => {
        if (e.detail) deleteUser(e.detail.id || e.detail);
    });

    // Expose functions globally
    window.userManager = {
        refresh: fetchUsers,
        filter: filterUsers,
    };

    window.filterUsers = filterUsers;

    window.userPagination = {
        prevPage: function () {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        },
        nextPage: function () {
            const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        },
        updatePageSize: function (newSize) {
            rowsPerPage = parseInt(newSize) || 10;
            currentPage = 1;
            renderTable();
        },
    };

    // Export functions (placeholders)
    window.exportPDF = function () {
        alert('PDF export functionality - Coming soon!');
    };

    window.exportExcel = function () {
        alert('Excel export functionality - Coming soon!');
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        fetchUsers();
        fetchRoles();
    });

    // Also run immediately if DOM is already loaded
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        fetchUsers();
        fetchRoles();
    }
})();
