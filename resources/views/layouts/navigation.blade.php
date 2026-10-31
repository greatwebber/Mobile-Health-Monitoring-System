@php
    $isPatient = Auth::user()->hasRole(\App\Enums\UserRole::Patient);
    $isAdministrator = Auth::user()->hasRole(\App\Enums\UserRole::Administrator);
    $initials = str(Auth::user()->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => str($part)->substr(0, 1)->upper())
        ->implode('');
@endphp

<header class="app-bar">
    <div class="mx-auto flex h-[4.5rem] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-2xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
            <x-application-logo class="h-10 w-10 shadow-lg shadow-teal-950/10 transition group-hover:scale-[1.03]" />
            <span class="leading-tight">
                <span class="block text-[0.68rem] font-black uppercase tracking-[0.16em] text-teal-700">Medical Centre</span>
                <span class="block text-sm font-black tracking-tight text-slate-950 sm:text-base">EdeHealth Sentinel</span>
            </span>
        </a>

        <div class="flex items-center gap-2 sm:gap-3">
            <span id="navigation-loader" class="navigation-loader" role="status" aria-live="polite" hidden>
                <span class="navigation-loader-ring" aria-hidden="true"></span>
                <span class="hidden sm:inline">Loading securely</span>
                <span class="sr-only sm:hidden">Loading page</span>
            </span>

            <span id="secure-session-indicator" class="hidden items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 sm:inline-flex">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-50 motion-reduce:animate-none"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </span>
                Secure session
            </span>

            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="account-trigger" aria-label="Open account menu">
                        <span class="account-avatar">{{ $initials }}</span>
                        <span class="hidden text-left sm:block">
                            <span class="block max-w-32 truncate text-xs font-black text-slate-900">{{ Auth::user()->name }}</span>
                            <span class="mt-0.5 block text-[0.65rem] font-bold uppercase tracking-wider text-teal-700">{{ Auth::user()->role->label() }}</span>
                        </span>
                        <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="border-b border-slate-100 px-4 py-3 sm:hidden">
                        <p class="truncate text-sm font-bold text-slate-900">{{ Auth::user()->name }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">{{ __('Account settings') }}</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Sign out securely') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>

<div class="app-dock-wrap">
    <nav class="app-dock" aria-label="Application navigation" data-testid="bottom-navigation">
        <x-bottom-nav-item :href="route('dashboard')" label="Home" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m3 10.8 9-7.3 9 7.3v8.7a1 1 0 0 1-1 1h-5.2v-6h-5.6v6H4a1 1 0 0 1-1-1v-8.7Z"/></svg>
            </x-slot>
        </x-bottom-nav-item>

        @if ($isPatient)
            <x-bottom-nav-item :href="route('patient-profile.edit')" label="Profile" :active="request()->routeIs('patient-profile.*', 'medical-history.*', 'consents.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8c.7-3.4 3.3-5.5 7-5.5s6.3 2.1 7 5.5"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item label="Record" disabled prominent>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2.2" d="M12 6v12M6 12h12"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item :href="route('timeline.index')" label="Timeline" :active="request()->routeIs('timeline.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 5v14m0-10h6m-6 7h10M11 6l3 3-3 3m4 1 3 3-3 3"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item :href="route('encounters.index')" label="Visits" :active="request()->routeIs('encounters.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 3v3m10-3v3M4.5 9.5h15M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg>
                </x-slot>
            </x-bottom-nav-item>
        @elseif ($isAdministrator)
            <x-bottom-nav-item label="Accounts" disabled>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7-1a2.75 2.75 0 1 0 0-5.5M3.5 20c.4-3.8 2.5-6 6-6s5.6 2.2 6 6m1-6c2.5.2 3.8 2 4 4.5"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item :href="route('threshold-rules.index')" label="Rules" :active="request()->routeIs('threshold-rules.*')" prominent>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 6h10m4 0h2M4 12h3m4 0h9M4 18h8m4 0h4M14 3v6M7 9v6m5 0v6"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item label="Audit" disabled>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M6 3h9l3 3v15H6V3Zm3 7h6m-6 4h6m-6 4h4"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item label="Reports" disabled>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 20V10m7 10V4m7 16v-7"/></svg>
                </x-slot>
            </x-bottom-nav-item>
        @else
            <x-bottom-nav-item :href="route('encounters.index')" label="Patients" :active="request()->routeIs('encounters.index', 'encounters.show', 'encounters.edit')">
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M9.5 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7-1a2.75 2.75 0 1 0 0-5.5M3.5 20c.4-3.8 2.5-6 6-6s5.6 2.2 6 6m1-6c2.5.2 3.8 2 4 4.5"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item :href="route('encounters.create')" label="Triage" :active="request()->routeIs('encounters.create')" prominent>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2.2" d="M12 6v12M6 12h12"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item :href="route('alerts.index')" label="Alerts" :active="request()->routeIs('alerts.*')">
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Zm-8 10h4"/></svg>
                </x-slot>
            </x-bottom-nav-item>

            <x-bottom-nav-item label="Reports" disabled>
                <x-slot name="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 20V10m7 10V4m7 16v-7"/></svg>
                </x-slot>
            </x-bottom-nav-item>
        @endif
    </nav>
</div>
