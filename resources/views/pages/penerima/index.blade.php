<x-dashboard-layout>
    <x-slot name="title">Data Penerima</x-slot>
    <x-slot name="breadcrumb">Data Penerima</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Penerima</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar penerima bantuan sosial di wilayah tugas Anda.</p>
        </div>
        @if (\Illuminate\Support\Facades\Route::has('penerima.create'))
            <a href="{{ route('penerima.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">+ Tambah Data</a>
        @else
            <span class="inline-flex cursor-not-allowed items-center justify-center rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500" title="Form tambah akan dibuat pada Issue 15">+ Tambah Data (Issue 15)</span>
        @endif
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">{{ session('error') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('penerima.index') }}" class="grid gap-3 border-b border-slate-100 p-4 md:grid-cols-[minmax(0,1fr)_minmax(160px,0.7fr)_minmax(140px,0.6fr)_auto] md:items-end">
            <div>
                <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">Cari nama atau NIK</label>
                <input id="search" name="search" value="{{ $search }}" placeholder="Cari nama atau NIK..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600" />
            </div>
            <div>
                <label for="wilayah" class="mb-1 block text-xs font-semibold text-slate-600">Wilayah</label>
                <select id="wilayah" name="wilayah" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Semua wilayah tugas</option>
                    @foreach ($wilayahOptions as $wilayah)
                        <option value="{{ $wilayah->id }}" @selected((string) $wilayahId === (string) $wilayah->id)>{{ $wilayah->kelurahan_desa }} — {{ $wilayah->kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status data</label>
                <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Semua Status</option>
                    <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
                    <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Terapkan</button>
                <a href="{{ route('penerima.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Reset</a>
            </div>
        </form>

        <div class="flex flex-wrap gap-2 border-b border-slate-100 px-4 py-3" aria-label="Filter status penerima">
            @foreach (['' => 'Semua', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $key => $label)
                @php
                    $params = array_filter(['search' => $search, 'wilayah' => $wilayahId, 'status' => $key], fn ($value) => $value !== '' && $value !== null);
                    $active = $status === $key;
                    $countKey = $key === '' ? 'semua' : $key;
                @endphp
                <a href="{{ route('penerima.index', $params) }}" @if($active) aria-current="page" @endif
                   class="rounded-lg px-3 py-2 text-xs font-semibold {{ $active ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }} <span class="ml-1 opacity-80">{{ $tabCounts[$countKey] }}</span>
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[830px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">NIK</th>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Wilayah</th>
                        <th class="px-5 py-3">Status Data</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($penerimas as $penerima)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600">{{ str_repeat('*', max(strlen($penerima->nik) - 4, 0)).substr($penerima->nik, -4) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $penerima->nama_lengkap }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $penerima->nama_wilayah ?? '—' }}<span class="block text-xs text-slate-400">{{ $penerima->nama_kecamatan }}</span></td>
                            <td class="px-5 py-4"><x-dashboard.status-badge :status="ucfirst($penerima->status_data)" /></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if (\Illuminate\Support\Facades\Route::has('penerima.edit'))
                                        <a href="{{ route('penerima.edit', $penerima->id) }}" class="rounded-md border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                    @else
                                        <span class="cursor-not-allowed rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-400" title="Issue 15">Edit</span>
                                    @endif
                                    @if (\Illuminate\Support\Facades\Route::has('penerima.destroy'))
                                        <form method="POST" action="{{ route('penerima.destroy', $penerima->id) }}" onsubmit="return confirm('Yakin ingin menghapus data penerima ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                        </form>
                                    @else
                                        <span class="cursor-not-allowed rounded-md border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-400" title="Issue 15">Hapus</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">Tidak ada data penerima sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">
            <div class="mb-3 text-xs text-slate-500">Menampilkan {{ $penerimas->firstItem() ?? 0 }}–{{ $penerimas->lastItem() ?? 0 }} dari {{ $penerimas->total() }} penerima</div>
            {{ $penerimas->links() }}
        </div>
    </div>
</x-dashboard-layout>
