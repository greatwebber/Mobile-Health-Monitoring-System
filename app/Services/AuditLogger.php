<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogger
{
    /** @param array<string, mixed> $metadata */
    public function record(
        string $event,
        ?Model $auditable = null,
        ?User $actor = null,
        array $metadata = [],
    ): AuditLog {
        $request = app()->bound('request') ? request() : null;

        return AuditLog::query()->create([
            'request_id' => $this->requestId($request),
            'actor_user_id' => $actor?->id,
            'event' => Str::limit($event, 120, ''),
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'route_name' => $request?->route()?->getName(),
            'request_method' => $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 500, ''),
            'metadata' => $metadata === [] ? null : $metadata,
            'occurred_at' => now(),
        ]);
    }

    private function requestId(?Request $request): string
    {
        if ($request === null) {
            return (string) Str::uuid();
        }

        if (! $request->attributes->has('audit_request_id')) {
            $request->attributes->set('audit_request_id', (string) Str::uuid());
        }

        return (string) $request->attributes->get('audit_request_id');
    }
}
