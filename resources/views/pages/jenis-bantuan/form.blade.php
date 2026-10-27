<x-dashboard-layout>
    @php $editing = $jenisBantuan->exists; @endphp
    <x-slot name="title">{{ $editing ? 'Edit' : 'Tambah' }} Jenis Bantuan</x-slot>
    <x-slot name="breadcrumb">Jenis Bantuan / {{ $editing ? 'Edit' : 'Tambah' }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('jenis-bantuan.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Kembali ke Jenis Bantuan</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">{{ $editing ? 'Edit' : 'Tambah' }} Jenis Bantuan</h1>
        <p class="mt-1 text-sm text-slate-500">Lengkapi informasi program bantuan sosial.</p>
    </div>

    <form method="POST" action="{{ $editing ? route('jenis-bantuan.update', $jenisBantuan) : route('jenis-bantuan.store') }}" class="max-w-3xl space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div>
            <label for="nama_bantuan" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Bantuan <span class="text-red-600">*</span></label>
            <input id="nama_bantuan" type="text" name="nama_bantuan" required maxlength="100" value="{{ old('nama_bantuan', $jenisBantuan->nama_bantuan) }}" placeholder="Contoh: BLT Desa" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
            @error('nama_bantuan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="nominal" class="mb-1.5 block text-sm font-semibold text-slate-700">Nominal Bantuan (Rp)</label>
            <input id="nominal" type="number" name="nominal" min="0" max="9999999999999.99" step="0.01" value="{{ old('nominal', $jenisBantuan->nominal) }}" placeholder="Contoh: 300000" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
            @error('nominal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="5000" placeholder="Keterangan mengenai jenis bantuan..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('deskripsi', $jenisBantuan->deskripsi) }}</textarea>
            @error('deskripsi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">{{ $editing ? 'Simpan Perubahan' : 'Tambah Bantuan' }}</button>
            <a href="{{ route('jenis-bantuan.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</x-dashboard-layout>
