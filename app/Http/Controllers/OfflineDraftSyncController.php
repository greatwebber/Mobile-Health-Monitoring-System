<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncPatientProfileDraftRequest;
use App\Models\OfflineSyncReceipt;
use App\Models\PatientProfile;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OfflineDraftSyncController extends Controller
{
    public function patientProfile(SyncPatientProfileDraftRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $existingReceipt = OfflineSyncReceipt::query()
            ->where('user_id', $request->user()->id)
            ->where('draft_id', $validated['draft_id'])
            ->first();

        if ($existingReceipt) {
            $request->session()->flash('status', 'offline-profile-synchronized');

            return response()->json([
                'status' => 'already-synchronized',
                'resource_id' => $existingReceipt->resource_id,
            ]);
        }

        $result = DB::transaction(function () use ($request, $validated): array {
            $profile = PatientProfile::query()
                ->whereKey($request->user()->patientProfile->id)
                ->lockForUpdate()
                ->firstOrFail();
            $baseVersion = CarbonImmutable::parse($validated['base_updated_at']);

            if (! $profile->updated_at->equalTo($baseVersion)) {
                return [
                    'conflict' => true,
                    'server_updated_at' => $profile->updated_at->toISOString(),
                ];
            }

            $profile->fill(Arr::except($validated, ['draft_id', 'base_updated_at']))->save();
            $profile->refreshCompletionStatus();

            OfflineSyncReceipt::query()->create([
                'user_id' => $request->user()->id,
                'draft_id' => $validated['draft_id'],
                'resource_type' => 'patient_profile',
                'resource_id' => $profile->id,
                'synced_at' => now(),
            ]);

            return [
                'conflict' => false,
                'resource_id' => $profile->id,
                'server_updated_at' => $profile->fresh()->updated_at->toISOString(),
            ];
        });

        if ($result['conflict']) {
            return response()->json([
                'status' => 'conflict',
                'message' => 'The server profile changed after this draft was created.',
                'server_updated_at' => $result['server_updated_at'],
            ], 409);
        }

        $request->session()->flash('status', 'offline-profile-synchronized');

        return response()->json([
            'status' => 'synchronized',
            'resource_id' => $result['resource_id'],
            'server_updated_at' => $result['server_updated_at'],
        ]);
    }
}
