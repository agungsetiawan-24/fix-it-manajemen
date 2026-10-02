<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progress Tiket {{ $ticket->ticket_code }} - FixIt Pro</title>

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

    <!-- Top Navbar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('tracking.index') }}" class="flex items-center gap-2.5 text-slate-300 hover:text-white transition">
                <span class="text-sm font-bold">&larr; Lacak Tiket Lain</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Kode Tiket:</span>
                <span class="font-mono font-extrabold text-blue-400 text-sm bg-blue-500/10 px-2.5 py-1 rounded-lg border border-blue-500/20">
                    {{ $ticket->ticket_code }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl mx-auto px-6 py-8 w-full space-y-6">

        <!-- Flash Notice -->
        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-start gap-3">
            <span class="text-lg">✅</span>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-sm flex items-start gap-3">
            <span class="text-lg">ℹ️</span>
            <div class="font-medium">{{ session('info') }}</div>
        </div>
        @endif

        <!-- Card: Unit Overview & Status -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                <div>
                    <span class="text-xs text-slate-400 block font-medium">Perangkat yang Diservis:</span>
                    <h1 class="text-2xl sm:text-3xl font-black text-white mt-0.5">
                        {{ $ticket->device_brand }} {{ $ticket->device_model }}
                    </h1>
                    <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                        <span>Pelanggan: <strong class="text-slate-200">{{ $ticket->customer->name }}</strong></span>
                        @if($ticket->device_color)
                        <span>&bull; Warna: {{ $ticket->device_color }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <span class="badge-status badge-{{ $ticket->status->value }} text-xs px-3.5 py-1.5 font-bold">
                        Status: {{ $ticket->status->label() }}
                    </span>
                </div>
            </div>

            <!-- Visual Stepper Progress Bar -->
            <div class="py-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">
                    Tahapan Pengerjaan Unit:
                </h3>

                @php
                    $steps = [
                        'antrian' => ['title' => 'Antrian', 'desc' => 'Unit diterima di toko'],
                        'diagnosis' => ['title' => 'Diagnosis', 'desc' => 'Pengecekan kerusakan'],
                        'proses' => ['title' => 'Pengerjaan', 'desc' => 'Perbaikan & ganti part'],
                        'testing_qc' => ['title' => 'Testing QC', 'desc' => 'Uji fungsi akhir'],
                        'siap_diambil' => ['title' => 'Siap Diambil', 'desc' => 'Bisa diambil di kasir'],
                        'selesai' => ['title' => 'Selesai', 'desc' => 'Unit telah diserahkan'],
                    ];
                    $stepKeys = array_keys($steps);
                    $currentIndex = array_search($ticket->status->value, $stepKeys);
                    if ($ticket->status->value === 'menunggu_approval') {
                        $currentIndex = 1; // Between diagnosis and proses
                    } elseif ($ticket->status->value === 'batal') {
                        $currentIndex = -1;
                    }
                @endphp

                <div class="relative">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-center">
                        @foreach($steps as $key => $st)
                            @php
                                $idx = array_search($key, $stepKeys);
                                $isPassed = ($currentIndex !== false && $idx <= $currentIndex);
                                $isCurrent = ($ticket->status->value === $key);
                            @endphp
                            <div class="p-3 rounded-2xl border transition {{ $isCurrent ? 'bg-blue-600/20 border-blue-500 text-white ring-2 ring-blue-500/40' : ($isPassed ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-slate-950/40 border-slate-800 text-slate-500') }}">
                                <div class="w-7 h-7 mx-auto mb-2 rounded-full flex items-center justify-center text-xs font-bold {{ $isCurrent ? 'bg-blue-600 text-white shadow-lg' : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                                    @if($isPassed && !$isCurrent)
                                        ✓
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </div>
                                <div class="font-bold text-xs {{ $isCurrent ? 'text-blue-400' : ($isPassed ? 'text-slate-200' : 'text-slate-500') }}">
                                    {{ $st['title'] }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 line-clamp-1">
                                    {{ $st['desc'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($ticket->status->value === 'siap_diambil')
                <div class="mt-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 flex items-center gap-3">
                    <span class="text-2xl animate-bounce">🔔</span>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-300">Unit Anda Sudah Selesai Diperbaiki!</h4>
                        <p class="text-xs text-emerald-200/90 mt-0.5">
                            Silakan datang ke toko FixIt Pro dengan membawa nota bukti tanda terima untuk melakukan pembayaran dan pengambilan unit.
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <!-- DIGITAL APPROVAL CARD (When Status: Menunggu Approval) -->
            @if($ticket->status->value === 'menunggu_approval')
            <div class="mt-4 p-6 rounded-2xl bg-gradient-to-br from-amber-500/15 via-amber-600/10 to-slate-900 border-2 border-amber-500/50 shadow-xl">
                <div class="flex items-start gap-3.5 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl flex-shrink-0 border border-amber-500/30">
                        ⚠️
                    </div>
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-400 block">
                            Persetujuan Pelanggan Diperlukan (Digital Approval)
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-white mt-0.5">
                            Konfirmasi Biaya / Kerusakan Tambahan
                        </h3>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Saat teknisi membongkar unit HP Anda, ditemukan komponen rusak tambahan yang memerlukan penggantian. Pengerjaan saat ini dibekukan sementara menunggu persetujuan Anda.
                        </p>
                    </div>
                </div>

                <div class="bg-slate-950/70 rounded-xl p-4 border border-slate-800 space-y-3 mb-5">
                    @if($ticket->technician_notes)
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan &amp; Alasan Teknisi:</span>
                        <p class="text-xs text-slate-200 mt-1 leading-relaxed bg-slate-900 p-2.5 rounded-lg border border-slate-800">
                            {{ $ticket->technician_notes }}
                        </p>
                    </div>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-slate-800 text-xs">
                        <span class="text-slate-400 font-medium">Total Estimasi Biaya Baru:</span>
                        <span class="text-base font-extrabold text-amber-400 font-mono">
                            Rp {{ number_format($ticket->total_cost, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <form action="{{ route('tracking.approve', $ticket->ticket_code) }}" method="POST" class="w-full sm:w-auto flex-1">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Apakah Anda yakin menyetujui estimasi perbaikan ini dan melanjutkan pengerjaan?')"
                                class="w-full py-3 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs tracking-wide shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                            <span>✓ Setujui &amp; Lanjutkan Servis</span>
                        </button>
                    </form>

                    <form action="{{ route('tracking.reject', $ticket->ticket_code) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Apakah Anda yakin ingin menolak perbaikan dan membatalkan tiket servis ini?')"
                                class="w-full py-3 px-4 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 border border-rose-500/40 text-rose-300 font-bold text-xs transition">
                            <span>✕ Tolak &amp; Batalkan</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card: Detail Biaya & Garansi -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 backdrop-blur-xl">
                <h3 class="font-bold text-sm text-white mb-4 pb-2 border-b border-slate-800 flex items-center justify-between">
                    <span>Estimasi Biaya &amp; Garansi</span>
                    <span class="text-xs text-blue-400">💰</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Biaya Perbaikan:</span>
                        <span class="text-base font-extrabold text-white font-mono">
                            Rp {{ number_format($ticket->total_cost, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Garansi Purna Jual:</span>
                        <span class="font-bold text-emerald-400">
                            {{ $ticket->warranty_days }} Hari
                        </span>
                    </div>

                    @if($ticket->warranty_expiry_date)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Masa Garansi Berlaku Hingga:</span>
                        <span class="font-medium text-slate-200">
                            {{ $ticket->warranty_expiry_date->locale('id')->isoFormat('D MMMM Y') }}
                        </span>
                    </div>
                    @endif

                    <div class="pt-2 border-t border-slate-800 text-[11px] text-slate-400 leading-relaxed">
                        * Garansi mencakup fungsi part yang diganti, tidak berlaku jika unit terkena air atau pecah akibat benturan ulang.
                    </div>
                </div>
            </div>

            <!-- Card: Checklist Fisik Intake Awal -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 backdrop-blur-xl">
                <h3 class="font-bold text-sm text-white mb-4 pb-2 border-b border-slate-800 flex items-center justify-between">
                    <span>Inspeksi Fisik Saat Diterima</span>
                    <span class="text-xs text-slate-400">{{ $ticket->checklists->count() }} Komponen</span>
                </h3>

                <div class="max-h-56 overflow-y-auto space-y-2 pr-1 text-xs">
                    @foreach($ticket->checklists as $chk)
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-800/60 last:border-0">
                        <span class="text-slate-300 font-medium">{{ $chk->item_name }}</span>
                        @php
                            $badgeCls = match($chk->condition_before->value) {
                                'normal' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                'rusak' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                'baret' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                                'mati' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeCls }}">
                            {{ $chk->condition_before->label() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Help / Contact Store Operator Card -->
        <div class="bg-gradient-to-r from-blue-900/40 via-indigo-900/30 to-slate-900 border border-blue-500/20 rounded-3xl p-6 text-center">
            <h4 class="font-bold text-white text-sm">Ada Pertanyaan Mengenai Servis Anda?</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                Hubungi customer service kami melalui WhatsApp dengan menyertakan kode tiket <strong>{{ $ticket->ticket_code }}</strong>.
            </p>
            <div class="mt-4">
                <a href="https://wa.me/6281234567890?text=Halo%20FixIt%20Pro,%20saya%20ingin%20menanyakan%20status%20tiket%20{{ $ticket->ticket_code }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                    <span>💬 Hubungi via WhatsApp</span>
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        FixIt Pro &bull; Tracking Unit Servis &bull; Kode: {{ $ticket->ticket_code }}
    </footer>

</body>
</html>
