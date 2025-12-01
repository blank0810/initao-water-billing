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
            <button onclick="confirmDeleteUser()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                <i class="fas fa-trash mr-2"></i>Delete User
            </button>
        </div>
    </div>
</div>

<script>
let userToDelete = null;

function showDeleteUserModal(userId) {
    userToDelete = userId;
    document.getElementById('deleteUserName').textContent = `User ${userId}`;
    document.getElementById('deleteUserId').textContent = userId;
    document.getElementById('deleteUserEmail').textContent = 'user@example.com';
    document.getElementById('deleteUserRole').textContent = 'User';
    document.getElementById('deleteUserModal').classList.remove('hidden');
}

function closeDeleteUserModal() {
    document.getElementById('deleteUserModal').classList.add('hidden');
    userToDelete = null;
}

function confirmDeleteUser() {
    if (userToDelete) {
        window.dispatchEvent(new CustomEvent('confirm-delete-user', { detail: userToDelete }));
        closeDeleteUserModal();
    }
}

window.addEventListener('show-delete-user', function(e) {
    showDeleteUserModal(e.detail);
});
</script>