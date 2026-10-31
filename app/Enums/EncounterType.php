<?php

namespace App\Enums;

enum EncounterType: string
{
    case Triage = 'triage';
    case Consultation = 'consultation';
    case FollowUp = 'follow_up';

    public function label(): string
    {
        return match ($this) {
            self::Triage => 'Triage',
            self::Consultation => 'Consultation',
            self::FollowUp => 'Follow-up',
        };
    }
}
