<!-- View User Modal -->
<div id="viewUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @view-user.window="show = true">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-full overflow-hidden">
                    <img id="viewUserAvatar" src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="User avatar">
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">User Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View user information</p>
                </div>
            </div>
            <button onclick="closeViewUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User ID</label>
                <div id="viewUserId" class="text-sm font-mono text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                <div id="viewUserName" class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                <div id="viewUserUsername" class="text-sm font-mono text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <div id="viewUserEmail" class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                <div id="viewUserRole" class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <div id="viewUserStatus" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"></div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created On</label>
                <div id="viewUserDate" class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeViewUserModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function showViewUserModal(user) {
    document.getElementById('viewUserId').textContent = user.id;
    document.getElementById('viewUserName').textContent = user.name || 'N/A';
    document.getElementById('viewUserUsername').textContent = user.username || 'N/A';
    document.getElementById('viewUserEmail').textContent = user.email || 'N/A';
    document.getElementById('viewUserRole').textContent = user.role?.display_name || user.role?.role_name || 'No Role';
    document.getElementById('viewUserDate').textContent = user.created_at_formatted || 'N/A';

    const statusElement = document.getElementById('viewUserStatus');
    const status = (user.status || '').toLowerCase();
    const isActive = status === 'active';
    statusElement.textContent = isActive ? 'Active' : 'Inactive';
    statusElement.className = `inline-flex px-3 py-1 text-sm font-semibold rounded-full ${
        isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }`;

    document.getElementById('viewUserAvatar').src = user.photo_url || '{{ asset("images/logo.png") }}';

    document.getElementById('viewUserModal').classList.remove('hidden');
}

function closeViewUserModal() {
    document.getElementById('viewUserModal').classList.add('hidden');
}

window.addEventListener('show-view-user', function(e) {
    showViewUserModal(e.detail);
});
</script>