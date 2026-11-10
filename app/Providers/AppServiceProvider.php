<?php

namespace App\Providers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.dashboard.sidebar', function (ViewInstance $view): void {
            $user = Auth::user();

            $wilayahOperasional = $user instanceof User ? $user->wilayah : null;
            $pengaduanBaru = $user instanceof User && $user->role === 'petugas' && $user->id_wilayah !== null
                ? Pengaduan::query()
                    ->where('id_wilayah', $user->id_wilayah)
                    ->where('status_pengaduan', 'baru')
                    ->count()
                : 0;

            $view->with(compact('wilayahOperasional', 'pengaduanBaru'));
        });
    }
}
