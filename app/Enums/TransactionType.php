<?php

namespace App\Enums;

enum TransactionType: string
{
    case PemasukanServis = 'pemasukan_servis';
    case PembelianSparepart = 'pembelian_sparepart';
    case PengeluaranOps = 'pengeluaran_ops';
    case PenjualanPos = 'penjualan_pos';

    public function label(): string
    {
        return match ($this) {
            self::PemasukanServis => 'Pemasukan Servis',
            self::PembelianSparepart => 'Pembelian Sparepart (Belanja Modal)',
            self::PengeluaranOps => 'Pengeluaran Operasional',
            self::PenjualanPos => 'Penjualan Langsung POS',
        };
    }

    public function isIncome(): bool
    {
        return match ($this) {
            self::PemasukanServis, self::PenjualanPos => true,
            self::PembelianSparepart, self::PengeluaranOps => false,
        };
    }
}
