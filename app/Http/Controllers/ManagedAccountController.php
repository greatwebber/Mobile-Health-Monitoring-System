<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreManagedAccountRequest;
use App\Http\Requests\UpdateManagedAccountRequest;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ManagedAccountController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);
        $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
            'role' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $accounts = User::query()
            ->with(['patientProfile', 'staffProfile'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('search'));
                $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('patientProfile', fn (Builder $profile) => $profile->where('patient_number', 'like', "%{$search}%"))
                    ->orWhereHas('staffProfile', fn (Builder $profile) => $profile->where('staff_number', 'like', "%{$search}%")));
            })
            ->when(
                UserRole::tryFrom((string) $request->input('role')),
                fn (Builder $query, UserRole $role) => $query->where('role', $role->value),
            )
            ->when($request->input('status') === 'active', fn (Builder $query) => $query->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn (Builder $query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.accounts.index', [
            'accounts' => $accounts,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.accounts.create', [
            'staffRoles' => [UserRole::Nurse, UserRole::Clinician, UserRole::Administrator],
        ]);
    }

    public function store(StoreManagedAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $account = DB::transaction(function () use ($validated): User {
            $account = new User;
            $account->forceFill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
                'is_active' => true,
                'email_verified_at' => now(),
            ])->save();

            $account->staffProfile()->create([
                'staff_number' => StaffProfile::numberFor($account),
                'professional_title' => $validated['professional_title'],
                'department' => $validated['department'],
                'license_number' => $validated['license_number'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]);

            return $account;
        });

        return redirect()->route('admin.accounts.edit', $account)->with('status', 'account-created');
    }

    public function edit(User $managedUser): View
    {
        Gate::authorize('update', $managedUser);
        $managedUser->load(['patientProfile', 'staffProfile']);

        return view('admin.accounts.edit', compact('managedUser'));
    }

    public function update(UpdateManagedAccountRequest $request, User $managedUser): RedirectResponse
    {
        $validated = $request->validated();
        $wasActive = $managedUser->is_active;

        DB::transaction(function () use ($managedUser, $validated): void {
            $managedUser->forceFill([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $validated['is_active'],
            ])->save();

            if (! $managedUser->hasRole(UserRole::Patient)) {
                $managedUser->staffProfile()->updateOrCreate(
                    [],
                    [
                        'staff_number' => $managedUser->staffProfile?->staff_number ?? StaffProfile::numberFor($managedUser),
                        'professional_title' => $validated['professional_title'] ?? null,
                        'department' => $validated['department'] ?? null,
                        'license_number' => $validated['license_number'] ?? null,
                        'phone' => $validated['phone'] ?? null,
                    ],
                );
            }
        });

        if ($wasActive && ! $managedUser->fresh()->is_active) {
            DB::table('sessions')->where('user_id', $managedUser->id)->delete();
        }

        return redirect()->route('admin.accounts.edit', $managedUser)->with('status', 'account-updated');
    }
}
