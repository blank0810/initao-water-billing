<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <!-- Additional styles from pages -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased h-full">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('components.sidebar')

            <div class="ml-0 lg:ml-72">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- AlpineJS -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- Theme Management Script -->
        <script>
            // Initialize theme immediately to prevent flash
            (function() {
                const savedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();

            // Theme management functions
            function toggleTheme() {
                const html = document.documentElement;

                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    return 'light';
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    return 'dark';
                }
            }

            // Alpine.js theme component
            document.addEventListener('alpine:init', () => {
                Alpine.data('themeToggle', () => {
                    return {
                        isDark: document.documentElement.classList.contains('dark'),

                        init() {
                            // Watch for class changes on html element
                            const observer = new MutationObserver((mutations) => {
                                mutations.forEach((mutation) => {
                                    if (mutation.attributeName === 'class') {
                                        this.isDark = document.documentElement.classList.contains('dark');
                                    }
                                });
                            });

                            observer.observe(document.documentElement, {
                                attributes: true,
                                attributeFilter: ['class']
                            });
                        },

                        toggle() {
                            const newTheme = toggleTheme();
                            this.isDark = newTheme === 'dark';
                        }
                    }
                });
            });
        </script>

        <!-- Additional scripts from pages -->
        @stack('scripts')
    </body>
</html>
