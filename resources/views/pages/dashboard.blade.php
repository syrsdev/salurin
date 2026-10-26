<x-dashboard-layout>
    <x-slot name="title">Dashboard petugas</x-slot>

    @php
        $today = now()->locale('id');
        $activities = [
            [
                'time' => '16.20',
                'title' => 'Data penerima ditambahkan',
                'detail' => 'Siti Aminah · menunggu persetujuan kepala desa',
                'area' => 'RW 01 / RT 02',
                'actor' => 'Dewi Lestari',
                'status' => 'Menunggu',
            ],
            [
                'time' => '15.45',
                'title' => 'Penyaluran BLT Desa dicatat',
                'detail' => '24 keluarga · total bantuan Rp7.200.000',
                'area' => 'RW 02',
                'actor' => 'Dewi Lestari',
                'status' => 'Selesai',
            ],
            [
                'time' => '14.30',
                'title' => 'Data penerima disetujui',
                'detail' => 'Budi Santoso · dokumen sudah lengkap',
                'area' => 'RW 01 / RT 03',
                'actor' => 'Hendra Wijaya',
                'status' => 'Disetujui',
            ],
            [
                'time' => '14.00',
                'title' => 'Pengaduan mulai diproses',
                'detail' => 'ADU-2606-012 · konfirmasi jadwal BLT Desa',
                'area' => 'RW 01 / RT 03',
                'actor' => 'Dewi Lestari',
                'status' => 'Diproses',
            ],
            [
                'time' => '11.10',
                'title' => 'Pengajuan data ditolak',
                'detail' => 'Agus Setiawan · dokumen tidak sesuai',
                'area' => 'RW 02 / RT 03',
                'actor' => 'Hendra Wijaya',
                'status' => 'Ditolak',
            ],
            [
                'time' => '09.40',
                'title' => 'Pengaduan mulai ditindaklanjuti',
                'detail' => 'ADU-2606-009 · pengecekan bukti pencairan',
                'area' => 'RW 04 / RT 02',
                'actor' => 'Dewi Lestari',
                'status' => 'Diproses',
            ],
        ];
    @endphp

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">Dashboard petugas</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Ringkasan operasional bantuan sosial di Desa Sukamaju.</p>
        </div>
        <div class="sm:pt-1 sm:text-right">
            <p class="text-[10px] text-slate-500">Pembaruan contoh</p>
            <p class="mt-0.5 text-[11px] font-medium text-slate-700">{{ $today->translatedFormat('d F Y') }} · {{ $today->format('H.i') }} WIB</p>
        </div>
    </div>

    <section class="mt-6" aria-labelledby="monthly-summary">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 id="monthly-summary" class="text-sm font-bold text-slate-900">Ringkasan bulan ini</h2>
            <p class="flex items-center gap-1.5 text-[10px] font-medium text-slate-500">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <rect x="4" y="6" width="16" height="15" rx="2" />
                    <path stroke-linecap="round" d="M8 3v6m8-6v6M4 11h16M8 15h.01M12 15h.01M16 15h.01" />
                </svg>
                {{ $today->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-dashboard.metric-card
                title="Total Data Penerima"
                value="248"
                unit="penerima"
                detail="216 disetujui · 24 menunggu · 8 ditolak"
                note="Seluruh data desa · termasuk menunggu dan ditolak"
                tone="blue"
                icon="users"
            />
            <x-dashboard.metric-card
                title="Bantuan Tersalurkan"
                value="60"
                unit="keluarga"
                detail="80% dari 75 penerima BLT Desa periode ini"
                note="BLT Desa · {{ $today->translatedFormat('F Y') }} · hanya status cair"
                tone="green"
                icon="wallet"
            />
            <x-dashboard.metric-card
                title="Pengaduan Menunggu"
                value="11"
                unit="pengaduan"
                detail="4 pengaduan masuk hari ini"
                note="Belum ditindaklanjuti oleh petugas desa"
                tone="amber"
                icon="chat"
            />
        </div>
    </section>

    <section class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="selected-activity">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
            <div>
                <h2 id="selected-activity" class="text-sm font-bold text-slate-900">Aktivitas terpilih</h2>
                <p class="mt-1 text-[10px] text-slate-500">Pencatatan penerima, penyaluran, dan pengaduan di wilayah Anda.</p>
            </div>
            <p class="flex items-center gap-1.5 text-[10px] text-slate-500">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                </svg>
                Hari ini, {{ $today->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] border-collapse text-left">
                <thead class="bg-slate-50">
                    <tr class="text-[10px] font-semibold text-slate-500">
                        <th scope="col" class="px-4 py-3 sm:px-5">Waktu</th>
                        <th scope="col" class="px-4 py-3">Aktivitas / rincian</th>
                        <th scope="col" class="px-4 py-3">Wilayah</th>
                        <th scope="col" class="px-4 py-3">Pelaku aksi</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($activities as $activity)
                        <tr class="text-[10px] even:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 align-top sm:px-5">
                                <p class="font-semibold text-slate-800">{{ $activity['time'] }} WIB</p>
                                <p class="mt-1 text-[9px] text-slate-400">{{ $today->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="min-w-64 px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $activity['title'] }}</p>
                                <p class="mt-1 text-[9px] text-slate-500">{{ $activity['detail'] }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $activity['area'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $activity['actor'] }}</td>
                            <td class="px-4 py-3"><x-dashboard.status-badge :status="$activity['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-1.5 border-t border-slate-200 px-4 py-3 text-[9px] text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <p>6 aktivitas pilihan · Urutan terbaru terlebih dahulu</p>
            <p>Sumber: catatan simulasi desa</p>
        </div>
    </section>

    <footer class="mt-5 text-[10px] text-slate-500">
        Sistem Bansos · Desa Sukamaju · Petugas Desa/Kelurahan
    </footer>
</x-dashboard-layout>
