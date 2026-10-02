<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Status Servis - FixIt Pro</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-between bg-radial from-slate-900 via-slate-950 to-black text-slate-100 antialiased">

    <!-- Top Public Navbar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('tracking.index') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-black text-white text-xl shadow-lg shadow-blue-500/20">
                    ⚙️
                </div>
                <div>
                    <span class="font-extrabold text-white text-lg tracking-tight">FixIt Pro</span>
                    <span class="text-[11px] text-blue-400 block font-medium">Customer Tracking Portal</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('tv.display') }}" target="_blank"
                   class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white px-3 py-1.5 rounded-lg border border-slate-800 hover:bg-slate-800/80 transition">
                    <span>📺 TV Antrian</span>
                </a>
                <a href="{{ route('login') }}"
                   class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-xl transition shadow-md shadow-blue-600/25">
                    Login Staf
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl mx-auto px-6 py-12 w-full">
        <!-- Hero Search Section -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-4">
                🔍 Cek Progress Perbaikan Unit HP Anda
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Lacak Status Servis Smartphone
            </h1>
            <p class="text-slate-400 text-sm mt-3 leading-relaxed">
                Ketikkan <strong>Kode Tiket</strong> yang tertera pada nota fisik (contoh: <code class="text-blue-400 bg-slate-800 px-1.5 py-0.5 rounded text-xs">SRV-202610-001</code>) atau masukkan <strong>Nomor HP</strong> Anda yang terdaftar.
            </p>

            <!-- Search Form -->
            <form action="{{ route('tracking.index') }}" method="GET" class="mt-8">
                <div class="relative flex items-center shadow-2xl rounded-2xl overflow-hidden bg-slate-900 border-2 border-slate-700/80 focus-within:border-blue-500 transition">
                    <input type="text"
                           name="q"
                           value="{{ $query }}"
                           required
                           placeholder="Masukkan Kode Tiket atau Nomor WhatsApp..."
                           class="w-full pl-5 pr-32 py-4 bg-transparent text-white placeholder-slate-500 text-sm md:text-base focus:outline-none font-medium">

                    <button type="submit"
                            class="absolute right-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs md:text-sm transition shadow-md shadow-blue-500/30 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Search Results (if searched by phone or multiple results found) -->
        @if($searched)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Hasil Pencarian untuk:</span>
                        <span class="text-blue-400 font-mono">"{{ $query }}"</span>
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">
                        {{ $tickets->count() }} unit ditemukan
                    </span>
                </div>

                @if($tickets->count() > 0)
                    <div class="divide-y divide-slate-800 space-y-3">
                        @foreach($tickets as $t)
                        <div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl hover:bg-slate-800/50 transition">
                            <div class="flex items-start gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-slate-800 border border-slate-700 text-white flex items-center justify-center text-lg flex-shrink-0">
                                    📱
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-blue-400 text-sm font-mono">{{ $t->ticket_code }}</span>
                                        <span class="badge-status badge-{{ $t->status->value }} text-[10px]">
                                            {{ $t->status->label() }}
                                        </span>
                                    </div>
                                    <h3 class="text-white font-bold text-sm mt-0.5">
                                        {{ $t->device_brand }} {{ $t->device_model }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">
                                        Keluhan: {{ $t->complaint_notes }}
                                    </p>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        Didaftarkan: {{ $t->created_at->locale('id')->isoFormat('D MMMM Y, H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <a href="{{ route('tracking.detail', $t->ticket_code) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition">
                                    <span>Lihat Progress</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="text-4xl mb-2">🔎</div>
                        <h3 class="text-white font-bold text-base">Tiket Tidak Ditemukan</h3>
                        <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">
                            Pastikan format nomor tiket atau nomor HP yang Anda masukkan sudah benar sesuai nota tanda terima.
                        </p>
                    </div>
                @endif
            </div>
        @else
            <!-- How to Track Guidance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="bg-slate-900/60 border border-slate-800/80 p-5 rounded-2xl text-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center justify-center text-xl mx-auto mb-3">
                        🧾
                    </div>
                    <h3 class="text-sm font-bold text-white">1. Cek Nota Fisik</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Lihat kode tiket servis di sudut atas nota cetak Anda (contoh: SRV-202610-001).
                    </p>
                </div>

                <div class="bg-slate-900/60 border border-slate-800/80 p-5 rounded-2xl text-center">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-xl mx-auto mb-3">
                        📲
                    </div>
                    <h3 class="text-sm font-bold text-white">2. Scan Kode QR</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Arahkan kamera ponsel Anda ke QR Code pada nota untuk langsung membuka halaman progres.
                    </p>
                </div>

                <div class="bg-slate-900/60 border border-slate-800/80 p-5 rounded-2xl text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl mx-auto mb-3">
                        ⚡
                    </div>
                    <h3 class="text-sm font-bold text-white">3. Approval Online</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Jika ada biaya part tambahan, Anda dapat menyetujuinya secara langsung lewat portal ini.
                    </p>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        FixIt Pro &bull; Portal Layanan Publik &amp; Transparansi Servis Smartphone
    </footer>

</body>
</html>
