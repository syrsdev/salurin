<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Pengaduan - {{ config('app.name', 'Sistem Bansos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="mx-auto max-w-2xl px-4 py-10">
        <h1 class="text-2xl font-bold">Pengaduan Bantuan Sosial</h1>
        <p class="mt-1 text-sm text-slate-500">Masukkan kode tiket untuk melihat status pengaduan Anda.</p>

        @include('pages.pengaduan._tabs')

        <form method="GET" action="{{ route('pengaduan.cari') }}" class="mt-6 flex flex-col gap-2 sm:flex-row">
            <label for="kode_tiket" class="sr-only">Kode tiket</label>
            <input id="kode_tiket" name="kode_tiket" value="{{ $kode }}" placeholder="Contoh: ADU-2610-001" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm uppercase focus:border-blue-600 focus:ring-blue-600">
            <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
        </form>

        @if ($kode !== '')
            @if ($pengaduan)
                <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr><th class="w-40 bg-slate-50 px-5 py-3 font-semibold text-slate-600">Kode Tiket</th><td class="px-5 py-3 font-bold">{{ $pengaduan->kode_tiket }}</td></tr>
                            <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Kategori</th><td class="px-5 py-3">{{ \App\Models\Pengaduan::KATEGORI[$pengaduan->kategori] ?? '-' }}</td></tr>
                            <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Isi Pengaduan</th><td class="whitespace-pre-line px-5 py-3">{{ $pengaduan->isi_pengaduan }}</td></tr>
                            <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Status</th><td class="px-5 py-3 font-semibold text-blue-800">{{ \App\Models\Pengaduan::STATUS[$pengaduan->status_pengaduan] ?? $pengaduan->status_pengaduan }}</td></tr>
                            <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Tanggal Masuk</th><td class="px-5 py-3">{{ $pengaduan->tgl_pengaduan?->format('d-m-Y H:i') }}</td></tr>
                            <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Tanggal Tanggapan</th><td class="px-5 py-3">{{ $pengaduan->tgl_tanggapan?->format('d-m-Y H:i') ?? 'Belum ditanggapi' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div role="alert" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">Pengaduan dengan kode tiket tersebut tidak ditemukan.</div>
            @endif
        @endif
    </main>
</body>
</html>
