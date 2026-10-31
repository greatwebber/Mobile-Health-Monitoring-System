<?php

namespace App\Http\Controllers;

use App\Enums\ConsentType;
use App\Enums\UserRole;
use App\Http\Requests\StoreConsentDecisionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole(UserRole::Patient), 403);

        $profile = $request->user()->patientProfile;
        abort_if($profile === null, 404);

        $currentDecisions = collect(ConsentType::cases())
            ->mapWithKeys(fn (ConsentType $type) => [$type->value => $profile->latestConsent($type)]);

        return view('consents.index', [
            'consentTypes' => ConsentType::cases(),
            'currentDecisions' => $currentDecisions,
            'documentVersion' => config('health.consent_document_version'),
        ]);
    }

    public function store(StoreConsentDecisionRequest $request, string $type): RedirectResponse
    {
        $consentType = ConsentType::tryFrom($type);
        abort_if($consentType === null, 404);

        $profile = $request->user()->patientProfile;
        $granted = $request->boolean('granted');
        $current = $profile->latestConsent($consentType);

        if ($current?->granted === $granted) {
            return redirect()->route('consents.index')->with('status', 'consent-unchanged');
        }

        $profile->consentRecords()->create([
            'consent_type' => $consentType,
            'document_version' => config('health.consent_document_version'),
            'granted' => $granted,
            'decided_at' => now(),
            'withdrawn_at' => $granted ? null : now(),
            'captured_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('consents.index')->with('status', 'consent-updated');
    }
}
