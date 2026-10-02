@extends('layouts.app', [
    'pageTitle' => 'Form Intake Unit Servis Baru',
    'pageSubtitle' => 'Pendaftaran unit smartphone masuk beserta inspeksi fisik & fungsional awal'
])

@section('content')
<div class="max-w-5xl mx-auto py-2">

    @if ($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800">
        <div class="flex items-center gap-2 font-bold text-sm mb-2">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Terdapat beberapa kesalahan pengisian form:</span>
        </div>
        <ul class="list-disc list-inside text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('tickets.store') }}" method="POST" id="ticket-form" class="space-y-6">
        @csrf

        <!-- STEP 1: DATA PELANGGAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-sm">1</span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Informasi Pelanggan (Customer)</h3>
                        <p class="text-xs text-slate-400">Pilih pelanggan lama atau input data pelanggan baru</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <label class="px-3 py-1.5 rounded-lg cursor-pointer transition flex items-center gap-1.5" id="lbl-cust-new">
                        <input type="radio" name="customer_mode" value="new" checked class="hidden" id="radio-cust-new">
                        <span>Pelanggan Baru</span>
                    </label>
                    <label class="px-3 py-1.5 rounded-lg cursor-pointer transition flex items-center gap-1.5" id="lbl-cust-existing">
                        <input type="radio" name="customer_mode" value="existing" class="hidden" id="radio-cust-existing">
                        <span>Pelanggan Terdaftar</span>
                    </label>
                </div>
            </div>

            <!-- Existing Customer Selector -->
            <div id="section-existing-customer" class="hidden mb-4">
                <label for="customer_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Pilih Pelanggan dari Database
                </label>
                <select name="customer_id" id="customer_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Cari Nama / Nomor HP Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->phone }}) - {{ $c->address ?? 'Tanpa Alamat' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- New Customer Form Fields -->
            <div id="section-new-customer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="customer_name"
                           id="customer_name"
                           value="{{ old('customer_name') }}"
                           placeholder="Contoh: Andi Pratama"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="customer_phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="customer_phone"
                           id="customer_phone"
                           value="{{ old('customer_phone') }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="customer_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email (Opsional)
                    </label>
                    <input type="email"
                           name="customer_email"
                           id="customer_email"
                           value="{{ old('customer_email') }}"
                           placeholder="andi@example.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="customer_address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Tinggal (Opsional)
                    </label>
                    <input type="text"
                           name="customer_address"
                           id="customer_address"
                           value="{{ old('customer_address') }}"
                           placeholder="Jl. Merdeka No. 10..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- STEP 2: DETAIL UNIT SMARTPHONE -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-sm">2</span>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Identitas &amp; Detail Perangkat HP</h3>
                    <p class="text-xs text-slate-400">Merk, tipe spesifik, warna, IMEI, serta PIN layar</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="device_brand" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Merk / Brand <span class="text-rose-500">*</span>
                    </label>
                    <select name="device_brand" id="device_brand" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Brand --</option>
                        <option value="Apple" {{ old('device_brand') == 'Apple' ? 'selected' : '' }}>Apple (iPhone)</option>
                        <option value="Samsung" {{ old('device_brand') == 'Samsung' ? 'selected' : '' }}>Samsung</option>
                        <option value="Xiaomi" {{ old('device_brand') == 'Xiaomi' ? 'selected' : '' }}>Xiaomi / Redmi / POCO</option>
                        <option value="Oppo" {{ old('device_brand') == 'Oppo' ? 'selected' : '' }}>Oppo</option>
                        <option value="Vivo" {{ old('device_brand') == 'Vivo' ? 'selected' : '' }}>Vivo</option>
                        <option value="Realme" {{ old('device_brand') == 'Realme' ? 'selected' : '' }}>Realme</option>
                        <option value="Infinix" {{ old('device_brand') == 'Infinix' ? 'selected' : '' }}>Infinix / Tecno</option>
                        <option value="Huawei" {{ old('device_brand') == 'Huawei' ? 'selected' : '' }}>Huawei</option>
                        <option value="Asus" {{ old('device_brand') == 'Asus' ? 'selected' : '' }}>Asus ROG</option>
                        <option value="Lainnya" {{ old('device_brand') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="device_model" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tipe / Model HP <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="device_model"
                           id="device_model"
                           required
                           value="{{ old('device_model') }}"
                           placeholder="Contoh: iPhone 13 Pro / Galaxy S22"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="device_color" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Warna Unit
                    </label>
                    <input type="text"
                           name="device_color"
                           id="device_color"
                           value="{{ old('device_color') }}"
                           placeholder="Contoh: Sierra Blue / Hitam"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="device_imei" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor IMEI / Serial (Opsional)
                    </label>
                    <input type="text"
                           name="device_imei"
                           id="device_imei"
                           value="{{ old('device_imei') }}"
                           placeholder="86xxxxxxxxxxxxx"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="encrypted_device_pin" class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>PIN / Pola / Password Layar</span>
                        <span class="text-[10px] text-blue-600 font-normal">Terenkripsi otomatis</span>
                    </label>
                    <input type="text"
                           name="encrypted_device_pin"
                           id="encrypted_device_pin"
                           value="{{ old('encrypted_device_pin') }}"
                           placeholder="Contoh: 123456 / Pola L"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="technician_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Teknisi Penanggung Jawab
                    </label>
                    <select name="technician_id" id="technician_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Masukkan ke Antrian Umum --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label for="complaint_notes" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Keluhan Kerusakan Awal dari Pelanggan <span class="text-rose-500">*</span>
                </label>
                <textarea name="complaint_notes"
                          id="complaint_notes"
                          rows="3"
                          required
                          placeholder="Jelaskan detail gejala kerusakan, riwayat jatuh/terkena air, atau permintaan penggantian part..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">{{ old('complaint_notes') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="estimated_cost" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Estimasi Biaya Awal (Rp)
                    </label>
                    <input type="number"
                           name="estimated_cost"
                           id="estimated_cost"
                           value="{{ old('estimated_cost', 0) }}"
                           min="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="warranty_days" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Garansi Servis Pasca Perbaikan (Jumlah Hari)
                    </label>
                    <input type="number"
                           name="warranty_days"
                           id="warranty_days"
                           value="{{ old('warranty_days', 30) }}"
                           min="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-medium focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- STEP 3: MANDATORY CHECKLIST FISIK & FUNGSI (docs/04-service-queue.md) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 mb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 font-extrabold flex items-center justify-center text-sm">3</span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Checklist Kondisi Fisik &amp; Fungsi Awal (Wajib)</h3>
                        <p class="text-xs text-slate-400">Inspeksi menyeluruh sebelum HP dibongkar untuk menghindari klaim sepihak</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            onclick="setAllChecklists('normal')"
                            class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs transition border border-emerald-200">
                        ✓ Set Semua Normal
                    </button>
                </div>
            </div>

            <!-- Checklist Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Komponen &amp; Fungsi</th>
                            <th class="py-3 px-4 text-center">Normal</th>
                            <th class="py-3 px-4 text-center">Rusak / Error</th>
                            <th class="py-3 px-4 text-center">Baret / Lecet</th>
                            <th class="py-3 px-4 text-center">Mati Total</th>
                            <th class="py-3 px-4">Catatan Tambahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($defaultChecklistItems as $index => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                <input type="hidden" name="checklists[{{ $index }}][item_name]" value="{{ $item }}">
                                {{ $item }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="radio"
                                       name="checklists[{{ $index }}][condition_before]"
                                       value="normal"
                                       checked
                                       class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer chk-normal">
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="radio"
                                       name="checklists[{{ $index }}][condition_before]"
                                       value="rusak"
                                       class="w-4 h-4 text-amber-600 focus:ring-amber-500 cursor-pointer">
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="radio"
                                       name="checklists[{{ $index }}][condition_before]"
                                       value="baret"
                                       class="w-4 h-4 text-sky-600 focus:ring-sky-500 cursor-pointer">
                            </td>
                            <td class="py-3 px-4 text-center">
                                <input type="radio"
                                       name="checklists[{{ $index }}][condition_before]"
                                       value="mati"
                                       class="w-4 h-4 text-rose-600 focus:ring-rose-500 cursor-pointer">
                            </td>
                            <td class="py-3 px-4">
                                <input type="text"
                                       name="checklists[{{ $index }}][notes]"
                                       placeholder="Catatan..."
                                       class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs focus:ring-1 focus:ring-blue-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-2 pb-6">
            <a href="{{ route('tickets.index') }}"
               class="px-5 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Daftarkan Unit Servis &amp; Terbitkan Tiket</span>
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    // Toggle New / Existing Customer
    const rNew = document.getElementById('radio-cust-new');
    const rExisting = document.getElementById('radio-cust-existing');
    const lblNew = document.getElementById('lbl-cust-new');
    const lblExisting = document.getElementById('lbl-cust-existing');
    const secNew = document.getElementById('section-new-customer');
    const secExisting = document.getElementById('section-existing-customer');

    function updateCustomerMode() {
        if (rNew.checked) {
            secNew.classList.remove('hidden');
            secExisting.classList.add('hidden');
            lblNew.className = 'px-3 py-1.5 rounded-lg cursor-pointer bg-white text-blue-700 shadow-xs font-bold';
            lblExisting.className = 'px-3 py-1.5 rounded-lg cursor-pointer text-slate-600 font-semibold';
        } else {
            secNew.classList.add('hidden');
            secExisting.classList.remove('hidden');
            lblExisting.className = 'px-3 py-1.5 rounded-lg cursor-pointer bg-white text-blue-700 shadow-xs font-bold';
            lblNew.className = 'px-3 py-1.5 rounded-lg cursor-pointer text-slate-600 font-semibold';
        }
    }

    rNew.addEventListener('change', updateCustomerMode);
    rExisting.addEventListener('change', updateCustomerMode);
    updateCustomerMode();

    function setAllChecklists(val) {
        if (val === 'normal') {
            document.querySelectorAll('.chk-normal').forEach(radio => {
                radio.checked = true;
            });
        }
    }
</script>
@endpush
