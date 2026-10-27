<x-dashboard-layout>
    <x-slot name="title">Jenis Bantuan</x-slot>
    <x-slot name="breadcrumb">Jenis Bantuan</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Jenis Bantuan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola jenis dan nominal program bantuan sosial.</p>
        </div>
        <a href="{{ route('jenis-bantuan.create') }}" class="inline-flex justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">+ Tambah Jenis Bantuan</a>
    </div>

    @if (session('success'))
        <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('jenis-bantuan.index') }}" class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row">
            <label class="sr-only" for="search">Cari jenis bantuan</label>
            <input id="search" name="search" value="{{ $search }}" placeholder="Cari nama bantuan..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600 sm:max-w-sm">
            <button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
            @if ($search !== '')
                <a href="{{ route('jenis-bantuan.index') }}" class="rounded-lg px-3 py-2 text-center text-sm text-slate-600 hover:bg-slate-100">Reset</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[690px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr><th class="px-5 py-3">Nama Bantuan</th><th class="px-5 py-3">Nominal</th><th class="px-5 py-3">Deskripsi</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jenisBantuans as $bantuan)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $bantuan->nama_bantuan }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $bantuan->nominal === null ? 'Belum ditentukan' : 'Rp'.number_format((float) $bantuan->nominal, 0, ',', '.') }}</td>
                            <td class="max-w-xs px-5 py-4 text-slate-500">{{ \Illuminate\Support\Str::limit($bantuan->deskripsi ?: '—', 90) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('jenis-bantuan.edit', $bantuan) }}" class="rounded-md border border-blue-200 px-3 py-1.5 font-medium text-blue-700 hover:bg-blue-50">Edit</a>
                                    <form method="POST" action="{{ route('jenis-bantuan.destroy', $bantuan) }}" onsubmit="return confirm('Yakin ingin menghapus jenis bantuan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 font-medium text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">Belum ada jenis bantuan yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $jenisBantuans->links() }}</div>
    </div>
</x-dashboard-layout>
