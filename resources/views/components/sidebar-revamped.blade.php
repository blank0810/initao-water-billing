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
                :class="openSubmenus.userManagement && 'icon-glow'"
                class="nav-btn w-full"
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
                @can('settings.manage')
                <a href="{{ route('admin.roles.index') }}" @click="setActiveMenu('user-roles')"
                    :class="activeMenu === 'user-roles' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-user-shield"></i>
                    <span>Roles</span>
                </a>
                <a href="{{ route('admin.permissions.index') }}" @click="setActiveMenu('user-permissions')"
                    :class="activeMenu === 'user-permissions' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-key"></i>
                    <span>Permissions</span>
                </a>
                <a href="{{ route('admin.role-permissions.matrix') }}" @click="setActiveMenu('user-matrix')"
                    :class="activeMenu === 'user-matrix' && 'active'"
                    class="nav-submenu-item">
                    <span class="nav-submenu-notch"></span>
                    <i class="fas fa-th"></i>
                    <span>Permission Matrix</span>
                </a>
                @endcan
            </div>
        </div>
        @endcan

        <!-- Connection Management -->
        @can('customers.manage')
        <div>
            <button @click="toggleSubmenu('connectionManagement')"
                :class="openSubmenus.connectionManagement && 'icon-glow'"
                class="nav-btn w-full"
                :data-tooltip="!sidebarOpen ? 'Connections' : null">
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
                :class="openSubmenus.customerManagement && 'icon-glow'"
                class="nav-btn w-full"
                :data-tooltip="!sidebarOpen ? 'Customers' : null">
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

        <!-- Analytics -->
        @can('reports.view')
        <a href="{{ route('analytics') }}" @click="setActiveMenu('analytics')"
            :class="activeMenu === 'analytics' && 'active'"
            class="nav-btn"
            :data-tooltip="!sidebarOpen ? 'Analytics' : null">
            <span class="nav-btn-notch"></span>
            <div class="nav-btn-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="nav-btn-text" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Analytics</span>
        </a>
        @endcan

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
                connectionManagement: {{ session('active_menu') && str_starts_with(session('active_menu'), 'connection-') ? 'true' : 'false' }}
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
                    '/analytics': 'analytics',
                    '/admin/roles': 'user-roles',
                    '/admin/permissions': 'user-permissions',
                    '/admin/role-permissions': 'user-matrix',
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
                // Close all submenus first
                this.openSubmenus.userManagement = false;
                this.openSubmenus.customerManagement = false;
                this.openSubmenus.connectionManagement = false;
                // Open the appropriate submenu
                if (menu.startsWith('user-')) {
                    this.openSubmenus.userManagement = true;
                } else if (menu.startsWith('customer-')) {
                    this.openSubmenus.customerManagement = true;
                } else if (menu.startsWith('connection-')) {
                    this.openSubmenus.connectionManagement = true;
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
