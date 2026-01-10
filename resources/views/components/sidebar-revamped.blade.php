@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
];
@endphp

<!-- Sidebar -->
<aside x-data="sidebar()"
    class="flex flex-col flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 sidebar-transition h-screen fixed left-0 top-0 z-50 overflow-hidden max-lg:hidden lg:flex"
    :class="sidebarOpen ? 'w-72' : 'w-20'"
    x-init="init()">

    <!-- Header with Logo -->
    <div class="flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-800 h-24">
        <button @click="toggleSidebar()" class="flex items-center space-x-3 w-full min-w-0 group">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-11 h-11 rounded-lg object-cover">
            </div>

            <!-- Title (when expanded) -->
            <div class="min-w-0 flex-1" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
<p class="text-2xl font-semibold tracking-wide text-gray-700 dark:text-gray-300 flex items-center">
    MEEDO
</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                    Administrator
                </p>
            </div>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" @click="setActiveMenu('dashboard')"
            :class="activeMenu === 'dashboard' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'dashboard' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-chart-pie text-sm" :class="activeMenu === 'dashboard' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        <!-- User Management -->
        <div class="space-y-1">
            <button @click="toggleSubmenu('userManagement')"
                :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-users text-sm" :class="(activeMenu.startsWith('user-') || openSubmenus.userManagement) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>User Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.userManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.userManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                <a href="{{ route('user.add') }}" @click="setActiveMenu('user-add')"
                    :class="activeMenu === 'user-add' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-user-plus w-4 text-xs mr-2.5"></i>
                    <span>Add User</span>
                </a>
                <a href="{{ route('user.list') }}" @click="setActiveMenu('user-list')"
                    :class="activeMenu === 'user-list' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-list w-4 text-xs mr-2.5"></i>
                    <span>User List</span>
                </a>
            </div>
        </div>

        <!-- Application Management -->
        <div class="space-y-1">
            <button @click="toggleSubmenu('applicationManagement')"
                :class="(openSubmenus.applicationManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(openSubmenus.applicationManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-user-tie text-sm" :class="openSubmenus.applicationManagement ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>Application Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.applicationManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.applicationManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                <a href="{{ route('customer.add') }}" @click="setActiveMenu('customer-add')"
                    :class="activeMenu === 'customer-add' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-user-plus w-4 text-xs mr-2.5"></i>
                    <span>Add Customer</span>
                </a>
                <a href="{{ route('application.list') }}" @click="setActiveMenu('application-list')"
                    :class="activeMenu === 'application-list' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-list w-4 text-xs mr-2.5"></i>
                    <span>Application List</span>
                </a>
                <a href="{{ route('approve.customer') }}" @click="setActiveMenu('approve-customer')"
                    :class="activeMenu === 'approve-customer' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-check-circle w-4 text-xs mr-2.5"></i>
                    <span>Approval</span>
                </a>
            </div>
        </div>

        <!-- Customer Management -->
        <div class="space-y-1">
            <button @click="toggleSubmenu('customerManagement')"
                :class="(openSubmenus.customerManagement) ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 group">
                <div class="flex items-center min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                        :class="(openSubmenus.customerManagement) ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                        <i class="fas fa-users text-sm" :class="(openSubmenus.customerManagement) ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium truncate" x-show="sidebarOpen" x-transition>Customer Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.customerManagement }" x-show="sidebarOpen" x-transition></i>
            </button>

            <div x-show="openSubmenus.customerManagement && sidebarOpen" x-collapse class="ml-3 pl-6 border-l-2 border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                <a href="{{ route('customer.list') }}" @click="setActiveMenu('customer-list')"
                    :class="activeMenu === 'customer-list' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-users w-4 text-xs mr-2.5"></i>
                    <span>Customer List</span>
                </a>
                <a href="{{ route('service.connection') }}" @click="setActiveMenu('service-connection')"
                    :class="activeMenu === 'service-connection' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-plug w-4 text-xs mr-2.5"></i>
                    <span>Service Connections</span>
                </a>
            </div>
        </div>

        <a href="{{ route('payment.management') }}" @click="setActiveMenu('payment-management')"
            :class="activeMenu === 'payment-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'payment-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-credit-card text-sm" :class="activeMenu === 'payment-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Payment Management</span>
        </a>

        <!-- Billing Management -->
        <a href="{{ route('billing.management') }}" @click="setActiveMenu('billing-management')"
            :class="activeMenu === 'billing-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'billing-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-file-invoice-dollar text-sm" :class="activeMenu === 'billing-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Billing</span>
        </a>

        <!-- Meter Management -->
        <a href="{{ route('meter.management') }}" @click="setActiveMenu('meter-management')"
            :class="activeMenu === 'meter-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'meter-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-tachometer-alt text-sm" :class="activeMenu === 'meter-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Meter</span>
        </a>

        <!-- Rate Management -->
        <a href="{{ route('rate.management') }}" @click="setActiveMenu('rate-management')"
            :class="activeMenu === 'rate-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'rate-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-percentage text-sm" :class="activeMenu === 'rate-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Rate</span>
        </a>

        <!-- Ledger Management -->
        <a href="{{ route('ledger.management') }}" @click="setActiveMenu('ledger-management')"
            :class="activeMenu === 'ledger-management' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'ledger-management' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-book text-sm" :class="activeMenu === 'ledger-management' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Ledger</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('analytics') }}" @click="setActiveMenu('analytics')"
            :class="activeMenu === 'analytics' ? 'bg-blue-600 dark:bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200"
                :class="activeMenu === 'analytics' ? 'bg-blue-500' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-gray-200 dark:group-hover:bg-gray-700'">
                <i class="fas fa-chart-line text-sm" :class="activeMenu === 'analytics' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
            </div>
            <span class="ml-3 text-sm font-medium" x-show="sidebarOpen" x-transition>Analytics</span>
        </a>
    </nav>
