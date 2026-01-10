<div id="createRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-plus text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create New Role</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Define a new role with permissions</p>
                </div>
            </div>
            <button onclick="closeCreateRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <form id="createRoleForm" class="p-6 overflow-y-auto flex-1">
            <!-- Role Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createRoleName" name="role_name" required
                    placeholder="e.g., custom_role"
                    pattern="^[a-z_]+$"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Use lowercase letters and underscores only (e.g., billing_manager)
                </p>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea id="createRoleDescription" name="description" rows="2"
                    placeholder="Brief description of this role's purpose..."
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Assign Permissions
                </label>
                <div id="createRolePermissions" class="space-y-4 max-h-64 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <!-- Permissions will be populated by JavaScript -->
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading permissions...
                    </div>
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeCreateRoleModal()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                Cancel
            </button>
            <button onclick="saveNewRole()" type="button"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                <i class="fas fa-plus mr-2"></i>Create Role
            </button>
        </div>
    </div>
</div>

<script>
    function openCreateRoleModal() {
        // Reset form
        document.getElementById('createRoleForm').reset();

        // Populate permissions
        populatePermissionCheckboxes('createRolePermissions', []);

        document.getElementById('createRoleModal').classList.remove('hidden');
    }

    function closeCreateRoleModal() {
        document.getElementById('createRoleModal').classList.add('hidden');
    }

    async function saveNewRole() {
        const roleName = document.getElementById('createRoleName').value.trim();
        const description = document.getElementById('createRoleDescription').value.trim();
        const permissions = getCheckedPermissions('createRolePermissions');

        // Validation
        if (!roleName) {
            showRoleAlert('error', 'Role name is required');
            return;
        }

        if (!/^[a-z_]+$/.test(roleName)) {
            showRoleAlert('error', 'Role name must be lowercase letters and underscores only');
            return;
        }

        try {
            const response = await fetch('/admin/roles', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    role_name: roleName,
                    description: description,
                    permissions: permissions,
                }),
            });

            const result = await response.json();

            if (result.success) {
                closeCreateRoleModal();
                showRoleAlert('success', result.message);
                fetchRoles();
            } else {
                showRoleAlert('error', result.message || 'Error creating role');
            }
        } catch (error) {
            console.error('Error:', error);
            showRoleAlert('error', 'An error occurred while creating the role');
        }
    }
</script>
