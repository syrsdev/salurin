<x-dashboard-layout>
    <x-slot name="title">Hak Akses</x-slot>
    <x-slot name="breadcrumb">Hak Akses</x-slot>
    <h1 class="text-xl font-bold">Hak Akses</h1>
    <p class="mt-1 text-xs text-slate-500">Ringkasan akses berdasarkan peran. Untuk mengubah peran pengguna, gunakan Kelola Akun.</p>
    <section class="mt-5 overflow-x-auto rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3">Peran</th><th class="p-3">Menu operasional</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <tr><th scope="row" class="p-3">Petugas</th><td class="p-3">Dashboard, Data Penerima, Jenis Bantuan, Kelola Periode, Penyaluran Bantuan, Pengaduan Masuk</td></tr>
                <tr><th scope="row" class="p-3">Kepala Desa</th><td class="p-3">Dashboard, Persetujuan Data, Audit Trail, Pengaduan Diteruskan</td></tr>
                <tr><th scope="row" class="p-3">Admin</th><td class="p-3">Dashboard, Kelola Akun, Hak Akses</td></tr>
            </tbody>
        </table>
        <a href="{{ route('akun.index') }}" class="mt-5 inline-block rounded-lg bg-blue-700 px-4 py-2 text-xs font-semibold text-white">Kelola Akun</a>
    </section>
</x-dashboard-layout>
