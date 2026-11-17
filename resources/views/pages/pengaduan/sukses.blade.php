<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaduan Terkirim - {{ config('app.name', 'Sistem Bansos') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <x-public-navbar />

    <main class="mx-auto max-w-lg px-4 py-14">
        <div class="rounded-xl border border-slate-200 bg-white p-7 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-700">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 5 5L20 7" />
                </svg>
            </div>
            <h1 class="mt-4 text-2xl font-bold">Pengaduan Berhasil Dikirim</h1>
            <p class="mt-1 text-sm text-slate-500">Simpan kode tiket di bawah untuk memantau status pengaduan Anda.</p>

            <div class="mt-6 rounded-lg border border-dashed border-blue-300 bg-blue-50 p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-700">Kode Tiket</p>
                <div class="mt-1 flex items-center justify-center gap-2">
                    <p id="kode" class="text-3xl font-bold tracking-wider text-blue-900">{{ $pengaduan->kode_tiket }}</p>
                    <button type="button" id="btn-salin" title="Salin kode" aria-label="Salin kode tiket"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-blue-700 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <svg id="ikon-salin" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="9" y="9" width="11" height="11" rx="2" />
                        <path stroke-linecap="round" d="M5 15V6a2 2 0 0 1 2-2h9" />
                    </svg>
                    <svg id="ikon-centang" class="hidden h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 5 5L20 7" />
                    </svg>
                </button>
            </div>
            <p id="pesan-salin" class="mt-1 hidden text-xs font-medium text-green-700" role="status">Kode tersalin</p>
        </div>

            <p class="mt-4 text-xs text-slate-500">Status saat ini: <strong>{{ \App\Models\Pengaduan::STATUS[$pengaduan->status_pengaduan] ?? $pengaduan->status_pengaduan }}</strong></p>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
                <a href="{{ route('pengaduan.cari', ['kode_tiket' => $pengaduan->kode_tiket]) }}" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Lacak Pengaduan Ini</a>
                <a href="{{ route('pengaduan.create') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Buat Pengaduan Lain</a>
            </div>
        </div>
    </main>

    <script>
    document.getElementById('btn-salin').addEventListener('click', async function () {
        const kode = document.getElementById('kode').innerText.trim();

        try {
            await navigator.clipboard.writeText(kode);
        } catch (e) {
            // cadangan kalau clipboard API tidak tersedia
            const t = document.createElement('textarea');
            t.value = kode;
            document.body.appendChild(t);
            t.select();
            document.execCommand('copy');
            document.body.removeChild(t);
        }

        document.getElementById('ikon-salin').classList.add('hidden');
        document.getElementById('ikon-centang').classList.remove('hidden');
        document.getElementById('pesan-salin').classList.remove('hidden');

        setTimeout(() => {
            document.getElementById('ikon-salin').classList.remove('hidden');
            document.getElementById('ikon-centang').classList.add('hidden');
            document.getElementById('pesan-salin').classList.add('hidden');
        }, 2000);
    });
</script>

</body>
</html>
