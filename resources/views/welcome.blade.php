<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JudgeIt - Professional Judging System</title>
    <!-- Using system fonts for offline compatibility -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        @keyframes gradient { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-gradient { animation: gradient 15s ease infinite; background-size: 200% 200%; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 glass shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg blur opacity-75 group-hover:opacity-100 transition duration-300"></div>
                        <svg class="h-10 w-10 relative" viewBox="0 0 54 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#prefix__clip0)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" fill="url(#gradient1)"/>
                            </g>
                            <defs>
                                <linearGradient id="gradient1" x1="0" y1="0" x2="54" y2="32.4" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#3b82f6"/>
                                    <stop offset="1" stop-color="#6366f1"/>
                                </linearGradient>
                                <clipPath id="prefix__clip0">
                                    <path fill="#fff" d="M0 0h54v32.4H0z"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">JudgeIt</span>
                </div>
                
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="group relative inline-flex items-center px-6 py-3 overflow-hidden font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative">Dashboard</span>
                                <svg class="relative ml-2 h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="group relative inline-flex items-center px-6 py-3 overflow-hidden font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative">Sign In</span>
                                <svg class="relative ml-2 h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
            <div class="absolute top-40 right-10 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 mb-8 glass rounded-full shadow-lg animate-fadeInUp">
                    <span class="flex h-2 w-2 relative mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Live Scoring & Real-time Results</span>
                </div>

                <h1 class="text-6xl md:text-7xl lg:text-8xl font-extrabold text-gray-900 mb-8 leading-tight animate-fadeInUp" style="animation-delay: 0.1s;">
                    Professional Judging
                    <span class="block mt-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent animate-gradient">Made Simple</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed animate-fadeInUp" style="animation-delay: 0.2s;">
                    A comprehensive platform for managing competitions with real-time scoring, 
                    judge assignments, and instant results tabulation.
                </p>
                
                @if (Route::has('login'))
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fadeInUp" style="animation-delay: 0.3s;">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="group relative inline-flex items-center px-10 py-4 overflow-hidden font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-2xl hover:shadow-blue-500/50 transform hover:scale-105 transition-all duration-300">
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative text-lg">Go to Dashboard</span>
                                <svg class="relative ml-3 h-6 w-6 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="group relative inline-flex items-center px-10 py-4 overflow-hidden font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-2xl hover:shadow-blue-500/50 transform hover:scale-105 transition-all duration-300">
                                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative text-lg">Get Started</span>
                                <svg class="relative ml-3 h-6 w-6 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>
                            <a href="#features" class="inline-flex items-center px-10 py-4 font-semibold text-gray-700 glass rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                                <span class="text-lg">Learn More</span>
                                <svg class="ml-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </a>
                        @endauth
                    </div>
                @endif

                <!-- Stats -->
                <div class="mt-20 grid grid-cols-3 gap-8 max-w-3xl mx-auto animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">100%</div>
                        <div class="text-sm text-gray-600 mt-2">Accurate Scoring</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Real-time</div>
                        <div class="text-sm text-gray-600 mt-2">Live Results</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Instant</div>
                        <div class="text-sm text-gray-600 mt-2">Tabulation</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center mb-16">
            <h2 class="text-5xl font-extrabold text-gray-900 mb-4">Powerful Features</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to run a seamless competition</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group relative glass rounded-3xl p-8 hover:shadow-2xl hover:shadow-blue-500/20 transform hover:-translate-y-2 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Judge Management</h3>
                    <p class="text-gray-600 leading-relaxed">Assign judges to specific categories and manage their scoring permissions with ease.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="group relative glass rounded-3xl p-8 hover:shadow-2xl hover:shadow-indigo-500/20 transform hover:-translate-y-2 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Real-time Scoring</h3>
                    <p class="text-gray-600 leading-relaxed">Judges can submit scores instantly with automatic validation and calculation.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="group relative glass rounded-3xl p-8 hover:shadow-2xl hover:shadow-purple-500/20 transform hover:-translate-y-2 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Instant Results</h3>
                    <p class="text-gray-600 leading-relaxed">View live rankings and final results with comprehensive scoring breakdowns.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Section -->
    <div class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzMuMzEgMCA2LTIuNjkgNi02cy0yLjY5LTYtNi02LTYgMi42OS02IDYgMi42OSA2IDYgNnptMC0xMGMxLjEgMCAyIC45IDIgMnMtLjkgMi0yIDItMi0uOS0yLTIgLjktMiAyLTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-20"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-extrabold text-white mb-4">System Roles</h2>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">Designed for every stakeholder in your competition</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Admin Role -->
                <div class="group relative bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 hover:bg-white/20 hover:border-white/40 transform hover:-translate-y-2 transition-all duration-300">
                    <div class="text-center">
                        <div class="relative inline-block mb-6">
                            <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                            <div class="relative w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Admin</h3>
                        <p class="text-blue-100 leading-relaxed">Manage contestants, categories, criteria, and user assignments</p>
                    </div>
                </div>

                <!-- Judge Role -->
                <div class="group relative bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 hover:bg-white/20 hover:border-white/40 transform hover:-translate-y-2 transition-all duration-300">
                    <div class="text-center">
                        <div class="relative inline-block mb-6">
                            <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                            <div class="relative w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Judge</h3>
                        <p class="text-blue-100 leading-relaxed">Submit scores for assigned categories and contestants</p>
                    </div>
                </div>

                <!-- Tabulator Role -->
                <div class="group relative bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 hover:bg-white/20 hover:border-white/40 transform hover:-translate-y-2 transition-all duration-300">
                    <div class="text-center">
                        <div class="relative inline-block mb-6">
                            <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
                            <div class="relative w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-10 w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-3">Tabulator</h3>
                        <p class="text-blue-100 leading-relaxed">View real-time results and generate final rankings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-white py-16">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMiI+PHBhdGggZD0iTTM2IDE0YzMuMzEgMCA2LTIuNjkgNi02cy0yLjY5LTYtNi02LTYgMi42OS02IDYgMi42OSA2IDYgNnptMC0xMGMxLjEgMCAyIC45IDIgMnMtLjkgMi0yIDItMi0uOS0yLTIgLjktMiAyLTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-50"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center">
                <div class="flex items-center space-x-3 mb-6">
                    <svg class="h-10 w-10" viewBox="0 0 54 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#prefix__clip0)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" fill="url(#gradient2)"/>
                        </g>
                        <defs>
                            <linearGradient id="gradient2" x1="0" y1="0" x2="54" y2="32.4" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#60a5fa"/>
                                <stop offset="1" stop-color="#a78bfa"/>
                            </linearGradient>
                            <clipPath id="prefix__clip0">
                                <path fill="#fff" d="M0 0h54v32.4H0z"/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">JudgeIt</span>
                </div>
                
                <p class="text-gray-400 text-center mb-8 max-w-md">
                    Professional judging made simple. Streamline your competitions with real-time scoring and instant results.
                </p>
                
                <div class="border-t border-gray-800 pt-8 w-full text-center">
                    <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} JudgeIt. All rights reserved. Built with precision and care.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
