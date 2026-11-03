```blade
<x-dashboard-layout>
    <x-slot name="title">Data Penerima</x-slot>
    <x-slot name="breadcrumb">Data Penerima</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Data Penerima
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola data penerima bantuan sosial di wilayah tugasmu.
                </p>
            </div>

            @if (Route::has('penerima.create'))
                <a href="{{ route('penerima.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                    <span class="text-lg">+</span>
                    Tambah Penerima
                </a>
            @else
                <span
                    class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-3 text-sm font-semibold text-slate-600">
                    <span class="text-lg">+</span>
                    Tambah Penerima
                </span>
            @endif
        </div>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Pencarian dan Filter --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('penerima.index') }}" method="GET"
                class="grid grid-cols-1 gap-4 md:grid-cols-4">

                <div class="md:col-span-2">
                    <label for="search" class="mb-2 block text-sm font-medium text-slate-700">
                        Cari Penerima
                    </label>
                    <input id="search" type="text" name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Cari nama atau NIK..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="wilayah" class="mb-2 block text-sm font-medium text-slate-700">
                        Wilayah
                    </label>
                    <select id="wilayah" name="wilayah"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Wilayah</option>
                        @foreach ($wilayahOptions as $wilayah)
                            <option value="{{ $wilayah->id }}"
                                @selected((string) ($wilayahId ?? '') === (string) $wilayah->id)>
                                {{ $wilayah->kelurahan_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
                        Status
                    </label>
                    <select id="status" name="status"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Status</option>
                        <option value="menunggu" @selected(($status ?? '') === 'menunggu')>
                            Menunggu
                        </option>
                        <option value="disetujui" @selected(($status ?? '') === 'disetujui')>
                            Disetujui
                        </option>
                        <option value="ditolak" @selected(($status ?? '') === 'ditolak')>
                            Ditolak
                        </option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-2 md:col-span-4">
                    <button type="submit"
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                        Cari Data
                    </button>

                    <a href="{{ route('penerima.index') }}"
                        class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tab Status --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach ([
                'menunggu' => 'Menunggu',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
            ] as $key => $label)
                <a href="{{ route('penerima.index', array_merge(request()->except('page', 'status'), ['status' => $key])) }}"
                    class="rounded-xl border p-4 transition hover:shadow-sm
                    {{ ($status ?? '') === $key ? 'border-blue-400 bg-blue-50' : 'border-slate-200 bg-white' }}">
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $tabCounts[$key] ?? 0 }}
                    </p>
                </a>
            @endforeach
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">
                    Daftar Penerima Bantuan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi penerima bantuan sosial.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[750px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-semibold">No.</th>
                            <th class="px-5 py-4 font-semibold">NIK</th>
                            <th class="px-5 py-4 font-semibold">Nama Lengkap</th>
                            <th class="px-5 py-4 font-semibold">Wilayah</th>
                            <th class="px-5 py-4 font-semibold">Status</th>
                            <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($penerima as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $penerima->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 font-medium text-slate-700">
                                    {{ str_repeat('*', max(0, strlen($item->nik) - 4)) . substr($item->nik, -4) }}
                                </td>

                                <td class="px-5 py-4 font-medium text-slate-900">
                                    {{ $item->nama_lengkap }}
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    {{ $item->kelurahan_desa ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $statusClass = match ($item->status_data) {
                                            'disetujui' => 'bg-green-100 text-green-700',
                                            'ditolak' => 'bg-red-100 text-red-700',
                                            default => 'bg-yellow-100 text-yellow-700',
                                        };
                                    @endphp

                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($item->status_data) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (Route::has('penerima.edit'))
                                            <a href="{{ route('penerima.edit', $item->id) }}"
                                                class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                                Edit
                                            </a>
                                        @else
                                            <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-400">
                                                Edit
                                            </span>
                                        @endif

                                        @if (Route::has('penerima.destroy'))
                                            <form action="{{ route('penerima.destroy', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data penerima ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-400">
                                                Hapus
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <p class="font-semibold text-slate-700">
                                        Data penerima belum tersedia
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Belum ada data yang sesuai dengan pencarian atau filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($penerima->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $penerima->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</x-dashboard-layout>
