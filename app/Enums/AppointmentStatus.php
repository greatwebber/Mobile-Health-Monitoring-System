<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::NoShow => 'Did not attend',
        };
    }

    public function isFinal(): bool
    {
        return $this !== self::Scheduled;
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Scheduled => 'bg-sky-100 text-sky-800',
            self::Completed => 'bg-emerald-100 text-emerald-800',
            self::Cancelled => 'bg-slate-200 text-slate-700',
            self::NoShow => 'bg-amber-100 text-amber-900',
        };
    }
}
