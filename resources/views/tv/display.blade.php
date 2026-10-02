<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TV Display Papan Antrian - FixIt Pro</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #020617;
            overflow: hidden;
            user-select: none;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 25px rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 50px rgba(16, 185, 129, 0.6); border-color: rgba(16, 185, 129, 0.8); }
        }
        .glow-ready {
            animation: pulseGlow 2.5s infinite;
        }
    </style>
</head>
<body class="h-full flex flex-col justify-between text-slate-100 antialiased">

    <!-- Top TV Bar -->
    <header class="bg-slate-900/90 border-b border-slate-800 px-8 py-4 flex items-center justify-between shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30">
                ⚙️
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2.5">
                    <span>FIXIT SERVICE PRO</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </h1>
                <p class="text-xs text-blue-400 font-bold uppercase tracking-wider">
                    Papan Informasi Antrian &amp; Pengambilan Unit Real-Time
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <!-- Audio Toggle & Controls -->
            <div class="flex items-center gap-2">
                <button type="button"
                        id="btn-toggle-audio"
                        onclick="toggleAudio()"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 border border-slate-700 transition flex items-center gap-1.5">
                    <span id="audio-icon">🔊</span>
                    <span id="audio-label">Suara Aktif</span>
                </button>

                <button type="button"
                        onclick="testSpeechVoice()"
                        class="px-3 py-1.5 rounded-xl bg-blue-600/30 hover:bg-blue-600/50 text-blue-300 border border-blue-500/40 text-xs font-bold transition">
                    Test Panggilan
                </button>

                <button type="button"
                        onclick="toggleFullScreen()"
                        class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs"
                        title="Mode Layar Penuh">
                    ⛶
                </button>
            </div>

            <!-- Live Digital Clock -->
            <div class="text-right pl-4 border-l border-slate-800">
                <div id="tv-clock" class="text-2xl font-black text-white font-mono tracking-wider leading-none">
                    00:00:00
                </div>
                <div id="tv-date" class="text-xs text-slate-400 font-semibold mt-1">
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Dual Screen Content (16:9 Split) -->
    <main class="flex-1 p-6 grid grid-cols-1 md:grid-cols-2 gap-6 min-h-0">

        <!-- LEFT COLUMN: Sedang Dalam Pengerjaan (Proses / Diagnosis) -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-3xl p-6 flex flex-col shadow-2xl backdrop-blur-xl">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl font-bold">
                        ⚡
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-white tracking-tight uppercase">
                            Sedang Dikerjakan
                        </h2>
                        <p class="text-xs text-indigo-400">Diagnosis, Perbaikan &amp; Testing QC</p>
                    </div>
                </div>
                <span id="count-in-progress" class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-extrabold">
                    {{ $inProgress->count() }} Unit
                </span>
            </div>

            <!-- List Container -->
            <div id="container-in-progress" class="flex-1 space-y-3 overflow-y-auto pr-1">
                @forelse($inProgress as $item)
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-4 transition">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-300 font-black flex items-center justify-center text-lg flex-shrink-0 border border-slate-700">
                            📱
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-lg text-blue-400">{{ $item->ticket_code }}</span>
                                <span class="badge-status badge-{{ $item->status->value }} text-[10px] py-0.5">
                                    {{ $item->status->label() }}
                                </span>
                            </div>
                            <div class="text-sm font-bold text-white mt-0.5 truncate">
                                {{ $item->device_brand }} {{ $item->device_model }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                Pelanggan: <span class="text-slate-200 font-semibold">{{ $item->customer->name }}</span>
                                &bull; Teknisi: <span class="text-indigo-300 font-medium">{{ $item->technician?->name ?? 'Tim Teknisi' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-slate-500 text-sm py-16">
                    <div class="text-4xl mb-2">✨</div>
                    <p>Tidak ada unit dalam proses pengerjaan saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN: Selesai & Siap Diambil (Highlight Emerald Glow) -->
        <div class="bg-gradient-to-b from-emerald-950/20 via-slate-900/90 to-slate-950 border border-emerald-500/30 rounded-3xl p-6 flex flex-col shadow-2xl backdrop-blur-xl">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-emerald-500/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold">
                        🔔
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-white tracking-tight uppercase flex items-center gap-2">
                            <span>Siap Diambil</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        </h2>
                        <p class="text-xs text-emerald-400">Silakan menuju meja kasir untuk pengambilan unit</p>
                    </div>
                </div>
                <span id="count-ready" class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-extrabold">
                    {{ $readyForPickup->count() }} Unit
                </span>
            </div>

            <!-- List Container -->
            <div id="container-ready" class="flex-1 space-y-3 overflow-y-auto pr-1">
                @forelse($readyForPickup as $item)
                <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 flex items-center justify-between gap-4 glow-ready transition">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 font-black flex items-center justify-center text-xl flex-shrink-0 border border-emerald-500/40">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-xl text-emerald-300">{{ $item->ticket_code }}</span>
                                <span class="badge-status badge-siap_diambil text-[10px] py-0.5">
                                    Siap Di Kasir
                                </span>
                            </div>
                            <div class="text-base font-extrabold text-white mt-0.5 truncate">
                                {{ $item->device_brand }} {{ $item->device_model }}
                            </div>
                            <div class="text-xs text-emerald-200 mt-0.5">
                                Pelanggan: <strong class="text-white font-bold">{{ $item->customer->name }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <button type="button"
                                onclick="speakCall('{{ $item->ticket_code }}', '{{ addslashes($item->customer->name) }}', '{{ addslashes($item->device_brand . ' ' . $item->device_model) }}')"
                                class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center gap-1.5">
                            <span>🔊</span>
                            <span>Panggil</span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-slate-500 text-sm py-16">
                    <div class="text-4xl mb-2">⏳</div>
                    <p>Belum ada unit yang siap diambil saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Bottom Running Text Ticker -->
    <footer class="bg-slate-900 border-t border-slate-800 px-6 py-2.5 flex items-center overflow-hidden">
        <div class="flex items-center gap-2 flex-shrink-0 pr-4 border-r border-slate-800 font-extrabold text-xs text-blue-400 uppercase tracking-wider">
            <span>📢 INFO:</span>
        </div>
        <div class="flex-1 overflow-hidden whitespace-nowrap pl-4">
            <div class="inline-block animate-marquee text-xs text-slate-300 font-medium">
                Selamat datang di FixIt Service Pro &bull; Harap simpan bukti nota tanda terima fisik Anda &bull; Periksa kelengkapan unit sebelum meninggalkan toko &bull; Garansi resmi berlaku sesuai masa garansi pada nota &bull; Scan QR Code pada nota untuk tracking digital dari smartphone Anda.
            </div>
        </div>
    </footer>

    <!-- Text-To-Speech (TTS) & Realtime Polling Script -->
    <script>
        let isAudioEnabled = true;
        let knownReadyTicketIds = new Set([
            @foreach($readyForPickup as $r)
                '{{ $r->id }}',
            @endforeach
        ]);
        let audioContext = null;

        // Digital Clock Live
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('tv-clock').innerText = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Web Audio Chime Sound (Ding-Dong)
        function playChime() {
            try {
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                const now = audioContext.currentTime;
                // First Tone (High Note)
                const osc1 = audioContext.createOscillator();
                const gain1 = audioContext.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now); // D5
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc1.connect(gain1);
                gain1.connect(audioContext.destination);
                osc1.start(now);
                osc1.stop(now + 0.6);

                // Second Tone (Low Note)
                const osc2 = audioContext.createOscillator();
                const gain2 = audioContext.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(440.00, now + 0.35); // A4
                gain2.gain.setValueAtTime(0.35, now + 0.35);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
                osc2.connect(gain2);
                gain2.connect(audioContext.destination);
                osc2.start(now + 0.35);
                osc2.stop(now + 1.2);
            } catch (e) {
                console.warn('Audio chime error:', e);
            }
        }

        // Web Speech API Call
        function speakCall(ticketCode, customerName, deviceModel) {
            if (!isAudioEnabled) return;

            // Play Chime first
            playChime();

            // Format Spoken Ticket Code (e.g. S-R-V 202610 001)
            const cleanCode = ticketCode.replace(/-/g, ' ');
            const textToSpeak = `Panggilan untuk nomor tiket ${cleanCode}. Atas nama ${customerName}. Unit ${deviceModel} telah selesai diperbaiki dan siap diambil di meja kasir. Terima kasih.`;

            setTimeout(() => {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel(); // Stop any pending speech
                    const utterance = new SpeechSynthesisUtterance(textToSpeak);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    utterance.pitch = 1.0;

                    // Choose Indonesian voice if available
                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang.startsWith('id'));
                    if (idVoice) {
                        utterance.voice = idVoice;
                    }

                    window.speechSynthesis.speak(utterance);
                }
            }, 600);
        }

        function toggleAudio() {
            isAudioEnabled = !isAudioEnabled;
            const icon = document.getElementById('audio-icon');
            const label = document.getElementById('audio-label');
            if (isAudioEnabled) {
                icon.innerText = '🔊';
                label.innerText = 'Suara Aktif';
            } else {
                icon.innerText = '🔇';
                label.innerText = 'Suara Nonaktif';
            }
        }

        function testSpeechVoice() {
            speakCall('SRV-TEST-001', 'Budi Santoso', 'iPhone 13 Pro');
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Auto Polling via Fetch every 6 seconds
        async function fetchQueueStatus() {
            try {
                const res = await fetch('/api/queue-board');
                if (!res.ok) return;

                const data = await res.json();

                // Update Counts
                document.getElementById('count-in-progress').innerText = `${data.in_progress.length} Unit`;
                document.getElementById('count-ready').innerText = `${data.ready_for_pickup.length} Unit`;

                // Render In Progress
                const inProgressContainer = document.getElementById('container-in-progress');
                if (data.in_progress.length === 0) {
                    inProgressContainer.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-slate-500 text-sm py-16">
                            <div class="text-4xl mb-2">✨</div>
                            <p>Tidak ada unit dalam proses pengerjaan saat ini.</p>
                        </div>
                    `;
                } else {
                    inProgressContainer.innerHTML = data.in_progress.map(item => `
                        <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between gap-4 transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-300 font-black flex items-center justify-center text-lg flex-shrink-0 border border-slate-700">
                                    📱
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-lg text-blue-400">${item.ticket_code}</span>
                                        <span class="badge-status badge-${item.status} text-[10px] py-0.5">
                                            ${item.status_label}
                                        </span>
                                    </div>
                                    <div class="text-sm font-bold text-white mt-0.5 truncate">
                                        ${item.device}
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        Pelanggan: <span class="text-slate-200 font-semibold">${item.customer_name}</span>
                                        &bull; Teknisi: <span class="text-indigo-300 font-medium">${item.technician}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                // Render Ready For Pickup & Trigger TTS for newly arrived ready tickets
                const readyContainer = document.getElementById('container-ready');
                if (data.ready_for_pickup.length === 0) {
                    readyContainer.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-slate-500 text-sm py-16">
                            <div class="text-4xl mb-2">⏳</div>
                            <p>Belum ada unit yang siap diambil saat ini.</p>
                        </div>
                    `;
                } else {
                    readyContainer.innerHTML = data.ready_for_pickup.map(item => `
                        <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 flex items-center justify-between gap-4 glow-ready transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-300 font-black flex items-center justify-center text-xl flex-shrink-0 border border-emerald-500/40">
                                    ✓
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-black text-xl text-emerald-300">${item.ticket_code}</span>
                                        <span class="badge-status badge-siap_diambil text-[10px] py-0.5">
                                            Siap Di Kasir
                                        </span>
                                    </div>
                                    <div class="text-base font-extrabold text-white mt-0.5 truncate">
                                        ${item.device}
                                    </div>
                                    <div class="text-xs text-emerald-200 mt-0.5">
                                        Pelanggan: <strong class="text-white font-bold">${item.customer_name}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <button type="button"
                                        onclick="speakCall('${item.ticket_code}', '${item.full_customer_name.replace(/'/g, "\\'")}', '${item.device.replace(/'/g, "\\'")}')"
                                        class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center gap-1.5">
                                    <span>🔊</span>
                                    <span>Panggil</span>
                                </button>
                            </div>
                        </div>
                    `).join('');

                    // Check for newly added ready tickets to trigger automatic speech call
                    data.ready_for_pickup.forEach(item => {
                        if (!knownReadyTicketIds.has(item.id)) {
                            knownReadyTicketIds.add(item.id);
                            speakCall(item.ticket_code, item.full_customer_name, item.device);
                        }
                    });
                }

            } catch (err) {
                console.error('Polling error:', err);
            }
        }

        // Start Polling every 6 seconds
        setInterval(fetchQueueStatus, 6000);

        // Preload speech synthesis voices
        if ('speechSynthesis' in window) {
            window.speechSynthesis.onvoiceschanged = () => {
                window.speechSynthesis.getVoices();
            };
        }
    </script>
</body>
</html>
