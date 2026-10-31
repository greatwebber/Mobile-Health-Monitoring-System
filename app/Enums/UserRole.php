<?php

namespace App\Enums;

enum UserRole: string
{
    case Patient = 'patient';
    case Nurse = 'nurse';
    case Clinician = 'clinician';
    case Administrator = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::Patient => 'Patient',
            self::Nurse => 'Nurse',
            self::Clinician => 'Clinician',
            self::Administrator => 'Administrator',
        };
    }
}
