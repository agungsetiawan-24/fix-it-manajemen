<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'FixIt' }} - Sistem Manajemen Servis HP & Kasir</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800 bg-slate-100 flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex items-center justify-between shadow-lg sticky top-0 z-40">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-extrabold text-white shadow-md">
                ⚡
            </div>
            <span class="font-bold text-lg tracking-tight">FixIt Pro</span>
        </div>
        <button id="mobile-menu-toggle" type="button" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="w-72 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col justify-between hidden md:flex min-h-screen border-r border-slate-800 fixed md:sticky top-0 z-50">
        <div>
            <!-- Brand Logo -->
            <div class="h-18 px-6 flex items-center gap-3 border-b border-slate-800/80">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-black text-white text-xl shadow-lg shadow-blue-500/25">
                    ⚙️
                </div>
                <div>
                    <h1 class="font-extrabold text-white text-lg tracking-tight leading-none">FixIt Pro</h1>
                    <p class="text-xs text-slate-400 mt-1">Servis & Manajemen Keuangan</p>
                </div>
            </div>

            <!-- Active User Quick Pill -->
            @auth
            <div class="px-5 py-4 m-4 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center text-sm shadow">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ Auth::user()->isAdmin() ? 'text-amber-400' : 'text-sky-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ Auth::user()->isAdmin() ? 'bg-amber-400' : 'bg-sky-400' }}"></span>
                            {{ Auth::user()->role->label() }}
                        </span>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Nav Links -->
            <nav class="px-4 space-y-1.5 mt-2">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard Utama</span>
                </a>

                <div class="pt-3 pb-1 px-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    Modul Operasional
                </div>

                <a href="{{ route('tickets.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('tickets.index*') || request()->routeIs('tickets.show*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>Daftar Tiket Servis</span>
                    </div>
                </a>

                <a href="{{ route('tickets.create') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('tickets.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Unit Servis Baru</span>
                </a>

                <div class="pt-3 pb-1 px-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    Display & Layanan
                </div>

                <a href="{{ route('tickets.index', ['status' => 'siap_diambil']) }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-400 hover:text-white hover:bg-slate-800/80 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Siap Diambil</span>
                    </div>
                </a>

                <a href="{{ route('tv.display') }}" target="_blank"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-400 hover:text-white hover:bg-slate-800/80 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>TV Display Antrian</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded">Live</span>
                </a>

                <a href="{{ route('tracking.index') }}" target="_blank"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-400 hover:text-white hover:bg-slate-800/80 transition-all duration-150">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Portal Lacak Customer</span>
                    </div>
                    <span class="text-[10px] text-slate-400">Publik</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-slate-800">
            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
            <div class="px-6 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-snug">
                            {{ $pageTitle ?? 'Dashboard' }}
                        </h2>
                        <p class="text-xs text-slate-500">
                            {{ $pageSubtitle ?? 'Sistem Antrian & Manajemen Servis Smartphone' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Live Date / Badge -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>

                    <!-- Quick Action Button -->
                    @if(!request()->routeIs('tickets.create'))
                    <a href="{{ route('tickets.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs tracking-wide shadow-md shadow-blue-600/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Input Unit Baru</span>
                    </a>
                    @endif
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-5">
            @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-rose-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-start gap-3 shadow-xs">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
            @endif
        </div>

        <!-- Page Main Body -->
        <main class="flex-1 px-6 pb-12">
            @yield('content')
        </main>
    </div>

    <!-- Mobile sidebar toggler script -->
    <script>
        const toggleBtn = document.getElementById('mobile-menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
