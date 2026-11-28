<x-dashboard-layout>
    <x-slot name="title">Dashboard Admin</x-slot>
    <h1 class="text-xl font-bold text-slate-900">Dashboard Admin</h1>
    <p class="mt-1 text-xs text-slate-500">Ringkasan akun pengguna sistem di seluruh wilayah.</p>
    <section class="mt-6" aria-labelledby="account-summary">
        <h2 id="account-summary" class="mb-3 text-sm font-bold">Jumlah akun per peran</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($roles as $role => $label)
                <x-dashboard.metric-card :title="$label" :value="(int) $counts->get($role, 0)" unit="akun"
                    :detail="$active->get($role, 0).' aktif · '.($counts->get($role, 0) - $active->get($role, 0)).' nonaktif'"
                    note="Seluruh wilayah" :tone="['petugas'=>'blue','kepala_desa'=>'green','admin'=>'amber'][$role]" icon="users" />
            @endforeach
        </div>
    </section>
    <section class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-4">
            <div><h2 class="text-sm font-bold">Akun terbaru</h2><p class="mt-1 text-xs text-slate-500">Lima akun terakhir yang dibuat.</p></div>
            <a href="{{ route('akun.index') }}" class="rounded-lg bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Kelola Akun</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600"><tr>
                    @foreach (['Nama / Email','Peran','Status','Tanggal dibuat','Aksi'] as $heading)
                        <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">{{ $heading }}</th>
                    @endforeach
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($accounts as $account)
                        <tr><td class="px-4 py-3"><p class="font-semibold">{{ $account->name }}</p><p class="mt-1 text-slate-500">{{ $account->email }}</p></td>
                            <td class="px-4 py-3">{{ $roles[$account->role] ?? $account->role }}</td>
                            <td class="px-4 py-3"><span @class(['rounded-full px-2 py-1 font-semibold','bg-emerald-50 text-emerald-700'=>$account->aktif,'bg-slate-100 text-slate-600'=>!$account->aktif])>{{ $account->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $account->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('admin.dashboard.accounts.show', $account) }}" class="font-semibold text-blue-700 hover:underline">Detail</a></td></tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-500">Belum ada akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-dashboard-layout>
