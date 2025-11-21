<x-guest-layout>
    <div class="flex min-h-screen w-full bg-gray-50 dark:bg-gray-900 overflow-hidden">
        <!-- LEFT: Centered Register Section -->
        <div class="flex flex-col justify-center items-center w-full lg:w-1/2 px-8 sm:px-16 lg:px-24 relative z-10">
            <div class="w-full max-w-md text-left">
                <!-- Logo + Heading -->
                <div class="flex flex-col items-center mb-10 text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 mb-6">
                    <h2 class="text-3xl font-semibold text-gray-800 dark:text-white mb-2">
                        Create an Account
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('Register your account to get started.') }}
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="Enter your full name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="Enter your email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <x-input-label for="password" :value="__('Password')" />
                        <div class="relative flex items-center">
                            <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white pr-10"
                                placeholder="••••••••" />
                            <!-- Eye toggle -->
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

                    <!-- Confirm Password -->
                    <div class="relative">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <div class="relative flex items-center">
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white pr-10"
                                placeholder="••••••••" />
                            <!-- Eye toggle confirm -->
                            <button type="button" id="toggleConfirmPassword"
                                class="absolute right-3 flex items-center h-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.19-3.394m2.44-2.44A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.958 9.958 0 01-1.459 2.513M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Password toggle script -->
                    <script>
                        const togglePassword = document.getElementById('togglePassword');
                        const password = document.getElementById('password');
                        const eyeOpen = document.getElementById('eyeOpen');
                        const eyeClosed = document.getElementById('eyeClosed');

                        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                        const passwordConfirm = document.getElementById('password_confirmation');
                        const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
                        const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');

                        togglePassword.addEventListener('click', function() {
                            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                            password.setAttribute('type', type);
                            eyeOpen.classList.toggle('hidden');
                            eyeClosed.classList.toggle('hidden');
                        });

                        toggleConfirmPassword.addEventListener('click', function() {
                            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                            passwordConfirm.setAttribute('type', type);
                            eyeOpenConfirm.classList.toggle('hidden');
                            eyeClosedConfirm.classList.toggle('hidden');
                        });
                    </script>

                    <!-- Register Button -->
                    <div>
                        <x-primary-button
                            class="w-full justify-center bg-indigo-600 hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-300 text-white font-semibold py-3 rounded-lg transition-colors duration-300 shadow hover:shadow-md">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>

                    <!-- Already have account -->
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            {{ __('Already registered? Login here') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Image with bottom-left arc -->
        <div class="hidden lg:block w-1/2 h-screen relative overflow-hidden">
            <div class="absolute bottom-0 left-0 w-full h-full bg-cover bg-center rounded-bl-[300px] transition-all duration-700 ease-in-out"
                style="background-image: url('{{ asset('images/auth.jpg') }}');">
            </div>
        </div>
    </div>
</x-guest-layout>
