<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Beauty Pageant Judging System') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Toast animation */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast-enter {
            animation: slideInRight 0.3s ease-out;
        }
        
        .toast-exit {
            animation: slideOutRight 0.3s ease-in;
        }

        /* Dropdown menu styles */
        .dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 50;
            min-width: 12rem;
            margin-top: 0;
            padding-top: 0.25rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .dropdown:hover .dropdown-menu,
        .dropdown-menu:hover {
            display: block;
        }
        
        .dropdown-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #4b5563;
            text-decoration: none;
            transition: background-color 0.15s;
        }
        
        .dropdown-menu a:hover {
            background-color: #f3f4f6;
        }
        
        .dropdown-menu a:first-child {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        
        .dropdown-menu a:last-child {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        
        .dropdown-menu a.active {
            background-color: #eff6ff;
            color: #2563eb;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Desktop Navigation -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 text-gray-900 hover:text-gray-700">
                            <!-- Tailwind CSS Logo -->
                            <svg class="h-8 w-8" viewBox="0 0 54 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#prefix__clip0)">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" fill="#38bdf8"/>
                                </g>
                                <defs>
                                    <clipPath id="prefix__clip0">
                                        <path fill="#fff" d="M0 0h54v32.4H0z"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            <span class="text-xl font-bold">JudgeIt</span>
                        </a>
                    </div>
                    
                    @auth
                        <!-- Desktop Navigation -->
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            @if(auth()->user()->isAdmin())
                                <!-- Management Dropdown -->
                                <div class="dropdown inline-flex items-center">
                                    <button class="inline-flex items-center px-1 h-16 border-b-2 text-sm font-medium {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.contestants.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Management
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Users
                                        </a>
                                        <a href="{{ route('admin.contestants.index') }}" class="{{ request()->routeIs('admin.contestants.*') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Contestants
                                        </a>
                                    </div>
                                </div>

                                <!-- Competition Setup Dropdown -->
                                <div class="dropdown inline-flex items-center">
                                    <button class="inline-flex items-center px-1 h-16 border-b-2 text-sm font-medium {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.criteria.*') || request()->routeIs('admin.judges.*') || request()->routeIs('admin.overall-rankings.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Setup
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.criteria.*') || request()->routeIs('admin.judges.*') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Categories
                                        </a>
                                        <a href="{{ route('admin.overall-rankings.index') }}" class="{{ request()->routeIs('admin.overall-rankings.*') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Overall Rankings
                                        </a>
                                    </div>
                                </div>

                                <!-- Scoresheets -->
                                <a href="{{ route('admin.scoresheets.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.scoresheets.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Scoresheets
                                </a>

                                <!-- Finalists -->
                                <a href="{{ route('tabulator.finalists.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tabulator.finalists*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Finalists
                                </a>
                            @endif
                            @if(auth()->user()->isJudge())
                                <a href="{{ route('judge.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('judge.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Score Sheet
                                </a>
                            @endif
                            @if(auth()->user()->isTabulator())
                                <!-- Results & Rankings Dropdown -->
                                <div class="dropdown inline-flex items-center">
                                    <button class="inline-flex items-center px-1 h-16 border-b-2 text-sm font-medium {{ request()->routeIs('tabulator.results') || request()->routeIs('tabulator.rankings') || request()->routeIs('tabulator.awards') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Results
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('tabulator.results') }}" class="{{ request()->routeIs('tabulator.results') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Category Results
                                        </a>
                                        <a href="{{ route('tabulator.rankings') }}" class="{{ request()->routeIs('tabulator.rankings') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                            Overall Rankings
                                        </a>
                                        <a href="{{ route('tabulator.awards') }}" class="{{ request()->routeIs('tabulator.awards') ? 'active' : '' }}">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Awards
                                        </a>
                                    </div>
                                </div>

                                <!-- Scoresheets -->
                                <a href="{{ route('tabulator.scoresheets') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tabulator.scoresheets*') && !request()->routeIs('tabulator.finalists*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Scoresheets
                                </a>

                                <!-- Finalists -->
                                <a href="{{ route('tabulator.finalists.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tabulator.finalists*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Finalists
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>
                
                <!-- User Menu and Mobile Button -->
                <div class="flex items-center">
                    @auth
                        <div class="hidden md:flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">
                                {{ auth()->user()->name }}
                                <span class="text-xs text-gray-500 ml-1">({{ ucfirst(auth()->user()->role->value) }})</span>
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                        
                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-menu-button">
                                <span class="sr-only">Open main menu</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        @auth
            <div class="md:hidden" id="mobile-menu" style="display: none;">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-gray-50 border-t border-gray-200">
                    @if(auth()->user()->isAdmin())
                        <!-- Management Section -->
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</div>
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.users.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('admin.contestants.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.contestants.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Contestants
                        </a>

                        <!-- Competition Setup Section -->
                        <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Competition Setup</div>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.criteria.*') || request()->routeIs('admin.judges.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Categories
                        </a>
                        <a href="{{ route('admin.overall-rankings.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.overall-rankings.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Overall Rankings
                        </a>

                        <!-- Scoring Section -->
                        <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Scoring</div>
                        <a href="{{ route('admin.scoresheets.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.scoresheets.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Scoresheets
                        </a>
                        <a href="{{ route('tabulator.finalists.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.finalists*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Finalists
                        </a>
                    @endif
                    @if(auth()->user()->isJudge())
                        <a href="{{ route('judge.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('judge.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Score Sheet
                        </a>
                    @endif
                    @if(auth()->user()->isTabulator())
                        <!-- Results & Rankings Section -->
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Results & Rankings</div>
                        <a href="{{ route('tabulator.results') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.results') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Category Results
                        </a>
                        <a href="{{ route('tabulator.rankings') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.rankings') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Overall Rankings
                        </a>
                        <a href="{{ route('tabulator.awards') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.awards') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Awards
                        </a>

                        <!-- Configuration Section -->
                        <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Configuration</div>
                        <a href="{{ route('tabulator.overall-rankings.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.overall-rankings.*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Overall Rankings
                        </a>

                        <!-- Scoring Section -->
                        <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Scoring</div>
                        <a href="{{ route('tabulator.scoresheets') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.scoresheets*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Scoresheets
                        </a>
                        <a href="{{ route('tabulator.finalists.index') }}" 
                           class="flex items-center px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tabulator.finalists*') ? 'text-blue-700 bg-blue-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Finalists
                        </a>
                    @endif
                    
                    <!-- Mobile User Info and Logout -->
                    <div class="border-t border-gray-200 pt-4 pb-3">
                        <div class="px-3 py-2">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm text-gray-500">({{ ucfirst(auth()->user()->role->value) }})</div>
                        </div>
                        <div class="px-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </nav>

    <!-- Main Content -->
    <main class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages (hidden, converted to toasts via JavaScript) -->
            <div class="hidden">
                @if(session('success'))
                    <div data-flash-success="{{ session('success') }}"></div>
                @endif

                @if(session('error'))
                    <div data-flash-error="{{ session('error') }}"></div>
                @endif
            </div>

            @yield('content')
        </div>
    </main>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-20 right-6 z-50 space-y-2"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity duration-300">
        <div class="bg-white rounded-2xl p-8 shadow-2xl transform transition-all duration-300 scale-100">
            <div class="flex flex-col items-center space-y-4">
                <!-- Animated Spinner -->
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-blue-400 rounded-full animate-spin" style="animation-duration: 1.5s; animation-direction: reverse;"></div>
                </div>
                <p class="text-gray-700 font-medium text-lg">Loading...</p>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                if (mobileMenu.style.display === 'none' || mobileMenu.style.display === '') {
                    mobileMenu.style.display = 'block';
                } else {
                    mobileMenu.style.display = 'none';
                }
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (mobileMenu && mobileMenuButton && 
                !mobileMenu.contains(event.target) && 
                !mobileMenuButton.contains(event.target)) {
                mobileMenu.style.display = 'none';
            }
        });

        // Loading overlay functions with smooth animations
        window.showLoading = function() {
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('hidden');
            // Trigger reflow to enable transition
            overlay.offsetHeight;
            overlay.style.opacity = '1';
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('loading-overlay');
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        };

        // Toast notification function
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const typeStyles = {
                'success': { bg: '#22c55e', text: '#ffffff' },
                'error': { bg: '#ef4444', text: '#ffffff' },
                'warning': { bg: '#f59e0b', text: '#ffffff' },
                'info': { bg: '#3b82f6', text: '#ffffff' }
            };
            
            const icons = {
                'success': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
                'error': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />',
                'warning': '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />',
                'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />'
            };
            
            const style = typeStyles[type] || typeStyles['success'];
            const iconPath = icons[type] || icons['success'];
            
            toast.setAttribute('role', 'alert');
            toast.className = 'shadow-lg min-w-[300px] max-w-md toast-enter';
            toast.style.cssText = `
                background-color: ${style.bg} !important;
                color: ${style.text} !important;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            `;
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.5rem; height: 1.5rem; flex-shrink: 0; fill: currentColor;" viewBox="0 0 20 20">
                    ${iconPath}
                </svg>
                <span style="flex: 1;">${message}</span>
                <button onclick="this.parentElement.remove()" style="flex-shrink: 0; background: transparent; border: none; cursor: pointer; color: inherit; padding: 0.25rem; margin-left: 0.5rem; opacity: 0.8; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.25rem; height: 1.25rem; fill: currentColor;" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto-dismiss success messages after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    toast.classList.add('toast-exit');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        };

        // Show loading spinner on form submissions
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[data-loading="true"]');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    showLoading();
                });
            });
            
            // Auto-show toasts for flash messages
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
