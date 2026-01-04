<!-- Delete User Modal -->
<div id="deleteUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @delete-user.window="show = true">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Delete User</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-gray-900 dark:text-white"></span>? This action cannot be undone.
                </p>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-6">
                    <div class="text-sm text-red-700 dark:text-red-300 space-y-2">
                        <div class="flex justify-between">
                            <span>User ID:</span>
                            <span id="deleteUserId" class="font-medium font-mono"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Email:</span>
                            <span id="deleteUserEmail" class="font-medium"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Role:</span>
                            <span id="deleteUserRole" class="font-medium"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeDeleteUserModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button id="confirmDeleteBtn" onclick="confirmDeleteUser()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                <i class="fas fa-trash mr-2"></i>Delete User
            </button>
        </div>
    </div>
</div>

<script>
let userToDelete = null;

function showDeleteUserModal(user) {
    // Handle both user object and user ID
    if (typeof user === 'object') {
        userToDelete = user;
        document.getElementById('deleteUserName').textContent = user.name || user.UserName || 'this user';
        document.getElementById('deleteUserId').textContent = user.id;
        document.getElementById('deleteUserEmail').textContent = user.email || user.Email || 'N/A';
        document.getElementById('deleteUserRole').textContent = user.role?.display_name || user.role?.role_name || user.Role || 'N/A';
    } else {
        userToDelete = { id: user };
        document.getElementById('deleteUserName').textContent = `User ${user}`;
        document.getElementById('deleteUserId').textContent = user;
        document.getElementById('deleteUserEmail').textContent = 'N/A';
        document.getElementById('deleteUserRole').textContent = 'N/A';
    }
    document.getElementById('deleteUserModal').classList.remove('hidden');
}

function closeDeleteUserModal() {
    document.getElementById('deleteUserModal').classList.add('hidden');
    userToDelete = null;
}

async function confirmDeleteUser() {
    if (!userToDelete) return;

    const userId = userToDelete.id || userToDelete;
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;

    // Show loading state
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...';
    deleteBtn.disabled = true;

    try {
        const response = await fetch(`/user/${userId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });

        const result = await response.json();

        if (result.success) {
            closeDeleteUserModal();
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type: 'success', message: result.message || 'User deleted successfully' }
            }));
            // Refresh user list
            if (window.userManager?.refresh) {
                window.userManager.refresh();
            }
        } else {
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type: 'error', message: result.message || 'Failed to delete user' }
            }));
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        window.dispatchEvent(new CustomEvent('show-alert', {
            detail: { type: 'error', message: 'Network error. Please try again.' }
        }));
    } finally {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}

window.addEventListener('show-delete-user', function(e) {
    showDeleteUserModal(e.detail);
});
</script>
