<?php

namespace App\Enums;

enum ChecklistCondition: string
{
    case Normal = 'normal';
    case Rusak = 'rusak';
    case Baret = 'baret';
    case Mati = 'mati';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal / Baik',
            self::Rusak => 'Rusak / Malfungsi',
            self::Baret => 'Baret / Lecet Fisik',
            self::Mati => 'Mati Total / Tidak Berfungsi',
        };
    }
}
