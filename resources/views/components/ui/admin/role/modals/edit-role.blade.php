<div id="editRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-edit text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Role</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Modify role details and permissions</p>
                </div>
            </div>
            <button onclick="closeEditRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <form id="editRoleForm" class="p-6 overflow-y-auto flex-1">
            <input type="hidden" id="editRoleId">

            <!-- Role Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editRoleName" name="role_name" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 dark:disabled:bg-gray-600 disabled:cursor-not-allowed">
                <p id="editRoleNameNote" class="hidden mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                    <i class="fas fa-info-circle mr-1"></i>System role names cannot be changed
                </p>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea id="editRoleDescription" name="description" rows="2"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>

            <!-- Super Admin Warning -->
            <div id="editRoleSuperAdminWarning" class="hidden mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Super Admin has all permissions by default and cannot be modified.
                </p>
            </div>

            <!-- Permissions -->
            <div id="editRolePermissionsSection">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Assign Permissions
                </label>
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="selectAllEditPermissions()" class="text-xs px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                        <i class="fas fa-check-double mr-1"></i>Select All
                    </button>
                    <button type="button" onclick="deselectAllEditPermissions()" class="text-xs px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>Deselect All
                    </button>
                </div>
                <div id="editRolePermissions" class="space-y-4 max-h-64 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <!-- Permissions will be populated by JavaScript -->
                </div>
            </div>
        </form>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeEditRoleModal()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                Cancel
            </button>
            <button onclick="updateRole()" type="button" id="editRoleSaveBtn"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
</div>

<script>
    let currentEditRoleData = null;

    async function editRole(roleId) {
        try {
            const response = await fetch(`/admin/roles/${roleId}`);
            const result = await response.json();
            const roleData = result.data;

            currentEditRoleData = roleData;

            document.getElementById('editRoleId').value = roleData.role_id;
            document.getElementById('editRoleName').value = roleData.role_name;
            document.getElementById('editRoleDescription').value = roleData.description || '';

            // Handle system roles
            const roleNameInput = document.getElementById('editRoleName');
            const roleNameNote = document.getElementById('editRoleNameNote');
            const superAdminWarning = document.getElementById('editRoleSuperAdminWarning');
            const permissionsSection = document.getElementById('editRolePermissionsSection');
            const saveBtn = document.getElementById('editRoleSaveBtn');

            if (roleData.is_super_admin) {
                roleNameInput.disabled = true;
                roleNameNote.classList.remove('hidden');
                superAdminWarning.classList.remove('hidden');
                permissionsSection.classList.add('hidden');
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                // Check if it's a system role (but not super admin)
                const systemRoles = ['admin', 'billing_officer', 'meter_reader', 'cashier', 'viewer'];
                if (systemRoles.includes(roleData.role_name)) {
                    roleNameInput.disabled = true;
                    roleNameNote.classList.remove('hidden');
                } else {
                    roleNameInput.disabled = false;
                    roleNameNote.classList.add('hidden');
                }

                superAdminWarning.classList.add('hidden');
                permissionsSection.classList.remove('hidden');
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Populate permissions with current assignments
                const currentPermissions = roleData.permissions.map(p => p.permission_name);
                populatePermissionCheckboxes('editRolePermissions', currentPermissions);
            }

            document.getElementById('editRoleModal').classList.remove('hidden');
        } catch (error) {
            console.error('Error:', error);
            showRoleAlert('error', 'Error loading role data');
        }
    }

    function closeEditRoleModal() {
        document.getElementById('editRoleModal').classList.add('hidden');
        currentEditRoleData = null;
    }

    function selectAllEditPermissions() {
        document.querySelectorAll('#editRolePermissions input[type="checkbox"]').forEach(cb => cb.checked = true);
    }

    function deselectAllEditPermissions() {
        document.querySelectorAll('#editRolePermissions input[type="checkbox"]').forEach(cb => cb.checked = false);
    }

    async function updateRole() {
        const roleId = document.getElementById('editRoleId').value;
        const roleName = document.getElementById('editRoleName').value.trim();
        const description = document.getElementById('editRoleDescription').value.trim();
        const permissions = getCheckedPermissions('editRolePermissions');

        try {
            const response = await fetch(`/admin/roles/${roleId}`, {
                method: 'PUT',
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
                closeEditRoleModal();
                showRoleAlert('success', result.message);
                fetchRoles();
            } else {
                showRoleAlert('error', result.message || 'Error updating role');
            }
        } catch (error) {
            console.error('Error:', error);
            showRoleAlert('error', 'An error occurred while updating the role');
        }
    }
</script>
