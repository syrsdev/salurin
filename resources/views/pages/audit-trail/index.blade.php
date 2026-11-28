<x-dashboard-layout>
    <x-slot name="title">Audit Trail</x-slot>

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">Audit Trail</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                Riwayat perubahan data pada {{ $scopeName }}. NIK disamarkan pada halaman detail.
            </p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
            {{ $scopeName }}
        </span>
    </div>

    <section class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Total aktivitas</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Data ditambahkan</p>
            <p class="mt-2 text-2xl font-bold text-green-700">{{ number_format($summary['create']) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Data diperbarui</p>
            <p class="mt-2 text-2xl font-bold text-blue-700">{{ number_format($summary['update']) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">Data dihapus</p>
            <p class="mt-2 text-2xl font-bold text-red-700">{{ number_format($summary['delete']) }}</p>
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('audit-trail.index') }}"
            class="grid grid-cols-1 gap-3 border-b border-slate-100 p-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="xl:col-span-2">
                <label for="cari-audit" class="mb-1 block text-xs font-semibold text-slate-600">Cari aktivitas</label>
                <input id="cari-audit" name="cari" value="{{ $filters['cari'] ?? '' }}"
                    placeholder="Nama pengguna, tabel, atau ID..."
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
            <div>
                <label for="filter-aksi" class="mb-1 block text-xs font-semibold text-slate-600">Jenis aksi</label>
                <select id="filter-aksi" name="aksi" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua aksi</option>
                    <option value="create" @selected(($filters['aksi'] ?? '') === 'create')>Create</option>
                    <option value="update" @selected(($filters['aksi'] ?? '') === 'update')>Update</option>
                    <option value="delete" @selected(($filters['aksi'] ?? '') === 'delete')>Delete</option>
                </select>
            </div>
            <div>
                <label for="filter-tabel" class="mb-1 block text-xs font-semibold text-slate-600">Jenis data</label>
                <select id="filter-tabel" name="tabel" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua data</option>
                    @foreach ($tableLabels as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['tabel'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter-dari" class="mb-1 block text-xs font-semibold text-slate-600">Dari tanggal</label>
                <input id="filter-dari" type="date" name="dari" value="{{ $filters['dari'] ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="filter-sampai" class="mb-1 block text-xs font-semibold text-slate-600">Sampai tanggal</label>
                <input id="filter-sampai" type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div class="flex flex-wrap items-end gap-2 sm:col-span-2 xl:col-span-6">
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Terapkan Filter</button>
                <a href="{{ route('audit-trail.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">Data yang diubah</th>
                        <th class="px-5 py-3">Jenis aksi</th>
                        <th class="px-5 py-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($audits as $audit)
                        @php
                            $aksiLabel = ['create' => 'Create', 'update' => 'Update', 'delete' => 'Delete'][$audit->aksi] ?? ucfirst($audit->aksi);
                            $aksiClass = ['create' => 'bg-green-50 text-green-700', 'update' => 'bg-blue-50 text-blue-700', 'delete' => 'bg-red-50 text-red-700'][$audit->aksi] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-medium text-slate-800">{{ $audit->waktu?->format('d/m/Y') }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $audit->waktu?->format('H:i:s') }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">{{ $audit->pengguna?->name ?? 'Pengguna tidak tersedia' }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ $tableLabels[$audit->nama_tabel] ?? $audit->nama_tabel }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">ID data: {{ $audit->id_record }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $aksiClass }}">{{ $aksiLabel }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('audit-trail.show', $audit) }}"
                                    class="inline-flex rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="font-semibold text-slate-700">Belum ada riwayat perubahan</p>
                                <p class="mt-1 text-xs text-slate-500">Aktivitas yang tercatat pada wilayah ini akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-2 border-t border-slate-100 px-5 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>Menampilkan {{ $audits->firstItem() ?? 0 }}–{{ $audits->lastItem() ?? 0 }} dari {{ $audits->total() }} aktivitas</span>
            {{ $audits->links() }}
        </div>
    </section>
</x-dashboard-layout>
