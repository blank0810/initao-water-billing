<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Add New User</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Create a new system user account</p>
                    </div>
                </div>
                <a href="{{ route('user.list') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-list mr-2"></i>View User List
                </a>
            </div>

            <!-- Single Container Form -->
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                <form id="userRegistrationForm" x-data="{ avatarPreview: null }" class="p-6">
                    @csrf

                    <!-- Personal Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">First Name *</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 10.625C11.7261 10.625 13.1251 9.22601 13.1251 7.5C13.1251 5.77399 11.7261 4.375 10.0001 4.375C8.27409 4.375 6.8751 5.77399 6.8751 7.5C6.8751 9.22601 8.27409 10.625 10.0001 10.625ZM10.0001 12.125C12.5546 12.125 14.6251 10.0545 14.6251 7.5C14.6251 4.94552 12.5546 2.875 10.0001 2.875C7.44562 2.875 5.3751 4.94552 5.3751 7.5C5.3751 10.0545 7.44562 12.125 10.0001 12.125Z" fill="#667085"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 12.875C7.82263 12.875 5.82123 13.5546 4.24297 14.7022C2.66984 15.8463 1.5835 17.4146 1.5835 19.1667C1.5835 19.5809 1.91928 19.9167 2.3335 19.9167C2.74771 19.9167 3.0835 19.5809 3.0835 19.1667C3.0835 17.9187 3.83152 16.7371 5.14203 15.7978C6.44741 14.8619 8.24904 14.375 10.0001 14.375C11.7512 14.375 13.5528 14.8619 14.8582 15.7978C16.1687 16.7371 16.9167 17.9187 16.9167 19.1667C16.9167 19.5809 17.2525 19.9167 17.6667 19.9167C18.081 19.9167 18.4167 19.5809 18.4167 19.1667C18.4167 17.4146 17.3304 15.8463 15.7573 14.7022C14.179 13.5546 12.1776 12.875 10.0001 12.875Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="first_name" required placeholder="Enter first name"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Last Name *</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 10.625C11.7261 10.625 13.1251 9.22601 13.1251 7.5C13.1251 5.77399 11.7261 4.375 10.0001 4.375C8.27409 4.375 6.8751 5.77399 6.8751 7.5C6.8751 9.22601 8.27409 10.625 10.0001 10.625ZM10.0001 12.125C12.5546 12.125 14.6251 10.0545 14.6251 7.5C14.6251 4.94552 12.5546 2.875 10.0001 2.875C7.44562 2.875 5.3751 4.94552 5.3751 7.5C5.3751 10.0545 7.44562 12.125 10.0001 12.125Z" fill="#667085"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 12.875C7.82263 12.875 5.82123 13.5546 4.24297 14.7022C2.66984 15.8463 1.5835 17.4146 1.5835 19.1667C1.5835 19.5809 1.91928 19.9167 2.3335 19.9167C2.74771 19.9167 3.0835 19.5809 3.0835 19.1667C3.0835 17.9187 3.83152 16.7371 5.14203 15.7978C6.44741 14.8619 8.24904 14.375 10.0001 14.375C11.7512 14.375 13.5528 14.8619 14.8582 15.7978C16.1687 16.7371 16.9167 17.9187 16.9167 19.1667C16.9167 19.5809 17.2525 19.9167 17.6667 19.9167C18.081 19.9167 18.4167 19.5809 18.4167 19.1667C18.4167 17.4146 17.3304 15.8463 15.7573 14.7022C14.179 13.5546 12.1776 12.875 10.0001 12.875Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="last_name" required placeholder="Enter last name"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                            <i class="fas fa-lock text-blue-600 dark:text-blue-400"></i>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Username *</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 10.625C11.7261 10.625 13.1251 9.22601 13.1251 7.5C13.1251 5.77399 11.7261 4.375 10.0001 4.375C8.27409 4.375 6.8751 5.77399 6.8751 7.5C6.8751 9.22601 8.27409 10.625 10.0001 10.625ZM10.0001 12.125C12.5546 12.125 14.6251 10.0545 14.6251 7.5C14.6251 4.94552 12.5546 2.875 10.0001 2.875C7.44562 2.875 5.3751 4.94552 5.3751 7.5C5.3751 10.0545 7.44562 12.125 10.0001 12.125Z" fill="#667085"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0001 12.875C7.82263 12.875 5.82123 13.5546 4.24297 14.7022C2.66984 15.8463 1.5835 17.4146 1.5835 19.1667C1.5835 19.5809 1.91928 19.9167 2.3335 19.9167C2.74771 19.9167 3.0835 19.5809 3.0835 19.1667C3.0835 17.9187 3.83152 16.7371 5.14203 15.7978C6.44741 14.8619 8.24904 14.375 10.0001 14.375C11.7512 14.375 13.5528 14.8619 14.8582 15.7978C16.1687 16.7371 16.9167 17.9187 16.9167 19.1667C16.9167 19.5809 17.2525 19.9167 17.6667 19.9167C18.081 19.9167 18.4167 19.5809 18.4167 19.1667C18.4167 17.4146 17.3304 15.8463 15.7573 14.7022C14.179 13.5546 12.1776 12.875 10.0001 12.875Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="username" id="usernameInput" required placeholder="Enter username"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                </div>
                                <div id="usernameSuggestions" class="hidden mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Suggested:</p>
                                    <div id="suggestionChips" class="flex flex-wrap gap-1.5"></div>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email Address</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 7.06206V14.375C3.04175 14.6511 3.26561 14.875 3.54175 14.875H16.4584C16.7346 14.875 16.9584 14.6511 16.9584 14.375V7.06245L11.1443 11.1168C10.457 11.5961 9.54373 11.5961 8.85638 11.1168L3.04175 7.06206ZM16.9584 5.19262C16.9584 5.19341 16.9584 5.1942 16.9584 5.19498V5.20026C16.9572 5.22216 16.946 5.24239 16.9279 5.25501L10.2864 9.88638C10.1145 10.0062 9.8862 10.0062 9.71437 9.88638L3.07255 5.25485C3.05342 5.24151 3.04202 5.21967 3.04202 5.19636C3.042 5.15695 3.07394 5.125 3.11335 5.125H16.8871C16.9253 5.125 16.9564 5.15494 16.9584 5.19262ZM18.4584 5.21428V14.375C18.4584 15.4796 17.563 16.375 16.4584 16.375H3.54175C2.43718 16.375 1.54175 15.4796 1.54175 14.375V5.19498C1.54175 5.1852 1.54194 5.17546 1.54231 5.16577C1.55858 4.31209 2.25571 3.625 3.11335 3.625H16.8871C17.7549 3.625 18.4584 4.32843 18.4585 5.19622C18.4585 5.20225 18.4585 5.20826 18.4584 5.21428Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <input type="email" name="email" placeholder="email@example.com (optional)"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                </div>
                            </div>
                            <div x-data="{ showPassword: false }">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Password *</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.625 8.54175V6.66675C5.625 4.36561 7.48896 2.50165 9.79009 2.50165H10.2099C12.511 2.50165 14.375 4.36561 14.375 6.66675V8.54175H14.7917C15.8963 8.54175 16.7917 9.43718 16.7917 10.5417V15.6251C16.7917 16.7296 15.8963 17.6251 14.7917 17.6251H5.20833C4.10376 17.6251 3.20833 16.7296 3.20833 15.6251V10.5417C3.20833 9.43718 4.10376 8.54175 5.20833 8.54175H5.625ZM7.125 6.66675V8.54175H12.875V6.66675C12.875 5.19404 11.6827 4.00165 10.2099 4.00165H9.79009C8.31738 4.00165 7.125 5.19404 7.125 6.66675ZM5.20833 10.0417C4.93219 10.0417 4.70833 10.2656 4.70833 10.5417V15.6251C4.70833 15.9012 4.93219 16.1251 5.20833 16.1251H14.7917C15.0678 16.1251 15.2917 15.9012 15.2917 15.6251V10.5417C15.2917 10.2656 15.0678 10.0417 14.7917 10.0417H5.20833Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Enter password" minlength="8"
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
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
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">User Role *</label>
                                <div class="relative">
                                    <span class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66675 5.83333C6.66675 4.45262 7.78604 3.33333 9.16675 3.33333H10.8334C12.2141 3.33333 13.3334 4.45262 13.3334 5.83333V6.66667H14.1667C15.5474 6.66667 16.6667 7.78595 16.6667 9.16667V14.1667C16.6667 15.5474 15.5474 16.6667 14.1667 16.6667H5.83341C4.4527 16.6667 3.33341 15.5474 3.33341 14.1667V9.16667C3.33341 7.78595 4.4527 6.66667 5.83341 6.66667H6.66675V5.83333ZM8.16675 6.66667H11.8334V5.83333C11.8334 5.28105 11.3857 4.83333 10.8334 4.83333H9.16675C8.61446 4.83333 8.16675 5.28105 8.16675 5.83333V6.66667ZM5.83341 8.16667C5.28113 8.16667 4.83341 8.61438 4.83341 9.16667V14.1667C4.83341 14.7189 5.28113 15.1667 5.83341 15.1667H14.1667C14.719 15.1667 15.1667 14.7189 15.1667 14.1667V9.16667C15.1667 8.61438 14.719 8.16667 14.1667 8.16667H5.83341Z" fill="#667085"/>
                                        </svg>
                                    </span>
                                    <select name="role_id" id="roleSelect" required
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
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
                                    <select name="status_id" required
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <option value="">Select Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status['stat_id'] }}">{{ $status['stat_desc'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Image -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                            <i class="fas fa-image text-blue-600 dark:text-blue-400"></i>
                            Profile Picture
                        </h3>
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                <template x-if="!avatarPreview">
                                    <i class="fas fa-user text-4xl text-gray-400"></i>
                                </template>
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover" alt="Avatar preview">
                                </template>
                            </div>
                            <div class="flex-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Upload file</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                    class="h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400"
                                    @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => avatarPreview = e.target.result; reader.readAsDataURL(file); }">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">JPG, PNG or GIF (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Digital Signature -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                            <i class="fas fa-pen-fancy text-blue-600 dark:text-blue-400"></i>
                            Digital Signature
                            <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </h3>
                        <x-ui.signature-pad name="signature" />
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="window.history.back()"
                            class="px-5 py-2.5 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/data/user/add-user.js')
</x-app-layout>
