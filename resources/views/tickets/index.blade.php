@extends('layouts.app', [
    'pageTitle' => 'Manajemen Tiket Servis',
    'pageSubtitle' => 'Kelola seluruh alur perbaikan unit smartphone dan antrian teknisi'
])

@section('content')
<div class="space-y-6 pt-2">

    <!-- Status Tabs Navigation (conforming to docs/07-ui-ux-design-system.md) -->
    <div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max">
            @php
                $tabs = [
                    'all' => ['label' => 'Semua Tiket', 'color' => 'slate'],
                    'antrian' => ['label' => 'Antrian', 'color' => 'slate'],
                    'diagnosis' => ['label' => 'Diagnosis', 'color' => 'sky'],
                    'menunggu_approval' => ['label' => 'Menunggu Approval', 'color' => 'amber'],
                    'proses' => ['label' => 'Proses Pengerjaan', 'color' => 'indigo'],
                    'testing_qc' => ['label' => 'Testing & QC', 'color' => 'purple'],
                    'siap_diambil' => ['label' => 'Siap Diambil', 'color' => 'emerald'],
                    'selesai' => ['label' => 'Selesai & Lunas', 'color' => 'green'],
                    'batal' => ['label' => 'Dibatalkan', 'color' => 'rose'],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($statusFilter === $key) || (!$statusFilter && $key === 'all');
                    $count = $statusCounts[$key] ?? 0;
                @endphp
                <a href="{{ route('tickets.index', array_merge(request()->except('page'), ['status' => $key])) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $isActive ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <input type="hidden" name="status" value="{{ $statusFilter ?? 'all' }}">

            <div class="relative w-full md:w-96">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari kode tiket, nama, nomor HP, unit HP..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="technician_id"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Teknisi</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $technicianFilter == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>

                @if($search || $technicianFilter || ($statusFilter && $statusFilter !== 'all'))
                <a href="{{ route('tickets.index') }}"
                   class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                    Reset
                </a>
                @endif

                <a href="{{ route('tickets.create') }}"
                   class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Unit Baru</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-6">Kode Tiket</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Unit HP</th>
                        <th class="py-3.5 px-4">Keluhan Awal</th>
                        <th class="py-3.5 px-4">Teknisi</th>
                        <th class="py-3.5 px-4">Status Antrian</th>
                        <th class="py-3.5 px-4">Total Biaya</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-4 px-6 font-bold text-blue-600">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="hover:underline flex items-center gap-1.5">
                                <span>{{ $ticket->ticket_code }}</span>
                            </a>
                            <span class="text-[11px] text-slate-400 font-normal block">
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </span>
                        </td>
                        <td class="py-4 px-4 font-semibold text-slate-800">
                            {{ $ticket->customer->name }}
                            <div class="text-xs font-normal text-slate-500">{{ $ticket->customer->phone }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-800 block">{{ $ticket->device_brand }} {{ $ticket->device_model }}</span>
                            @if($ticket->device_color)
                            <span class="text-xs text-slate-400">Warna: {{ $ticket->device_color }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $ticket->complaint_notes }}
                            </p>
                        </td>
                        <td class="py-4 px-4 text-xs font-medium">
                            @if($ticket->technician)
                                <span class="text-slate-800 font-semibold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    {{ $ticket->technician->name }}
                                </span>
                            @else
                                <span class="text-amber-600 italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <span class="badge-status badge-{{ $ticket->status->value }}">
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                        <td class="py-4 px-4 font-bold text-slate-800">
                            Rp {{ number_format($ticket->total_cost, 0, ',', '.') }}
                            @if($ticket->warranty_days > 0)
                            <span class="block text-[11px] text-emerald-600 font-normal">Garansi {{ $ticket->warranty_days }} hari</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tickets.show', $ticket->id) }}"
                                   class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold text-xs transition">
                                    Detail / Pengerjaan
                                </a>
                                <a href="{{ route('tickets.receipt', $ticket->id) }}" target="_blank"
                                   title="Cetak Tanda Terima"
                                   class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">🔍</div>
                            <p class="font-medium text-slate-600">Tidak ada tiket servis yang sesuai kriteria filter.</p>
                            <a href="{{ route('tickets.create') }}" class="inline-block mt-3 text-xs font-bold text-blue-600 hover:underline">
                                + Daftarkan Unit Baru Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
