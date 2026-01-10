<div id="viewPermissionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-key text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Permission Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View permission information</p>
                </div>
            </div>
            <button onclick="closeViewPermissionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Permission Info -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <span id="viewPermissionModule" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200"></span>
                </div>
                <h4 id="viewPermissionName" class="text-lg font-bold text-gray-900 dark:text-white mb-2"></h4>
                <p id="viewPermissionDescription" class="text-gray-600 dark:text-gray-400 text-sm"></p>
            </div>

            <!-- Roles with this permission -->
            <div>
                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <i class="fas fa-user-shield text-gray-400"></i>
                    Roles with this Permission (<span id="viewPermissionRolesCount">0</span>)
                </h5>
                <div id="viewPermissionRolesList" class="space-y-2 max-h-48 overflow-y-auto">
                    <!-- Roles will be populated here -->
                </div>
                <div id="viewPermissionNoRoles" class="hidden text-sm text-gray-500 dark:text-gray-400 italic py-4 text-center">
                    No roles have this permission
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeViewPermissionModal()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showViewPermissionModal(permissionData) {
        document.getElementById('viewPermissionModule').textContent = permissionData.module;
        document.getElementById('viewPermissionName').textContent = permissionData.permission_name;
        document.getElementById('viewPermissionDescription').textContent = permissionData.description || 'No description provided';

        const rolesList = document.getElementById('viewPermissionRolesList');
        const noRoles = document.getElementById('viewPermissionNoRoles');
        const rolesCount = document.getElementById('viewPermissionRolesCount');

        rolesCount.textContent = permissionData.roles ? permissionData.roles.length : 0;
        rolesList.innerHTML = '';

        if (permissionData.roles && permissionData.roles.length > 0) {
            noRoles.classList.add('hidden');
            permissionData.roles.forEach(role => {
                rolesList.innerHTML += `
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${role.description || ''}</p>
                        </div>
                    </div>
                `;
            });
        } else {
            noRoles.classList.remove('hidden');
        }

        document.getElementById('viewPermissionModal').classList.remove('hidden');
    }

    function closeViewPermissionModal() {
        document.getElementById('viewPermissionModal').classList.add('hidden');
    }
</script>
