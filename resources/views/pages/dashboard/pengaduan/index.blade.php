<x-dashboard-layout>
    <x-slot name="title">Pengaduan Masuk</x-slot>
    <x-slot name="breadcrumb">Pengaduan Masuk</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaduan Masuk</h1>
            <p class="mt-1 text-sm text-slate-500">Tinjau dan tanggapi pengaduan masyarakat di wilayah tugas Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('dashboard.pengaduan.index') }}" class="grid grid-cols-1 gap-3 border-b border-slate-100 p-4 sm:grid-cols-[1fr_200px_auto_auto]">
            <label for="search" class="sr-only">Cari pengaduan</label>
            <input id="search" name="search" value="{{ $search }}" placeholder="Cari kode tiket atau isi pengaduan..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
            <label for="status" class="sr-only">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Semua status</option>
                @foreach (\App\Models\Pengaduan::STATUS as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Terapkan</button>
            @if ($search !== '' || $status !== '')
                <a href="{{ route('dashboard.pengaduan.index') }}" class="rounded-lg px-3 py-2 text-center text-sm text-slate-600 hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Tiket</th>
                        <th scope="col" class="px-5 py-3">Kategori / Pengaduan</th>
                        <th scope="col" class="px-5 py-3">Tanggal masuk</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengaduan as $item)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ $item->kode_tiket }}</td>
                            <td class="max-w-md px-5 py-4">
                                <p class="font-medium text-slate-700">{{ \App\Models\Pengaduan::KATEGORI[$item->kategori] ?? 'Lainnya' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($item->isi_pengaduan, 110) }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $item->tgl_pengaduan?->format('d-m-Y H:i') }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-blue-50 text-blue-700' => $item->status_pengaduan === 'baru',
                                    'bg-amber-50 text-amber-700' => in_array($item->status_pengaduan, ['diproses', 'eskalasi'], true),
                                    'bg-green-50 text-green-700' => $item->status_pengaduan === 'selesai',
                                ])>{{ \App\Models\Pengaduan::STATUS[$item->status_pengaduan] ?? $item->status_pengaduan }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('dashboard.pengaduan.show', $item) }}" class="inline-flex rounded-md border border-blue-200 px-3 py-1.5 font-medium text-blue-700 hover:bg-blue-50">Lihat &amp; tanggapi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500">Belum ada pengaduan yang sesuai di wilayah Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $pengaduan->links() }}</div>
    </div>
</x-dashboard-layout>
