<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Akun Salurin</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-blue-950 sm:text-3xl">Masuk ke akun</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Silakan masuk untuk melanjutkan ke layanan bantuan sosial.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" class="font-semibold" value="Alamat email" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl bg-slate-50 px-4 py-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" class="font-semibold" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full rounded-xl bg-slate-50 px-4 py-3" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-700 shadow-sm focus:ring-blue-600" name="remember">
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-blue-800 transition hover:text-blue-950 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full justify-center rounded-xl py-3">
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
