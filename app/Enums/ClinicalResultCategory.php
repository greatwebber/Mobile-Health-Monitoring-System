<?php

namespace App\Enums;

enum ClinicalResultCategory: string
{
    case Haematology = 'haematology';
    case Chemistry = 'chemistry';
    case Microbiology = 'microbiology';
    case Urinalysis = 'urinalysis';
    case Imaging = 'imaging';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Haematology => 'Haematology',
            self::Chemistry => 'Clinical chemistry',
            self::Microbiology => 'Microbiology',
            self::Urinalysis => 'Urinalysis',
            self::Imaging => 'Imaging or procedure',
            self::Other => 'Other clinical result',
        };
    }
}
