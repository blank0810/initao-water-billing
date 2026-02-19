@php
$user = Auth::user() ?? (object) [
    'name' => 'Demo User',
    'email' => 'demo@example.com',
];
@endphp

<!-- Sidebar -->
<aside x-data="sidebar()"
    class="hidden lg:flex flex-col flex-shrink-0
           sidebar-revamped h-screen fixed left-0 top-0
           z-50"
    :class="[sidebarOpen ? 'w-72' : 'w-20', !sidebarOpen && 'sidebar-collapsed']">

    <!-- Header -->
    <div class="sidebar-header">
        <button @click="toggleSidebar()" class="sidebar-logo-btn">
            <img src="{{ asset('images/logo.png') }}" class="sidebar-logo-img">
            <div class="sidebar-brand-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>
                <span class="sidebar-brand-title">MEEDO</span>
                <span class="sidebar-brand-subtitle">
                    <span class="sidebar-status-dot"></span>
                    Administrator
                </span>
            </div>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav-content">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" @click="setActiveMenu('dashboard')"
            :class="activeMenu === 'dashboard' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Dashboard' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Dashboard</span>
        </a>

        <!-- User Management -->
        @can('users.view')
        <div>
            <button @click="toggleSubmenu('userManagement')"
                :class="[activeMenu.startsWith('user-') && 'active', openSubmenus.userManagement && 'icon-glow']"
                class="nav-btn nav-btn-submenu"
                :data-tooltip="!sidebarOpen ? 'User Management' : null">
                <span class="nav-btn-notch"></span>
                <div class="nav-btn-icon">
                    <i class="fas fa-users"></i>
                </div>
                <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>User Management</span>
                <i class="fas fa-chevron-down nav-btn-chevron" :class="openSubmenus.userManagement && 'rotated'" x-show="sidebarOpen" x-transition.opacity></i>
            </button>

            <div x-show="openSubmenus.userManagement && sidebarOpen" x-collapse class="nav-submenu">
                @can('users.manage')
                <a href="{{ route('user.add') }}" @click="setActiveMenu('user-add')"
                    :class="activeMenu === 'user-add' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-user-plus"></i>
                    <span>Add User</span>
                </a>
                @endcan
                <a href="{{ route('user.list') }}" @click="setActiveMenu('user-list')"
                    :class="activeMenu === 'user-list' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-list"></i>
                    <span>User List</span>
                </a>
            </div>
        </div>
        @endcan

        <!-- Connection Management -->
        @can('customers.manage')
        <div>
            <button @click="toggleSubmenu('connectionManagement')"
                :class="[activeMenu.startsWith('connection-') && 'active', openSubmenus.connectionManagement && 'icon-glow']"
                class="nav-btn nav-btn-submenu"
                :data-tooltip="!sidebarOpen ? 'Connection Management' : null">
                <span class="nav-btn-notch"></span>
                <div class="nav-btn-icon">
                    <i class="fas fa-plug"></i>
                </div>
                <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Connection Management</span>
                <i class="fas fa-chevron-down nav-btn-chevron" :class="openSubmenus.connectionManagement && 'rotated'" x-show="sidebarOpen" x-transition.opacity></i>
            </button>

            <div x-show="openSubmenus.connectionManagement && sidebarOpen" x-collapse class="nav-submenu">
                <a href="{{ route('connection.service-application.create') }}" @click="setActiveMenu('connection-new')"
                    :class="activeMenu === 'connection-new' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-plus-circle"></i>
                    <span>New Application</span>
                </a>
                <a href="{{ route('connection.service-application.index') }}" @click="setActiveMenu('connection-applications')"
                    :class="activeMenu === 'connection-applications' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                </a>
                <a href="{{ route('service.connection') }}" @click="setActiveMenu('connection-active')"
                    :class="activeMenu === 'connection-active' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-plug"></i>
                    <span>Active Connections</span>
                </a>
            </div>
        </div>
        @endcan

        <!-- Customer Management -->
        @can('customers.view')
        <div>
            <button @click="toggleSubmenu('customerManagement')"
                :class="[activeMenu.startsWith('customer-') && 'active', openSubmenus.customerManagement && 'icon-glow']"
                class="nav-btn nav-btn-submenu"
                :data-tooltip="!sidebarOpen ? 'Customer Management' : null">
                <span class="nav-btn-notch"></span>
                <div class="nav-btn-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Customer Management</span>
                <i class="fas fa-chevron-down nav-btn-chevron" :class="openSubmenus.customerManagement && 'rotated'" x-show="sidebarOpen" x-transition.opacity></i>
            </button>

            <div x-show="openSubmenus.customerManagement && sidebarOpen" x-collapse class="nav-submenu">
                <a href="{{ route('customer.list') }}" @click="setActiveMenu('customer-list')"
                    :class="activeMenu === 'customer-list' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-list"></i>
                    <span>Customer List</span>
                </a>
                @can('customers.manage')
                <a href="{{ route('approve.customer') }}" @click="setActiveMenu('customer-approval')"
                    :class="activeMenu === 'customer-approval' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-check-circle"></i>
                    <span>Customer Approval</span>
                </a>
                @endcan
            </div>
        </div>
        @endcan

        <!-- Payment Management -->
        @can('payments.view')
        <a href="{{ route('payment.management') }}" @click="setActiveMenu('payment-management')"
            :class="activeMenu === 'payment-management' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Payments' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Payment Management</span>
        </a>
        @endcan

        <!-- Billing Management -->
        @can('billing.view')
        <a href="{{ route('billing.management') }}" @click="setActiveMenu('billing-management')"
            :class="activeMenu === 'billing-management' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Billing' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Billing</span>
        </a>
        @endcan

        <!-- Meter Management -->
        @can('meters.view')
        <a href="{{ route('meter.management') }}" @click="setActiveMenu('meter-management')"
            :class="activeMenu === 'meter-management' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Meter' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Meter</span>
        </a>
        @endcan

        <!-- Rate Management -->
        @can('settings.manage')
        <a href="{{ route('rate.management') }}" @click="setActiveMenu('rate-management')"
            :class="activeMenu === 'rate-management' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Rate' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Rate</span>
        </a>
        @endcan

        <!-- Ledger Management -->
        @can('billing.view')
        <a href="{{ route('ledger.management') }}" @click="setActiveMenu('ledger-management')"
            :class="activeMenu === 'ledger-management' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Ledger' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-book"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Ledger</span>
        </a>
        @endcan

        <!-- Reports -->
        @can('reports.view')
        <a href="{{ route('reports') }}" @click="setActiveMenu('reports')"
            :class="activeMenu === 'reports' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Reports' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Reports</span>
        </a>
        @endcan

        <!-- Admin Configuration -->
        @canany(['config.geographic.manage', 'config.billing.manage', 'config.access.manage'])
        <div>
            <button @click="toggleSubmenu('adminConfig')"
                :class="[activeMenu.startsWith('config-') && 'active', openSubmenus.adminConfig && 'icon-glow']"
                class="nav-btn nav-btn-submenu"
                :data-tooltip="!sidebarOpen ? 'Admin Configuration' : null">
                <span class="nav-btn-notch"></span>
                <div class="nav-btn-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Admin Configuration</span>
                <i class="fas fa-chevron-down nav-btn-chevron" :class="openSubmenus.adminConfig && 'rotated'" x-show="sidebarOpen" x-transition.opacity></i>
            </button>

            <div x-show="openSubmenus.adminConfig && sidebarOpen" x-collapse class="nav-submenu">

                <!-- Geographic Submenu -->
                @can('config.geographic.manage')
                <div class="space-y-1">
                    <button @click="toggleSubmenu('geographic')"
                        :class="activeMenu.startsWith('config-geographic-') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-map-marked-alt w-4 text-xs mr-2.5"></i>
                            <span>Geographic</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.geographic }"></i>
                    </button>

                    <div x-show="openSubmenus.geographic" x-collapse class="ml-6 space-y-1 mt-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('config.barangays.index') }}" @click="setActiveMenu('config-geographic-barangays')"
                            :class="activeMenu === 'config-geographic-barangays' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-map-marker-alt w-4 text-xs mr-2.5"></i>
                            <span>Barangays</span>
                        </a>
                        <a href="{{ route('config.areas.index') }}" @click="setActiveMenu('config-geographic-areas')"
                            :class="activeMenu === 'config-geographic-areas' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-layer-group w-4 text-xs mr-2.5"></i>
                            <span>Areas</span>
                        </a>
                        <a href="{{ route('config.puroks.index') }}" @click="setActiveMenu('config-geographic-puroks')"
                            :class="activeMenu === 'config-geographic-puroks' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-house-user w-4 text-xs mr-2.5"></i>
                            <span>Puroks</span>
                        </a>
                    </div>
                </div>
                @endcan

                <!-- Water Rates -->
                @can('config.billing.manage')
                <a href="{{ route('config.water-rates.index') }}" @click="setActiveMenu('config-water-rates')"
                    :class="activeMenu === 'config-water-rates' ? 'text-white bg-blue-600 dark:bg-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-dollar-sign w-4 text-xs mr-2.5"></i>
                    <span>Water Rates</span>
                </a>

                <!-- Billing Configuration Submenu -->
                <div class="space-y-1">
                    <button @click="toggleSubmenu('billingConfig')"
                        :class="activeMenu.startsWith('config-billing-') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-receipt w-4 text-xs mr-2.5"></i>
                            <span>Billing Configuration</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.billingConfig }"></i>
                    </button>

                    <div x-show="openSubmenus.billingConfig" x-collapse class="ml-6 space-y-1 mt-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('config.account-types.index') }}" @click="setActiveMenu('config-billing-account-types')"
                            :class="activeMenu === 'config-billing-account-types' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-users-cog w-4 text-xs mr-2.5"></i>
                            <span>Account Types</span>
                        </a>
                        <a href="{{ route('config.charge-items.index') }}" @click="setActiveMenu('config-billing-charge-items')"
                            :class="activeMenu === 'config-billing-charge-items' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-file-invoice-dollar w-4 text-xs mr-2.5"></i>
                            <span>Application Fee Templates</span>
                        </a>
                        <a href="{{ route('config.penalty.index') }}" @click="setActiveMenu('config-billing-penalty')"
                            :class="activeMenu === 'config-billing-penalty' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-gavel w-4 text-xs mr-2.5"></i>
                            <span>Penalty Configuration</span>
                        </a>
                    </div>
                </div>
                @endcan

                <!-- Access Control Submenu -->
                @can('config.access.manage')
                <div class="space-y-1">
                    <button @click="toggleSubmenu('accessControl')"
                        :class="activeMenu.startsWith('config-access-') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                        class="flex items-center justify-between w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt w-4 text-xs mr-2.5"></i>
                            <span>Access Control</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openSubmenus.accessControl }"></i>
                    </button>

                    <div x-show="openSubmenus.accessControl" x-collapse class="ml-6 space-y-1 mt-1 border-l-2 border-gray-200 dark:border-gray-700 pl-3">
                        <a href="{{ route('admin.roles.index') }}" @click="setActiveMenu('config-access-roles')"
                            :class="activeMenu === 'config-access-roles' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-user-shield w-4 text-xs mr-2.5"></i>
                            <span>Roles</span>
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" @click="setActiveMenu('config-access-permissions')"
                            :class="activeMenu === 'config-access-permissions' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-key w-4 text-xs mr-2.5"></i>
                            <span>Permissions</span>
                        </a>
                        <a href="{{ route('admin.role-permissions.matrix') }}" @click="setActiveMenu('config-access-matrix')"
                            :class="activeMenu === 'config-access-matrix' ? 'text-white bg-blue-600 dark:bg-blue-600 border-l-2 border-blue-400 -ml-[3px] pl-[11px]' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-th w-4 text-xs mr-2.5"></i>
                            <span>Permission Matrix</span>
                        </a>
                    </div>
                </div>
                @endcan

            </div>
        </div>
        @endcanany

        <!-- Activity Log - Super Admin Only -->
        @role('super_admin')
        <a href="{{ route('admin.activity-log') }}" @click="setActiveMenu('activity-log')"
            :class="activeMenu === 'activity-log' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Activity Log' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-history"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Activity Log</span>
        </a>
        @endrole

    </nav>

