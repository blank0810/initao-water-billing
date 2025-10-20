@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
];

// Define page titles mapping
$pageTitles = [
    'dashboard' => 'Dashboard',
    'user-add' => 'Add User',
    'user-list' => 'User List',
    'customer-add' => 'Add Customer',
    'customer-list' => 'Customer List',
    'payment-management' => 'Payment Management',
    'approve-customer' => 'Customer Approval',
    'consumer-list' => 'Consumer List',
    'billing-management' => 'Billing Management',
    'meter-management' => 'Meter Management',
    'rate-management' => 'Rate Management',
    'ledger-management' => 'Ledger Management',
    'analytics' => 'Analytics',
];

// Get current active menu from session or default to dashboard
$activeMenu = session('active_menu', 'dashboard');
$pageTitle = $pageTitles[$activeMenu] ?? 'Dashboard';

// Define breadcrumb mapping
$breadcrumbs = [
    'dashboard' => ['Dashboard'],
    'user-add' => ['User Management', 'Add User'],
    'user-list' => ['User Management', 'User List'],
    'customer-add' => ['Customer Application', 'Add Customer'],
    'customer-list' => ['Customer Application', 'Customer List'],
    'payment-management' => ['Customer Application', 'Payment Management'],
    'approve-customer' => ['Customer Application', 'Customer Approval'],
    'consumer-list' => ['Consumer List'],
    'billing-management' => ['Billing Management'],
    'meter-management' => ['Meter Management'],
    'rate-management' => ['Rate Management'],
    'ledger-management' => ['Ledger Management'],
    'analytics' => ['Analytics'],
];

$currentBreadcrumbs = $breadcrumbs[$activeMenu] ?? ['Dashboard'];
@endphp

<nav x-data="{ notifOpen: false, userOpen: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="max-w-full mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between h-24 items-center">

            <!-- Breadcrumb & Title -->
            <div class="flex flex-col justify-center">
                <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <li class="flex items-center">
                        <span class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Pages</span>
                        @if(count($currentBreadcrumbs) > 1)
                            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                {{ $currentBreadcrumbs[0] }}
                            </a>
                        @endif
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                    <li class="font-medium text-gray-700 dark:text-gray-300">{{ end($currentBreadcrumbs) }}</li>
                </ol>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pageTitle }}</h1>
            </div>

            <!-- Right: Controls -->
            <div class="flex items-center space-x-4">

                <!-- Search Bar -->
                <div class="relative hidden lg:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Search..."
                        class="pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all duration-200 w-64 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    >
                </div>

<!-- Theme Toggle - UPDATED -->
<button x-data="themeToggle()"
        @click="toggle()"
        class="p-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300"
        title="Toggle theme">
    <!-- Sun for Light Mode -->
    <svg x-show="!isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    <!-- Moon for Dark Mode -->
    <svg x-show="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>
                <!-- Rest of your navbar code remains the same -->
                <!-- Notifications -->
                <div class="relative" x-data="{ open: notifOpen }">
                    <button @click="notifOpen = !notifOpen"
                            class="relative p-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300">
                        <!-- Bell Icon -->
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center border-2 border-white dark:border-gray-800 font-medium">3</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="notifOpen = false"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">New customer application pending approval</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">2 minutes ago</span>
                            </div>
                            <div class="p-4 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">Payment received from John Doe</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">1 hour ago</span>
                            </div>
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition duration-150">
                                <p class="text-sm text-gray-800 dark:text-gray-200">System maintenance scheduled</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Yesterday</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: userOpen }">
                    <button @click="userOpen = !userOpen"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                        <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Administrator</div>
                        </div>
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="userOpen = false"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">

                        <!-- User Info -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                        </div>

                        <!-- Menu Items -->
                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                                <span>Settings</span>
                            </a>
                        </div>

                        <!-- Logout -->
                        <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>
