<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="This feature is coming soon - Initao Water Billing System">

    <title>Coming Soon - Initao Water Billing System</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-sky-600 via-sky-500 to-teal-400 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full text-center">
        <!-- Animated Icon -->
        <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center animate-pulse">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 sm:p-12 shadow-2xl">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                Coming Soon
            </h1>

            <p class="text-sky-100 text-lg mb-8">
                We're working hard to bring you this feature. It will be available soon!
            </p>

            <!-- Features List -->
            <div class="text-left bg-white/10 rounded-xl p-6 mb-8">
                <p class="text-white font-medium mb-3">What to expect:</p>
                <ul class="space-y-2 text-sky-100 text-sm">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Online bill payment
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        View consumption history
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Apply for new connections
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Report service issues
                    </li>
                </ul>
            </div>

            <!-- Back Button -->
            <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-white text-sky-600 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:bg-sky-50 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Contact Info -->
        <p class="text-sky-100 text-sm mt-8">
            Need assistance now? Call us at <span class="font-medium text-white">(088) 123-4567</span>
        </p>
    </div>

</body>
</html>
