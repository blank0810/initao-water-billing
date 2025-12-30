<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
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
                <form id="userRegistrationForm" class="p-6 md:p-8" x-data="{ avatarPreview: null }">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column: Avatar & Basic Info -->
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Avatar Section -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 text-center border border-gray-100 dark:border-gray-700">
                                <div class="relative inline-block mb-4">
                                    <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 border-4 border-white dark:border-gray-600 shadow-md mx-auto flex items-center justify-center">
                                        <template x-if="!avatarPreview">
                                            <i class="fas fa-user text-5xl text-gray-400"></i>
                                        </template>
                                        <template x-if="avatarPreview">
                                            <img :src="avatarPreview" class="w-full h-full object-cover" alt="Avatar preview">
                                        </template>
                                    </div>
                                    <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full cursor-pointer shadow-lg transition-colors" title="Upload Photo">
                                        <i class="fas fa-camera text-sm"></i>
                                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" 
                                            @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => avatarPreview = e.target.result; reader.readAsDataURL(file); }">
                                    </label>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Profile Picture</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG or GIF (Max 2MB)</p>
                                <button type="button" @click="avatarPreview = null; document.getElementById('avatar').value = ''" 
                                    x-show="avatarPreview" class="mt-3 text-xs text-red-600 hover:text-red-700 dark:text-red-400 font-medium">
                                    Remove Photo
                                </button>
                            </div>
                        </div>

                        <!-- Right Column: Form Fields -->
                        <div class="lg:col-span-2 space-y-8">
                            
                            <!-- Personal Details -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3 text-blue-600 dark:text-blue-400">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    Personal Details
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                                        <input type="text" name="name" required placeholder="e.g. Juan Dela Cruz" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                                        <input type="email" name="email" required placeholder="email@example.com" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                                        <input type="tel" name="phone" required placeholder="e.g. 0912 345 6789" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center mr-3 text-purple-600 dark:text-purple-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    Address Information
                                </h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                                        <input type="text" name="address" required placeholder="House No., Street, Barangay, City/Municipality" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Landmark</label>
                                        <input type="text" name="landmark" placeholder="Near (Famous structure/Area)" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                            </div>

                            <!-- Account Security -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3 text-green-600 dark:text-green-400">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    Account Security
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User Role *</label>
                                        <select name="role" required 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                            <option value="">Select Role</option>
                                            <option value="Admin">Administrator</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Staff">Staff</option>
                                            <option value="Billing, Teller and Meter Reader">Billing, Teller and Meter Reader</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                                        <input type="password" name="password" required placeholder="Enter password" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password *</label>
                                        <input type="password" name="password_confirmation" required placeholder="Confirm password" 
                                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-8 mt-8 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="window.history.back()" 
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-md shadow-blue-500/20 transition-all transform hover:scale-[1.02]">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/user.js')
</x-app-layout>
