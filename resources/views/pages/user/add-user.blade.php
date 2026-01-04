<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <x-ui.page-header
                title="Add New User"
                subtitle="Create a new system user account"
                :breadcrumbs="[
                    ['label' => 'User Management'],
                    ['label' => 'Add User']
                ]"
            >
                <x-slot name="actions">
                    <x-ui.button variant="outline" href="{{ route('user.list') }}">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <form id="userRegistrationForm" class="p-8" x-data="{ avatarPreview: null }">
                    @csrf

                    <!-- Avatar Upload Section -->
                    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-image mr-2 text-blue-600"></i>Profile Picture
                        </h3>
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border-4 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                    <template x-if="!avatarPreview">
                                        <i class="fas fa-user text-5xl text-gray-400"></i>
                                    </template>
                                    <template x-if="avatarPreview">
                                        <img :src="avatarPreview" class="w-full h-full object-cover" alt="Avatar preview">
                                    </template>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" 
                                    @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => avatarPreview = e.target.result; reader.readAsDataURL(file); }">
                                <label for="avatar" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg cursor-pointer transition-colors">
                                    <i class="fas fa-upload mr-2"></i>Upload Photo
                                </label>
                                <button type="button" @click="avatarPreview = null; document.getElementById('avatar').value = ''" 
                                    x-show="avatarPreview" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                    <i class="fas fa-times mr-2"></i>Remove
                                </button>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">JPG, PNG or GIF (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                                <input type="text" name="first_name" required placeholder="Enter first name" 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                                <input type="text" name="last_name" required placeholder="Enter last name" 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                                <input type="email" name="email" required placeholder="email@example.com" 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                                <input type="password" name="password" required placeholder="Enter password" 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                            <i class="fas fa-user-shield mr-2 text-blue-600"></i>Role & Status
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Role *</label>
                                <select name="role_id" id="roleSelect" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">Loading roles...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                                <select name="status" required 
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="window.history.back()" 
                            class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/data/user/add-user.js')

    <script>
        // Fetch roles and populate dropdown
        document.addEventListener('DOMContentLoaded', async function() {
            const roleSelect = document.getElementById('roleSelect');
            try {
                const response = await fetch('/api/roles/available', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
                if (response.ok) {
                    const result = await response.json();
                    roleSelect.innerHTML = '<option value="">Select Role</option>';
                    (result.data || []).forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.role_id;
                        option.textContent = role.role_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        roleSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error fetching roles:', error);
                roleSelect.innerHTML = '<option value="">Failed to load roles</option>';
            }

            // Handle form submission
            const form = document.getElementById('userRegistrationForm');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const firstName = formData.get('first_name') || '';
                const lastName = formData.get('last_name') || '';

                const data = {
                    name: `${firstName} ${lastName}`.trim(),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    password_confirmation: formData.get('password'),
                    role_id: parseInt(formData.get('role_id')),
                    status: formData.get('status'),
                };

                const submitBtn = form.querySelector('[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('/user', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify(data),
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('User created successfully!');
                        window.location.href = '/user/list';
                    } else {
                        const errors = result.errors ? Object.values(result.errors).flat().join(', ') : result.message;
                        alert('Error: ' + errors);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network error. Please try again.');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    </script>
</x-app-layout>
