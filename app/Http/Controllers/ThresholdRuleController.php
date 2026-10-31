<?php

namespace App\Http\Controllers;

use App\Enums\AlertSeverity;
use App\Enums\VitalMetric;
use App\Http\Requests\StoreThresholdRuleRequest;
use App\Models\ThresholdRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThresholdRuleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', ThresholdRule::class);

        return view('threshold-rules.index', [
            'rules' => ThresholdRule::query()
                ->with(['createdBy', 'approvedBy', 'deactivatedBy'])
                ->latest()
                ->paginate(20),
            'metrics' => VitalMetric::cases(),
            'severities' => AlertSeverity::cases(),
        ]);
    }

    public function store(StoreThresholdRuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $metric = VitalMetric::from($validated['metric']);

        ThresholdRule::create([
            ...$validated,
            'unit' => $metric->unit(),
            'is_active' => false,
            'created_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('threshold-rules.index')->with('status', 'threshold-created');
    }

    public function approve(ThresholdRule $thresholdRule): RedirectResponse
    {
        Gate::authorize('approve', $thresholdRule);

        $thresholdRule->update([
            'is_active' => true,
            'approved_by_user_id' => request()->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('threshold-rules.index')->with('status', 'threshold-approved');
    }

    public function deactivate(ThresholdRule $thresholdRule): RedirectResponse
    {
        Gate::authorize('deactivate', $thresholdRule);

        $thresholdRule->update([
            'is_active' => false,
            'deactivated_by_user_id' => request()->user()->id,
            'deactivated_at' => now(),
        ]);

        return redirect()->route('threshold-rules.index')->with('status', 'threshold-deactivated');
    }
}
