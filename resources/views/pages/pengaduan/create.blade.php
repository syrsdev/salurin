<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Pengaduan - {{ config('app.name', 'Sistem Bansos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="mx-auto max-w-2xl px-4 py-10">
        <h1 class="text-2xl font-bold">Form Pengaduan</h1>
        <p class="mt-1 text-sm text-slate-500">Sampaikan pengaduan terkait bantuan sosial. Simpan kode tiket Anda untuk memantau status.</p>
        <a href="{{ route('pengaduan.cari') }}" class="mt-3 inline-block text-sm font-medium text-blue-700 hover:underline">Sudah punya kode tiket? Cari pengaduan</a>

        @if (session('success'))
            <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
                <p class="mt-2">Kode tiket Anda:</p>
                <p class="text-xl font-bold tracking-wide">{{ session('kode_tiket') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('pengaduan.store') }}" class="mt-6 space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            @csrf
            <div>
                <label for="kategori" class="mb-1.5 block text-sm font-semibold text-slate-700">Kategori <span class="text-red-600">*</span></label>
                <select id="kategori" name="kategori" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <option value="">-- Pilih kategori --</option>
                    @foreach (\App\Models\Pengaduan::KATEGORI as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('kategori') === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="isi_pengaduan" class="mb-1.5 block text-sm font-semibold text-slate-700">Isi Pengaduan <span class="text-red-600">*</span></label>
                <textarea id="isi_pengaduan" name="isi_pengaduan" rows="6" required maxlength="5000" placeholder="Jelaskan pengaduan Anda..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('isi_pengaduan') }}</textarea>
                @error('isi_pengaduan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="border-t border-slate-100 pt-5">
                <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Kirim Pengaduan</button>
            </div>
        </form>
    </main>
</body>
</html>
