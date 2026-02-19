<x-app-layout>
    <div x-data="signatoryManager()" class="p-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Document Signatories</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign the official signatory for each position. These appear on printed documents like contracts and applications.</p>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading signatories...</p>
        </div>

        <!-- Signatory Cards -->
        <div x-show="!loading" class="space-y-4">
            <template x-for="sig in signatories" :key="sig.position_key">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                        <!-- Position Info -->
                        <div class="flex items-center gap-3 lg:w-56 flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-pen-fancy text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400"
                                      x-text="sig.position_key"></span>
                            </div>
                        </div>

                        <!-- Position Title (Editable) -->
                        <div class="flex-1 lg:max-w-xs">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Display Title</label>
                            <input type="text" x-model="sig.position_title"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Assigned User Dropdown -->
                        <div class="flex-1 lg:max-w-xs">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned User</label>
                            <select x-model="sig.user_id"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Select User --</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.name + (user.has_signature ? ' \u2713' : '')" :selected="sig.user_id == user.id"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Signature Preview -->
                        <div class="lg:w-32 flex-shrink-0 text-center">
                            <template x-if="getResolvedUser(sig)?.has_signature">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                        <i class="fas fa-check-circle mr-1"></i> Has Signature
                                    </span>
                                </div>
                            </template>
                            <template x-if="!getResolvedUser(sig)?.has_signature">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                        <i class="fas fa-font mr-1"></i> Printed Name
                                    </span>
                                </div>
                            </template>
                        </div>

                        <!-- Save Button -->
                        <div class="flex-shrink-0">
                            <button @click="save(sig)" :disabled="saving[sig.position_key]"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas mr-2" :class="saving[sig.position_key] ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                <span x-text="saving[sig.position_key] ? 'Saving...' : 'Save'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Resolved User Info -->
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <template x-if="getResolvedUserName(sig)">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-user-check mr-1"></i>
                                Will sign as: <strong class="text-gray-700 dark:text-gray-300" x-text="getResolvedUserName(sig)"></strong>
                                <span class="text-gray-400"> &mdash; </span>
                                <span x-text="sig.position_title"></span>
                            </p>
                        </template>
                        <template x-if="!getResolvedUserName(sig)">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                No user assigned. Documents will show a blank signature line for this position.
                            </p>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Success Notification -->
        <div x-show="showSuccess" x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError" x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>

    <script>
    function signatoryManager() {
        return {
            signatories: [],
            users: [],
            loading: true,
            saving: {},
            showSuccess: false,
            successMessage: '',
            showError: false,
            errorMessage: '',

            async init() {
                await Promise.all([this.fetchSignatories(), this.fetchUsers()]);
                this.signatories.forEach(s => this.saving[s.position_key] = false);
                this.loading = false;
            },

            async fetchSignatories() {
                try {
                    const res = await fetch('/config/document-signatories', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        }
                    });
                    const data = await res.json();
                    this.signatories = (data.data || []).map(s => ({
                        ...s,
                        user_id: s.user_id ? String(s.user_id) : '',
                    }));
                } catch (e) {
                    console.error('Error fetching signatories:', e);
                }
            },

            async fetchUsers() {
                try {
                    const res = await fetch('/config/document-signatories/active-users', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        }
                    });
                    const data = await res.json();
                    this.users = data.data || [];
                } catch (e) {
                    console.error('Error fetching users:', e);
                }
            },

            getResolvedUser(sig) {
                if (sig.user_id) {
                    return this.users.find(u => String(u.id) === String(sig.user_id));
                }
                return sig.user ? { id: sig.user.id, name: sig.user.name, has_signature: !!sig.user.signature_path } : null;
            },

            getResolvedUserName(sig) {
                const user = this.getResolvedUser(sig);
                return user?.name || '';
            },

            async save(sig) {
                this.saving[sig.position_key] = true;
                try {
                    const res = await fetch(`/config/document-signatories/${sig.position_key}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify({
                            user_id: sig.user_id || null,
                            position_title: sig.position_title,
                        }),
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.showSuccessNotification(data.message || 'Saved successfully');
                        // Update the signatory in the list with fresh data
                        const idx = this.signatories.findIndex(s => s.position_key === sig.position_key);
                        if (idx !== -1) {
                            this.signatories[idx] = {
                                ...data.data,
                                user_id: data.data.user_id ? String(data.data.user_id) : '',
                            };
                        }
                    } else {
                        this.showErrorNotification(data.message || 'Failed to save');
                    }
                } catch (e) {
                    console.error('Error saving signatory:', e);
                    this.showErrorNotification('Network error. Please try again.');
                } finally {
                    this.saving[sig.position_key] = false;
                }
            },

            showSuccessNotification(msg) {
                this.successMessage = msg;
                this.showSuccess = true;
                setTimeout(() => this.showSuccess = false, 3000);
            },

            showErrorNotification(msg) {
                this.errorMessage = msg;
                this.showError = true;
                setTimeout(() => this.showError = false, 5000);
            },
        };
    }
    </script>
</x-app-layout>
