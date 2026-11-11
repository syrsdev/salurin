<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Salurin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-gradient-to-b from-blue-50 to-slate-50 px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-6xl flex-col justify-center">
                <header class="mb-6 flex items-center justify-between gap-4">
                    <a href="{{ route('transparansi') }}" class="flex items-center gap-3 text-blue-950">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-900 text-white shadow-lg shadow-blue-950/15">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M5 9v10m4-10v10m6-10v10m4-10v10M3 19h18M12 3 3 7h18l-9-4Z" />
                            </svg>
                        </span>
                        <span class="flex flex-col">
                            <span class="text-base font-bold leading-tight">Salurin</span>
                            <span class="text-xs font-medium text-slate-500">Transparansi Bantuan Sosial</span>
                        </span>
                    </a>
                    <a href="{{ route('transparansi') }}" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-blue-900 sm:inline-flex">
                        Kembali ke portal publik
                    </a>
                </header>

                <main class="grid overflow-hidden rounded-3xl bg-white shadow-xl shadow-blue-950/5 ring-1 ring-slate-200 lg:min-h-[560px] lg:grid-cols-[0.9fr_1.1fr]">
                    <aside class="relative isolate overflow-hidden bg-blue-950 px-6 py-8 text-white sm:px-10 sm:py-10 lg:flex lg:flex-col lg:justify-between lg:px-12 lg:py-12">
                        <div class="pointer-events-none absolute -right-20 -top-24 -z-10 h-72 w-72 rounded-full border border-white/10"></div>
                        <div class="pointer-events-none absolute -bottom-36 -left-20 -z-10 h-72 w-72 rounded-full border border-white/10"></div>

                        <div class="relative">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold tracking-wide text-blue-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-300"></span>
                                LAYANAN PUBLIK
                            </span>
                            <h1 class="mt-6 max-w-sm text-2xl font-bold leading-tight sm:text-3xl">
                                Bantuan sosial yang lebih transparan.
                            </h1>
                            <p class="mt-4 max-w-md text-sm leading-6 text-blue-100/80">
                                Akses layanan Salurin untuk memantau informasi dan penyaluran bantuan sosial dengan mudah.
                            </p>
                        </div>

                        <div class="relative mt-8 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 lg:mt-12">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sky-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m5 2a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>
                            </span>
                            <p class="text-xs leading-5 text-blue-100/80">
                                Informasi layanan yang mudah diakses, aman, dan dapat dipercaya.
                            </p>
                        </div>
                    </aside>

                    <section class="flex items-center px-6 py-8 sm:px-10 sm:py-12 lg:px-14">
                        <div class="mx-auto w-full max-w-md">
                            {{ $slot }}
                        </div>
                    </section>
                </main>

                <footer class="mt-5 flex flex-col items-center justify-between gap-2 text-center text-xs text-slate-500 sm:flex-row sm:text-left">
                    <span>&copy; {{ date('Y') }} Salurin. Layanan Transparansi Bantuan Sosial.</span>
                    <span>Informasi untuk masyarakat</span>
                </footer>
            </div>
        </div>
    </body>
</html>
