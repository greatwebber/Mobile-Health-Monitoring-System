<?php

namespace App\Enums;

enum SexAtBirth: string
{
    case Female = 'female';
    case Male = 'male';
    case Intersex = 'intersex';
    case NotStated = 'not_stated';

    public function label(): string
    {
        return match ($this) {
            self::Female => 'Female',
            self::Male => 'Male',
            self::Intersex => 'Intersex',
            self::NotStated => 'Prefer not to state',
        };
    }
}
