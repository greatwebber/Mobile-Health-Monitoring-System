<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>EdeHealth Sentinel · Health monitoring that stays connected</title>
        @include('partials.pwa-meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-950">
        <div class="landing-shell min-h-screen overflow-hidden">
            <header class="relative z-20">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Primary navigation">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-teal-200">
                        <x-application-logo class="h-11 w-11 shadow-lg shadow-teal-950/15" />
                        <span class="text-lg font-bold tracking-tight">EdeHealth <span class="text-teal-700">Sentinel</span></span>
                    </a>

                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="button-primary">Open dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="button-quiet hidden sm:inline-flex">Sign in</a>
                            <a href="{{ route('register') }}" class="button-primary">Create account</a>
                        @endauth
                    </div>
                </nav>
            </header>

            <main>
                <section class="relative mx-auto grid max-w-7xl gap-12 px-5 pb-20 pt-10 sm:px-8 lg:grid-cols-[1.05fr_.95fr] lg:items-center lg:pb-28 lg:pt-20">
                    <div class="relative z-10 max-w-2xl">
                        <div class="eyebrow"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Built for Federal Polytechnic Ede</div>
                        <h1 class="mt-6 text-5xl font-black leading-[1.03] tracking-[-0.045em] text-slate-950 sm:text-6xl lg:text-7xl">
                            Your health story,<br><span class="text-teal-700">kept in view.</span>
                        </h1>
                        <p class="mt-7 max-w-xl text-lg leading-8 text-slate-600 sm:text-xl">
                            A secure, installable health companion that connects personal monitoring with timely, clinician-supervised care.
                        </p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="button-primary button-large">Continue to dashboard <span aria-hidden="true">→</span></a>
                            @else
                                <a href="{{ route('register') }}" class="button-primary button-large">Start monitoring <span aria-hidden="true">→</span></a>
                                <a href="{{ route('login') }}" class="button-secondary button-large">I already have an account</a>
                            @endauth
                        </div>

                        <div class="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-slate-600">
                            <span class="inline-flex items-center gap-2"><span class="check-dot">✓</span> Installable PWA</span>
                            <span class="inline-flex items-center gap-2"><span class="check-dot">✓</span> Low-bandwidth ready</span>
                            <span class="inline-flex items-center gap-2"><span class="check-dot">✓</span> Human-reviewed AI</span>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-xl lg:mx-0">
                        <div class="hero-orbit hero-orbit-one"></div>
                        <div class="hero-orbit hero-orbit-two"></div>
                        <div class="health-preview relative z-10">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Today’s overview</p>
                                    <h2 class="mt-2 text-2xl font-bold tracking-tight">Good evening, Amina</h2>
                                </div>
                                <span class="status-pill"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Synced</span>
                            </div>

                            <div class="mt-8 grid grid-cols-2 gap-3">
                                <article class="metric-card metric-card-featured">
                                    <div class="metric-icon">♥</div>
                                    <p class="metric-label">Heart rate</p>
                                    <p class="metric-value">72 <span>bpm</span></p>
                                    <p class="metric-note text-emerald-700">Within your usual range</p>
                                </article>
                                <article class="metric-card">
                                    <div class="metric-icon metric-icon-blue">⌁</div>
                                    <p class="metric-label">Blood pressure</p>
                                    <p class="metric-value">118<span>/76</span></p>
                                    <p class="metric-note">Last checked 2h ago</p>
                                </article>
                            </div>

                            <article class="mt-3 rounded-2xl border border-slate-200/80 bg-white p-5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">7-day wellness pattern</p>
                                        <p class="mt-1 text-xs text-slate-500">Your verified observations</p>
                                    </div>
                                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700">Stable</span>
                                </div>
                                <div class="mt-5 flex h-20 items-end gap-2" aria-label="Decorative seven-day trend">
                                    @foreach ([42, 55, 49, 66, 60, 72, 76] as $height)
                                        <span class="trend-bar" style="height: {{ $height }}%"></span>
                                    @endforeach
                                </div>
                            </article>

                            <div class="mt-4 flex items-start gap-3 rounded-2xl bg-amber-50 p-4 text-sm text-amber-950">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-200 font-bold">i</span>
                                <p><strong>Clinical support stays human.</strong> Risk insights are reviewed by authorized care professionals.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-y border-teal-100 bg-white/75 py-16 backdrop-blur">
                    <div class="mx-auto max-w-7xl px-5 sm:px-8">
                        <div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr] lg:gap-16">
                            <div>
                                <p class="eyebrow">One connected health record</p>
                                <h2 class="mt-5 text-3xl font-black tracking-[-0.03em] sm:text-4xl">Designed around continuity, not isolated visits.</h2>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <article class="feature-card">
                                    <span class="feature-number">01</span>
                                    <h3>Record</h3>
                                    <p>Capture symptoms and approved measurements with clear source and time information.</p>
                                </article>
                                <article class="feature-card">
                                    <span class="feature-number">02</span>
                                    <h3>Understand</h3>
                                    <p>Follow meaningful changes through a simple health timeline and trend views.</p>
                                </article>
                                <article class="feature-card">
                                    <span class="feature-number">03</span>
                                    <h3>Connect</h3>
                                    <p>Bring attention to concerning patterns while keeping clinicians responsible for care.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="bg-slate-950 py-8 text-slate-300">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-8">
                    <p>© {{ date('Y') }} EdeHealth Sentinel · Federal Polytechnic Ede Medical Centre</p>
                    <p class="text-slate-500">Monitoring and decision support—not emergency care.</p>
                </div>
            </footer>
        </div>
        <div id="connection-status" class="connection-status" role="status" aria-live="polite" hidden></div>
        @include('partials.pwa-update-notice')
    </body>
</html>
