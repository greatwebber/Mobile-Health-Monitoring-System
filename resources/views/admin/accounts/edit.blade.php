<x-app-layout>
    <x-slot name="title">Manage {{ $managedUser->name }}</x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('admin.accounts.index') }}" class="text-sm font-bold text-teal-700 hover:text-teal-900">← Account register</a>

        @if (session('status'))
            <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-900" role="status">{{ session('status') === 'account-created' ? 'The staff account and profile were created successfully.' : 'The account was updated successfully.' }}</div>
        @endif

        <section class="relative mt-6 overflow-hidden rounded-[2rem] bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10 sm:p-8">
            <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border border-teal-300/15"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-teal-200">{{ $managedUser->role->label() }}</span>
                        <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $managedUser->is_active ? 'bg-emerald-400/20 text-emerald-200' : 'bg-rose-400/20 text-rose-200' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <h1 class="mt-5 text-2xl font-black tracking-tight sm:text-3xl">{{ $managedUser->name }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ $managedUser->email }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Permanent role boundary</p>
                    <p class="mt-1 font-black text-white">{{ $managedUser->role->label() }}</p>
                    <p class="mt-1 text-xs text-slate-400">Role changes are intentionally disabled.</p>
                </div>
            </div>
        </section>

        <form method="POST" action="{{ route('admin.accounts.update', $managedUser) }}" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-black text-slate-800">Full name</label>
                    <input id="name" name="name" required maxlength="255" value="{{ old('name', $managedUser->name) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-black text-slate-800">Email address</label>
                    <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email', $managedUser->email) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                @unless ($managedUser->hasRole(App\Enums\UserRole::Patient))
                    <div>
                        <label for="professional_title" class="block text-sm font-black text-slate-800">Professional title</label>
                        <input id="professional_title" name="professional_title" maxlength="255" value="{{ old('professional_title', $managedUser->staffProfile?->professional_title) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        <x-input-error :messages="$errors->get('professional_title')" class="mt-2" />
                    </div>
                    <div>
                        <label for="department" class="block text-sm font-black text-slate-800">Department</label>
                        <input id="department" name="department" maxlength="255" value="{{ old('department', $managedUser->staffProfile?->department) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        <x-input-error :messages="$errors->get('department')" class="mt-2" />
                    </div>
                    <div>
                        <label for="license_number" class="block text-sm font-black text-slate-800">Professional licence</label>
                        <input id="license_number" name="license_number" maxlength="255" value="{{ old('license_number', $managedUser->staffProfile?->license_number) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-black text-slate-800">Phone</label>
                        <input id="phone" name="phone" maxlength="32" value="{{ old('phone', $managedUser->staffProfile?->phone) }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                @endunless
            </div>

            <fieldset class="mt-7 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <legend class="px-2 text-sm font-black text-slate-900">Account access</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                        <input name="is_active" type="radio" value="1" @checked((string) old('is_active', (int) $managedUser->is_active) === '1') class="mt-1 border-slate-300 text-teal-700 focus:ring-teal-600">
                        <span><span class="block text-sm font-black text-slate-900">Active</span><span class="mt-1 block text-xs leading-5 text-slate-500">The account can authenticate and use authorized features.</span></span>
                    </label>
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 {{ $managedUser->is(Auth::user()) ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                        <input name="is_active" type="radio" value="0" @checked((string) old('is_active', (int) $managedUser->is_active) === '0') @disabled($managedUser->is(Auth::user())) class="mt-1 border-slate-300 text-rose-700 focus:ring-rose-600">
                        <span><span class="block text-sm font-black text-slate-900">Inactive</span><span class="mt-1 block text-xs leading-5 text-slate-500">Blocks sign-in and terminates active sessions.</span></span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('is_active')" class="mt-3" />
            </fieldset>

            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.accounts.index') }}" class="button-secondary min-h-12 justify-center">Cancel</a>
                <button class="button-primary min-h-12 px-7" type="submit">Save account changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
