<!-- View User Modal -->
<div id="viewUserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 bg-white dark:bg-gray-900 rounded-t-2xl">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Details</h3>
            <button onclick="closeViewUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="overflow-y-auto flex-1 p-6 bg-white dark:bg-gray-900">
            <div class="flex flex-col items-center mb-6">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mb-4 border-2 border-gray-200 dark:border-gray-600">
                    <img id="viewUserAvatar" src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="User avatar">
                </div>
                <h4 id="viewUserName" class="text-xl font-semibold text-gray-900 dark:text-white"></h4>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">User ID</span>
                    <span id="viewUserId" class="text-sm font-mono text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Username</span>
                    <span id="viewUserUsername" class="text-sm font-mono text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Email</span>
                    <span id="viewUserEmail" class="text-sm text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Role</span>
                    <span id="viewUserRole" class="text-sm text-gray-900 dark:text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                    <div id="viewUserStatus" class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full"></div>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Created On</span>
                    <span id="viewUserDate" class="text-sm text-gray-900 dark:text-white"></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex-shrink-0 rounded-b-2xl">
        </div>
    </div>
</div>

<script>
function showViewUserModal(user) {
    if (!user) return;
    
    document.getElementById('viewUserId').textContent = user.id || 'N/A';
    document.getElementById('viewUserName').textContent = user.name || 'N/A';
    document.getElementById('viewUserUsername').textContent = user.username || 'N/A';
    document.getElementById('viewUserEmail').textContent = user.email || 'N/A';
    document.getElementById('viewUserRole').textContent = user.role?.display_name || user.role?.role_name || 'No Role';
    document.getElementById('viewUserDate').textContent = user.created_at_formatted || 'N/A';

    const statusElement = document.getElementById('viewUserStatus');
    const status = (user.status || '').toLowerCase();
    const isActive = status === 'active';
    statusElement.textContent = isActive ? 'Active' : 'Inactive';
    statusElement.className = `inline-flex px-2.5 py-1 text-xs font-semibold rounded-full ${
        isActive ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
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

window.addEventListener('open-modal', function(e) {
    if (e.detail === 'view-user') {
        document.getElementById('viewUserModal').classList.remove('hidden');
    }
});
</script>
