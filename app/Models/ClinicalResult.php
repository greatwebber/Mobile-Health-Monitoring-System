<?php

namespace App\Models;

use App\Enums\ClinicalResultCategory;
use App\Enums\ClinicalResultFlag;
use App\Enums\ObservationSource;
use Database\Factories\ClinicalResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalResult extends Model
{
    /** @use HasFactory<ClinicalResultFactory> */
    use HasFactory;

    protected $fillable = [
        'clinical_encounter_id',
        'category',
        'test_name',
        'test_code',
        'value_numeric',
        'value_text',
        'unit',
        'reference_range',
        'flag',
        'specimen',
        'collected_at',
        'resulted_at',
        'notes',
        'source',
        'recorded_by_user_id',
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => ClinicalResultCategory::class,
            'value_numeric' => 'decimal:4',
            'flag' => ClinicalResultFlag::class,
            'collected_at' => 'datetime',
            'resulted_at' => 'datetime',
            'source' => ObservationSource::class,
            'verified_at' => 'datetime',
        ];
    }

    public function displayValue(): string
    {
        if (filled($this->value_text)) {
            return $this->value_text;
        }

        $value = rtrim(rtrim((string) $this->value_numeric, '0'), '.');

        return trim($value.' '.($this->unit ?? ''));
    }

    public function clinicalEncounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
