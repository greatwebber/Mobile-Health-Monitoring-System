<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UpdateMedicalHistoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MedicalHistoryController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()->hasRole(UserRole::Patient), 403);

        $profile = $request->user()->patientProfile;
        abort_if($profile === null, 404);

        $history = $profile->medicalHistory()->firstOrCreate();
        Gate::authorize('view', $history);

        return view('medical-history.edit', ['history' => $history]);
    }

    public function update(UpdateMedicalHistoryRequest $request): RedirectResponse
    {
        $history = $request->user()->patientProfile->medicalHistory;
        $validated = $request->validated();

        $history->update([
            'allergies' => $this->lines($validated['allergies_text'] ?? null),
            'chronic_conditions' => $this->lines($validated['chronic_conditions_text'] ?? null),
            'current_medications' => $this->lines($validated['current_medications_text'] ?? null),
            'previous_surgeries' => $this->lines($validated['previous_surgeries_text'] ?? null),
            'family_history' => $this->lines($validated['family_history_text'] ?? null),
            'additional_notes' => filled($validated['additional_notes'] ?? null)
                ? trim($validated['additional_notes'])
                : null,
            'updated_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('medical-history.edit')->with('status', 'medical-history-updated');
    }

    /** @return list<string> */
    private function lines(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return Str::of($value)
            ->replace("\r\n", "\n")
            ->explode("\n")
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->unique(fn (string $line) => mb_strtolower($line))
            ->take(50)
            ->values()
            ->all();
    }
}
