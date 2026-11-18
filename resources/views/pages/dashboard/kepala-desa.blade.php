<x-dashboard-layout>
    <x-slot name="title">{{ $dashboardTitle }}</x-slot>

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">{{ $dashboardTitle }}</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                Pantau pengajuan penerima dan perkembangan bantuan di {{ $scopeName }}.
            </p>
        </div>
        <div class="sm:pt-1 sm:text-right">
            <p class="text-[10px] text-slate-500">Data diperbarui</p>
            <p class="mt-0.5 text-[11px] font-medium text-slate-700">
                {{ $updatedAt->translatedFormat('d F Y') }} · {{ $updatedAt->format('H.i') }} WIB
            </p>
        </div>
    </div>

    <section class="mt-6" aria-labelledby="recipient-summary">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 id="recipient-summary" class="text-sm font-bold text-slate-900">Status data penerima</h2>
            <p class="text-[10px] font-medium text-slate-500">{{ $scopeName }}</p>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-dashboard.metric-card
                title="Total Data Penerima"
                :value="$recipientTotal"
                unit="penerima"
                detail="Seluruh status"
                note="Data penerima di {{ $scopeName }}"
                tone="blue"
                icon="users"
            />
            <x-dashboard.metric-card
                title="Menunggu Persetujuan"
                :value="$recipientPending"
                unit="pengajuan"
                detail="Perlu ditinjau"
                note="Pengajuan yang belum diputuskan"
                tone="amber"
                icon="users"
            />
            <x-dashboard.metric-card
                title="Data Disetujui"
                :value="$recipientApproved"
                unit="penerima"
                detail="Telah disetujui"
                note="Jumlah penerima yang lolos verifikasi"
                tone="green"
                icon="users"
            />
            <x-dashboard.metric-card
                title="Data Ditolak"
                :value="$recipientRejected"
                unit="pengajuan"
                detail="Tidak disetujui"
                note="Jumlah pengajuan yang ditolak"
                tone="amber"
                icon="users"
            />
        </div>
    </section>

    <section class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="pending-recipients">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
            <div>
                <h2 id="pending-recipients" class="text-sm font-bold text-slate-900">Pengajuan menunggu persetujuan</h2>
                <p class="mt-1 text-[10px] text-slate-500">Pengajuan terbaru dari wilayah {{ $scopeName }}.</p>
            </div>
            <a href="{{ route('persetujuan-penerima.page') }}"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-semibold text-white transition hover:bg-blue-700">
                Tinjau pengajuan
            </a>
        </div>

        @if ($pendingRecipients->isEmpty())
            <p class="px-5 py-8 text-center text-xs text-slate-500">Tidak ada pengajuan yang menunggu persetujuan.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($pendingRecipients as $pendingRecipient)
                    <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div>
                            <p class="text-xs font-semibold text-slate-800">{{ $pendingRecipient->nama_lengkap }}</p>
                            <p class="mt-1 text-[10px] text-slate-500">{{ $pendingRecipient->alamat ?: 'Alamat belum tersedia' }}</p>
                        </div>
                        <x-dashboard.status-badge status="Menunggu" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <x-dashboard.recent-activities
        class="mt-5"
        title="Aktivitas terbaru"
        description="Perubahan data penerima, penyaluran, dan pengaduan di wilayah Anda."
        :activities="$recentActivities"
    />

    <footer class="mt-5 text-[10px] text-slate-500">
        Sistem Bansos · {{ $scopeName }} · Kepala Desa
    </footer>
</x-dashboard-layout>
