/**
 * Permissions Management JavaScript
 * Handles viewing permissions grouped by module
 */

(function() {
    'use strict';

    // Check if we're on the permissions page
    if (!document.getElementById('permissionsContainer')) return;

    let permissionsData = [];
    let groupedPermissions = {};
    let searchTimeout = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        fetchPermissions();
        fetchStats();
        setupSearch();
    });

    // Fetch all permissions
    async function fetchPermissions() {
        try {
            const response = await fetch('/api/admin/permissions');
            const result = await response.json();
            permissionsData = result.data;

            // Group by module
            groupedPermissions = {};
            permissionsData.forEach(perm => {
                if (!groupedPermissions[perm.module]) {
                    groupedPermissions[perm.module] = [];
                }
                groupedPermissions[perm.module].push(perm);
            });

            renderPermissions(groupedPermissions);
            document.getElementById('statTotalPermissions').textContent = permissionsData.length;
            document.getElementById('statTotalModules').textContent = Object.keys(groupedPermissions).length;
        } catch (error) {
            console.error('Error fetching permissions:', error);
            showError('Failed to load permissions');
        }
    }

    // Fetch stats
    async function fetchStats() {
        try {
            const response = await fetch('/api/admin/roles');
            const result = await response.json();
            document.getElementById('statTotalRoles').textContent = result.data.length;
        } catch (error) {
            document.getElementById('statTotalRoles').textContent = '-';
        }
    }

    // Setup search
    function setupSearch() {
        const searchInput = document.getElementById('permissionSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = e.target.value.toLowerCase().trim();
                    filterPermissions(query);
                }, 300);
            });
        }
    }

    // Filter permissions
    function filterPermissions(query) {
        if (!query) {
            renderPermissions(groupedPermissions);
            return;
        }

        const filtered = {};
        for (const [module, permissions] of Object.entries(groupedPermissions)) {
            const matchingPerms = permissions.filter(perm =>
                perm.permission_name.toLowerCase().includes(query) ||
                (perm.description && perm.description.toLowerCase().includes(query)) ||
                module.toLowerCase().includes(query)
            );
            if (matchingPerms.length > 0) {
                filtered[module] = matchingPerms;
            }
        }
        renderPermissions(filtered);
    }

    // Render permissions grouped by module
    function renderPermissions(grouped) {
        const container = document.getElementById('permissionsContainer');

        if (Object.keys(grouped).length === 0) {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center border border-gray-200 dark:border-gray-700">
                    <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No permissions found</p>
                </div>
            `;
            return;
        }

        let html = '';

        // Module icons mapping
        const moduleIcons = {
            'Users': 'fa-users',
            'Customers': 'fa-user-tie',
            'Billing': 'fa-file-invoice-dollar',
            'Payments': 'fa-credit-card',
            'Meters': 'fa-tachometer-alt',
            'Reports': 'fa-chart-bar',
            'Settings': 'fa-cog',
        };

        for (const [module, permissions] of Object.entries(grouped)) {
            const icon = moduleIcons[module] || 'fa-folder';

            html += `
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Module Header -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <i class="fas ${icon} text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${module}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${permissions.length} permission${permissions.length !== 1 ? 's' : ''}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
            `;

            permissions.forEach(perm => {
                html += `
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">${perm.permission_name}</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${perm.description || 'No description'}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200">
                                    <i class="fas fa-user-shield mr-1"></i>${perm.roles_count} role${perm.roles_count !== 1 ? 's' : ''}
                                </span>
                                <button onclick="viewPermission(${perm.permission_id})"
                                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += '</div></div>';
        }

        container.innerHTML = html;
    }

    // View permission details
    window.viewPermission = async function(permissionId) {
        try {
            const response = await fetch(`/admin/permissions/${permissionId}`);
            const result = await response.json();
            showViewPermissionModal(result.data);
        } catch (error) {
            console.error('Error:', error);
            showError('Error loading permission details');
        }
    };

    // Show error
    function showError(message) {
        const container = document.getElementById('permissionsContainer');
        container.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-8 text-center border border-red-200 dark:border-red-800">
                <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-3"></i>
                <p class="text-red-700 dark:text-red-300">${message}</p>
            </div>
        `;
    }

})();
