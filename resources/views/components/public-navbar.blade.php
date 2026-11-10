<header class="border-b border-slate-200 bg-white">
    <div
        class="mx-auto flex max-w-[1600px] flex-col justify-between gap-3 px-4 py-3 sm:flex-row sm:items-center sm:px-6 lg:px-8">
        <a href="{{ route('transparansi') }}" class="flex items-center gap-3 text-blue-900 no-underline">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-900 text-white"
                aria-hidden="true">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 9h18M5 9v10m4-10v10m6-10v10m4-10v10M3 19h18M12 3 3 7h18l-9-4Z" />
                </svg>
            </span>
            <span class="text-sm font-bold sm:text-base">Transparansi Bantuan Sosial</span>
        </a>
        <nav class="flex flex-wrap items-center gap-2 text-xs font-medium sm:gap-5 sm:text-sm"
            aria-label="Navigasi publik">
            <a href="{{ route('transparansi') }}"
                @if (request()->routeIs('transparansi')) aria-current="page" @endif
                class="rounded-lg px-3 py-2 {{ request()->routeIs('transparansi') ? 'bg-blue-50 text-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                Dashboard Publik
            </a>
            <a href="{{ route('cek-status-bantuan.index') }}"
                @if (request()->routeIs('cek-status-bantuan.*')) aria-current="page" @endif
                class="rounded-lg px-3 py-2 {{ request()->routeIs('cek-status-bantuan.*') ? 'bg-blue-50 text-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                Cek Status Bantuan
            </a>
            <a href="{{ route('pengaduan.create') }}"
                @if (request()->routeIs('pengaduan.*')) aria-current="page" @endif
                class="rounded-lg px-3 py-2 {{ request()->routeIs('pengaduan.*') ? 'bg-blue-50 text-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                Pengaduan
            </a>
            <span class="hidden border-l border-slate-200 pl-4 text-xs text-slate-500 md:inline">Layanan
                publik</span>
        </nav>
    </div>
</header>