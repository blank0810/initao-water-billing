<div id="viewRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Role Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View role information and permissions</p>
                </div>
            </div>
            <button onclick="closeViewRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Role Info -->
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <h4 id="viewRoleName" class="text-2xl font-bold text-gray-900 dark:text-white"></h4>
                    <span id="viewRoleSystemBadge" class="hidden px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">System Role</span>
                </div>
                <p id="viewRoleDescription" class="text-gray-600 dark:text-gray-400"></p>
            </div>

            <!-- Users Section -->
            <div class="mb-6">
                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-users text-gray-400"></i>
                    Assigned Users (<span id="viewRoleUsersCount">0</span>)
                </h5>
                <div id="viewRoleUsersList" class="space-y-2 max-h-32 overflow-y-auto">
                    <!-- Users will be populated here -->
                </div>
                <div id="viewRoleNoUsers" class="hidden text-sm text-gray-500 dark:text-gray-400 italic">
                    No users assigned to this role
                </div>
            </div>

            <!-- Permissions Section -->
            <div>
                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-key text-gray-400"></i>
                    Permissions (<span id="viewRolePermissionsCount">0</span>)
                </h5>
                <div id="viewRoleSuperAdminNote" class="hidden mb-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        Super Admin bypasses all permission checks and has full system access.
                    </p>
                </div>
                <div id="viewRolePermissionsList" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <!-- Permissions will be populated here -->
                </div>
                <div id="viewRoleNoPermissions" class="hidden text-sm text-gray-500 dark:text-gray-400 italic">
                    No permissions assigned to this role
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeViewRoleModal()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                Close
            </button>
            <button onclick="editRoleFromView()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                <i class="fas fa-edit mr-2"></i>Edit Role
            </button>
        </div>
    </div>
</div>

<script>
    let currentViewRoleId = null;

    function showViewRoleModal(roleData) {
        currentViewRoleId = roleData.role_id;

        // Set role name (formatted)
        document.getElementById('viewRoleName').textContent =
            roleData.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        // Set description
        document.getElementById('viewRoleDescription').textContent =
            roleData.description || 'No description provided';

        // System role badge
        const systemBadge = document.getElementById('viewRoleSystemBadge');
        if (roleData.is_system_role) {
            systemBadge.classList.remove('hidden');
        } else {
            systemBadge.classList.add('hidden');
        }

        // Super admin note
        const superAdminNote = document.getElementById('viewRoleSuperAdminNote');
        if (roleData.is_super_admin) {
            superAdminNote.classList.remove('hidden');
        } else {
            superAdminNote.classList.add('hidden');
        }

        // Users list
        const usersList = document.getElementById('viewRoleUsersList');
        const noUsers = document.getElementById('viewRoleNoUsers');
        const usersCount = document.getElementById('viewRoleUsersCount');

        usersCount.textContent = roleData.users ? roleData.users.length : 0;
        usersList.innerHTML = '';

        if (roleData.users && roleData.users.length > 0) {
            noUsers.classList.add('hidden');
            roleData.users.forEach(user => {
                usersList.innerHTML += `
                    <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${user.email}</p>
                        </div>
                    </div>
                `;
            });
        } else {
            noUsers.classList.remove('hidden');
        }

        // Permissions list
        const permissionsList = document.getElementById('viewRolePermissionsList');
        const noPermissions = document.getElementById('viewRoleNoPermissions');
        const permissionsCount = document.getElementById('viewRolePermissionsCount');

        permissionsCount.textContent = roleData.is_super_admin ? 'All' : (roleData.permissions ? roleData.permissions.length : 0);
        permissionsList.innerHTML = '';

        if (roleData.is_super_admin) {
            noPermissions.classList.add('hidden');
            permissionsList.innerHTML = `
                <div class="col-span-2 text-center py-4 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-infinity text-2xl mb-2"></i>
                    <p>All permissions (bypass mode)</p>
                </div>
            `;
        } else if (roleData.permissions && roleData.permissions.length > 0) {
            noPermissions.classList.add('hidden');
            roleData.permissions.forEach(perm => {
                permissionsList.innerHTML += `
                    <div class="flex items-center gap-2 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">${perm.permission_name}</span>
                    </div>
                `;
            });
        } else {
            noPermissions.classList.remove('hidden');
        }

        document.getElementById('viewRoleModal').classList.remove('hidden');
    }

    function closeViewRoleModal() {
        document.getElementById('viewRoleModal').classList.add('hidden');
        currentViewRoleId = null;
    }

    function editRoleFromView() {
        if (currentViewRoleId) {
            closeViewRoleModal();
            editRole(currentViewRoleId);
        }
    }

    // Listen for custom events
    window.addEventListener('show-view-role', (e) => showViewRoleModal(e.detail));
</script>
