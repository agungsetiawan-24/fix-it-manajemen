<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - FixIt Pro Manajemen</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-radial from-slate-900 via-slate-950 to-black text-slate-100">

    <div class="w-full max-w-md">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 items-center justify-center text-3xl shadow-xl shadow-blue-500/30 mb-4 border border-blue-400/30">
                ⚙️
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">FixIt Pro</h1>
            <p class="text-sm text-slate-400 mt-1">Sistem Informasi Servis HP & Kasir Keuangan</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-white">Selamat Datang</h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masuk menggunakan akun Admin atau Teknisi Anda.</p>
            </div>

            <!-- Error Notice -->
            @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-300 text-sm">
                {{ session('info') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input type="email"
                               name="email"
                               id="email"
                               required
                               autocomplete="email"
                               value="{{ old('email', 'admin@fixit.test') }}"
                               placeholder="nama@fixit.test"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Kata Sandi (Password)
                        </label>
                    </div>
                    <div class="relative">
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               autocomplete="current-password"
                               value="password"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 rounded-xl bg-slate-950/70 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <button type="button" id="toggle-password" class="absolute right-3 top-3 text-slate-400 hover:text-white text-xs">
                            Lihat
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-slate-300">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition duration-150 transform hover:-translate-y-0.5 active:translate-y-0">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Demo Credentials Picker -->
            <div class="mt-8 pt-6 border-t border-slate-800">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">
                    Akses Cepat Mode Uji Coba:
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            onclick="setDemoAccount('admin@fixit.test', 'password')"
                            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-left transition group">
                        <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Role Admin</div>
                        <div class="text-xs font-semibold text-slate-200 mt-0.5 truncate group-hover:text-white">admin@fixit.test</div>
                        <div class="text-[10px] text-slate-400">Pass: password</div>
                    </button>
                    <button type="button"
                            onclick="setDemoAccount('teknisi1@fixit.test', 'password')"
                            class="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-left transition group">
                        <div class="text-[10px] font-bold text-sky-400 uppercase tracking-wider">Role Teknisi</div>
                        <div class="text-xs font-semibold text-slate-200 mt-0.5 truncate group-hover:text-white">teknisi1@fixit.test</div>
                        <div class="text-[10px] text-slate-400">Pass: password</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Public Portal & TV Display Quick Links -->
        <div class="mt-6 flex items-center justify-center gap-3">
            <a href="{{ route('tracking.index') }}"
               class="px-3.5 py-1.5 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5 shadow-sm">
                <span>🔍 Lacak Servis Publik</span>
            </a>
            <a href="{{ route('tv.display') }}" target="_blank"
               class="px-3.5 py-1.5 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-1.5 shadow-sm">
                <span>📺 TV Display Antrian</span>
            </a>
        </div>

        <p class="text-center text-xs text-slate-600 mt-4">
            FixIt Pro &copy; {{ date('Y') }} &bull; HP Repair &amp; Finance Management System
        </p>
    </div>

    <script>
        function setDemoAccount(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }

        const toggleBtn = document.getElementById('toggle-password');
        const passInput = document.getElementById('password');
        toggleBtn.addEventListener('click', () => {
            if (passInput.type === 'password') {
                passInput.type = 'text';
                toggleBtn.innerText = 'Sembunyi';
            } else {
                passInput.type = 'password';
                toggleBtn.innerText = 'Lihat';
            }
        });
    </script>
</body>
</html>
