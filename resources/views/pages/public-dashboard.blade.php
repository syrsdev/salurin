<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Publik - Transparansi Bantuan Sosial</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-[1600px] flex-col justify-between gap-3 px-4 py-3 sm:flex-row sm:items-center sm:px-6 lg:px-8">
            <a href="{{ route('transparansi') }}" class="flex items-center gap-3 text-blue-900 no-underline">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-900 text-white" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M5 9v10m4-10v10m6-10v10m4-10v10M3 19h18M12 3 3 7h18l-9-4Z"/></svg>
                </span>
                <span class="text-sm font-bold sm:text-base">Transparansi Bantuan Sosial</span>
            </a>
            <nav class="flex flex-wrap items-center gap-2 text-xs font-medium sm:gap-5 sm:text-sm" aria-label="Navigasi publik">
                <a href="{{ route('transparansi') }}" class="rounded-lg bg-blue-50 px-3 py-2 text-blue-900">Dashboard Publik</a>
                <a href="#daftar-penerima" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100">Cek Status Bantuan</a>
                <a href="#informasi-pengaduan" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100">Pengaduan</a>
                <span class="hidden border-l border-slate-200 pl-4 text-xs text-slate-500 md:inline">Layanan publik</span>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 sm:py-7 lg:px-8">
        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">Dashboard Publik</h1>
                <p class="mt-1 text-sm text-slate-500">Ringkasan penyaluran bantuan sosial di wilayah Anda.</p>
            </div>
            <p class="text-xs text-slate-500">Diperbarui {{ now()->locale('id')->translatedFormat('d F Y') }} · {{ now()->format('H.i') }} WIB</p>
        </div>

        <section class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3" aria-label="Ringkasan penyaluran">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Penerima bantuan periode ini</p>
                    <span class="rounded-lg bg-blue-50 p-2 text-blue-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path stroke-linecap="round" d="M3 19v-1a6 6 0 0 1 12 0v1M16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.5V19"/></svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">{{ number_format($totalPenerima, 0, ',', '.') }} keluarga</p>
                <p class="mt-2 text-xs text-slate-500">{{ number_format($totalCair, 0, ',', '.') }} sudah cair · {{ number_format((int) ($summary->total_dijadwalkan ?? 0), 0, ',', '.') }} dijadwalkan</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Total dana tersalurkan</p>
                    <span class="rounded-lg bg-blue-50 p-2 text-blue-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" d="M3 9h18m-5 5h2"/></svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">Rp{{ number_format((float) ($summary->total_dana ?? 0), 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-500">Akumulasi nominal penyaluran berstatus cair</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Persentase penyaluran</p>
                    <span class="rounded-lg bg-blue-50 p-2 text-blue-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 17 5-5 4 3 8-9M15 6h5v5"/><path stroke-linecap="round" d="M5 20v-2m4 2v-5m4 5v-3m4 3v-7m4 7V9"/></svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">{{ $persentase }}%</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-500" style="width: {{ $persentase }}%"></div></div>
                <p class="mt-2 text-xs text-slate-500">{{ $totalCair }} dari {{ $totalPenerima }} keluarga telah menerima bantuan</p>
            </article>
        </section>

        <form method="GET" action="{{ route('transparansi') }}" class="mt-5 grid grid-cols-1 gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
            <div>
                <label for="wilayah" class="mb-2 block text-xs font-semibold text-slate-700">Wilayah</label>
                <select id="wilayah" name="wilayah" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua desa</option>
                    @foreach ($wilayahOptions as $wilayah)
                        <option value="{{ $wilayah->id }}" @selected((string) $filters['wilayah'] === (string) $wilayah->id)>{{ $wilayah->kelurahan_desa }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bantuan" class="mb-2 block text-xs font-semibold text-slate-700">Jenis bantuan</label>
                <select id="bantuan" name="bantuan" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua jenis bantuan</option>
                    @foreach ($bantuanOptions as $bantuan)
                        <option value="{{ $bantuan->id }}" @selected((string) $filters['bantuan'] === (string) $bantuan->id)>{{ $bantuan->nama_bantuan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="periode" class="mb-2 block text-xs font-semibold text-slate-700">Periode</label>
                <select id="periode" name="periode" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua periode</option>
                    @foreach ($periodeOptions as $periode)
                        <option value="{{ $periode->id }}" @selected((string) $filters['periode'] === (string) $periode->id)>{{ $periode->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 lg:w-auto">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z"/></svg>
                    Terapkan Filter
                </button>
                <a href="{{ route('transparansi') }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <section class="mt-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="distribusi-title">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 id="distribusi-title" class="text-base font-bold">Distribusi bantuan per wilayah</h2>
                    <p class="mt-1 text-xs text-slate-500">Jumlah keluarga dengan status penyaluran cair</p>
                </div>
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>{{ number_format($totalCair, 0, ',', '.') }} keluarga sudah cair</span>
            </div>
            @php $maxDistribusi = max(1, (int) $distribusi->max('jumlah')); @endphp
            @if ($distribusi->isNotEmpty())
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($distribusi as $item)
                        <div class="flex min-w-0 flex-col items-center">
                            <div class="flex h-36 w-full items-end justify-center border-b border-slate-200 bg-slate-50/50 px-3">
                                <div class="flex w-full max-w-16 items-start justify-center rounded-t-md bg-blue-500 pt-2 text-xs font-bold text-white" style="height: {{ max(12, (int) round(($item->jumlah / $maxDistribusi) * 100)) }}%">
                                    {{ $item->jumlah }}
                                </div>
                            </div>
                            <p class="mt-2 w-full truncate text-center text-xs text-slate-500" title="{{ $item->wilayah }}">{{ $item->wilayah }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-5 rounded-lg bg-slate-50 p-8 text-center text-sm text-slate-500">Belum ada data penyaluran cair untuk filter yang dipilih.</p>
            @endif
            <div class="mt-5 flex flex-col justify-between gap-2 border-t border-slate-200 pt-4 text-xs text-slate-500 sm:flex-row">
                <p>{{ $distribusi->count() }} wilayah memiliki penyaluran cair pada filter ini.</p>
                <p class="font-semibold text-blue-900">Total dana cair: Rp{{ number_format((float) ($summary->total_dana ?? 0), 0, ',', '.') }}</p>
            </div>
        </section>

        <section id="daftar-penerima" class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="penerima-title">
            <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
                <div>
                    <h2 id="penerima-title" class="text-base font-bold">Daftar penerima bantuan</h2>
                    <p class="mt-1 text-xs text-slate-500">Nama disamarkan untuk menjaga privasi penerima bantuan.</p>
                </div>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-blue-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                    Tanpa identitas sensitif
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold text-slate-500">
                            <th scope="col" class="px-4 py-3 sm:px-5">Nama penerima</th>
                            <th scope="col" class="px-4 py-3">Wilayah</th>
                            <th scope="col" class="px-4 py-3">Jenis bantuan</th>
                            <th scope="col" class="px-4 py-3">Periode</th>
                            <th scope="col" class="px-4 py-3 text-right">Nominal bantuan</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($penerima as $row)
                            @php
                                $parts = preg_split('/\s+/', trim($row->nama_lengkap));
                                $namaSamaran = count($parts) > 1 ? $parts[0].' '.mb_substr($parts[1], 0, 1).'.' : mb_substr($parts[0] ?? 'Penerima', 0, 1).'.';
                                $statusLabel = ['cair' => 'Cair', 'dijadwalkan' => 'Dijadwalkan', 'diverifikasi' => 'Diverifikasi', 'terdaftar' => 'Terdaftar'][$row->status_penyaluran] ?? ucfirst($row->status_penyaluran);
                                $statusClass = $row->status_penyaluran === 'cair' ? 'bg-green-50 text-green-700' : ($row->status_penyaluran === 'dijadwalkan' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600');
                            @endphp
                            <tr class="text-xs even:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3.5 font-medium text-slate-800 sm:px-5">{{ $namaSamaran }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">Desa {{ $row->wilayah }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">{{ $row->nama_bantuan }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-500">{{ $row->nama_periode }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-slate-800">Rp{{ number_format((float) $row->nominal, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5"><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $statusLabel }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data penerima untuk filter yang dipilih.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p>Menampilkan {{ $penerima->firstItem() ?? 0 }}–{{ $penerima->lastItem() ?? 0 }} dari {{ $penerima->total() }} data</p>
                <div class="flex items-center gap-2">{{ $penerima->links() }}</div>
            </div>
        </section>

        <section id="informasi-pengaduan" class="mt-5 rounded-xl border border-blue-100 bg-blue-50/70 p-4 sm:p-5">
            <h2 class="text-sm font-bold text-blue-950">Informasi layanan publik</h2>
            <p class="mt-1 text-sm text-slate-600">Jika terdapat data yang tidak sesuai atau bantuan belum diterima, silakan hubungi petugas desa melalui kanal pengaduan resmi.</p>
            <p class="mt-2 text-xs text-slate-500">Dashboard ini menampilkan ringkasan publik dan menyamarkan nama penerima. Jangan tampilkan NIK, nomor KK, alamat lengkap, atau dokumen pribadi.</p>
        </section>

        <footer class="mt-6 border-t border-slate-200 py-4 text-xs text-slate-500">
            Sistem Monitoring Transparansi Bantuan Sosial
        </footer>
    </main>
</body>
</html>
