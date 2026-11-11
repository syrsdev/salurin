<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Bantuan akun</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-blue-950 sm:text-3xl">Lupa kata sandi?</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.
        </p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" class="font-semibold" value="Alamat email" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl bg-slate-50 px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="space-y-3 pt-1">
            <x-primary-button class="w-full justify-center rounded-xl py-3">
                Kirim tautan reset kata sandi
            </x-primary-button>
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12" />
                </svg>
                Kembali ke halaman masuk
            </a>
        </div>
    </form>
</x-guest-layout>
