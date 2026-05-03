<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TechNova Marketplace')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                TechNova
            </a>

            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    Home
                </a>
                <a href="{{ route('marketplace.index') }}" class="text-gray-600 hover:text-gray-900">
                    Marketplace
                </a>
                <a href="{{ route('marketplace.cart') }}" class="text-gray-600 hover:text-gray-900">
                    Cart
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 font-semibold text-blue-600">
                            Admin
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                            Logout
                        </button>
                    </form>

                    <span class="text-gray-700">
                        {{ auth()->user()->name }}
                    </span>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto px-4 py-8 text-center text-gray-600">
            <p>&copy; 2026 TechNova Systems. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
