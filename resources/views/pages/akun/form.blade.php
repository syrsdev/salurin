<x-dashboard-layout>
    @php
        $editing = $user->exists;
        $diriSendiri = $editing && $user->id === auth()->id();
    @endphp
    <x-slot name="title">{{ $editing ? 'Edit' : 'Tambah' }} Akun</x-slot>
    <x-slot name="breadcrumb">Kelola Akun / {{ $editing ? 'Edit' : 'Tambah' }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('akun.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Kembali ke Kelola Akun</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">{{ $editing ? 'Edit' : 'Tambah' }} Akun Pengguna</h1>
        <p class="mt-1 text-sm text-slate-500">Hanya akun yang terdaftar dan aktif yang dapat masuk ke sistem.</p>
    </div>

    <form method="POST" action="{{ $editing ? route('akun.update', $user->id) : route('akun.store') }}" class="max-w-3xl space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div>
            <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama <span class="text-red-600">*</span></label>
            <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email <span class="text-red-600">*</span></label>
            <input id="email" type="email" name="email" required maxlength="255" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="role" class="mb-1.5 block text-sm font-semibold text-slate-700">Peran <span class="text-red-600">*</span></label>
                <select id="role" name="role" @disabled($diriSendiri) class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600 disabled:bg-slate-100">
                    <option value="">-- Pilih peran --</option>
                    @foreach ($roles as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('role', $user->role) === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($diriSendiri)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="mt-1 text-xs text-slate-500">Anda tidak dapat mengubah peran akun sendiri.</p>
                @endif
                @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="id_wilayah" class="mb-1.5 block text-sm font-semibold text-slate-700">Wilayah</label>
                <select id="id_wilayah" name="id_wilayah" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">-- Pilih wilayah --</option>
                    @foreach ($wilayahOptions as $w)
                        <option value="{{ $w->id }}" @selected((string) old('id_wilayah', $user->id_wilayah) === (string) $w->id)>{{ $w->kelurahan_desa }}, Kec. {{ $w->kecamatan }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Wajib untuk petugas dan kepala desa.</p>
                @error('id_wilayah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password @unless ($editing) <span class="text-red-600">*</span> @endunless</label>
                <input id="password" type="password" name="password" autocomplete="new-password" @required(! $editing) class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
                @if ($editing) <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</p> @endif
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
        </div>

        <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-5">
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">{{ $editing ? 'Simpan Perubahan' : 'Tambah Akun' }}</button>
            <a href="{{ route('akun.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</x-dashboard-layout>
