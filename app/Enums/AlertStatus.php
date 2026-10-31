<?php

namespace App\Enums;

enum AlertStatus: string
{
    case Open = 'open';
    case Acknowledged = 'acknowledged';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Acknowledged => 'Acknowledged',
            self::Resolved => 'Resolved',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-rose-100 text-rose-800',
            self::Acknowledged => 'bg-amber-100 text-amber-900',
            self::Resolved => 'bg-emerald-100 text-emerald-800',
        };
    }
}
