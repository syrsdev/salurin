<x-dashboard-layout>
    <x-slot name="title">Detail Audit Trail</x-slot>

    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
        <div>
            <a href="{{ route('audit-trail.index') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">← Kembali ke Audit Trail</a>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Detail Riwayat Perubahan</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $tableLabel }} · ID data {{ $audit->id_record }}</p>
        </div>
        @php
            $aksiLabel = ['create' => 'Create', 'update' => 'Update', 'delete' => 'Delete'][$audit->aksi] ?? ucfirst($audit->aksi);
            $aksiClass = ['create' => 'bg-green-50 text-green-700', 'update' => 'bg-blue-50 text-blue-700', 'delete' => 'bg-red-50 text-red-700'][$audit->aksi] ?? 'bg-slate-100 text-slate-600';
        @endphp
        <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-semibold {{ $aksiClass }}">{{ $aksiLabel }}</span>
    </div>

    <section class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">Waktu perubahan</p>
            <p class="mt-1 font-semibold text-slate-800">{{ $audit->waktu?->format('d F Y') }}</p>
            <p class="text-xs text-slate-500">{{ $audit->waktu?->format('H:i:s') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">Dilakukan oleh</p>
            <p class="mt-1 font-semibold text-slate-800">{{ $audit->pengguna?->name ?? 'Pengguna tidak tersedia' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">ID Audit</p>
            <p class="mt-1 font-semibold text-slate-800">#{{ $audit->id }}</p>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                <h2 class="font-bold text-slate-800">Data Lama</h2>
                <p class="mt-0.5 text-xs text-slate-500">Kondisi sebelum perubahan</p>
            </div>
            <dl class="divide-y divide-slate-100">
                @forelse ($dataLama as $key => $value)
                    <div class="grid grid-cols-1 gap-1 px-5 py-3 sm:grid-cols-[minmax(130px,0.8fr)_1.2fr] sm:gap-4">
                        <dt class="text-xs font-semibold text-slate-500">{{ \Illuminate\Support\Str::headline($key) }}</dt>
                        <dd class="break-words text-sm text-slate-800">
                            @if (is_array($value))
                                <pre class="whitespace-pre-wrap break-words text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif (is_bool($value))
                                {{ $value ? 'Ya' : 'Tidak' }}
                            @elseif ($value === null || $value === '')
                                <span class="text-slate-400">-</span>
                            @else
                                {{ $value }}
                            @endif
                        </dd>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-400">Tidak ada data lama (aktivitas penambahan).</div>
                @endforelse
            </dl>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                <h2 class="font-bold text-slate-800">Data Baru</h2>
                <p class="mt-0.5 text-xs text-slate-500">Kondisi setelah perubahan</p>
            </div>
            <dl class="divide-y divide-slate-100">
                @forelse ($dataBaru as $key => $value)
                    <div class="grid grid-cols-1 gap-1 px-5 py-3 sm:grid-cols-[minmax(130px,0.8fr)_1.2fr] sm:gap-4">
                        <dt class="text-xs font-semibold text-slate-500">{{ \Illuminate\Support\Str::headline($key) }}</dt>
                        <dd class="break-words text-sm text-slate-800">
                            @if (is_array($value))
                                <pre class="whitespace-pre-wrap break-words text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif (is_bool($value))
                                {{ $value ? 'Ya' : 'Tidak' }}
                            @elseif ($value === null || $value === '')
                                <span class="text-slate-400">-</span>
                            @else
                                {{ $value }}
                            @endif
                        </dd>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-400">Tidak ada data baru (aktivitas penghapusan).</div>
                @endforelse
            </dl>
        </div>
    </section>

    <div class="mt-5 flex justify-end">
        <a href="{{ route('audit-trail.index') }}" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Kembali ke Daftar</a>
    </div>
</x-dashboard-layout>
