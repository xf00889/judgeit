@extends('layouts.app')

@section('content')
<div class="min-h-screen flex relative overflow-hidden">
    <!-- Left side - Enhanced Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-blue-600 relative">
        <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full">
            <!-- Enhanced Logo -->
            <div class="flex items-center space-x-4 mb-12">
                <div class="relative">
                    <svg class="h-16 w-16" viewBox="0 0 54 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#prefix__clip0)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" fill="white"/>
                        </g>
                        <defs>
                            <clipPath id="prefix__clip0">
                                <path fill="#fff" d="M0 0h54v32.4H0z"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <h1 class="text-5xl font-bold text-white">
                    JudgeIt
                </h1>
            </div>
            
            <!-- Welcome Section -->
            <div class="text-center max-w-lg">
                <h2 class="text-4xl font-bold mb-6">Welcome Back!</h2>
                <p class="text-xl text-blue-100 mb-12 leading-relaxed">
                    Experience the future of professional judging with our advanced, intuitive platform
                </p>
            </div>
        </div>
    </div>
    
    <!-- Right side - Enhanced Login Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-white relative">
        <!-- Subtle Pattern Overlay -->
        <div class="absolute inset-0 opacity-5">
            <svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                <g fill="none" fill-rule="evenodd">
                    <g fill="#3b82f6" fill-opacity="0.1">
                        <circle cx="30" cy="30" r="2"/>
                    </g>
                </g>
            </svg>
        </div>
        
        <div class="max-w-md w-full space-y-8 relative z-10">
            <!-- Mobile Logo with Enhanced Animation -->
            <div class="lg:hidden text-center animate-fade-in-down">
                <div class="flex items-center justify-center space-x-3 mb-8">
                    <div class="relative">
                        <svg class="h-12 w-12" viewBox="0 0 54 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#prefix__clip0)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" fill="#3b82f6"/>
                            </g>
                            <defs>
                                <clipPath id="prefix__clip0">
                                    <path fill="#fff" d="M0 0h54v32.4H0z"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <div class="absolute -inset-1 bg-blue-500 rounded-full opacity-20 animate-ping"></div>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        JudgeIt
                    </h1>
                </div>
            </div>
            
            <!-- Enhanced Form Header -->
            <div class="text-center animate-fade-in-up">
                <h2 class="text-4xl font-bold text-gray-900 mb-3">
                    Sign in to your account
                </h2>
                <p class="text-gray-600 text-lg">
                    Enter your credentials to access the judging system
                </p>
                <div class="mt-4 w-24 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full mx-auto"></div>
            </div>
            
            <!-- Enhanced Error Messages -->
            @if ($errors->any())
                <div class="rounded-xl bg-red-50 border border-red-200 p-4 animate-shake">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                @if (session('throttle'))
                                    Too many login attempts. Please try again in {{ session('throttle') }} seconds.
                                @else
                                    {{ $errors->first() }}
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enhanced Login Form -->
            <form class="space-y-6 animate-fade-in-up animation-delay-200" action="{{ route('login.post') }}" method="POST" data-loading="true">
                @csrf
                
                <div class="space-y-5">
                    <!-- Enhanced Email Field -->
                    <div class="group">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors duration-200">
                            Email address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 transform hover:scale-[1.02] focus:scale-[1.02] @error('email') border-red-400 focus:ring-red-500 @enderror" 
                                   placeholder="Enter your email address" value="{{ old('email') }}">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full opacity-0 transition-opacity duration-200" id="email-valid"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Password Field -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors duration-200">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="block w-full pl-12 pr-12 py-4 border-2 border-gray-200 rounded-xl placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 transform hover:scale-[1.02] focus:scale-[1.02] @error('password') border-red-400 focus:ring-red-500 @enderror" 
                                   placeholder="Enter your password">
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center" onclick="togglePassword()">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="password-toggle">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg transition duration-200 transform hover:scale-110">
                        <label for="remember" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                            Remember me for 30 days
                        </label>
                    </div>
                </div>

                <!-- Enhanced Submit Button -->
                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <div class="loading-spinner hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-200 transition duration-200 login-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <span class="button-text">Sign in to JudgeIt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-left {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out;
}

.animate-fade-in-down {
    animation: fade-in-down 0.6s ease-out;
}

.animate-fade-in-left {
    animation: fade-in-left 0.6s ease-out;
}

.animate-shake {
    animation: shake 0.5s ease-in-out;
}

.animation-delay-200 {
    animation-delay: 200ms;
}

.animation-delay-400 {
    animation-delay: 400ms;
}

.animation-delay-600 {
    animation-delay: 600ms;
}

.animation-delay-800 {
    animation-delay: 800ms;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
}

.animation-delay-2000 {
    animation-delay: 2000ms;
}

.animation-delay-4000 {
    animation-delay: 4000ms;
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
        `;
    } else {
        passwordInput.type = 'password';
        toggleIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}

// Email validation indicator
document.getElementById('email').addEventListener('input', function(e) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const indicator = document.getElementById('email-valid');
    
    if (emailRegex.test(e.target.value)) {
        indicator.style.opacity = '1';
    } else {
        indicator.style.opacity = '0';
    }
});

// Enhanced form submission
document.querySelector('form').addEventListener('submit', function() {
    const button = this.querySelector('button[type="submit"]');
    const buttonText = button.querySelector('.button-text');
    const loginIcon = button.querySelector('.login-icon');
    const spinner = button.querySelector('.loading-spinner');
    
    buttonText.textContent = 'Signing in...';
    loginIcon.style.display = 'none';
    spinner.classList.remove('hidden');
    button.disabled = true;
});
</script>
@endsection
