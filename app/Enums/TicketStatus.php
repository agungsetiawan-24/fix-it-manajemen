<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Antrian = 'antrian';
    case Diagnosis = 'diagnosis';
    case MenungguApproval = 'menunggu_approval';
    case Proses = 'proses';
    case TestingQc = 'testing_qc';
    case SiapDiambil = 'siap_diambil';
    case Selesai = 'selesai';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::Antrian => 'Antrian',
            self::Diagnosis => 'Diagnosis Kerusakan',
            self::MenungguApproval => 'Menunggu Persetujuan',
            self::Proses => 'Dalam Pengerjaan',
            self::TestingQc => 'Testing & QC',
            self::SiapDiambil => 'Siap Diambil',
            self::Selesai => 'Selesai & Lunas',
            self::Batal => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Antrian => 'slate',
            self::Diagnosis => 'sky',
            self::MenungguApproval => 'amber',
            self::Proses => 'indigo',
            self::TestingQc => 'purple',
            self::SiapDiambil => 'emerald',
            self::Selesai => 'green',
            self::Batal => 'rose',
        };
    }
}
