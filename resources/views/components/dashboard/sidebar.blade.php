<div class="flex flex-col h-full">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 border-b min-h-16 border-white/10">
        <span class="flex items-center justify-center w-10 h-10 text-white shrink-0 rounded-xl bg-white/15">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 10h18M5 10v9m4-9v9m6-9v9m4-9v9M3 21h18M12 3 3 8h18l-9-5Z" />
            </svg>
        </span>
        <span class="min-w-0">
            <span class="block text-sm font-bold tracking-wide truncate">Sistem Bansos</span>
            <span class="mt-0.5 block text-[10px] text-blue-200">Monitoring &amp; transparansi</span>
        </span>
    </a>

    <div class="px-3 pt-5">
        <div class="rounded-lg border border-white/10 bg-white/[0.04] px-3 py-3">
            <p class="text-[9px] font-semibold uppercase tracking-wider text-blue-200">Wilayah operasional</p>
            <p class="mt-1.5 text-xs font-semibold text-white">Desa Sukamaju</p>
            <p class="mt-1 text-[10px] text-blue-200">Kecamatan Sukasari</p>
        </div>
    </div>

    <nav class="flex-1 px-3 mt-6" aria-label="Menu ruang kerja">
        <p class="px-2 text-[9px] font-semibold uppercase tracking-wider text-blue-200">Ruang kerja</p>
        <div class="mt-2 space-y-1">
            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" @class([
                'flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs font-semibold transition',
                'bg-blue-800 text-white shadow-sm' => request()->routeIs('dashboard'),
                'text-blue-100 hover:bg-white/10 hover:text-white' => !request()->routeIs(
                    'dashboard'),
            ])
                @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.8" aria-hidden="true">
                    <rect x="4" y="4" width="6" height="6" rx="1" />
                    <rect x="14" y="4" width="6" height="6" rx="1" />
                    <rect x="4" y="14" width="6" height="6" rx="1" />
                    <rect x="14" y="14" width="6" height="6" rx="1" />
                </svg>
                <span>Dashboard</span>
            </a>
            @if (auth()->user()->role == 'petugas')
                <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs text-blue-200/80"
                    aria-disabled="true">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" aria-hidden="true">
                        <circle cx="9" cy="8" r="3" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.5 19a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8M17 14a5 5 0 0 1 3.5 4.8" />
                    </svg>
                    <span>Data Penerima</span>
                </span>

                <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs text-blue-200/80"
                    aria-disabled="true">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m12 3 9 5-9 5-9-5 9-5Zm-9 9 9 5 9-5m-18 5 9 5 9-5" />
                    </svg>
                    <span>Jenis Bantuan</span>
                </span>

                <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs text-blue-200/80"
                    aria-disabled="true">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 4h14a2 2 0 0 1 2 2v14l-4-2H7a2 2 0 0 1-2-2V4Zm4 5h6m-6 4h4" />
                    </svg>
                    <span>Penyaluran Bantuan</span>
                </span>

                <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs text-blue-200/80"
                    aria-disabled="true">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v12H8l-4 3V5Z" />
                    </svg>
                    <span class="flex-1">Pengaduan Masuk</span>
                    <span class="rounded bg-blue-800 px-1.5 py-0.5 text-[9px] font-semibold text-blue-100">11</span>
                </span>
            @endif

        </div>
    </nav>

    <div class="p-3 border-t border-white/10">
        <div class="flex items-center gap-2.5 rounded-lg px-2 py-2">
            <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-900">
                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
            </span>
            <span class="flex-1 min-w-0">
                <span class="block truncate text-[11px] font-semibold text-white">{{ Auth::user()->name }}</span>
                <span class="mt-0.5 block truncate text-[9px] text-blue-200">Petugas Desa/Kelurahan</span>
            </span>
            <a href="{{ route('profile.edit') }}" class="p-1 text-blue-200 rounded hover:bg-white/10 hover:text-white"
                aria-label="Buka profil">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    aria-hidden="true">
                    <circle cx="12" cy="12" r="3" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m19.4 15 .1.1a1.7 1.7 0 0 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 0 1-3.4 0v-.2a1.7 1.7 0 0 0-2.9-1.2l-.1.1a1.7 1.7 0 0 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 0 1 0-3.4h.2a1.7 1.7 0 0 0 1.2-2.9l-.1-.1a1.7 1.7 0 0 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a1.7 1.7 0 0 1 3.4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a1.7 1.7 0 0 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 0 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2 2.9Z" />
                </svg>
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="px-2">
            @csrf
            <button type="submit"
                class="w-full mt-3 rounded-lg px-2 py-1.5 text-left text-[10px]  bg-red-600 hover:bg-red-700 text-white">
                Keluar
            </button>
        </form>
    </div>
</div>
