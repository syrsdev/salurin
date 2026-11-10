
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Bantuan - {{ config('app.name', 'Salurin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    <x-public-navbar />

    <main class="mx-auto max-w-2xl px-4 py-10 sm:py-16">

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                <svg class="h-7 w-7" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2Z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">
                Cek Status Bantuan
            </h1>

            <p class="mt-3 text-sm leading-relaxed text-slate-500">
                Masukkan data kependudukan untuk melihat status dan perkembangan bantuan sosial Anda.
                Tidak perlu membuat akun.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">

            <div class="mb-6 rounded-lg border border-blue-100 bg-blue-50 p-4">
                <p class="text-sm leading-relaxed text-blue-800">
                    <strong>Informasi:</strong> Data yang dimasukkan akan digunakan
                    untuk mencocokkan identitas Anda dengan data penerima bantuan.
                    Pastikan data sesuai dokumen kependudukan.
                </p>
            </div>

            <form action="{{ route('cek-status-bantuan.cari') }}"
                method="POST" class="space-y-5">

                @csrf

                <div>
                    <label for="nik" class="mb-2 block text-sm font-semibold text-slate-700">
                        Nomor Induk Kependudukan (NIK)
                        <span class="text-red-600">*</span>
                    </label>

                    <input id="nik" name="nik" type="text"
                        inputmode="numeric" maxlength="16" pattern="[0-9]{16}"
                        value="{{ old('nik') }}" required
                        autocomplete="off"
                        placeholder="Masukkan 16 digit NIK"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('nik')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_kk" class="mb-2 block text-sm font-semibold text-slate-700">
                        Nomor Kartu Keluarga
                        <span class="text-red-600">*</span>
                    </label>

                    <input id="no_kk" name="no_kk" type="text"
                        inputmode="numeric" maxlength="16" pattern="[0-9]{16}"
                        value="{{ old('no_kk') }}" required
                        autocomplete="off"
                        placeholder="Masukkan 16 digit nomor KK"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('no_kk')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_lahir" class="mb-2 block text-sm font-semibold text-slate-700">
                        Tanggal Lahir
                        <span class="text-red-600">*</span>
                    </label>

                    <input id="tanggal_lahir" name="tanggal_lahir"
                        type="date" max="{{ now()->toDateString() }}"
                        value="{{ old('tanggal_lahir') }}" required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('tanggal_lahir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Cek Status Bantuan
                </button>

            </form>
        </div>

        <p class="mt-5 text-center text-xs leading-relaxed text-slate-500">
            Jaga kerahasiaan NIK dan Nomor KK Anda. Jangan membagikan data kependudukan kepada orang lain.
        </p>

    </main>
</body>
</html>
