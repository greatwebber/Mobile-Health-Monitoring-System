<x-app-layout>
    <x-slot name="title">Account administration</x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Identity and access</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Account administration</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Review account status, maintain staff credentials and provision authorized Medical Centre personnel. Existing roles are locked to protect record ownership.</p>
            </div>
            <a href="{{ route('admin.accounts.create') }}" class="button-primary min-h-12 justify-center">Provision staff account</a>
        </div>

        <form method="GET" action="{{ route('admin.accounts.index') }}" class="mt-7 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[1fr_12rem_12rem_auto] sm:p-5">
            <div>
                <label for="account_search" class="sr-only">Search accounts</label>
                <input id="account_search" name="search" value="{{ request('search') }}" maxlength="160" class="block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Name, email or identifier">
            </div>
            <div>
                <label for="account_role" class="sr-only">Role</label>
                <select id="account_role" name="role" class="block min-h-12 w-full rounded-2xl border-slate-300">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="account_status" class="sr-only">Status</label>
                <select id="account_status" name="status" class="block min-h-12 w-full rounded-2xl border-slate-300">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <button class="button-secondary min-h-12 justify-center" type="submit">Filter accounts</button>
        </form>

        <div class="mt-7 grid gap-4 lg:grid-cols-2">
            @forelse ($accounts as $account)
                @php $identifier = $account->staffProfile?->staff_number ?? $account->patientProfile?->patient_number; @endphp
                <a href="{{ route('admin.accounts.edit', $account) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-teal-950/5 focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200 sm:p-6">
                    <div class="flex items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl {{ $account->is_active ? 'bg-teal-100 text-teal-800' : 'bg-slate-200 text-slate-500' }} font-black">{{ str($account->name)->substr(0, 1)->upper() }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate font-black text-slate-950">{{ $account->name }}</h2>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[0.64rem] font-black uppercase tracking-wider text-slate-700">{{ $account->role->label() }}</span>
                                <span class="rounded-full px-2.5 py-1 text-[0.64rem] font-black uppercase tracking-wider {{ $account->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <p class="mt-2 truncate text-sm text-slate-600">{{ $account->email }}</p>
                            <p class="mt-1 text-xs font-bold text-slate-400">{{ $identifier ?? 'Profile identifier unavailable' }}</p>
                        </div>
                        <span class="text-sm font-black text-teal-700 transition group-hover:translate-x-0.5">→</span>
                    </div>
                </a>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center lg:col-span-2">
                    <h2 class="font-black text-slate-900">No accounts match</h2>
                    <p class="mt-2 text-sm text-slate-500">Clear or adjust the filters to review the account register.</p>
                </div>
            @endforelse
        </div>

        @if ($accounts->hasPages())
            <div class="mt-7">{{ $accounts->links() }}</div>
        @endif
    </div>
</x-app-layout>
