<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EdeHealth Sentinel') }}</title>
        @include('partials.pwa-meta')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="auth-shell min-h-screen px-4 py-8 sm:flex sm:items-center sm:justify-center">
            <div class="w-full sm:max-w-md">
                <a href="{{ route('home') }}" class="mb-7 flex items-center justify-center gap-3 rounded-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                    <x-application-logo class="h-12 w-12" />
                    <span class="text-xl font-bold tracking-tight text-slate-950">EdeHealth <span class="text-teal-700">Sentinel</span></span>
                </a>

                <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/95 p-6 shadow-xl shadow-teal-950/10 sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs leading-5 text-slate-500">
                    Clinical decision support, not an emergency or diagnostic service.
                </p>
            </div>
        </div>
        <div id="connection-status" class="connection-status" role="status" aria-live="polite" hidden></div>
        @include('partials.pwa-update-notice')
    </body>
</html>
