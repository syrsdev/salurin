<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Wilayah::create([
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Sumedang',
            'kecamatan' => 'Sukabiru',
            'kelurahan_desa' => 'Sukamaju',
            'rw' => '01',
            'rt' => '01'
        ]);
        Wilayah::create([
            'provinsi' => 'Jawa Timur',
            'kabupaten_kota' => 'Gresik',
            'kecamatan' => 'Benjeng',
            'kelurahan_desa' => 'Karangturi',
            'rw' => '01',
            'rt' => '04'
        ]);

        User::create([
            'name' => 'Petugas',
            'email' => 'petugas@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
            'id_wilayah' => 1
        ]);
        User::create([
            'name' => 'Petugas',
            'email' => 'petugas2@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
            'id_wilayah' => 2
        ]);

        User::create([
            'name' => 'Kepala Desa',
            'email' => 'kades@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_desa',
            'id_wilayah' => 1
        ]);
        User::create([
            'name' => 'Kepala Desa 2',
            'email' => 'kades2@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'kepala_desa',
            'id_wilayah' => 2
        ]);
    }
}