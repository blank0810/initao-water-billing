<!-- Edit User Modal -->
<div id="editUserModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-gray-200 dark:border-gray-700">
        <!-- Header (Fixed) -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 bg-white dark:bg-gray-900 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-edit text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit User</h3>
            </div>
            <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body (Scrollable) -->
        <form id="editUserForm" class="overflow-y-auto flex-1 bg-white dark:bg-gray-900">
            <input type="hidden" id="editUserId">
            
            <!-- Avatar Upload -->
            <div class="flex flex-col items-center pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <img id="editAvatarImg" src="{{ asset('images/logo.png') }}" class="h-24 w-24 rounded-full object-cover" alt="Avatar">
                    <input type="file" id="editAvatar" accept="image/*" class="hidden" onchange="handleEditAvatarChange(this)">
                    <label for="editAvatar" class="absolute bottom-0 right-0 inline-flex items-center justify-center w-8 h-8 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full cursor-pointer transition-colors">
                        <i class="fas fa-edit text-xs"></i>
                    </label>
                </div>
                <button type="button" id="editRemoveAvatarBtn" onclick="removeEditAvatar()" class="hidden mt-2 inline-flex items-center justify-center px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-xs rounded-lg transition-colors">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>

            <!-- Form Fields Grid -->
            <div class="grid grid-cols-2 gap-4 p-6">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full Name *</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 10.625C11.7261 10.625 13.1251 9.22601 13.1251 7.5C13.1251 5.77399 11.7261 4.375 10.0001 4.375C8.27409 4.375 6.8751 5.77399 6.8751 7.5C6.8751 9.22601 8.27409 10.625 10.0001 10.625ZM10.0001 12.125C12.5546 12.125 14.6251 10.0545 14.6251 7.5C14.6251 4.94552 12.5546 2.875 10.0001 2.875C7.44562 2.875 5.3751 4.94552 5.3751 7.5C5.3751 10.0545 7.44562 12.125 10.0001 12.125Z" fill="#667085"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 12.875C7.82263 12.875 5.82123 13.5546 4.24297 14.7022C2.66984 15.8463 1.5835 17.4146 1.5835 19.1667C1.5835 19.5809 1.91928 19.9167 2.3335 19.9167C2.74771 19.9167 3.0835 19.5809 3.0835 19.1667C3.0835 17.9187 3.83152 16.7371 5.14203 15.7978C6.44741 14.8619 8.24904 14.375 10.0001 14.375C11.7512 14.375 13.5528 14.8619 14.8582 15.7978C16.1687 16.7371 16.9167 17.9187 16.9167 19.1667C16.9167 19.5809 17.2525 19.9167 17.6667 19.9167C18.081 19.9167 18.4167 19.5809 18.4167 19.1667C18.4167 17.4146 17.3304 15.8463 15.7573 14.7022C14.179 13.5546 12.1776 12.875 10.0001 12.875Z" fill="#667085"/>
                            </svg>
                        </span>
                        <input type="text" id="editUserName" required placeholder="Enter full name" class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username *</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 10.625C11.7261 10.625 13.1251 9.22601 13.1251 7.5C13.1251 5.77399 11.7261 4.375 10.0001 4.375C8.27409 4.375 6.8751 5.77399 6.8751 7.5C6.8751 9.22601 8.27409 10.625 10.0001 10.625ZM10.0001 12.125C12.5546 12.125 14.6251 10.0545 14.6251 7.5C14.6251 4.94552 12.5546 2.875 10.0001 2.875C7.44562 2.875 5.3751 4.94552 5.3751 7.5C5.3751 10.0545 7.44562 12.125 10.0001 12.125Z" fill="#667085"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 12.875C7.82263 12.875 5.82123 13.5546 4.24297 14.7022C2.66984 15.8463 1.5835 17.4146 1.5835 19.1667C1.5835 19.5809 1.91928 19.9167 2.3335 19.9167C2.74771 19.9167 3.0835 19.5809 3.0835 19.1667C3.0835 17.9187 3.83152 16.7371 5.14203 15.7978C6.44741 14.8619 8.24904 14.375 10.0001 14.375C11.7512 14.375 13.5528 14.8619 14.8582 15.7978C16.1687 16.7371 16.9167 17.9187 16.9167 19.1667C16.9167 19.5809 17.2525 19.9167 17.6667 19.9167C18.081 19.9167 18.4167 19.5809 18.4167 19.1667C18.4167 17.4146 17.3304 15.8463 15.7573 14.7022C14.179 13.5546 12.1776 12.875 10.0001 12.875Z" fill="#667085"/>
                            </svg>
                        </span>
                        <input type="text" id="editUserUsername" required placeholder="Enter username" class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email Address</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 7.06206V14.375C3.04175 14.6511 3.26561 14.875 3.54175 14.875H16.4584C16.7346 14.875 16.9584 14.6511 16.9584 14.375V7.06245L11.1443 11.1168C10.457 11.5961 9.54373 11.5961 8.85638 11.1168L3.04175 7.06206ZM16.9584 5.19262C16.9584 5.19341 16.9584 5.1942 16.9584 5.19498V5.20026C16.9572 5.22216 16.946 5.24239 16.9279 5.25501L10.2864 9.88638C10.1145 10.0062 9.8862 10.0062 9.71437 9.88638L3.07255 5.25485C3.05342 5.24151 3.04202 5.21967 3.04202 5.19636C3.042 5.15695 3.07394 5.125 3.11335 5.125H16.8871C16.9253 5.125 16.9564 5.15494 16.9584 5.19262ZM18.4584 5.21428V14.375C18.4584 15.4796 17.563 16.375 16.4584 16.375H3.54175C2.43718 16.375 1.54175 15.4796 1.54175 14.375V5.19498C1.54175 5.1852 1.54194 5.17546 1.54231 5.16577C1.55858 4.31209 2.25571 3.625 3.11335 3.625H16.8871C17.7549 3.625 18.4584 4.32843 18.4585 5.19622C18.4585 5.20225 18.4585 5.20826 18.4584 5.21428Z" fill="#667085"/>
                            </svg>
                        </span>
                        <input type="email" id="editUserEmail" placeholder="Enter email (optional)" class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Role *</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66675 5.83333C6.66675 4.45262 7.78604 3.33333 9.16675 3.33333H10.8334C12.2141 3.33333 13.3334 4.45262 13.3334 5.83333V6.66667H14.1667C15.5474 6.66667 16.6667 7.78595 16.6667 9.16667V14.1667C16.6667 15.5474 15.5474 16.6667 14.1667 16.6667H5.83341C4.4527 16.6667 3.33341 15.5474 3.33341 14.1667V9.16667C3.33341 7.78595 4.4527 6.66667 5.83341 6.66667H6.66675V5.83333ZM8.16675 6.66667H11.8334V5.83333C11.8334 5.28105 11.3857 4.83333 10.8334 4.83333H9.16675C8.61446 4.83333 8.16675 5.28105 8.16675 5.83333V6.66667ZM5.83341 8.16667C5.28113 8.16667 4.83341 8.61438 4.83341 9.16667V14.1667C4.83341 14.7189 5.28113 15.1667 5.83341 15.1667H14.1667C14.719 15.1667 15.1667 14.7189 15.1667 14.1667V9.16667C15.1667 8.61438 14.719 8.16667 14.1667 8.16667H5.83341Z" fill="#667085"/>
                            </svg>
                        </span>
                        <select id="editUserRole" required class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                            <option value="">Loading roles...</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status *</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 3.125C6.20304 3.125 3.125 6.20304 3.125 10C3.125 13.797 6.20304 16.875 10 16.875C13.797 16.875 16.875 13.797 16.875 10C16.875 6.20304 13.797 3.125 10 3.125ZM1.875 10C1.875 5.51269 5.51269 1.875 10 1.875C14.4873 1.875 18.125 5.51269 18.125 10C18.125 14.4873 14.4873 18.125 10 18.125C5.51269 18.125 1.875 14.4873 1.875 10ZM13.2322 7.98223C13.4769 8.22688 13.4769 8.62312 13.2322 8.86777L9.48223 12.6178C9.23758 12.8624 8.84135 12.8624 8.5967 12.6178L6.7467 10.7678C6.50205 10.5231 6.50205 10.1269 6.7467 9.88223C6.99135 9.63758 7.38758 9.63758 7.63223 9.88223L9.03947 11.2895L12.3467 7.98223C12.5914 7.73758 12.9876 7.73758 13.2322 7.98223Z" fill="#667085"/>
                            </svg>
                        </span>
                        <select id="editUserStatus" required class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                            <option value="{{ \App\Models\Status::getIdByDescription(\App\Models\Status::ACTIVE) }}">Active</option>
                            <option value="{{ \App\Models\Status::getIdByDescription(\App\Models\Status::INACTIVE) }}">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="col-span-2" x-data="{ showPassword: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">New Password</label>
                    <div class="relative">
                        <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.625 8.54175V6.66675C5.625 4.36561 7.48896 2.50165 9.79009 2.50165H10.2099C12.511 2.50165 14.375 4.36561 14.375 6.66675V8.54175H14.7917C15.8963 8.54175 16.7917 9.43718 16.7917 10.5417V15.6251C16.7917 16.7296 15.8963 17.6251 14.7917 17.6251H5.20833C4.10376 17.6251 3.20833 16.7296 3.20833 15.6251V10.5417C3.20833 9.43718 4.10376 8.54175 5.20833 8.54175H5.625ZM7.125 6.66675V8.54175H12.875V6.66675C12.875 5.19404 11.6827 4.00165 10.2099 4.00165H9.79009C8.31738 4.00165 7.125 5.19404 7.125 6.66675ZM5.20833 10.0417C4.93219 10.0417 4.70833 10.2656 4.70833 10.5417V15.6251C4.70833 15.9012 4.93219 16.1251 5.20833 16.1251H14.7917C15.0678 16.1251 15.2917 15.9012 15.2917 15.6251V10.5417C15.2917 10.2656 15.0678 10.0417 14.7917 10.0417H5.20833Z" fill="#667085"/>
                            </svg>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" id="editUserPassword" placeholder="Leave blank to keep current" class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent dark:bg-gray-900 px-4 py-2.5 pl-[62px] pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30">
                        <span @click="showPassword = !showPassword" class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer">
                            <svg x-show="!showPassword" class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" />
                            </svg>
                            <svg x-show="showPassword" class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Digital Signature -->
            <div id="editSignatureSection" class="px-6 pb-6">
                <x-ui.signature-pad name="edit_signature" remove-name="edit_remove_signature" label="Digital Signature" />
            </div>
        </form>

        <!-- Footer (Fixed) -->
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex-shrink-0 rounded-b-2xl">
            <button onclick="closeEditUserModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                Cancel
            </button>
            <button id="saveUserBtn" onclick="saveUser()" class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
