@php $cari = request()->routeIs('pengaduan.cari'); @endphp
<nav class="mt-6 flex gap-1 border-b border-slate-200" aria-label="Menu pengaduan">
    <a href="{{ route('pengaduan.create') }}"
       class="-mb-px border-b-2 px-4 py-2.5 text-sm font-semibold {{ $cari ? 'border-transparent text-slate-500 hover:text-slate-700' : 'border-blue-700 text-blue-700' }}">
        Buat Pengaduan
    </a>
    <a href="{{ route('pengaduan.cari') }}"
       class="-mb-px border-b-2 px-4 py-2.5 text-sm font-semibold {{ $cari ? 'border-blue-700 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        Lacak Pengaduan
    </a>
</nav>
