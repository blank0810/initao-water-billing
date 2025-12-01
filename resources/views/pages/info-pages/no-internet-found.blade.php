@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        <!-- No Internet Icon -->
        <div class="mb-8">
            <div class="mx-auto w-24 h-24 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728m0 0L12 12m-6.364 6.364L12 12m6.364-6.364L12 12"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">No Internet Connection</h1>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Please check your internet connection and try again.</p>
            
            <!-- Connection Status -->
            <div id="connectionStatus" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                <div class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                Offline
            </div>
        </div>

        <!-- Troubleshooting Tips -->
        <div class="mb-8 text-left bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Troubleshooting Tips:</h3>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    Check your Wi-Fi or ethernet connection
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    Restart your router or modem
                </li>
                <li class="flex items-start">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                    Contact your internet service provider
                </li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <button onclick="checkConnection()" 
                    id="retryBtn"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span id="retryText">Check Connection</span>
            </button>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                </a>
                
                <button onclick="history.back()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function updateConnectionStatus(isOnline) {
    const statusEl = document.getElementById('connectionStatus');
    if (isOnline) {
        statusEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
        statusEl.innerHTML = '<div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>Online';
    } else {
        statusEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        statusEl.innerHTML = '<div class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></div>Offline';
    }
}

async function checkConnection() {
    const retryBtn = document.getElementById('retryBtn');
    const retryText = document.getElementById('retryText');
    
    retryBtn.disabled = true;
    retryText.textContent = 'Checking...';
    
    try {
        const response = await fetch('/', { 
            method: 'HEAD',
            cache: 'no-cache',
            timeout: 5000
        });
        
        if (response.ok) {
            updateConnectionStatus(true);
            retryText.textContent = 'Connected! Redirecting...';
            setTimeout(() => {
                window.location.href = '{{ route("dashboard") }}';
            }, 1500);
        } else {
            throw new Error('Connection failed');
        }
    } catch (error) {
        updateConnectionStatus(false);
        retryText.textContent = 'Still Offline';
        setTimeout(() => {
            retryBtn.disabled = false;
            retryText.textContent = 'Check Connection';
        }, 2000);
    }
}

// Monitor connection status
window.addEventListener('online', () => updateConnectionStatus(true));
window.addEventListener('offline', () => updateConnectionStatus(false));

// Initial status check
updateConnectionStatus(navigator.onLine);
</script>
@endsection