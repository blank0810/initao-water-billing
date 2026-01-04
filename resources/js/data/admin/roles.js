/**
 * Roles Management JavaScript
 * Handles CRUD operations for roles
 */

(function() {
    'use strict';

    // Check if we're on the roles page
    if (!document.getElementById('rolesTableBody')) return;

    let rolesData = [];
    let allPermissions = {};
    let searchTimeout = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        fetchRoles();
        fetchPermissions();
        setupSearch();
    });

    // Fetch all roles from API
    window.fetchRoles = async function() {
        try {
            const response = await fetch('/api/admin/roles');
            const result = await response.json();
            rolesData = result.data;
            renderTable(rolesData);
            updateStats(rolesData);
        } catch (error) {
            console.error('Error fetching roles:', error);
            showRoleAlert('error', 'Failed to load roles');
        }
    };

    // Fetch all permissions for create/edit forms
    window.fetchPermissions = async function() {
        try {
            const response = await fetch('/api/admin/permissions/grouped');
            const result = await response.json();
            allPermissions = result.data;
        } catch (error) {
            console.error('Error fetching permissions:', error);
        }
    };

    // Setup search functionality
    function setupSearch() {
        const searchInput = document.getElementById('roleSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = e.target.value.toLowerCase().trim();
                    const filtered = rolesData.filter(role =>
                        role.role_name.toLowerCase().includes(query) ||
                        (role.description && role.description.toLowerCase().includes(query))
                    );
                    renderTable(filtered);
                }, 300);
            });
        }
    }

    // Render table rows
    function renderTable(roles) {
        const tbody = document.getElementById('rolesTableBody');
        const template = document.getElementById('role-row-template');
        const loadingRow = document.getElementById('loadingRow');

        // Remove loading row
        if (loadingRow) loadingRow.remove();

        // Clear existing rows
        tbody.innerHTML = '';

        if (roles.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user-shield text-4xl opacity-50"></i>
                            <p>No roles found</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        roles.forEach(role => {
            const clone = template.content.firstElementChild.cloneNode(true);

            // Role name
            const roleNameEl = clone.querySelector('[data-field="role_name"]');
            roleNameEl.textContent = formatRoleName(role.role_name);

            // Role type badge
            const roleTypeEl = clone.querySelector('[data-field="role_type"]');
            roleTypeEl.textContent = role.is_system_role ? 'System Role' : 'Custom Role';
            roleTypeEl.className = role.is_system_role
                ? 'text-xs px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 inline-block'
                : 'text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 inline-block';

            // Description
            clone.querySelector('[data-col="description"]').textContent = role.description || '-';

            // Permissions count
            const permSpan = clone.querySelector('[data-col="permissions"] span');
            permSpan.textContent = role.permissions_count;
            if (role.role_name === 'super_admin') {
                permSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200';
            }

            // Users count
            clone.querySelector('[data-col="users"] span').textContent = role.users_count;

            // Actions
            const actionsDiv = clone.querySelector('[data-col="actions"] div');
            actionsDiv.innerHTML = buildActionButtons(role);

            tbody.appendChild(clone);
        });
    }

    // Format role name for display
    function formatRoleName(name) {
        return name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Build action buttons based on role
    function buildActionButtons(role) {
        let html = `
            <button onclick="viewRole(${role.role_id})"
                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                title="View Details">
                <i class="fas fa-eye"></i>
            </button>
            <button onclick="editRole(${role.role_id})"
                class="p-2 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-200 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                title="Edit Role">
                <i class="fas fa-edit"></i>
            </button>
        `;

        if (role.can_delete) {
            html += `
                <button onclick="showDeleteRoleModal(${role.role_id}, '${role.role_name}')"
                    class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                    title="Delete Role">
                    <i class="fas fa-trash"></i>
                </button>
            `;
        } else {
            html += `
                <button disabled
                    class="p-2 text-gray-300 dark:text-gray-600 cursor-not-allowed"
                    title="${role.role_name === 'super_admin' || role.role_name === 'admin' ? 'System role cannot be deleted' : 'Role has assigned users'}">
                    <i class="fas fa-trash"></i>
                </button>
            `;
        }

        return html;
    }

    // Update statistics
    function updateStats(roles) {
        document.getElementById('statTotalRoles').textContent = roles.length;
        document.getElementById('statSystemRoles').textContent = roles.filter(r => r.is_system_role).length;
        document.getElementById('statAssignedUsers').textContent = roles.reduce((sum, r) => sum + (typeof r.users_count === 'number' ? r.users_count : 0), 0);

        // Get permissions count from first fetch or cache
        fetchPermissionCount();
    }

    async function fetchPermissionCount() {
        try {
            const response = await fetch('/api/admin/permissions');
            const result = await response.json();
            document.getElementById('statTotalPermissions').textContent = result.data.length;
        } catch (error) {
            document.getElementById('statTotalPermissions').textContent = '-';
        }
    }

    // View role details
    window.viewRole = async function(roleId) {
        try {
            const response = await fetch(`/admin/roles/${roleId}`);
            const result = await response.json();
            window.dispatchEvent(new CustomEvent('show-view-role', { detail: result.data }));
        } catch (error) {
            console.error('Error:', error);
            showRoleAlert('error', 'Error loading role details');
        }
    };

    // Populate permission checkboxes in modals
    window.populatePermissionCheckboxes = function(containerId, selectedPermissions = []) {
        const container = document.getElementById(containerId);
        if (!container || !Object.keys(allPermissions).length) {
            // Retry if permissions not loaded yet
            setTimeout(() => populatePermissionCheckboxes(containerId, selectedPermissions), 100);
            return;
        }

        let html = '';

        for (const [module, permissions] of Object.entries(allPermissions)) {
            html += `
                <div class="mb-4">
                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                        <i class="fas fa-folder text-gray-400"></i>
                        ${module}
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pl-4">
            `;

            permissions.forEach(perm => {
                const isChecked = selectedPermissions.includes(perm.permission_name);
                html += `
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors">
                        <input type="checkbox" name="permissions[]" value="${perm.permission_name}"
                            ${isChecked ? 'checked' : ''}
                            class="w-4 h-4 text-blue-600 rounded border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">${perm.permission_name}</span>
                    </label>
                `;
            });

            html += '</div></div>';
        }

        container.innerHTML = html;
    };

    // Get checked permissions from a container
    window.getCheckedPermissions = function(containerId) {
        const container = document.getElementById(containerId);
        const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    };

})();
