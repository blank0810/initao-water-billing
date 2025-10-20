<x-guest-layout>
    <div class="flex min-h-screen w-full bg-gray-50 dark:bg-gray-900 overflow-hidden">
        <!-- LEFT: Centered Login Section -->
        <div class="flex flex-col justify-center items-center w-full lg:w-1/2 px-8 sm:px-16 lg:px-24 relative z-10">
            <div class="w-full max-w-md text-left">
                <!-- Logo + Heading -->
                <div class="flex flex-col items-center mb-10 text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 mb-6">
                    <h2 class="text-3xl font-semibold text-gray-800 dark:text-white mb-2">
                        Administrator Login
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">
                        Enter your registered email and password.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <form id="loginForm" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="Enter your email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password with toggle -->
                    <div class="relative">
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative flex items-center">
                            <x-text-input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white pr-10"
                                placeholder="••••••••" />
                            <!-- Eye icon -->
                            <button type="button" id="togglePassword"
                                class="absolute right-3 flex items-center h-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.19-3.394m2.44-2.44A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.958 9.958 0 01-1.459 2.513M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:focus:ring-indigo-600"
                                name="remember">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Button -->
                    <div>
                        <x-primary-button
                            class="w-full justify-center bg-indigo-600 hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-300 text-white font-semibold py-3 rounded-lg transition-colors duration-300 shadow hover:shadow-md">
                            {{ __('Sign In') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Optional footer or links -->
                <div class="text-left mt-8">
                    <p class="text-gray-500 text-sm dark:text-gray-400">
                        Need help? <a href="#" class="text-indigo-600 hover:underline">Contact support</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT: Image with bottom-only arc -->
        <div class="hidden lg:block w-1/2 h-screen relative overflow-hidden">
            <div class="absolute bottom-0 left-0 w-full h-full bg-cover bg-center rounded-bl-[300px] transition-all duration-700 ease-in-out"
                style="background-image: url('{{ asset('images/auth.jpg') }}');">
            </div>
        </div>
    </div>

    <!-- Password toggle + fake login redirect script -->
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeOpen.classList.toggle('hidden');
            eyeClosed.classList.toggle('hidden');
        });

        // Fake login redirect for frontend only
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // prevent POST to backend
            window.location.href = '/dashboard'; // redirect to dashboard
        });
    </script>
</x-guest-layout>
