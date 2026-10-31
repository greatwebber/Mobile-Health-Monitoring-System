<?php

namespace App\Enums;

enum ObservationSource: string
{
    case Clinical = 'clinical';
    case Patient = 'patient';

    public function label(): string
    {
        return match ($this) {
            self::Clinical => 'Clinician verified',
            self::Patient => 'Patient reported',
        };
    }
}
