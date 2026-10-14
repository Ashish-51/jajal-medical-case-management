<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Jajal Medical') - Case Management System</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-teal-500 selection:text-white">
    <div class="min-h-full flex">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 text-slate-300">
            <!-- Brand Header -->
            <div class="h-16 flex items-center px-6 gap-3 border-b border-slate-800/80 bg-slate-950/40">
                <div class="w-9 h-9 rounded-lg bg-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-500/20">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-bold text-white tracking-tight text-base">JAJAL MEDICAL</span>
                    <span class="block text-[10px] uppercase font-semibold text-teal-400 tracking-wider">Case Tracker</span>
                </div>
            </div>

            <!-- User Info Card -->
            <div class="p-4 mx-3 my-4 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-teal-500/20 border border-teal-500/30 flex items-center justify-center text-teal-300 font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @if(auth()->user()->role === 'admin')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    ADMIN
                                </span>
                            @elseif(auth()->user()->role === 'engineer')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    ENGINEER
                                </span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                    VIEWER
                                </span>
                            @endif
                            <span class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    Dashboard
                </a>

                <a href="{{ route('cases.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('cases.*') && !request()->routeIs('cases.create') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                    <i data-lucide="folder-archive" class="w-4 h-4"></i>
                    All Cases
                </a>

                @can('create', App\Models\CaseRecord::class)
                <a href="{{ route('cases.create') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('cases.create') ? 'bg-teal-600 text-white shadow-sm' : 'hover:bg-slate-800 text-slate-300 hover:text-white' }}">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    New Implant Case
                </a>
                @endcan
            </nav>

            <!-- Bottom Actions: Logout -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/20">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            id="logout-btn"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-800 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-y-auto">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-10 shadow-xs">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">@yield('page_title', 'Case Management')</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-xs text-slate-500 font-medium hidden sm:block">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Internal Production System
                    </div>
                    @can('create', App\Models\CaseRecord::class)
                    <a href="{{ route('cases.create') }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-semibold text-white bg-teal-600 hover:bg-teal-500 shadow-sm transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Create Case</span>
                    </a>
                    @endcan
                </div>
            </header>

            <!-- Alerts Container -->
            <div class="px-8 pt-6">
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium shadow-xs">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium shadow-xs">
                        <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-600 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <div class="p-8 pt-2 flex-1">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="py-4 px-8 border-t border-slate-200 text-center text-xs text-slate-500 bg-white">
                &copy; {{ date('Y') }} Jajal Medical Inc. Administrative Case Tracking System. Internal evaluation use only.
            </footer>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
