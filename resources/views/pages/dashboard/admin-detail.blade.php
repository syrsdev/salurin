<x-dashboard-layout>
    <x-slot name="title">Detail Akun</x-slot>
    <x-slot name="breadcrumb">Detail Akun</x-slot>
    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kembali ke Dashboard</a>
    <section class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h1 class="text-lg font-bold">Detail Akun</h1>
        <dl class="mt-5 grid gap-5 sm:grid-cols-2">
            @foreach (['Nama'=>$account->name,'Email'=>$account->email,'Peran'=>$roles[$account->role] ?? $account->role,'Status'=>$account->aktif ? 'Aktif' : 'Nonaktif','Wilayah'=>$account->wilayah?->kelurahan_desa ?? 'Tidak ditetapkan','Tanggal dibuat'=>$account->created_at?->format('d/m/Y H:i') ?? '—'] as $label=>$value)
                <div><dt class="text-xs text-slate-500">{{ $label }}</dt><dd class="mt-1 break-words text-sm font-semibold">{{ $value }}</dd></div>
            @endforeach
        </dl>
        <a href="{{ route('akun.edit', $account) }}" class="mt-6 inline-block rounded-lg bg-blue-700 px-4 py-2 text-xs font-semibold text-white">Edit Akun</a>
    </section>
</x-dashboard-layout>
