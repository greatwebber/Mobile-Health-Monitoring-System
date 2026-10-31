<?php

namespace App\Enums;

enum ConsentType: string
{
    case CareDataProcessing = 'care_data_processing';
    case HealthMonitoring = 'health_monitoring';
    case ResearchUse = 'research_use';
    case AiRiskSupport = 'ai_risk_support';

    public function label(): string
    {
        return match ($this) {
            self::CareDataProcessing => 'Care data processing',
            self::HealthMonitoring => 'Longitudinal health monitoring',
            self::ResearchUse => 'De-identified research use',
            self::AiRiskSupport => 'AI-assisted risk support',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CareDataProcessing => 'Allow the Medical Centre to use the information you provide for registration, consultation and continuity of care.',
            self::HealthMonitoring => 'Allow verified measurements and symptoms to be stored over time and reviewed by authorized clinical personnel.',
            self::ResearchUse => 'Allow appropriately authorized, de-identified information to be considered for academic evaluation. This preference is optional.',
            self::AiRiskSupport => 'Allow eligible information to be evaluated by an approved risk-support model when one becomes available. This preference is optional and is not a diagnosis.',
        };
    }

    public function isRequiredForCoreCare(): bool
    {
        return in_array($this, [self::CareDataProcessing, self::HealthMonitoring], true);
    }
}
