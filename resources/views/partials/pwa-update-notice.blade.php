@php($aboveDock ??= false)

<section
    id="pwa-update-notice"
    data-pwa-update-notice
    data-turbo-permanent
    class="fixed inset-x-4 {{ $aboveDock ? 'bottom-28 sm:bottom-6' : 'bottom-4 sm:bottom-6' }} z-50 mx-auto max-w-md rounded-3xl border border-teal-200 bg-white/95 p-4 shadow-2xl shadow-teal-950/20 backdrop-blur"
    role="region"
    aria-labelledby="pwa-update-title"
    aria-describedby="pwa-update-description"
    hidden>
    <div class="flex items-start gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-800" aria-hidden="true">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20 7v5h-5M4 17v-5h5m9.2-2A7 7 0 0 0 6.7 6.7L4 9m16 6-2.7 2.3A7 7 0 0 1 5.8 14"/>
            </svg>
        </span>
        <div class="min-w-0 flex-1">
            <h2 id="pwa-update-title" class="text-sm font-black text-slate-950">App update ready</h2>
            <p id="pwa-update-description" class="mt-1 text-xs leading-5 text-slate-600">Refresh when you are ready to use the latest secure version. Unsaved form changes will not be preserved.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" class="button-primary min-h-10 px-4 py-2 text-xs" data-pwa-update-refresh>Update now</button>
                <button type="button" class="button-quiet min-h-10 px-4 py-2 text-xs" data-pwa-update-dismiss>Later</button>
            </div>
        </div>
    </div>
</section>