</aside>

<script>
    function sidebar() {
        return {
            sidebarOpen: true,
            activeMenu: '{{ session('active_menu', 'dashboard') }}',
            openSubmenus: {
                userManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'user-') ? 'true' : 'false' }},
                applicationManagement: {{ session('active_menu') && (in_array(session('active_menu'), ['application-list','customer-add', 'approve-customer', 'invoice-list', 'declined-customer', 'payment-management'])) ? 'true' : 'false' }},
                customerManagement: {{ session('active_menu') && (in_array(session('active_menu'), ['customer-list','service-connection'])) ? 'true' : 'false' }}
            },

            init() {
                const savedSidebarState = localStorage.getItem('sidebarOpen');
                if (savedSidebarState !== null) {
                    this.sidebarOpen = savedSidebarState === 'true';
                }

                const path = window.location.pathname;
                const routeMap = {
                    '/dashboard': 'dashboard',
                    '/customer/add': 'customer-add',
                    '/customer/list': 'customer-list',
                    '/application/list': 'application-list',
                    '/customer/payment-management': 'payment-management',
                    '/customer/approve-customer': 'approve-customer',
                    '/customer/invoice-list': 'approve-customer',
                    '/customer/declined-customer': 'approve-customer',
                    '/user/add': 'user-add',
                    '/user/list': 'user-list',
                    '/billing': 'billing-management',
                    '/billing/management': 'billing-management',
                    '/billing/consumer': 'billing-management',
                    '/billing/collections': 'billing-management',
                    '/billing/generation': 'billing-management',
                    '/billing/adjustments': 'billing-management',
                    '/meter/management': 'meter-management',
                    '/rate/management': 'rate-management',
                    '/ledger/management': 'ledger-management',
                    '/analytics': 'analytics'
                };

                if (path.startsWith('/customer/payment/')) {
                    this.activeMenu = 'payment-management';
                } else if (path.startsWith('/customer/invoice-list') || path.startsWith('/customer/declined-customer')) {
                    this.activeMenu = 'approve-customer';
                } else if (path.startsWith('/billing')) {
                    this.activeMenu = 'billing-management';
                } else {
                    this.activeMenu = routeMap[path] || '{{ session('active_menu', 'dashboard') }}';
                }

                localStorage.setItem('activeMenu', this.activeMenu);

                if (this.activeMenu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (['application-list','customer-add', 'approve-customer', 'invoice-list', 'declined-customer', 'payment-management'].includes(this.activeMenu)) {
                    this.openSubmenus.applicationManagement = true;
                } else if (['customer-list', 'service-connection'].includes(this.activeMenu)) {
                    this.openSubmenus.customerManagement = true;
                }

                if (window.appState) {
                    window.appState.sidebarOpen = this.sidebarOpen;
                }

                if (window.innerWidth < 1024) {
                    this.sidebarOpen = false;
                }
            },

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
                if (window.appState) {
                    window.appState.sidebarOpen = this.sidebarOpen;
                }
            },

            toggleSubmenu(submenu) {
                if (!this.sidebarOpen) {
                    this.sidebarOpen = true;
                    return;
                }
                Object.keys(this.openSubmenus).forEach(key => {
                    if (key !== submenu) {
                        this.openSubmenus[key] = false;
                    }
                });
                this.openSubmenus[submenu] = !this.openSubmenus[submenu];
            },

            setActiveMenu(menu) {
                this.activeMenu = menu;
                localStorage.setItem('activeMenu', menu);
                if (menu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                    this.openSubmenus.applicationManagement = false;
                    this.openSubmenus.customerManagement = false;
                } else if (['application-list','customer-add', 'approve-customer', 'invoice-list', 'declined-customer', 'payment-management'].includes(menu)) {
                    this.openSubmenus.applicationManagement = true;
                    this.openSubmenus.userManagement = false;
                    this.openSubmenus.customerManagement = false;
                } else if (['customer-list','service-connection'].includes(menu)) {
                    this.openSubmenus.customerManagement = true;
                    this.openSubmenus.userManagement = false;
                    this.openSubmenus.applicationManagement = false;
                } else {
                    this.openSubmenus.userManagement = false;
                    this.openSubmenus.applicationManagement = false;
                    this.openSubmenus.customerManagement = false;
                }
            },

            handleMenuClick(menu, event) {
                event.preventDefault();
                event.stopPropagation();
                this.setActiveMenu(menu);
                const routes = {
                    'payment-management': '{{ route("payment.management") }}',
                    'approve-customer': '{{ route("approve.customer") }}',
                    'service-connection': '{{ route("service.connection") }}'
                };
                if (routes[menu]) {
                    setTimeout(() => {
                        window.location.href = routes[menu];
                    }, 150);
                }
            }
        }
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('appState', () => {
            return {
                sidebarOpen: true,
                isMobile: false,
                init() {
                    window.appState = this;
                    this.checkMobile();
                    window.addEventListener('resize', () => this.checkMobile());
                    const savedSidebarState = localStorage.getItem('sidebarOpen');
                    if (savedSidebarState !== null) {
                        this.sidebarOpen = savedSidebarState === 'true';
                    }
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                    this.initTheme();
                },
                checkMobile() {
                    this.isMobile = window.innerWidth < 1024;
                    if (this.isMobile && this.sidebarOpen) {
                        this.sidebarOpen = false;
                    }
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
                closeSidebar() {
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    }
                },
                initTheme() {
                    const savedTheme = localStorage.getItem('theme-preference');
                    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    let isDark;
                    if (savedTheme !== null) {
                        isDark = savedTheme === 'dark';
                    } else {
                        isDark = systemPrefersDark;
                    }
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (window.appState && window.appState.isMobile && !e.target.closest('aside') && !e.target.closest('[data-sidebar-toggle]')) {
            window.appState.closeSidebar();
        }
    });
</script>
