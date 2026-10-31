<?php

namespace App\Enums;

enum AlertSeverity: string
{
    case Advisory = 'advisory';
    case Urgent = 'urgent';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Advisory => 'Advisory',
            self::Urgent => 'Urgent review',
            self::Critical => 'Critical review',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Advisory => 'bg-amber-100 text-amber-900',
            self::Urgent => 'bg-orange-100 text-orange-900',
            self::Critical => 'bg-rose-100 text-rose-800',
        };
    }
}
