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

<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-900">
    <x-public-navbar />

    <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 sm:py-7 lg:px-8">
        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">Dashboard Publik</h1>
                <p class="mt-1 text-sm text-slate-500">Ringkasan penyaluran bantuan sosial di wilayah Anda.</p>
            </div>
            <p class="text-xs text-slate-500">Diperbarui {{ now()->locale('id')->translatedFormat('d F Y') }} ·
                {{ now()->format('H.i') }} WIB</p>
        </div>

        <section class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-3" aria-label="Ringkasan penyaluran">
            <article class="p-5 bg-white border shadow-sm rounded-xl border-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Penerima bantuan periode ini</p>
                    <span class="p-2 text-blue-600 rounded-lg bg-blue-50"><svg class="w-5 h-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="9" cy="8" r="3" />
                            <path stroke-linecap="round"
                                d="M3 19v-1a6 6 0 0 1 12 0v1M16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.5V19" />
                        </svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">{{ number_format($totalPenerima, 0, ',', '.') }}
                    keluarga</p>
                <p class="mt-2 text-xs text-slate-500">{{ number_format($totalCair, 0, ',', '.') }} sudah cair ·
                    {{ number_format((int) ($summary->total_dijadwalkan ?? 0), 0, ',', '.') }} dijadwalkan</p>
            </article>
            <article class="p-5 bg-white border shadow-sm rounded-xl border-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Total dana tersalurkan</p>
                    <span class="p-2 text-blue-600 rounded-lg bg-blue-50"><svg class="w-5 h-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path stroke-linecap="round" d="M3 9h18m-5 5h2" />
                        </svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">
                    Rp{{ number_format((float) ($summary->total_dana ?? 0), 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-slate-500">Akumulasi nominal penyaluran berstatus cair</p>
            </article>
            <article class="p-5 bg-white border shadow-sm rounded-xl border-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Persentase penyaluran</p>
                    <span class="p-2 text-blue-600 rounded-lg bg-blue-50"><svg class="w-5 h-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 17 5-5 4 3 8-9M15 6h5v5" />
                            <path stroke-linecap="round" d="M5 20v-2m4 2v-5m4 5v-3m4 3v-7m4 7V9" />
                        </svg></span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight">{{ $persentase }}%</p>
                <div class="h-2 mt-3 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $persentase }}%"></div>
                </div>
                <p class="mt-2 text-xs text-slate-500">{{ $totalCair }} dari {{ $totalPenerima }} keluarga telah
                    menerima bantuan</p>
            </article>
        </section>

        <form method="GET" action="{{ route('transparansi') }}"
            class="mt-5 grid grid-cols-1 gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
            <div>
                <label for="wilayah" class="block mb-2 text-xs font-semibold text-slate-700">Wilayah</label>
                <select id="wilayah" name="wilayah"
                    class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua desa</option>
                    @foreach ($wilayahOptions as $wilayah)
                        <option value="{{ $wilayah->id }}" @selected((string) $filters['wilayah'] === (string) $wilayah->id)>
                            {{ $wilayah->kelurahan_desa }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bantuan" class="block mb-2 text-xs font-semibold text-slate-700">Jenis bantuan</label>
                <select id="bantuan" name="bantuan"
                    class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua jenis bantuan</option>
                    @foreach ($bantuanOptions as $bantuan)
                        <option value="{{ $bantuan->id }}" @selected((string) $filters['bantuan'] === (string) $bantuan->id)>{{ $bantuan->nama_bantuan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="periode" class="block mb-2 text-xs font-semibold text-slate-700">Periode</label>
                <select id="periode" name="periode"
                    class="w-full text-sm rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua periode</option>
                    @foreach ($periodeOptions as $periode)
                        <option value="{{ $periode->id }}" @selected((string) $filters['periode'] === (string) $periode->id)>{{ $periode->nama_periode }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg min-h-10 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 lg:w-auto">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5Z" />
                    </svg>
                    Terapkan Filter
                </button>
                <a href="{{ route('transparansi') }}"
                    class="inline-flex items-center justify-center px-3 text-sm border rounded-lg min-h-10 border-slate-200 text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <section class="p-4 mt-5 bg-white border shadow-sm rounded-xl border-slate-200 sm:p-5"
            aria-labelledby="distribusi-title">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h2 id="distribusi-title" class="text-base font-bold">Distribusi bantuan per wilayah</h2>
                    <p class="mt-1 text-xs text-slate-500">Jumlah keluarga dengan status penyaluran cair</p>
                </div>
                <span
                    class="inline-flex w-fit items-center gap-2 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700"><span
                        class="h-1.5 w-1.5 rounded-full bg-green-600"></span>{{ number_format($totalCair, 0, ',', '.') }}
                    keluarga sudah cair</span>
            </div>
            @php $maxDistribusi = max(1, (int) $distribusi->max('jumlah')); @endphp
            @if ($distribusi->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 mt-5 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($distribusi as $item)
                        <div class="flex flex-col items-center min-w-0">
                            <div
                                class="flex items-end justify-center w-full px-3 border-b h-36 border-slate-200 bg-slate-50/50">
                                <div class="flex items-start justify-center w-full pt-2 text-xs font-bold text-white bg-blue-500 max-w-16 rounded-t-md"
                                    style="height: {{ max(12, (int) round(($item->jumlah / $maxDistribusi) * 100)) }}%">
                                    {{ $item->jumlah }}
                                </div>
                            </div>
                            <p class="w-full mt-2 text-xs text-center truncate text-slate-500"
                                title="{{ $item->wilayah }}">{{ $item->wilayah }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="p-8 mt-5 text-sm text-center rounded-lg bg-slate-50 text-slate-500">Belum ada data penyaluran
                    cair untuk filter yang dipilih.</p>
            @endif
            <div
                class="flex flex-col justify-between gap-2 pt-4 mt-5 text-xs border-t border-slate-200 text-slate-500 sm:flex-row">
                <p>{{ $distribusi->count() }} wilayah memiliki penyaluran cair pada filter ini.</p>
                <p class="font-semibold text-blue-900">Total dana cair:
                    Rp{{ number_format((float) ($summary->total_dana ?? 0), 0, ',', '.') }}</p>
            </div>
        </section>

        <section id="daftar-penerima"
            class="mt-5 overflow-hidden bg-white border shadow-sm rounded-xl border-slate-200"
            aria-labelledby="penerima-title">
            <div
                class="flex flex-col justify-between gap-3 px-4 py-4 border-b border-slate-200 sm:flex-row sm:items-center sm:px-5">
                <div>
                    <h2 id="penerima-title" class="text-base font-bold">Daftar penerima bantuan</h2>
                    <p class="mt-1 text-xs text-slate-500">Nama disamarkan untuk menjaga privasi penerima bantuan.</p>
                </div>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-blue-800">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
                    </svg>
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
                                $namaSamaran =
                                    count($parts) > 1
                                        ? $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.'
                                        : mb_substr($parts[0] ?? 'Penerima', 0, 1) . '.';
                                $statusLabel =
                                    [
                                        'cair' => 'Cair',
                                        'dijadwalkan' => 'Dijadwalkan',
                                        'diverifikasi' => 'Diverifikasi',
                                        'terdaftar' => 'Terdaftar',
                                    ][$row->status_penyaluran] ?? ucfirst($row->status_penyaluran);
                                $statusClass =
                                    $row->status_penyaluran === 'cair'
                                        ? 'bg-green-50 text-green-700'
                                        : ($row->status_penyaluran === 'dijadwalkan'
                                            ? 'bg-amber-50 text-amber-700'
                                            : 'bg-slate-100 text-slate-600');
                            @endphp
                            <tr class="text-xs even:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3.5 font-medium text-slate-800 sm:px-5">
                                    {{ $namaSamaran }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">Desa {{ $row->wilayah }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">{{ $row->nama_bantuan }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-500">{{ $row->nama_periode }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-right text-slate-800">
                                    Rp{{ number_format((float) $row->nominal, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5"><span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}"><span
                                            class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-sm text-center text-slate-500">Belum ada
                                    data penerima untuk filter yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="flex flex-col gap-3 px-4 py-3 text-xs border-t border-slate-200 text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p>Menampilkan {{ $penerima->firstItem() ?? 0 }}–{{ $penerima->lastItem() ?? 0 }} dari
                    {{ $penerima->total() }} data</p>
                <div class="flex items-center gap-2">{{ $penerima->links() }}</div>
            </div>
        </section>

        <section id="informasi-pengaduan" class="p-4 mt-5 border border-blue-100 rounded-xl bg-blue-50/70 sm:p-5">
            <h2 class="text-sm font-bold text-blue-950">Informasi layanan publik</h2>
            <p class="mt-1 text-sm text-slate-600">Jika terdapat data yang tidak sesuai atau bantuan belum diterima,
                silakan hubungi petugas desa melalui kanal pengaduan resmi.</p>
            <p class="mt-2 text-xs text-slate-500">Dashboard ini menampilkan ringkasan publik dan menyamarkan nama
                penerima. Jangan tampilkan NIK, nomor KK, alamat lengkap, atau dokumen pribadi.</p>
        </section>

        <footer class="py-4 mt-6 text-xs border-t border-slate-200 text-slate-500">
            Sistem Monitoring Transparansi Bantuan Sosial
        </footer>
    </main>
</body>

</html>
