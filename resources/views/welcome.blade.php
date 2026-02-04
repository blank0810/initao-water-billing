<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Initao Water Billing System - Reliable water service for over 33,000 residents across 16 barangays in Initao, Misamis Oriental. Pay bills, check usage, and manage your water service online.">
    <meta name="keywords" content="Initao, Water Billing, Misamis Oriental, LGU, Municipal Water, Pay Bill, Water Service">
    <meta name="author" content="Municipality of Initao">

    <!-- Open Graph / Social -->
    <meta property="og:title" content="Initao Water Billing System">
    <meta property="og:description" content="Reliable water service for Initao, Misamis Oriental. Pay bills and manage your water service online.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_PH">

    <title>Initao Water Billing System - Municipality of Initao</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-white text-slate-900" x-data="{ mobileMenuOpen: false }">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Initao Water" class="h-10 w-10">
                    <div class="hidden sm:block">
                        <p class="font-bold text-slate-900 leading-tight">Initao Water</p>
                        <p class="text-xs text-slate-500">Billing System</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#services" class="text-sm font-medium text-slate-600 hover:text-sky-600 transition">Services</a>
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-sky-600 transition">About</a>
                    <a href="#announcements" class="text-sm font-medium text-slate-600 hover:text-sky-600 transition">Announcements</a>
                    <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-sky-600 transition">Contact</a>
                </div>

                <!-- Login Button -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-sky-600 transition">
                            Log in
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition shadow-sm">
                            Staff Portal
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden py-4 border-t border-slate-200">
                <div class="flex flex-col space-y-3">
                    <a href="#services" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-600 hover:text-sky-600">Services</a>
                    <a href="#about" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-600 hover:text-sky-600">About</a>
                    <a href="#announcements" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-600 hover:text-sky-600">Announcements</a>
                    <a href="#contact" @click="mobileMenuOpen = false" class="text-sm font-medium text-slate-600 hover:text-sky-600">Contact</a>
                    <hr class="border-slate-200">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition">
                        Staff Portal
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-sky-600 via-sky-500 to-teal-400 pt-16">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="water-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="2" fill="white"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#water-pattern)"/>
            </svg>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Eyebrow -->
            <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium mb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Municipality of Initao Water Billing System
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
                Clean Water,<br>
                <span class="text-sky-100">Better Life</span>
            </h1>

            <!-- Subheadline -->
            <p class="text-lg sm:text-xl text-sky-100 max-w-2xl mx-auto mb-10">
                Reliable water service for over 33,000 residents across 16 barangays in Initao, Misamis Oriental.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('coming-soon') }}" class="inline-flex items-center px-8 py-4 bg-white text-sky-600 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:bg-sky-50 transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Pay Your Bill
                </a>
                <a href="{{ route('coming-soon') }}" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white/10 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Check Usage
                </a>
            </div>
        </div>

        <!-- Wave Decoration -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg class="w-full h-24 sm:h-32" viewBox="0 0 1440 120" fill="none" preserveAspectRatio="none">
                <path d="M0,64 C480,150 960,-20 1440,64 L1440,120 L0,120 Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="py-16 sm:py-20 bg-white -mt-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">What would you like to do?</h2>
                <p class="text-slate-600 max-w-xl mx-auto">Quick access to our most popular services</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pay Bill -->
                <a href="{{ route('coming-soon') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-lg hover:border-sky-300 transition-all duration-300">
                    <div class="w-14 h-14 bg-sky-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-sky-500 transition-colors">
                        <svg class="w-7 h-7 text-sky-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Pay Bill</h3>
                    <p class="text-sm text-slate-500">Pay your water bill online securely</p>
                </a>

                <!-- View Usage -->
                <a href="{{ route('coming-soon') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-lg hover:border-sky-300 transition-all duration-300">
                    <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-500 transition-colors">
                        <svg class="w-7 h-7 text-teal-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">View Usage</h3>
                    <p class="text-sm text-slate-500">Check your water consumption history</p>
                </a>

                <!-- New Connection -->
                <a href="{{ route('coming-soon') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-lg hover:border-sky-300 transition-all duration-300">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 transition-colors">
                        <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">New Connection</h3>
                    <p class="text-sm text-slate-500">Apply for water service connection</p>
                </a>

                <!-- Report Issue -->
                <a href="{{ route('coming-soon') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-lg hover:border-sky-300 transition-all duration-300">
                    <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-500 transition-colors">
                        <svg class="w-7 h-7 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Report Issue</h3>
                    <p class="text-sm text-slate-500">Report leaks or service problems</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Our Services</h2>
                <p class="text-slate-600 max-w-xl mx-auto">Committed to serving Initao's water needs with excellence</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Residential -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Residential Connections</h3>
                    <p class="text-slate-500 text-sm">Reliable water supply for homes and households throughout Initao's 16 barangays.</p>
                </div>

                <!-- Commercial -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Commercial Connections</h3>
                    <p class="text-slate-500 text-sm">Water solutions for businesses, offices, and industrial establishments.</p>
                </div>

                <!-- Meter Reading -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Meter Reading</h3>
                    <p class="text-slate-500 text-sm">Accurate monthly meter readings to ensure fair and transparent billing.</p>
                </div>

                <!-- Billing -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Billing & Payments</h3>
                    <p class="text-slate-500 text-sm">Convenient payment options and transparent billing statements.</p>
                </div>

                <!-- Maintenance -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Maintenance & Repairs</h3>
                    <p class="text-slate-500 text-sm">Quick response to leaks, repairs, and infrastructure maintenance.</p>
                </div>

                <!-- Support -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-slate-900 mb-2">Customer Support</h3>
                    <p class="text-slate-500 text-sm">Dedicated support team ready to assist with your inquiries and concerns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-6">Serving Initao with Pride</h2>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        The Initao Municipal Water System is committed to providing clean, safe, and affordable water to all residents. As a 1st class municipality in Misamis Oriental, we serve 16 barangays with reliable water infrastructure and dedicated service.
                    </p>
                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Our mission is to ensure every household and business in Initao has access to quality water services, supporting the community's growth and well-being.
                    </p>
                    <a href="{{ route('coming-soon') }}" class="inline-flex items-center text-sky-600 font-medium hover:text-sky-700 transition">
                        Learn more about us
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-sky-500 to-sky-600 p-6 rounded-2xl text-white">
                        <p class="text-4xl font-bold mb-1">33K+</p>
                        <p class="text-sky-100 text-sm">Residents Served</p>
                    </div>
                    <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-6 rounded-2xl text-white">
                        <p class="text-4xl font-bold mb-1">16</p>
                        <p class="text-teal-100 text-sm">Barangays Covered</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 rounded-2xl text-white">
                        <p class="text-4xl font-bold mb-1">24/7</p>
                        <p class="text-emerald-100 text-sm">Support Available</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-2xl text-white">
                        <p class="text-4xl font-bold mb-1">99%</p>
                        <p class="text-amber-100 text-sm">Service Reliability</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcements Section -->
    <section id="announcements" class="py-16 sm:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Latest Announcements</h2>
                <p class="text-slate-600 max-w-xl mx-auto">Stay updated with service news and advisories</p>
            </div>

            <div class="max-w-3xl mx-auto space-y-4">
                <!-- Advisory -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded mb-2">Service Advisory</span>
                                <h3 class="font-semibold text-slate-900">Scheduled Maintenance in Brgy. Poblacion</h3>
                                <p class="text-slate-500 text-sm mt-1">Water interruption expected from 8:00 AM to 12:00 PM for pipe repairs.</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">Jan 3</span>
                    </div>
                </div>

                <!-- News -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-block px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded mb-2">Good News</span>
                                <h3 class="font-semibold text-slate-900">New Year, Same Reliable Service</h3>
                                <p class="text-slate-500 text-sm mt-1">Thank you for trusting Initao Water. Wishing everyone a prosperous 2025!</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">Jan 1</span>
                    </div>
                </div>

                <!-- Reminder -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-block px-2 py-1 bg-amber-100 text-amber-700 text-xs font-medium rounded mb-2">Reminder</span>
                                <h3 class="font-semibold text-slate-900">Pay Your Bills Before January 15</h3>
                                <p class="text-slate-500 text-sm mt-1">Avoid disconnection penalties by paying your water bills on time.</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">Dec 28</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('coming-soon') }}" class="inline-flex items-center text-sky-600 font-medium hover:text-sky-700 transition">
                    View all announcements
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">Contact Us</h2>
                <p class="text-slate-600 max-w-xl mx-auto">We're here to help with your water service needs</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Office Address</h3>
                            <p class="text-slate-600">Jampason, Initao<br>Misamis Oriental</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Phone Number</h3>
                            <p class="text-slate-600">(088) 123-4567</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Email Address</h3>
                            <p class="text-slate-600">water@initao.gov.ph</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Office Hours</h3>
                            <p class="text-slate-600">Monday - Friday<br>8:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>

                <!-- Map - Initao Municipal Hall (Plus Code: G855+HPX Initao, Misamis Oriental) -->
                <div class="h-80 lg:h-full min-h-[320px] rounded-2xl overflow-hidden shadow-lg">
                    @if(config('services.google_maps.api_key'))
                        <iframe
                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key') }}&q=G855%2BHPX+Initao,+Misamis+Oriental&zoom=18&maptype=satellite"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    @else
                        <iframe
                            src="https://maps.google.com/maps?q=G855%2BHPX+Initao,+Misamis+Oriental&t=k&z=18&ie=UTF8&iwloc=&output=embed"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Initao Water" class="h-10 w-10">
                        <div>
                            <p class="font-bold leading-tight">Initao Water</p>
                            <p class="text-xs text-slate-400">Billing System</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm">
                        Providing clean and reliable water service to the Municipality of Initao, Misamis Oriental.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-slate-400 hover:text-white transition">Home</a></li>
                        <li><a href="#about" class="text-slate-400 hover:text-white transition">About</a></li>
                        <li><a href="#services" class="text-slate-400 hover:text-white transition">Services</a></li>
                        <li><a href="#contact" class="text-slate-400 hover:text-white transition">Contact</a></li>
                        <li><a href="#announcements" class="text-slate-400 hover:text-white transition">Announcements</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="font-semibold mb-4">Services</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">Pay Bill</a></li>
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">New Connection</a></li>
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">Report Issue</a></li>
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">View Rates</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="{{ route('coming-soon') }}" class="text-slate-400 hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <hr class="border-slate-800 my-8">

            <div class="flex flex-col md:flex-row items-center justify-between text-sm text-slate-400">
                <p>&copy; {{ date('Y') }} Municipality of Initao. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Powered by MEEDO (Municipal Economic Enterprise Development Office)</p>
            </div>
        </div>
    </footer>

</body>
</html>
