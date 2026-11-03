```blade
<x-dashboard-layout>
    @php
        $editing = isset($penerima) && $penerima->exists;
    @endphp

    <x-slot name="title">
        {{ $editing ? 'Edit Penerima' : 'Tambah Penerima' }}
    </x-slot>

    <x-slot name="breadcrumb">
        Data Penerima / {{ $editing ? 'Edit' : 'Tambah' }}
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('penerima.index') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-blue-700 hover:text-blue-900">
            ← Kembali ke Data Penerima
        </a>

        <h1 class="mt-4 text-2xl font-bold text-slate-900">
            {{ $editing ? 'Edit Data Penerima' : 'Tambah Data Penerima' }}
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            {{ $editing
                ? 'Perbarui informasi penerima bantuan sosial.'
                : 'Lengkapi informasi untuk mendaftarkan penerima bantuan sosial baru.' }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Data belum dapat disimpan.</p>
            <p class="mt-1">Silakan periksa kembali bagian yang masih perlu diperbaiki.</p>
        </div>
    @endif

    <form method="POST"
        action="{{ $editing ? route('penerima.update', $penerima->id) : route('penerima.store') }}"
        class="max-w-3xl space-y-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">

        @csrf

        @if ($editing)
            @method('PUT')
        @endif

        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Identitas Penerima</h2>
            <p class="mt-1 text-sm text-slate-500">Masukkan identitas sesuai dokumen kependudukan.</p>
        </div>

        <div>
            <label for="nik" class="mb-2 block text-sm font-medium text-slate-700">
                Nomor Induk Kependudukan (NIK) <span class="text-red-500">*</span>
            </label>
            <input id="nik" type="text" name="nik" inputmode="numeric"
                maxlength="16" pattern="[0-9]{16}" required
                value="{{ old('nik', $penerima->nik ?? '') }}"
                placeholder="Masukkan 16 digit NIK"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            @error('nik')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="no_kk" class="mb-2 block text-sm font-medium text-slate-700">
                Nomor Kartu Keluarga
            </label>
            <input id="no_kk" type="text" name="no_kk" inputmode="numeric"
                maxlength="16"
                value="{{ old('no_kk', $penerima->no_kk ?? '') }}"
                placeholder="Masukkan 16 digit nomor KK"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            @error('no_kk')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama_lengkap" class="mb-2 block text-sm font-medium text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input id="nama_lengkap" type="text" name="nama_lengkap"
                maxlength="100" required
                value="{{ old('nama_lengkap', $penerima->nama_lengkap ?? '') }}"
                placeholder="Masukkan nama lengkap"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            @error('nama_lengkap')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tanggal_lahir" class="mb-2 block text-sm font-medium text-slate-700">
                Tanggal Lahir
            </label>
            <input id="tanggal_lahir" type="date" name="tanggal_lahir"
                value="{{ old('tanggal_lahir', isset($penerima) && $penerima->tanggal_lahir ? \Illuminate\Support\Carbon::parse($penerima->tanggal_lahir)->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

            @error('tanggal_lahir')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-b border-slate-100 pb-4 pt-2">
            <h2 class="text-base font-semibold text-slate-900">Informasi Domisili</h2>
            <p class="mt-1 text-sm text-slate-500">Masukkan alamat dan wilayah tempat tinggal penerima.</p>
        </div>

        <div>
            <label for="alamat" class="mb-2 block text-sm font-medium text-slate-700">
                Alamat Lengkap
            </label>
            <textarea id="alamat" name="alamat" rows="4"
                placeholder="Masukkan alamat lengkap penerima"
                class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('alamat', $penerima->alamat ?? '') }}</textarea>

            @error('alamat')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="id_wilayah" class="mb-2 block text-sm font-medium text-slate-700">
                Wilayah <span class="text-red-500">*</span>
            </label>
            <select id="id_wilayah" name="id_wilayah" required
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">

                <option value="">Pilih wilayah</option>

                @foreach ($wilayahOptions as $wilayah)
                    <option value="{{ $wilayah->id }}"
                        @selected((string) old('id_wilayah', $penerima->id_wilayah ?? '') === (string) $wilayah->id)>
                        {{ $wilayah->kelurahan_desa }} — {{ $wilayah->kecamatan }}
                    </option>
                @endforeach
            </select>

            @error('id_wilayah')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('penerima.index') }}"
                class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button type="submit"
                class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                {{ $editing ? 'Simpan Perubahan' : 'Tambah Penerima' }}
            </button>
        </div>
    </form>
</x-dashboard-layout>
```