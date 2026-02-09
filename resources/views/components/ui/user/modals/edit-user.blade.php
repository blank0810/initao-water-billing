<!-- Edit User Modal -->
<div id="editUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @edit-user.window="show = true">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-edit text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit User</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Update user information</p>
                </div>
            </div>
            <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editUserForm" class="p-6 space-y-4">
            <input type="hidden" id="editUserId">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                <input type="text" id="editUserName" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username *</label>
                <input type="text" id="editUserUsername" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input type="email" id="editUserEmail" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Optional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role *</label>
                <select id="editUserRole" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Loading roles...</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                <select id="editUserStatus" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password (leave blank to keep current)</label>
                <input type="password" id="editUserPassword" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Enter new password">
            </div>
        </form>

        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button onclick="closeEditUserModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </button>
            <button id="saveUserBtn" onclick="saveUser()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
</div>

<script>
let editUserRoles = [];

// Fetch roles for the dropdown
async function fetchEditUserRoles() {
    try {
        const response = await fetch('/api/roles/available', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });

        if (response.ok) {
            const result = await response.json();
            editUserRoles = result.data || [];
            populateEditRoleDropdown();
        }
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
}

function populateEditRoleDropdown(selectedRoleId = null) {
    const roleSelect = document.getElementById('editUserRole');
    roleSelect.innerHTML = '<option value="">Select Role</option>';

    editUserRoles.forEach(role => {
        const option = document.createElement('option');
        option.value = role.role_id;
        option.textContent = role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        if (selectedRoleId && role.role_id == selectedRoleId) {
            option.selected = true;
        }
        roleSelect.appendChild(option);
    });
}

function showEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name || user.UserName || '';
    document.getElementById('editUserUsername').value = user.username || '';
    document.getElementById('editUserEmail').value = user.email || user.Email || '';
    document.getElementById('editUserPassword').value = '';

    // Set status
    const status = (user.status || user.Status || '').toLowerCase();
    document.getElementById('editUserStatus').value = status === 'active' ? 'active' : 'inactive';

    // Populate roles and select current role
    if (editUserRoles.length > 0) {
        const roleId = user.role?.role_id || null;
        populateEditRoleDropdown(roleId);
    } else {
        fetchEditUserRoles().then(() => {
            const roleId = user.role?.role_id || null;
            populateEditRoleDropdown(roleId);
        });
    }

    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    document.getElementById('editUserForm').reset();
}

async function saveUser() {
    const form = document.getElementById('editUserForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const password = document.getElementById('editUserPassword').value;
    const email = document.getElementById('editUserEmail').value;

    const userData = {
        id: document.getElementById('editUserId').value,
        name: document.getElementById('editUserName').value,
        username: document.getElementById('editUserUsername').value,
        email: email ? email.trim() : null,
        role_id: parseInt(document.getElementById('editUserRole').value),
        status: document.getElementById('editUserStatus').value,
    };

    // Only include password if it was changed
    if (password) {
        userData.password = password;
    }

    // Show loading state
    const saveBtn = document.getElementById('saveUserBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    saveBtn.disabled = true;

    try {
        const response = await fetch(`/user/${userData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(userData),
        });

        const result = await response.json();

        if (result.success) {
            closeEditUserModal();
            window.dispatchEvent(new CustomEvent('show-alert', {
                detail: { type: 'success', message: result.message || 'User updated successfully' }
            }));
            // Refresh user list
            if (window.userManager?.refresh) {
                window.userManager.refresh();
            }
        } else {
            // Handle validation errors
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat();
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', message: errorMessages.join(', ') }
                }));
            } else {
                window.dispatchEvent(new CustomEvent('show-alert', {
                    detail: { type: 'error', message: result.message || 'Failed to update user' }
                }));
            }
        }
    } catch (error) {
        console.error('Error saving user:', error);
        window.dispatchEvent(new CustomEvent('show-alert', {
            detail: { type: 'error', message: 'Network error. Please try again.' }
        }));
    } finally {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
}

// Listen for show-edit-user event
window.addEventListener('show-edit-user', function(e) {
    showEditUserModal(e.detail);
});

// Pre-fetch roles when page loads
document.addEventListener('DOMContentLoaded', fetchEditUserRoles);
</script>
