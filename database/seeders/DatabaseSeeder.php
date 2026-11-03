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
            'kabupaten_kota' => 'Kabupaten Contoh',
            'kecamatan' => 'Kecamatan Contoh',
            'kelurahan_desa' => 'Sukamaju',
            'rw' => '01',
            'rt' => '01'
        ]);
        
        User::create([
            'name' => 'Petugas',
            'email' => 'petugas@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
            'id_wilayah' => 1
        ]);
    }
}