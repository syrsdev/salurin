<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Sistem Bansos') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-slate-950/50 md:hidden"
                @click="sidebarOpen = false"
                @keydown.escape.window="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full bg-blue-950 text-white transition-transform duration-200 md:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
                aria-label="Navigasi utama"
            >
                <x-dashboard.sidebar />
            </aside>

            <div class="min-h-screen md:pl-64">
                <header class="sticky top-0 z-30 flex min-h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600 md:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka navigasi"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="flex min-w-0 items-center gap-2 text-xs sm:text-sm">
                            <span class="hidden text-slate-500 sm:inline">Ruang kerja</span>
                            <svg class="hidden h-4 w-4 shrink-0 text-slate-400 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                            <span class="truncate font-semibold text-blue-900">{{ $breadcrumb ?? 'Dashboard' }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3 sm:gap-5">
                        <span class="hidden text-xs text-slate-500 md:inline">Sistem Monitoring Transparansi Bantuan Sosial</span>
                        <button
                            type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600"
                            aria-label="Notifikasi"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
                            </svg>
                        </button>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
