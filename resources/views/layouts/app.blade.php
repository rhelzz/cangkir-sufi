<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'POS Mobile') }} - @yield('title', 'Dashboard')</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .sidebar-link {
                @apply flex items-center py-2 px-4 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition-all duration-200 border-l-4 border-transparent;
            }
            .sidebar-link.active {
                @apply text-white bg-gray-700 border-l-4 border-blue-400;
            }
            .sidebar-link i {
                @apply mr-3 w-5 text-center;
            }
            .content-card {
                @apply bg-white rounded-lg shadow-md p-6;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans h-full min-h-screen overflow-x-hidden">
    <div class="flex min-h-screen h-full overflow-x-hidden">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="bg-gray-800 text-white w-64 fixed md:static inset-y-0 left-0 z-40 h-full shadow-lg md:translate-x-0 -translate-x-full transition-transform duration-300 flex-shrink-0">
            <div class="p-5 flex flex-col h-full">
                <h3 class="text-xl font-bold flex items-center">
                    <i class="fas fa-cash-register mr-2"></i> POS Mobile
                </h3>
                <hr class="my-4 border-gray-600">
                <nav class="space-y-2 mt-5 flex-1">
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('cashier') }}" class="sidebar-link {{ request()->routeIs('cashier') ? 'active' : '' }}">
                        <i class="fas fa-calculator"></i> Cashier
                    </a>
                    <a href="{{ route('cashier.orders') }}" class="sidebar-link {{ request()->routeIs('cashier.orders') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> Orders
                    </a>
                    @if(auth()->user()->isOwner())
                        <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> Products
                        </a>
                        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                        <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <i class="fas fa-money-bill"></i> Expenses
                        </a>
                        <a href="{{ route('reports.sales') }}" class="sidebar-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Sales Report
                        </a>
                        <a href="{{ route('reports.busy-hours') }}" class="sidebar-link {{ request()->routeIs('reports.busy-hours') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i> Busy Hours
                        </a>
                        <a href="{{ route('register') }}" class="sidebar-link {{ request()->routeIs('register') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                    @endif
                </nav>
                <hr class="my-4 border-gray-600">
                <div class="mt-auto">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-colors duration-200" type="submit">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <!-- Page Wrapper -->
        <div id="content" class="flex-1 flex flex-col bg-gray-100 min-h-screen md:ml-64 transition-all duration-300 overflow-x-hidden">
            <!-- Mobile Topbar -->
            <header class="fixed md:hidden top-0 inset-x-0 z-30 bg-gray-800 text-white flex items-center justify-between px-4 py-2 shadow">
                <button id="sidebarToggle" class="text-2xl p-1 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="font-bold text-lg truncate">@yield('page-title', 'Dashboard')</span>
                <span class="text-sm truncate">{{ auth()->user()->name }}</span>
            </header>
            <!-- Desktop Topbar -->
            <header class="hidden md:flex items-center justify-between px-8 py-6 bg-gray-800 text-white shadow sticky top-0 z-20 w-full">
                <h1 class="text-2xl font-bold">@yield('page-title', 'Dashboard')</h1>
                <span class="text-sm"><i class="fas fa-user-circle mr-1"></i>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
            </header>
            <!-- Main Content -->
            <main class="flex-1 pt-14 md:pt-0 pb-20 md:pb-8 px-2 sm:px-4 md:px-8 w-full max-w-full min-h-screen overflow-x-hidden">
                @if(session('success'))
                    <div id="successAlert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                        <div class="flex items-center">
                            <div class="py-1"><i class="fas fa-check-circle text-green-500 mr-2"></i></div>
                            <div>{{ session('success') }}</div>
                            <button type="button" onclick="document.getElementById('successAlert').remove()" class="ml-auto">
                                <i class="fas fa-times text-green-500"></i>
                            </button>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div id="errorAlert" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        <div class="flex items-center">
                            <div class="py-1"><i class="fas fa-exclamation-circle text-red-500 mr-2"></i></div>
                            <div>{{ session('error') }}</div>
                            <button type="button" onclick="document.getElementById('errorAlert').remove()" class="ml-auto">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </div>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-gray-800 text-white z-40 shadow-lg">
        <div class="flex justify-around">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-gray-300' }}">
                <i class="fas fa-home text-lg"></i>
                <span class="text-xs mt-1">Dashboard</span>
            </a>
            <a href="{{ route('cashier') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('cashier') ? 'text-blue-400' : 'text-gray-300' }}">
                <i class="fas fa-calculator text-lg"></i>
                <span class="text-xs mt-1">Cashier</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('products.*') ? 'text-blue-400' : 'text-gray-300' }}">
                <i class="fas fa-box text-lg"></i>
                <span class="text-xs mt-1">Products</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('expenses.*') ? 'text-blue-400' : 'text-gray-300' }}">
                <i class="fas fa-money-bill text-lg"></i>
                <span class="text-xs mt-1">Expenses</span>
            </a>
        </div>
    </nav>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        function toggleSidebar() {
            // Mobile only
            if (window.innerWidth >= 768) return;
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                const overlay = document.createElement('div');
                overlay.id = 'sidebar-overlay';
                overlay.className = 'fixed inset-0 bg-black bg-opacity-40 z-30';
                document.body.appendChild(overlay);
                overlay.addEventListener('click', toggleSidebar);
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                const overlay = document.getElementById('sidebar-overlay');
                if (overlay) overlay.remove();
            }
        }
        if(sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        function checkWidth() {
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                const overlay = document.getElementById('sidebar-overlay');
                if (overlay) overlay.remove();
            }
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);
    });
    </script>
    @stack('scripts')
</body>
</html>