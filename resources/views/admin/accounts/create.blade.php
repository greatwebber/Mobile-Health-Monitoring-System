<x-app-layout>
    <x-slot name="title">Provision staff account</x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <a href="{{ route('admin.accounts.index') }}" class="text-sm font-bold text-teal-700 hover:text-teal-900">← Account register</a>
        <p class="mt-6 text-xs font-black uppercase tracking-[0.18em] text-teal-700">Authorized provisioning</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Create a staff account</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Assign the correct role at creation. Roles cannot be changed later because clinical records and permissions depend on that identity boundary.</p>

        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-950"><strong>Credential handling:</strong> use a unique temporary password, share it through an approved private channel and instruct the staff member to change it immediately after first sign-in.</div>

        <form method="POST" action="{{ route('admin.accounts.store') }}" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-black text-slate-800">Full name</label>
                    <input id="name" name="name" required maxlength="255" value="{{ old('name') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-black text-slate-800">Work email</label>
                    <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" autocomplete="email">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="role" class="block text-sm font-black text-slate-800">Authorized role</label>
                    <select id="role" name="role" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                        @foreach ($staffRoles as $role)
                            <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>
                <div>
                    <label for="professional_title" class="block text-sm font-black text-slate-800">Professional title</label>
                    <input id="professional_title" name="professional_title" required maxlength="255" value="{{ old('professional_title') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" placeholder="Medical Officer">
                    <x-input-error :messages="$errors->get('professional_title')" class="mt-2" />
                </div>
                <div>
                    <label for="department" class="block text-sm font-black text-slate-800">Department</label>
                    <input id="department" name="department" required maxlength="255" value="{{ old('department') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                    <x-input-error :messages="$errors->get('department')" class="mt-2" />
                </div>
                <div>
                    <label for="license_number" class="block text-sm font-black text-slate-800">Professional licence <span class="font-semibold text-slate-400">(if applicable)</span></label>
                    <input id="license_number" name="license_number" maxlength="255" value="{{ old('license_number') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300">
                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                </div>
                <div>
                    <label for="phone" class="block text-sm font-black text-slate-800">Phone <span class="font-semibold text-slate-400">(optional)</span></label>
                    <input id="phone" name="phone" maxlength="32" value="{{ old('phone') }}" class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" autocomplete="tel">
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div></div>
                <div>
                    <label for="password" class="block text-sm font-black text-slate-800">Temporary password</label>
                    <input id="password" name="password" type="password" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-black text-slate-800">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-2 block min-h-12 w-full rounded-2xl border-slate-300" autocomplete="new-password">
                </div>
            </div>
            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.accounts.index') }}" class="button-secondary min-h-12 justify-center">Cancel</a>
                <button class="button-primary min-h-12 px-7" type="submit">Create active staff account</button>
            </div>
        </form>
    </div>
</x-app-layout>
