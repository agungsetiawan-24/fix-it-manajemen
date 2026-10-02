@extends('layouts.app')

@section('content')
<div class="space-y-6 pt-2">

    <!-- Hero / Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 p-6 md:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-blue-200 backdrop-blur-md border border-white/10 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Operasional Online
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                    Halo, {{ Auth::user()->name }} 👋
                </h1>
                <p class="text-blue-100 text-sm mt-1.5 max-w-xl">
                    Pantau alur servis smartphone, antrian fisik pelanggan, serta status laba kasir secara real-time.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tickets.create') }}"
                   class="px-5 py-3 rounded-xl bg-white text-blue-700 hover:bg-blue-50 font-bold text-sm shadow-lg transition duration-150 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Unit Masuk</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Antrian -->
        <a href="{{ route('tickets.index', ['status' => 'antrian']) }}"
           class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Antrian</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">📥</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-800">{{ $queueCount }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Menunggu diagnosis</div>
            </div>
        </a>

        <!-- Proses / Diagnosis -->
        <a href="{{ route('tickets.index', ['status' => 'proses']) }}"
           class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Pengerjaan</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">⚡</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-indigo-700">{{ $inProgressCount }}</div>
                <div class="text-[11px] text-indigo-500 mt-0.5">Sedang diproses</div>
            </div>
        </a>

        <!-- Menunggu Approval -->
        <a href="{{ route('tickets.index', ['status' => 'menunggu_approval']) }}"
           class="bg-white rounded-2xl p-4 border border-amber-200 bg-amber-50/20 shadow-xs hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Approval</span>
                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold">⏳</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-800">{{ $waitingApprovalCount }}</div>
                <div class="text-[11px] text-amber-600 mt-0.5">Konfirmasi customer</div>
            </div>
        </a>

        <!-- Siap Diambil -->
        <a href="{{ route('tickets.index', ['status' => 'siap_diambil']) }}"
           class="bg-white rounded-2xl p-4 border border-emerald-200 bg-emerald-50/20 shadow-xs hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Siap Ambil</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">🔔</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-800">{{ $readyForPickupCount }}</div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Selesai diperbaiki</div>
            </div>
        </a>

        <!-- Selesai Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Selesai Hari Ini</span>
                <span class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xs font-bold">✅</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-800">{{ $completedTodayCount }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Unit telah diambil</div>
            </div>
        </div>

        <!-- Pemasukan Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemasukan</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">💰</span>
            </div>
            <div class="mt-3">
                <div class="text-lg font-black text-slate-800 truncate">Rp {{ number_format($incomeToday, 0, ',', '.') }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">Total kas masuk</div>
            </div>
        </div>
    </div>

    <!-- Main Section: Priority & Attention Queue -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Antrian Perlu Tindakan Segera -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="font-bold text-slate-800 text-sm tracking-tight">Antrian Butuh Tindakan Cepat</h3>
                </div>
                <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($urgentTickets as $ticket)
                <div class="p-4 hover:bg-slate-50/80 transition flex items-center justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-extrabold flex items-center justify-center text-xs flex-shrink-0 border border-slate-200">
                            📱
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="font-bold text-sm text-blue-600 hover:underline">
                                    {{ $ticket->ticket_code }}
                                </a>
                                <span class="badge-status badge-{{ $ticket->status->value }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </div>
                            <div class="text-xs font-medium text-slate-800 mt-0.5 truncate">
                                {{ $ticket->device_brand }} {{ $ticket->device_model }}
                                <span class="text-slate-400 font-normal">&bull; {{ $ticket->customer->name }} ({{ $ticket->customer->phone }})</span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mt-0.5">
                                Keluhan: {{ $ticket->complaint_notes }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('tickets.show', $ticket->id) }}"
                           class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                            Proses
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-sm">
                    ✨ Tidak ada tiket mendesak dalam antrian saat ini.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right Col: Informasi Toko & Shortcut -->
        <div class="space-y-6">
            <!-- Quick Action Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-800 text-sm mb-3">Aksi Cepat Teknisi & Kasir</h3>
                <div class="space-y-2">
                    <a href="{{ route('tickets.create') }}"
                       class="w-full flex items-center justify-between p-3 rounded-xl bg-blue-50/60 hover:bg-blue-50 border border-blue-100 text-blue-900 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">📝</span>
                            <span class="text-xs font-bold">Input Servis Baru (Intake)</span>
                        </div>
                        <span class="text-blue-500">&rarr;</span>
                    </a>

                    <a href="{{ route('tickets.index', ['status' => 'siap_diambil']) }}"
                       class="w-full flex items-center justify-between p-3 rounded-xl bg-emerald-50/60 hover:bg-emerald-50 border border-emerald-100 text-emerald-900 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">🔔</span>
                            <span class="text-xs font-bold">Panggilan Unit Siap Diambil</span>
                        </div>
                        <span class="text-emerald-500">&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Sparepart Alert -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-slate-800 text-sm">Status Stok Sparepart</h3>
                    @if($lowStockCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                            {{ $lowStockCount }} Menipis
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                            Aman
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem mendeteksi sparepart yang mendekati batas minimum stok untuk kelancaran pengerjaan servis.
                </p>
            </div>
        </div>

    </div>

    <!-- Recent Tickets Overview -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Tiket Terbaru</h3>
            <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Kelola Semua Tiket &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-6">Kode Tiket</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Perangkat Unit</th>
                        <th class="py-3 px-4">Teknisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTickets as $ticket)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-6 font-bold text-blue-600">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="hover:underline">
                                {{ $ticket->ticket_code }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            {{ $ticket->customer->name }}
                            <div class="text-xs font-normal text-slate-400">{{ $ticket->customer->phone }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">
                            {{ $ticket->device_brand }} {{ $ticket->device_model }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 text-xs">
                            {{ $ticket->technician?->name ?? 'Belum Ditugaskan' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="badge-status badge-{{ $ticket->status->value }}">
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            Rp {{ number_format($ticket->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-6 text-right">
                            <a href="{{ route('tickets.show', $ticket->id) }}"
                               class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            Belum ada riwayat tiket servis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
