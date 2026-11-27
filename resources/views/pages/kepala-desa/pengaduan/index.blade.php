<x-dashboard-layout>
    <x-slot name="title">Pengaduan Diteruskan</x-slot>
    <x-slot name="breadcrumb">Pengaduan Diteruskan</x-slot>

    @php
        $badge = [
            'baru' => 'bg-slate-100 text-slate-700',
            'diproses' => 'bg-blue-50 text-blue-700',
            'eskalasi' => 'bg-amber-50 text-amber-700',
            'selesai' => 'bg-green-50 text-green-700',
        ];
    @endphp

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Pengaduan Diteruskan</h1>
        <p class="mt-1 text-sm text-slate-500">Pengaduan yang diteruskan petugas dan membutuhkan keputusan atau arahan Anda.</p>
    </div>

    @if (session('success'))
        <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <nav class="mt-6 flex gap-1 border-b border-slate-200" aria-label="Filter pengaduan">
        <a href="{{ route('kades.pengaduan.index', ['tab' => 'menunggu']) }}"
           class="-mb-px border-b-2 px-4 py-2.5 text-sm font-semibold {{ $tab === 'menunggu' ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Menunggu Arahan
            <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800">{{ $jumlahMenunggu }}</span>
        </a>
        <a href="{{ route('kades.pengaduan.index', ['tab' => 'sudah']) }}"
           class="-mb-px border-b-2 px-4 py-2.5 text-sm font-semibold {{ $tab === 'sudah' ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            Sudah Diarahkan
            <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700">{{ $jumlahSudah }}</span>
        </a>
    </nav>

    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('kades.pengaduan.index') }}" class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <label class="sr-only" for="cari">Cari kode tiket</label>
            <input id="cari" name="cari" value="{{ $cari }}" placeholder="Cari kode tiket, contoh: ADU-2611-001" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase focus:border-blue-600 focus:ring-blue-600 sm:max-w-sm">
            <button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
            @if ($cari !== '')
                <a href="{{ route('kades.pengaduan.index', ['tab' => $tab]) }}" class="rounded-lg px-3 py-2 text-center text-sm text-slate-600 hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kode Tiket</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Isi Pengaduan</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengaduans as $p)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ $p->kode_tiket }}</td>
                            <td class="px-5 py-4">{{ \App\Models\Pengaduan::KATEGORI[$p->kategori] ?? '-' }}</td>
                            <td class="max-w-xs px-5 py-4 text-slate-500">{{ \Illuminate\Support\Str::limit($p->isi_pengaduan, 80) }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $p->tgl_pengaduan?->format('d-m-Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[$p->status_pengaduan] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ \App\Models\Pengaduan::STATUS[$p->status_pengaduan] ?? $p->status_pengaduan }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('kades.pengaduan.show', $p) }}" class="rounded-md border border-blue-200 px-3 py-1.5 font-medium text-blue-700 hover:bg-blue-50">
                                    {{ $tab === 'menunggu' ? 'Beri Arahan' : 'Lihat' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            {{ $tab === 'menunggu' ? 'Tidak ada pengaduan yang menunggu arahan.' : 'Belum ada pengaduan yang Anda arahkan.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $pengaduans->links() }}</div>
    </div>
</x-dashboard-layout>
