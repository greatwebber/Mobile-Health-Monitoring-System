<nav class="mt-7 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Care profile sections">
    <div class="grid min-w-[34rem] grid-cols-3 gap-1">
        <a href="{{ route('patient-profile.edit') }}" class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition {{ request()->routeIs('patient-profile.*') ? 'bg-teal-700 text-white shadow-md shadow-teal-900/10' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}" @if (request()->routeIs('patient-profile.*')) aria-current="page" @endif>
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 12.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8c.7-3.4 3.3-5.5 7-5.5s6.3 2.1 7 5.5"/></svg>
            Personal details
        </a>
        <a href="{{ route('medical-history.edit') }}" class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition {{ request()->routeIs('medical-history.*') ? 'bg-teal-700 text-white shadow-md shadow-teal-900/10' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}" @if (request()->routeIs('medical-history.*')) aria-current="page" @endif>
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 4h8m-7-1h6v3H9V3ZM6 5h12a2 2 0 0 1 2 2v13H4V7a2 2 0 0 1 2-2Zm3 7h6m-3-3v6"/></svg>
            Medical history
        </a>
        <a href="{{ route('consents.index') }}" class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition {{ request()->routeIs('consents.*') ? 'bg-teal-700 text-white shadow-md shadow-teal-900/10' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}" @if (request()->routeIs('consents.*')) aria-current="page" @endif>
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m8 12 2.5 2.5L16.5 8.5M12 3l8 3v5c0 5.2-3.2 8.5-8 10-4.8-1.5-8-4.8-8-10V6l8-3Z"/></svg>
            Consent & privacy
        </a>
    </div>
</nav>
