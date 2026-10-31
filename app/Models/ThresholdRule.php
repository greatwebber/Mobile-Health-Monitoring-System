<?php

namespace App\Models;

use App\Enums\AlertSeverity;
use App\Enums\VitalMetric;
use Database\Factories\ThresholdRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThresholdRule extends Model
{
    /** @use HasFactory<ThresholdRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'metric',
        'minimum_value',
        'maximum_value',
        'unit',
        'severity',
        'guidance',
        'version',
        'is_active',
        'created_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'deactivated_by_user_id',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'metric' => VitalMetric::class,
            'minimum_value' => 'decimal:4',
            'maximum_value' => 'decimal:4',
            'severity' => AlertSeverity::class,
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    public function isBreached(float $value): bool
    {
        return ($this->minimum_value !== null && $value < (float) $this->minimum_value)
            || ($this->maximum_value !== null && $value > (float) $this->maximum_value);
    }

    public function rangeLabel(): string
    {
        if ($this->minimum_value !== null && $this->maximum_value !== null) {
            return $this->trimmed($this->minimum_value).'–'.$this->trimmed($this->maximum_value).' '.$this->unit;
        }

        if ($this->minimum_value !== null) {
            return 'At least '.$this->trimmed($this->minimum_value).' '.$this->unit;
        }

        return 'At most '.$this->trimmed($this->maximum_value).' '.$this->unit;
    }

    private function trimmed(string $value): string
    {
        return rtrim(rtrim($value, '0'), '.');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by_user_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(ClinicalAlert::class);
    }
}
