@extends('layouts.app', [
    'pageTitle' => 'Detail Tiket #' . $ticket->ticket_code,
    'pageSubtitle' => $ticket->device_brand . ' ' . $ticket->device_model . ' milik ' . $ticket->customer->name
])

@section('content')
<div class="space-y-6 pt-2 max-w-6xl mx-auto">

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl font-black text-slate-800">{{ $ticket->ticket_code }}</span>
            <span class="badge-status badge-{{ $ticket->status->value }} text-xs px-3 py-1">
                {{ $ticket->status->label() }}
            </span>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('tickets.receipt', $ticket->id) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Nota Thermal</span>
            </a>
            <a href="{{ route('tickets.index') }}"
               class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Stepper / Lifecycle Indicator -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs overflow-x-auto">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Alur Pengerjaan Servis:</h3>
        <div class="flex items-center justify-between min-w-[700px] text-xs">
            @php
                $steps = [
                    'antrian' => '1. Antrian',
                    'diagnosis' => '2. Diagnosis',
                    'menunggu_approval' => '3. Approval',
                    'proses' => '4. Proses',
                    'testing_qc' => '5. Testing QC',
                    'siap_diambil' => '6. Siap Ambil',
                    'selesai' => '7. Selesai',
                ];
                $statuses = array_keys($steps);
                $currentIndex = array_search($ticket->status->value, $statuses);
                if ($ticket->status->value === 'batal') {
                    $currentIndex = -1;
                }
            @endphp

            @foreach($steps as $key => $label)
                @php
                    $stepIndex = array_search($key, $statuses);
                    $isPassed = ($currentIndex !== false && $stepIndex <= $currentIndex);
                    $isCurrent = ($ticket->status->value === $key);
                @endphp
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow' : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400') }}">
                        @if($isPassed && !$isCurrent)
                            ✓
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </div>
                    <span class="font-bold {{ $isCurrent ? 'text-blue-700 font-extrabold' : ($isPassed ? 'text-slate-700' : 'text-slate-400') }}">
                        {{ $label }}
                    </span>
                    @if(!$loop->last)
                    <div class="w-8 h-0.5 {{ $isPassed && $stepIndex < $currentIndex ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Device Details & Checklists -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Device & Complaint Info -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                <h3 class="font-bold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100">
                    Informasi Unit &amp; Keluhan
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Merk &amp; Tipe HP:</span>
                        <span class="text-slate-800 font-bold text-sm">{{ $ticket->device_brand }} {{ $ticket->device_model }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Warna Unit:</span>
                        <span class="text-slate-800 font-semibold">{{ $ticket->device_color ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Nomor IMEI:</span>
                        <span class="text-slate-800 font-mono font-semibold">{{ $ticket->device_imei ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">PIN / Pola Layar:</span>
                        <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                            {{ $ticket->encrypted_device_pin ?? 'Tidak ada' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Teknisi:</span>
                        <span class="text-slate-800 font-semibold">{{ $ticket->technician?->name ?? 'Belum Ditugaskan' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Garansi Servis:</span>
                        <span class="text-emerald-700 font-bold">
                            {{ $ticket->warranty_days }} Hari
                            @if($ticket->warranty_expiry_date)
                                (s/d {{ $ticket->warranty_expiry_date->format('d M Y') }})
                            @endif
                        </span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <span class="font-bold text-slate-700 block mb-1">Keluhan Pelanggan:</span>
                    <p class="text-slate-600 leading-relaxed">{{ $ticket->complaint_notes }}</p>
                </div>

                @if($ticket->technician_notes)
                <div class="p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-100 text-xs mt-3">
                    <span class="font-bold text-indigo-900 block mb-1">Catatan Pengerjaan Teknisi:</span>
                    <p class="text-indigo-800 leading-relaxed">{{ $ticket->technician_notes }}</p>
                </div>
                @endif
            </div>

            <!-- Mandatory Intake Checklist Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Checklist Kondisi Fisik &amp; Fungsional</h3>
                        <p class="text-xs text-slate-400">Pemeriksaan kondisi unit saat diserahkan ke toko</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                        {{ $ticket->checklists->count() }} Komponen
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3">Komponen / Item</th>
                                <th class="py-2.5 px-3">Kondisi Awal (Intake)</th>
                                <th class="py-2.5 px-3">Kondisi Akhir (QC)</th>
                                <th class="py-2.5 px-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ticket->checklists as $chk)
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $chk->item_name }}</td>
                                <td class="py-2.5 px-3">
                                    @php
                                        $badgeColor = match($chk->condition_before->value) {
                                            'normal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'rusak' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'baret' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'mati' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $chk->condition_before->label() }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    @if($chk->condition_after)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $chk->condition_after->label() }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Belum QC</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-slate-500">{{ $chk->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Col: Update Status & Customer Card -->
        <div class="space-y-6">

            <!-- Customer Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-800 text-sm mb-3 pb-2 border-b border-slate-100 flex items-center justify-between">
                    <span>Data Pelanggan</span>
                    <span class="text-xs text-blue-600 font-bold">👤</span>
                </h3>
                <div class="space-y-2 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Nama:</span>
                        <span class="text-slate-800 font-bold text-sm">{{ $ticket->customer->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Nomor WhatsApp / HP:</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->customer->phone) }}" target="_blank"
                           class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                            <span>{{ $ticket->customer->phone }}</span>
                            <span class="text-[10px]">↗</span>
                        </a>
                    </div>
                    @if($ticket->customer->email)
                    <div>
                        <span class="text-slate-400 block font-medium">Email:</span>
                        <span class="text-slate-700">{{ $ticket->customer->email }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="text-slate-400 block font-medium">Alamat:</span>
                        <span class="text-slate-700 leading-relaxed">{{ $ticket->customer->address ?? 'Tidak ada data alamat' }}</span>
                    </div>
                </div>
            </div>

            <!-- Update Status & Action Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h3 class="font-bold text-slate-800 text-sm mb-3 pb-2 border-b border-slate-100">
                    Perbarui Status &amp; Catatan
                </h3>

                <form action="{{ route('tickets.update-status', $ticket->id) }}" method="POST" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                            Status Tiket Pengerjaan
                        </label>
                        <select name="status" id="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                            @foreach(\App\Enums\TicketStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ $ticket->status === $st ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="technician_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tugaskan ke Teknisi
                        </label>
                        <select name="technician_id" id="technician_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $ticket->technician_id == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="total_cost" class="block text-xs font-semibold text-slate-700 mb-1">
                            Total Biaya Servis (Rp)
                        </label>
                        <input type="number"
                               name="total_cost"
                               id="total_cost"
                               value="{{ old('total_cost', $ticket->total_cost) }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="warranty_days" class="block text-xs font-semibold text-slate-700 mb-1">
                            Masa Garansi (Hari)
                        </label>
                        <input type="number"
                               name="warranty_days"
                               id="warranty_days"
                               value="{{ old('warranty_days', $ticket->warranty_days) }}"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="technician_notes" class="block text-xs font-semibold text-slate-700 mb-1">
                            Catatan Pengerjaan Teknisi
                        </label>
                        <textarea name="technician_notes"
                                  id="technician_notes"
                                  rows="3"
                                  placeholder="Tuliskan tindakan perbaikan atau sparepart yang diganti..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">{{ old('technician_notes', $ticket->technician_notes) }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition">
                        Simpan Perubahan Status
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
