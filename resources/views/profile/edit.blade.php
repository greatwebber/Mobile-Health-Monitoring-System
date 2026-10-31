<x-app-layout>
    <x-slot name="title">Account settings</x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Security and identity</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Account settings</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Manage the identity and sign-in details used to protect your EdeHealth account.</p>
        </div>

        <div class="mt-8 space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-3xl border border-rose-200 bg-rose-50/50 p-6 shadow-sm sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
