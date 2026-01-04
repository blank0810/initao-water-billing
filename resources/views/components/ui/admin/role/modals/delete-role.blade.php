<div id="deleteRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4">

        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-trash text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Delete Role</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </div>
            <button onclick="closeDeleteRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6">
            <input type="hidden" id="deleteRoleId">

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete the role
                    <span id="deleteRoleName" class="font-bold text-gray-900 dark:text-white"></span>?
                </p>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    This will permanently remove the role and all its permission assignments.
                    Users with this role will lose access to associated features.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeDeleteRoleModal()"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                Cancel
            </button>
            <button onclick="confirmDeleteRole()" type="button"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium">
                <i class="fas fa-trash mr-2"></i>Delete Role
            </button>
        </div>
    </div>
</div>

<script>
    function showDeleteRoleModal(roleId, roleName) {
        document.getElementById('deleteRoleId').value = roleId;
        document.getElementById('deleteRoleName').textContent =
            roleName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        document.getElementById('deleteRoleModal').classList.remove('hidden');
    }

    function closeDeleteRoleModal() {
        document.getElementById('deleteRoleModal').classList.add('hidden');
    }

    async function confirmDeleteRole() {
        const roleId = document.getElementById('deleteRoleId').value;

        try {
            const response = await fetch(`/admin/roles/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (result.success) {
                closeDeleteRoleModal();
                showRoleAlert('success', result.message);
                fetchRoles();
            } else {
                showRoleAlert('error', result.message || 'Error deleting role');
            }
        } catch (error) {
            console.error('Error:', error);
            showRoleAlert('error', 'An error occurred while deleting the role');
        }
    }
</script>