</aside>

<script>
    function sidebar() {
        return {
            sidebarOpen: true,
            activeMenu: '{{ session('active_menu', 'dashboard') }}',
            openSubmenus: {
                userManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'user-') ? 'true' : 'false' }},
                customerManagement: {{ session('active_menu') && (str_starts_with(session('active_menu'), 'customer-')) ? 'true' : 'false' }},
                connectionManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'connection-') ? 'true' : 'false' }},
                adminConfig: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-') ? 'true' : 'false' }},
                geographic: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-geographic-') ? 'true' : 'false' }},
                billingConfig: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-billing-') ? 'true' : 'false' }},
                accessControl: {{ session('active_menu') && str_starts_with(session('active_menu'), 'config-access-') ? 'true' : 'false' }}
            },

            init() {
                const savedSidebarState = localStorage.getItem('sidebarOpen');
                if (savedSidebarState !== null) {
                    this.sidebarOpen = savedSidebarState === 'true';
                }

                const path = window.location.pathname;
                const routeMap = {
                    '/dashboard': 'dashboard',
                    '/customer/list': 'customer-list',
                    '/customer/payment-management': 'payment-management',
                    '/customer/approve-customer': 'customer-approval',
                    '/customer/invoice-list': 'invoice-list',
                    '/customer/declined-customer': 'declined-customer',
                    '/connection/service-application/create': 'connection-new',
                    '/connection/service-application': 'connection-applications',
                    '/customer/service-connection': 'connection-active',
                    '/user/add': 'user-add',
                    '/user/list': 'user-list',
                    '/consumer/list': 'consumer-list',
                    '/billing/management': 'billing-management',
                    '/meter/management': 'meter-management',
                    '/rate/management': 'rate-management',
                    '/ledger/management': 'ledger-management',
                    '/reports': 'reports',
                    '/config/barangays': 'config-geographic-barangays',
                    '/config/areas': 'config-geographic-areas',
                    '/config/puroks': 'config-geographic-puroks',
                    '/config/water-rates': 'config-water-rates',
                    '/config/account-types': 'config-billing-account-types',
                    '/config/charge-items': 'config-billing-charge-items',
                    '/config/penalty': 'config-billing-penalty',
                    '/admin/roles': 'config-access-roles',
                    '/admin/permissions': 'config-access-permissions',
                    '/admin/role-permissions': 'config-access-matrix',
                    '/admin/activity-log': 'activity-log'
                };

                if (path.startsWith('/customer/payment/')) {
                    this.activeMenu = 'payment-management';
                } else {
                    this.activeMenu = routeMap[path] || '{{ session('active_menu', 'dashboard') }}';
                }

                localStorage.setItem('activeMenu', this.activeMenu);

                if (this.activeMenu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (this.activeMenu.startsWith('customer-')) {
                    this.openSubmenus.customerManagement = true;
                } else if (this.activeMenu.startsWith('connection-')) {
                    this.openSubmenus.connectionManagement = true;
                } else if (this.activeMenu.startsWith('config-')) {
                    this.openSubmenus.adminConfig = true;
                    if (this.activeMenu.startsWith('config-geographic-')) {
                        this.openSubmenus.geographic = true;
                    } else if (this.activeMenu.startsWith('config-billing-')) {
                        this.openSubmenus.billingConfig = true;
                    } else if (this.activeMenu.startsWith('config-access-')) {
                        this.openSubmenus.accessControl = true;
                    }
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

                // Define parent-child relationships
                const childSubmenus = {
                    'adminConfig': ['geographic', 'billingConfig', 'accessControl']
                };

                // Check if this is a child submenu
                let parentSubmenu = null;
                for (const [parent, children] of Object.entries(childSubmenus)) {
                    if (children.includes(submenu)) {
                        parentSubmenu = parent;
                        break;
                    }
                }

                // Get list of all child submenus
                const allChildren = Object.values(childSubmenus).flat();

                // Close logic based on hierarchy
                Object.keys(this.openSubmenus).forEach(key => {
                    if (key !== submenu) {
                        // If toggling a child submenu, keep parent open
                        if (parentSubmenu && key === parentSubmenu) {
                            return; // Don't close parent
                        }
                        // If toggling a child, close only sibling children
                        if (parentSubmenu && childSubmenus[parentSubmenu]?.includes(key)) {
                            this.openSubmenus[key] = false; // Close siblings
                        }
                        // If toggling a parent, close all other top-level submenus (but not children yet)
                        else if (!parentSubmenu && !allChildren.includes(key)) {
                            this.openSubmenus[key] = false;
                            // Also close children of other parents
                            if (childSubmenus[key]) {
                                childSubmenus[key].forEach(child => {
                                    this.openSubmenus[child] = false;
                                });
                            }
                        }
                    }
                });

                // Toggle the requested submenu
                this.openSubmenus[submenu] = !this.openSubmenus[submenu];

                // If opening a child, ensure parent is open
                if (parentSubmenu && this.openSubmenus[submenu]) {
                    this.openSubmenus[parentSubmenu] = true;
                }
            },

            setActiveMenu(menu) {
                this.activeMenu = menu;
                localStorage.setItem('activeMenu', menu);
                // Close all submenus first
                this.openSubmenus.userManagement = false;
                this.openSubmenus.customerManagement = false;
                this.openSubmenus.connectionManagement = false;
                this.openSubmenus.adminConfig = false;
                this.openSubmenus.geographic = false;
                this.openSubmenus.billingConfig = false;
                this.openSubmenus.accessControl = false;
                // Open the appropriate submenu
                if (menu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (menu.startsWith('customer-')) {
                    this.openSubmenus.customerManagement = true;
                } else if (menu.startsWith('connection-')) {
                    this.openSubmenus.connectionManagement = true;
                } else if (menu.startsWith('config-')) {
                    this.openSubmenus.adminConfig = true;
                    if (menu.startsWith('config-geographic-')) {
                        this.openSubmenus.geographic = true;
                    } else if (menu.startsWith('config-billing-')) {
                        this.openSubmenus.billingConfig = true;
                    } else if (menu.startsWith('config-access-')) {
                        this.openSubmenus.accessControl = true;
                    }
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
