<?php

namespace App\Enums;

enum ClinicalResultFlag: string
{
    case NotFlagged = 'not_flagged';
    case Low = 'low';
    case High = 'high';
    case Abnormal = 'abnormal';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::NotFlagged => 'Not flagged',
            self::Low => 'Low',
            self::High => 'High',
            self::Abnormal => 'Abnormal',
            self::Critical => 'Critical',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::NotFlagged => 'bg-emerald-100 text-emerald-800',
            self::Low, self::High => 'bg-amber-100 text-amber-900',
            self::Abnormal => 'bg-orange-100 text-orange-900',
            self::Critical => 'bg-rose-100 text-rose-800',
        };
    }
}
