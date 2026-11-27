<x-dashboard-layout>
    <x-slot name="title">Detail Pengaduan {{ $pengaduan->kode_tiket }}</x-slot>
    <x-slot name="breadcrumb">Pengaduan Diteruskan / {{ $pengaduan->kode_tiket }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('kades.pengaduan.index') }}" class="text-sm font-medium text-blue-700 hover:underline">← Kembali ke Pengaduan Diteruskan</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">Pengaduan {{ $pengaduan->kode_tiket }}</h1>
    </div>

    @if (session('error'))
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="max-w-3xl space-y-5">
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr><th class="w-44 bg-slate-50 px-5 py-3 font-semibold text-slate-600">Kode Tiket</th><td class="px-5 py-3 font-bold">{{ $pengaduan->kode_tiket }}</td></tr>
                    <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Kategori</th><td class="px-5 py-3">{{ \App\Models\Pengaduan::KATEGORI[$pengaduan->kategori] ?? '-' }}</td></tr>
                    <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Wilayah</th><td class="px-5 py-3">{{ $pengaduan->wilayah ? $pengaduan->wilayah->kelurahan_desa . ', Kec. ' . $pengaduan->wilayah->kecamatan : '-' }}</td></tr>
                    <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Isi Pengaduan</th><td class="whitespace-pre-line px-5 py-3">{{ $pengaduan->isi_pengaduan }}</td></tr>
                    <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Status</th><td class="px-5 py-3 font-semibold text-blue-800">{{ \App\Models\Pengaduan::STATUS[$pengaduan->status_pengaduan] ?? $pengaduan->status_pengaduan }}</td></tr>
                    <tr><th class="bg-slate-50 px-5 py-3 font-semibold text-slate-600">Tanggal Masuk</th><td class="px-5 py-3">{{ $pengaduan->tgl_pengaduan?->format('d-m-Y H:i') }}</td></tr>
                </tbody>
            </table>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900">Riwayat Tanggapan dan Arahan</h2>
            @forelse ($pengaduan->tanggapans->sortBy('waktu') as $t)
                <div class="mt-4 rounded-lg border border-slate-100 bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">
                        <span class="font-semibold text-slate-700">{{ $t->pengguna?->name ?? 'Pengguna' }}</span>
                        · {{ $t->pengguna?->role === 'kepala_desa' ? 'Kepala Desa' : 'Petugas Desa' }}
                        · {{ $t->waktu?->format('d-m-Y H:i') }}
                    </p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ $t->isi_tanggapan }}</p>
                </div>
            @empty
                <p class="mt-3 text-sm text-slate-500">Belum ada tanggapan.</p>
            @endforelse
        </section>

        @if ($pengaduan->status_pengaduan === 'eskalasi')
            <form method="POST" action="{{ route('kades.pengaduan.arahan', $pengaduan) }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                <div>
                    <label for="isi_tanggapan" class="mb-1.5 block text-sm font-semibold text-slate-700">Arahan Kepala Desa <span class="text-red-600">*</span></label>
                    <textarea id="isi_tanggapan" name="isi_tanggapan" rows="5" required minlength="10" maxlength="2000" placeholder="Tulis keputusan atau arahan untuk petugas..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">{{ old('isi_tanggapan') }}</textarea>
                    @error('isi_tanggapan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-4">
                    <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Kirim Arahan</button>
                    <a href="{{ route('kades.pengaduan.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                </div>
            </form>
        @else
            <p class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">Pengaduan ini sudah Anda beri arahan dan sedang ditindaklanjuti petugas.</p>
        @endif
    </div>
</x-dashboard-layout>
