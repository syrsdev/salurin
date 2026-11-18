<x-dashboard-layout>
    <x-slot name="title">{{ $dashboardTitle }}</x-slot>

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-[28px]">{{ $dashboardTitle }}</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                Ringkasan bantuan sosial untuk {{ $scopeName }}.
            </p>
        </div>
        <div class="sm:pt-1 sm:text-right">
            <p class="text-[10px] text-slate-500">Data diperbarui</p>
            <p class="mt-0.5 text-[11px] font-medium text-slate-700">
                {{ $updatedAt->translatedFormat('d F Y') }} · {{ $updatedAt->format('H.i') }} WIB
            </p>
        </div>
    </div>

    <section class="mt-6" aria-labelledby="operational-summary">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 id="operational-summary" class="text-sm font-bold text-slate-900">Ringkasan operasional</h2>
            <p class="text-[10px] font-medium text-slate-500">{{ $scopeName }}</p>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-dashboard.metric-card
                title="Total Data Penerima"
                :value="$recipientTotal"
                unit="penerima"
                :detail="$recipientApproved.' disetujui · '.$recipientPending.' menunggu · '.$recipientRejected.' ditolak'"
                note="Data penerima di {{ $scopeName }}"
                tone="blue"
                icon="users"
            />
            <x-dashboard.metric-card
                title="Bantuan Tersalurkan"
                :value="$distributionsPaid"
                unit="penyaluran"
                :detail="$distributionsPaid.' dari '.$distributionsTotal.' penyaluran telah cair'"
                :note="$activePeriodName.' · '.$distributionsPaidPercentage.'% telah cair'"
                tone="green"
                icon="wallet"
            />
            <x-dashboard.metric-card
                title="Pengaduan Menunggu"
                :value="$complaintsWaiting"
                unit="pengaduan"
                :detail="$complaintsToday.' pengaduan masuk hari ini'"
                note="Pengaduan berstatus baru di wilayah ini"
                tone="amber"
                icon="chat"
            />
        </div>
    </section>

    <x-dashboard.recent-activities
        class="mt-5"
        title="Aktivitas terbaru"
        description="Perubahan penerima, penyaluran, dan pengaduan di wilayah operasional."
        :activities="$recentActivities"
    />

    <footer class="mt-5 text-[10px] text-slate-500">
        Sistem Bansos · {{ $scopeName }} · {{ ucfirst(str_replace('_', ' ', $dashboardRole)) }}
    </footer>
</x-dashboard-layout>
