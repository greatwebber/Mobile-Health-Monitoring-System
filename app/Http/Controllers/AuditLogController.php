<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', AuditLog::class);
        $request->validate([
            'event' => ['nullable', 'string', 'max:120'],
            'actor' => ['nullable', 'string', 'max:160'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $logs = AuditLog::query()
            ->with('actor')
            ->when($request->filled('event'), fn (Builder $query) => $query
                ->where('event', 'like', '%'.trim((string) $request->string('event')).'%'))
            ->when($request->filled('actor'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('actor'));
                $query->whereHas('actor', fn (Builder $actorQuery) => $actorQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('date'), fn (Builder $query) => $query
                ->whereDate('occurred_at', (string) $request->input('date')))
            ->latest('occurred_at')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('audit-logs.index', compact('logs'));
    }
}
