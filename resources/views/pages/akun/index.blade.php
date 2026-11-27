<x-dashboard-layout>
    <x-slot name="title">Kelola Akun</x-slot>
    <x-slot name="breadcrumb">Kelola Akun</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Akun Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">Tambah, ubah, dan nonaktifkan akun petugas dan kepala desa.</p>
        </div>
        <a href="{{ route('akun.create') }}" class="inline-flex justify-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">+ Tambah Akun</a>
    </div>

    @if (session('success'))
        <div role="status" class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('akun.index') }}" class="flex flex-col gap-2 border-b border-slate-100 p-4 lg:flex-row">
            <label class="sr-only" for="cari">Cari akun</label>
            <input id="cari" name="cari" value="{{ $cari }}" placeholder="Cari nama atau email..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600 lg:max-w-xs">
            <select name="role" aria-label="Filter peran" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Semua peran</option>
                @foreach ($roles as $nilai => $label)
                    <option value="{{ $nilai }}" @selected($role === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Filter status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Semua status</option>
                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
            </select>
            <button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
            @if ($cari !== '' || $role !== '' || $status !== '')
                <a href="{{ route('akun.index') }}" class="rounded-lg px-3 py-2 text-center text-sm text-slate-600 hover:bg-slate-100">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Peran</th>
                        <th class="px-5 py-3">Wilayah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        @php $diriSendiri = $u->id === auth()->id(); @endphp
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-800">
                                {{ $u->name }}
                                @if ($diriSendiri) <span class="ml-1 text-xs font-normal text-slate-400">(Anda)</span> @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $u->email }}</td>
                            <td class="px-5 py-4">{{ $roles[$u->role] ?? $u->role }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $u->kelurahan_desa ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @if ($u->aktif)
                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('akun.edit', $u->id) }}" class="rounded-md border border-blue-200 px-3 py-1.5 font-medium text-blue-700 hover:bg-blue-50">Edit</a>
                                    @unless ($diriSendiri)
                                        <form method="POST" action="{{ route('akun.status', $u->id) }}"
                                              onsubmit="return confirm('{{ $u->aktif ? 'Nonaktifkan akun ini? Pengguna tidak akan bisa masuk lagi.' : 'Aktifkan kembali akun ini?' }}')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-md border px-3 py-1.5 font-medium {{ $u->aktif ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-green-200 text-green-700 hover:bg-green-50' }}">
                                                {{ $u->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">Belum ada akun yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    </div>
</x-dashboard-layout>
