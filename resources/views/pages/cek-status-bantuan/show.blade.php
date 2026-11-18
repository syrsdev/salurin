<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Cek Status Bantuan - {{ config('app.name', 'Salurin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-900">

    <x-public-navbar />

    <main class="max-w-4xl px-4 py-10 mx-auto space-y-6">

        {{-- Judul halaman --}}
        <div>
            <a href="{{ route('cek-status-bantuan.index') }}" class="text-sm font-medium text-blue-700 hover:underline">
                ← Kembali ke halaman cek status
            </a>

            <h1 class="mt-4 text-2xl font-bold sm:text-3xl">
                Hasil Cek Status Bantuan
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Berikut informasi status dan perkembangan bantuan Anda.
            </p>
        </div>

        @if (!$penerima)

            {{-- Data tidak ditemukan --}}
            <div class="p-6 border border-yellow-200 rounded-xl bg-yellow-50">
                <h2 class="font-bold text-yellow-900">
                    Data Penerima Tidak Ditemukan
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-yellow-800">
                    Data yang Anda masukkan belum cocok dengan data penerima bantuan.
                    Silakan periksa kembali NIK dan tanggal lahir Anda.
                </p>

                <a href="{{ route('cek-status-bantuan.index') }}"
                    class="mt-5 inline-flex rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                    Coba Lagi
                </a>
            </div>
        @else
            {{-- Informasi penerima --}}
            <section class="p-5 bg-white border shadow-sm rounded-xl border-slate-200 sm:p-6">
                <h2 class="text-lg font-bold">Informasi Penerima</h2>

                <div class="grid gap-4 mt-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-medium uppercase text-slate-500">
                            Nama Lengkap
                        </p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $penerima->nama_lengkap }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase text-slate-500">
                            Wilayah
                        </p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $penerima->wilayah?->kelurahan_desa ?? '-' }},
                            {{ $penerima->wilayah?->kecamatan ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase text-slate-500">
                            Status Data Penerima
                        </p>

                        @php
                            $statusData = $penerima->status_data;

                            $statusDataClass = match ($statusData) {
                                'disetujui' => 'bg-green-100 text-green-700',
                                'ditolak' => 'bg-red-100 text-red-700',
                                default => 'bg-yellow-100 text-yellow-700',
                            };
                        @endphp

                        <span
                            class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusDataClass }}">
                            {{ ucfirst($statusData) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase text-slate-500">
                            Tanggal Persetujuan
                        </p>
                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $penerima->tgl_disetujui?->format('d-m-Y') ?? 'Belum disetujui' }}
                        </p>
                    </div>

                </div>
            </section>

            {{-- Informasi penyaluran --}}
            <section class="space-y-5">
                <div>
                    <h2 class="text-xl font-bold">Perkembangan Bantuan</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Pantau tahapan proses penyaluran bantuan Anda.
                    </p>
                </div>

                @forelse ($penerima->penyalurans as $penyaluran)
                    @php
                        $tahapan = [
                            'terdaftar' => 'Terdaftar',
                            'diverifikasi' => 'Diverifikasi',
                            'dijadwalkan' => 'Dijadwalkan',
                            'cair' => 'Bantuan Dicairkan',
                        ];

                        $urutanStatus = array_search($penyaluran->status_penyaluran, array_keys($tahapan), true);

                        $urutanStatus = $urutanStatus === false ? -1 : $urutanStatus;

                        $statusDikenal = $urutanStatus >= 0;
                    @endphp

                    <article class="p-5 bg-white border shadow-sm rounded-xl border-slate-200 sm:p-6">

                        <div class="flex flex-col justify-between gap-3 sm:flex-row">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">
                                    {{ $penyaluran->jenisBantuan?->nama_bantuan ?? 'Bantuan Sosial' }}
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Periode:
                                    {{ $penyaluran->periode?->nama_periode ?? '-' }}
                                </p>
                            </div>

                            <div class="sm:text-right">
                                <p class="text-xs text-slate-500">Nominal bantuan</p>
                                <p class="mt-1 font-bold text-blue-700">
                                    @if ($penyaluran->jenisBantuan?->nominal !== null)
                                        Rp{{ number_format((float) $penyaluran->jenisBantuan->nominal, 0, ',', '.') }}
                                    @else
                                        Belum tersedia
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 mt-6 border-t border-slate-100">
                            <h4 class="mb-5 font-semibold text-slate-800">
                                Timeline Penyaluran
                            </h4>

                            @if ($statusDikenal)
                                <div class="relative space-y-6">

                                    @foreach ($tahapan as $kode => $nama)
                                        @php
                                            $indexTahap = $loop->index;
                                            $selesai = $indexTahap <= $urutanStatus;
                                            $tahapAktif = $indexTahap === $urutanStatus;
                                        @endphp

                                        <div class="relative flex gap-4">

                                            @if (!$loop->last)
                                                <div
                                                    class="absolute left-[11px] top-7 h-full w-0.5 {{ $selesai ? 'bg-green-400' : 'bg-slate-200' }}">
                                                </div>
                                            @endif

                                            <div
                                                class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                                                {{ $selesai ? 'bg-green-600 text-white' : 'border-2 border-slate-300 bg-white text-slate-400' }}">

                                                @if ($selesai)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="m5 12 4 4L19 6" />
                                                    </svg>
                                                @else
                                                    <span class="text-xs">{{ $indexTahap + 1 }}</span>
                                                @endif

                                            </div>

                                            <div class="pb-2">
                                                <p
                                                    class="font-semibold {{ $selesai ? 'text-slate-900' : 'text-slate-400' }}">
                                                    {{ $nama }}
                                                </p>

                                                @if ($tahapAktif)
                                                    <span
                                                        class="mt-1 inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                        Status saat ini
                                                    </span>
                                                @elseif (!$selesai)
                                                    <p class="mt-1 text-xs text-slate-400">
                                                        Menunggu proses sebelumnya
                                                    </p>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach

                                </div>
                            @else
                                <p class="text-sm text-slate-600">
                                    Status penyaluran belum tersedia atau sedang diperbarui.
                                </p>
                            @endif
                        </div>

                        <div class="grid gap-3 pt-5 mt-5 border-t border-slate-100 sm:grid-cols-2">

                            <div>
                                <p class="text-xs text-slate-500">Tanggal penyaluran</p>
                                <p class="mt-1 text-sm font-medium">
                                    {{ $penyaluran->tgl_penyaluran?->format('d-m-Y') ?? 'Belum dijadwalkan' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">Catatan</p>
                                <p class="mt-1 text-sm font-medium whitespace-pre-line">
                                    {{ $penyaluran->catatan ?: 'Tidak ada catatan.' }}
                                </p>
                            </div>

                        </div>

                        @if ($penyaluran->status_penyaluran === 'cair')
                            <div class="pt-5 mt-5 border-t border-slate-100">
                                <h4 class="font-semibold text-slate-800">Bukti Foto Pencairan</h4>

                                @forelse ($penyaluran->buktiPenyalurans as $bukti)
                                    <a href="{{ $bukti->url }}" target="_blank" rel="noopener noreferrer"
                                        class="mt-3 inline-flex">
                                        <img src="{{ $bukti->url }}" alt="Bukti foto penyaluran bantuan"
                                            loading="lazy"
                                            class="max-h-96 rounded-lg border border-slate-200 object-contain">
                                    </a>
                                @empty
                                    <p class="mt-2 text-sm text-slate-500">Bukti foto belum tersedia.</p>
                                @endforelse
                            </div>
                        @endif

                    </article>

                @empty

                    <div class="p-6 text-center bg-white border shadow-sm rounded-xl border-slate-200">
                        <h3 class="font-semibold text-slate-800">
                            Belum Ada Data Penyaluran
                        </h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Belum terdapat informasi penyaluran bantuan untuk data penerima ini.
                        </p>
                    </div>
                @endforelse

            </section>


        @endif
        {{-- Tombol pengaduan --}}
        <section class="p-5 border border-blue-100 rounded-xl bg-blue-50 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Ada kendala dengan bantuan Anda?
            </h2>

            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                Jika terdapat masalah atau informasi yang tidak sesuai,
                Anda dapat menyampaikan pengaduan melalui layanan pengaduan Salurin.
            </p>

            <a href="{{ route('pengaduan.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 mt-5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800">
                Ajukan Pengaduan
            </a>
        </section>

    </main>

</body>

</html>
