<?php

namespace App\Enums;

enum SymptomSeverity: string
{
    case Mild = 'mild';
    case Moderate = 'moderate';
    case Severe = 'severe';

    public function label(): string
    {
        return match ($this) {
            self::Mild => 'Mild',
            self::Moderate => 'Moderate',
            self::Severe => 'Severe',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Mild => 'bg-emerald-100 text-emerald-800',
            self::Moderate => 'bg-amber-100 text-amber-900',
            self::Severe => 'bg-rose-100 text-rose-800',
        };
    }
}
