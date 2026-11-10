<x-dashboard-layout>
    <x-slot name="title">Detail Pengaduan</x-slot>
    <x-slot name="breadcrumb">Pengaduan Masuk / {{ $pengaduan->kode_tiket }}</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard.pengaduan.index') }}" class="text-sm font-medium text-blue-700 hover:underline">&larr; Kembali ke pengaduan masuk</a>
            <h1 class="mt-3 text-2xl font-bold text-slate-900">Detail Pengaduan</h1>
            <p class="mt-1 text-sm text-slate-500">Tiket {{ $pengaduan->kode_tiket }}</p>
        </div>
        <span @class([
            'inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold',
            'bg-blue-50 text-blue-700' => $pengaduan->status_pengaduan === 'baru',
            'bg-amber-50 text-amber-700' => in_array($pengaduan->status_pengaduan, ['diproses', 'eskalasi'], true),
            'bg-green-50 text-green-700' => $pengaduan->status_pengaduan === 'selesai',
        ])>{{ \App\Models\Pengaduan::STATUS[$pengaduan->status_pengaduan] ?? $pengaduan->status_pengaduan }}</span>
    </div>

    @if (session('success'))
        <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
        <section class="space-y-5">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900">Isi pengaduan</h2>
                <dl class="mt-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Kategori</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ \App\Models\Pengaduan::KATEGORI[$pengaduan->kategori] ?? 'Lainnya' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Wilayah</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $pengaduan->wilayah?->kelurahan_desa ?? 'Wilayah tidak tersedia' }}{{ $pengaduan->wilayah ? ' — '.$pengaduan->wilayah->kecamatan : '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Dikirim</dt>
                        <dd class="mt-1 text-slate-800">{{ $pengaduan->tgl_pengaduan?->format('d-m-Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Tanggapan terakhir</dt>
                        <dd class="mt-1 text-slate-800">{{ $pengaduan->tgl_tanggapan?->format('d-m-Y H:i') ?? 'Belum ada' }}</dd>
                    </div>
                </dl>
                <p class="mt-5 whitespace-pre-line rounded-lg bg-slate-50 p-4 text-sm leading-6 text-slate-700">{{ $pengaduan->isi_pengaduan }}</p>
            </article>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="history-title">
                <h2 id="history-title" class="text-base font-bold text-slate-900">Riwayat tanggapan</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($pengaduan->tanggapans as $tanggapan)
                        <article class="border-l-2 border-blue-200 pl-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800">{{ $tanggapan->pengguna?->name ?? 'Petugas' }}</p>
                                <time class="text-xs text-slate-500">{{ $tanggapan->waktu?->format('d-m-Y H:i') }}</time>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $tanggapan->isi_tanggapan }}</p>
                        </article>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada tanggapan untuk tiket ini.</p>
                    @endforelse
                </div>
            </section>
        </section>

        <section class="h-fit rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="reply-title">
            <h2 id="reply-title" class="text-base font-bold text-slate-900">Kirim tanggapan</h2>
            <p class="mt-1 text-xs text-slate-500">Tanggapan ini dapat dibaca masyarakat melalui pelacakan tiket.</p>

            @if ($errors->any())
                <div role="alert" class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <p class="font-semibold">Periksa kembali isian:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('dashboard.pengaduan.tanggapan', $pengaduan) }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="isi_tanggapan" class="mb-1.5 block text-sm font-semibold text-slate-700">Isi tanggapan</label>
                    <textarea id="isi_tanggapan" name="isi_tanggapan" rows="7" required minlength="5" maxlength="5000" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Tuliskan tindak lanjut atau informasi untuk masyarakat...">{{ old('isi_tanggapan') }}</textarea>
                    @error('isi_tanggapan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status_pengaduan" class="mb-1.5 block text-sm font-semibold text-slate-700">Status setelah ditanggapi</label>
                    <select id="status_pengaduan" name="status_pengaduan" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-600 focus:ring-blue-600">
                        @foreach (['diproses' => 'Diproses', 'eskalasi' => 'Eskalasi', 'selesai' => 'Selesai'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status_pengaduan', $pengaduan->status_pengaduan === 'baru' ? 'diproses' : $pengaduan->status_pengaduan) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_pengaduan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Kirim tanggapan</button>
            </form>
        </section>
    </div>
</x-dashboard-layout>
