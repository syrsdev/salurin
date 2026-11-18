<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Bantuan - {{ config('app.name', 'Salurin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-slate-50 text-slate-900">

    <x-public-navbar />

    <main class="max-w-2xl px-4 py-10 mx-auto sm:py-16">

        <div class="mb-8 text-center">
            <div class="flex items-center justify-center mx-auto mb-4 text-blue-700 bg-blue-100 rounded-full h-14 w-14">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2Z" />
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

        <div class="p-5 bg-white border shadow-sm rounded-2xl border-slate-200 sm:p-8">

            <div class="p-4 mb-6 border border-blue-100 rounded-lg bg-blue-50">
                <p class="text-sm leading-relaxed text-blue-800">
                    <strong>Informasi:</strong> Data yang dimasukkan akan digunakan
                    untuk mencocokkan identitas Anda dengan data penerima bantuan.
                    Pastikan data sesuai dokumen kependudukan.
                </p>
            </div>

            <form action="{{ route('cek-status-bantuan.cari') }}" method="POST" class="space-y-5">

                @csrf

                <div>
                    <label for="nik" class="block mb-2 text-sm font-semibold text-slate-700">
                        Nomor Induk Kependudukan (NIK)
                        <span class="text-red-600">*</span>
                    </label>

                    <input id="nik" name="nik" type="text" inputmode="numeric" maxlength="16"
                        pattern="[0-9]{16}" value="{{ old('nik') }}" required autocomplete="off"
                        placeholder="Masukkan 16 digit NIK"
                        class="w-full px-4 py-3 text-sm transition border rounded-lg outline-none border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('nik')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_lahir" class="block mb-2 text-sm font-semibold text-slate-700">
                        Tanggal Lahir
                        <span class="text-red-600">*</span>
                    </label>

                    <input id="tanggal_lahir" name="tanggal_lahir" type="date" max="{{ now()->toDateString() }}"
                        value="{{ old('tanggal_lahir') }}" required
                        class="w-full px-4 py-3 text-sm transition border rounded-lg outline-none border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('tanggal_lahir')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full px-5 py-3 font-semibold text-white transition bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Cek Status Bantuan
                </button>

            </form>
        </div>

        <p class="mt-5 text-xs leading-relaxed text-center text-slate-500">
            Jaga kerahasiaan NIK Anda. Jangan membagikan data kependudukan kepada orang lain.
        </p>

    </main>
</body>

</html>
