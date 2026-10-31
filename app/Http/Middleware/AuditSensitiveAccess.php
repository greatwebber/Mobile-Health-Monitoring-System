<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\AuditLogger;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditSensitiveAccess
{
    public function __construct(private AuditLogger $auditLogger) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && $request->isMethod('GET')) {
            $auditable = collect($request->route()?->parameters() ?? [])
                ->first(fn (mixed $parameter) => $parameter instanceof Model);

            $this->auditLogger->record(
                'access.attempted',
                $auditable instanceof Model ? $auditable : null,
                $user,
            );
        }

        return $next($request);
    }
}
