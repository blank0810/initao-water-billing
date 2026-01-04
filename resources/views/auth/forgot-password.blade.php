<x-guest-layout>
    <div class="flex min-h-screen w-full bg-gray-50 dark:bg-gray-900 overflow-hidden">
        <!-- LEFT: Centered Forgot Password Section -->
        <div class="flex flex-col justify-center items-center w-full lg:w-1/2 px-8 sm:px-16 lg:px-24 relative z-10">
            <div class="w-full max-w-md text-left">
                <!-- Logo + Heading -->
                <div class="flex flex-col items-center mb-10 text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 mb-6">
                    <h2 class="text-3xl font-semibold text-gray-800 dark:text-white mb-2">
                        Forgot Password
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('No worries. Enter your email and we will send you a password reset link.') }}
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Forgot Password Form -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            placeholder="Enter your email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Button -->
                    <div>
                        <x-primary-button
                            type="submit"
                            class="w-full justify-center bg-indigo-600 hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-300 text-white font-semibold py-3 rounded-lg transition-colors duration-300 shadow hover:shadow-md">
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>

                    <!-- Back to Login -->
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                            {{ __('Back to login') }}
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