</div>

<script>
let editUserRoles = [];
let editAvatarBase64 = null;

function handleEditAvatarChange(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            editAvatarBase64 = e.target.result;
            document.getElementById('editAvatarImg').src = editAvatarBase64;
            document.getElementById('editRemoveAvatarBtn').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeEditAvatar() {
    editAvatarBase64 = 'remove';
    document.getElementById('editAvatarImg').src = '{{ asset("images/logo.png") }}';
    document.getElementById('editAvatar').value = '';
    document.getElementById('editRemoveAvatarBtn').classList.add('hidden');
}

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

    editAvatarBase64 = null;
    document.getElementById('editAvatarImg').src = user.photo_url || '{{ asset("images/logo.png") }}';
    document.getElementById('editAvatar').value = '';
    document.getElementById('editRemoveAvatarBtn').classList.add('hidden');

    if (user.status_id) {
        document.getElementById('editUserStatus').value = user.status_id;
    }

    if (editUserRoles.length > 0) {
        const roleId = user.role?.role_id || null;
        populateEditRoleDropdown(roleId);
    } else {
        fetchEditUserRoles().then(() => {
            const roleId = user.role?.role_id || null;
            populateEditRoleDropdown(roleId);
        });
    }

    const sigSection = document.getElementById('editSignatureSection');
    if (sigSection) {
        const sigComponent = sigSection.querySelector('[x-data]');
        if (sigComponent && sigComponent.__x) {
            const sigData = Alpine.$data(sigComponent);
            if (sigData) {
                sigData.signatureData = '';
                sigData.removeSignature = false;
                sigData.hasExisting = !!user.signature_url;
                sigData.existingUrl = user.signature_url || null;
                if (sigData.pad) {
                    sigData.pad.clear();
                    sigData.isEmpty = true;
                }
            }
        }
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
    const userName = document.getElementById('editUserName').value;

    const userData = {
        id: document.getElementById('editUserId').value,
        name: userName,
        username: document.getElementById('editUserUsername').value,
        email: email ? email.trim() : null,
        role_id: parseInt(document.getElementById('editUserRole').value),
        status_id: parseInt(document.getElementById('editUserStatus').value),
    };

    if (password) {
        userData.password = password;
    }

    if (editAvatarBase64 && editAvatarBase64 !== 'remove') {
        userData.avatar = editAvatarBase64;
    } else if (editAvatarBase64 === 'remove') {
        userData.remove_avatar = true;
    }

    const editSigInput = document.querySelector('#editSignatureSection input[name="edit_signature"]');
    const editSigRemoveInput = document.querySelector('#editSignatureSection input[name="edit_remove_signature"]');
    if (editSigInput && editSigInput.value) {
        userData.signature = editSigInput.value;
    } else if (editSigRemoveInput && editSigRemoveInput.value === '1') {
        userData.remove_signature = true;
    }

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
            showToast('Edit User Success', `${userName} has been updated successfully`, 'success');
            if (window.userManager?.refresh) {
                window.userManager.refresh();
            }
        } else {
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat();
                showToast('Edit User Failed', errorMessages.join(', '), 'error');
            } else {
                showToast('Edit User Failed', result.message || 'Failed to update user', 'error');
            }
        }
    } catch (error) {
        console.error('Error saving user:', error);
        showToast('Edit User Error', 'Network error. Please try again.', 'error');
    } finally {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        fetchEditUserRoles();
        window.addEventListener('show-edit-user', function(e) {
            showEditUserModal(e.detail);
        });
    });
} else {
    fetchEditUserRoles();
    window.addEventListener('show-edit-user', function(e) {
        showEditUserModal(e.detail);
    });
}
</script>
