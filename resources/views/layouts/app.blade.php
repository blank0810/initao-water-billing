<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Theme initialization - MUST run before vite scripts -->
        <script>
            (function() {
                try {
                    const theme = localStorage.getItem('theme-preference');
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    // Determine if dark mode should be active
                    const shouldBeDark = theme === 'dark' || (theme === null && prefersDark);

                    // Apply immediately to prevent flash
                    if (shouldBeDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (e) {
                    console.error('Theme init error:', e);
                }
            })();
        </script>

        <!-- Export Libraries -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/network-detector.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }

            /* Toast animations */
            @keyframes slide-in {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
                transition: all 0.3s ease-out;
            }

            /* Smooth transitions for sidebar */
            .sidebar-transition {
                transition: all 0.3s ease-in-out;
            }

            /* Fix scroll issues */
            body, html {
                overflow-x: hidden;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            /* Ensure main container doesn't cause overflow */
            .min-h-screen {
                min-height: 100vh;
            }

            /* Custom scrollbar for sidebar */
            .sidebar-scroll {
                scrollbar-width: thin;
                scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
            }

            .sidebar-scroll::-webkit-scrollbar {
                width: 4px;
            }

            .sidebar-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .sidebar-scroll::-webkit-scrollbar-thumb {
                background-color: rgba(156, 163, 175, 0.5);
                border-radius: 20px;
            }

            .sidebar-scroll::-webkit-scrollbar-thumb:hover {
                background-color: rgba(156, 163, 175, 0.7);
            }

            /* Mobile sidebar overlay */
            @media (max-width: 1023px) {
                aside {
                    z-index: 50 !important;
                }

                nav {
                    z-index: 40 !important;
                    position: relative;
                }

                .sidebar-transition {
                    margin-left: 0 !important;
                    padding-left: 0 !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased h-full">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="appState()">
            @include('components.sidebar-revamped')

            <!-- Main Content Area -->
            <div class="sidebar-transition"
                 :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-20'"
                 x-init="$watch('sidebarOpen', value => {
                    localStorage.setItem('sidebarOpen', value);
                 })">

                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="p-4 sm:p-6">
                    {{-- Support both component and section syntax --}}
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </main>

                <x-footer />
            </div>
        </div>

        <script>
            function appState() {
                return {
                    sidebarOpen: true,

                    init() {
                        // Get sidebar state from localStorage
                        const savedSidebarState = localStorage.getItem('sidebarOpen');
                        if (savedSidebarState !== null) {
                            this.sidebarOpen = savedSidebarState === 'true';
                        }

                        // Make instance globally available
                        window.appState = this;
                    },

                    toggleSidebar() {
                        this.sidebarOpen = !this.sidebarOpen;
                        localStorage.setItem('sidebarOpen', this.sidebarOpen);
                    }
                }
            }
        </script>

    <!--@ include('components.page-loader')-->
   

        @stack('scripts')

        <!-- Global Error Handling -->
        <script>
            // Handle offline/online events
            window.addEventListener('offline', function() {
                if (!window.location.pathname.includes('/no-internet-found')) {
                    window.location.href = '/no-internet-found';
                }
            });

            // Handle 404 errors from fetch/axios
            window.addEventListener('unhandledrejection', function(event) {
                if (event.reason && event.reason.response && event.reason.response.status === 404) {
                    console.warn('404 error detected:', event.reason);
                }
            });

            // Check connection status on load
            if (!navigator.onLine && !window.location.pathname.includes('/no-internet-found')) {
                window.location.href = '/no-internet-found';
            }
        </script>
    </body>
</html>
