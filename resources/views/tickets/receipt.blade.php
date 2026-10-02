<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota Servis - {{ $ticket->ticket_code }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 8px;
            color: #000;
            background: #fff;
            max-width: 80mm;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 6px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .qr-placeholder {
            display: inline-block;
            padding: 6px;
            border: 2px solid #000;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 6px 0;
            font-size: 10px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 12px; padding: 6px; background: #e0f2fe; text-align: center; font-family: sans-serif;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #0284c7; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Cetak Ulang Nota
        </button>
        <span style="font-size: 11px; margin-left: 8px;">(Format Thermal 58mm / 80mm)</span>
    </div>

    <div class="text-center">
        <div class="font-bold" style="font-size: 14px;">FIX-IT SERVICE PRO</div>
        <div>Spesialis Servis Smartphone & Gadget</div>
        <div>Jl. Jend. Sudirman No. 12 &bull; 0812-3456-7890</div>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>No. Tiket:</span>
        <span class="font-bold">{{ $ticket->ticket_code }}</span>
    </div>
    <div class="row">
        <span>Tanggal:</span>
        <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row">
        <span>Kasir/Petugas:</span>
        <span>{{ Auth::user()->name ?? 'Admin Kasir' }}</span>
    </div>

    <div class="divider"></div>

    <div class="row">
        <span>Pelanggan:</span>
        <span class="font-bold">{{ $ticket->customer->name }}</span>
    </div>
    <div class="row">
        <span>Telepon/WA:</span>
        <span>{{ $ticket->customer->phone }}</span>
    </div>
    <div class="row">
        <span>Perangkat:</span>
        <span class="font-bold">{{ $ticket->device_brand }} {{ $ticket->device_model }}</span>
    </div>
    @if($ticket->device_imei)
    <div class="row">
        <span>IMEI:</span>
        <span>{{ $ticket->device_imei }}</span>
    </div>
    @endif
    <div class="row">
        <span>Status:</span>
        <span class="font-bold">{{ strtoupper($ticket->status->label()) }}</span>
    </div>

    <div class="divider"></div>

    <div class="font-bold">KELUHAN AWAL:</div>
    <div style="margin-bottom: 4px;">{{ $ticket->complaint_notes }}</div>

    @if($ticket->checklists->count() > 0)
    <div class="font-bold" style="margin-top: 6px;">KONDISI FISIK SAAT DITERIMA:</div>
    @foreach($ticket->checklists as $chk)
        @if($chk->condition_before->value !== 'normal')
        <div class="row" style="font-size: 10px;">
            <span>- {{ $chk->item_name }}:</span>
            <span class="font-bold">{{ strtoupper($chk->condition_before->label()) }}</span>
        </div>
        @endif
    @endforeach
    @endif

    <div class="double-divider"></div>

    <div class="row" style="font-size: 12px;">
        <span class="font-bold">TOTAL BIAYA:</span>
        <span class="font-bold">Rp {{ number_format($ticket->total_cost, 0, ',', '.') }}</span>
    </div>

    <div class="row" style="margin-top: 2px;">
        <span>Masa Garansi:</span>
        <span>{{ $ticket->warranty_days }} Hari</span>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 6px;">
        <div class="qr-placeholder">
            [ KODE QR TRACKING ]<br>
            {{ $ticket->ticket_code }}
        </div>
        <div style="font-size: 9px; margin-top: 4px;">
            Scan QR atau simpan nota ini untuk melacak status servis unit Anda.
        </div>
        <div style="font-size: 9px; margin-top: 4px;">
            * Harap membawa tanda terima ini saat pengambilan unit *
        </div>
        <div style="font-size: 9px; margin-top: 6px; font-weight: bold;">
            Terima Kasih Atas Kepercayaan Anda
        </div>
    </div>

</body>
</html>
