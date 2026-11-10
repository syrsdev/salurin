<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

it('shows the signed-in user village and district in the sidebar', function () {
    $wilayahId = DB::table('wilayah')->insertGetId([
        'provinsi' => 'Jawa Barat',
        'kabupaten_kota' => 'Bandung',
        'kecamatan' => 'Coblong',
        'kelurahan_desa' => 'Dago',
    ]);

    $userId = DB::table('users')->insertGetId([
        'name' => 'Petugas Uji',
        'email' => 'sidebar-with-region@example.com',
        'password' => 'password',
        'role' => 'petugas',
        'id_wilayah' => $wilayahId,
    ]);

    $this->actingAs(User::query()->findOrFail($userId));

    $sidebar = view('components.dashboard.sidebar')->render();

    expect($sidebar)
        ->toContain('Desa/Kelurahan Dago')
        ->toContain('Kecamatan Coblong');
});

it('shows a fallback when the signed-in user has no assigned region', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'Admin Uji',
        'email' => 'sidebar-without-region@example.com',
        'password' => 'password',
        'role' => 'admin',
        'id_wilayah' => null,
    ]);

    $this->actingAs(User::query()->findOrFail($userId));

    $sidebar = view('components.dashboard.sidebar')->render();

    expect($sidebar)
        ->toContain('Wilayah belum diatur')
        ->not->toContain('Kecamatan');
});
