<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data uji untuk Desa Sukamaju.
 * Aman dijalankan berulang kali (tidak membuat data ganda).
 *
 * Jalankan: php artisan db:seed --class=DemoBansosSeeder
 */
class DemoBansosSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Wilayah
        DB::table('wilayah')->updateOrInsert(
            ['kelurahan_desa' => 'Sukamaju'],
            [
                'provinsi'       => 'Jawa Barat',
                'kabupaten_kota' => 'Kabupaten Contoh',
                'kecamatan'      => 'Kecamatan Contoh',
                'rw'             => '01',
                'rt'             => '01',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]
        );
        $wilayahId = DB::table('wilayah')->where('kelurahan_desa', 'Sukamaju')->value('id');

        // 2. Akun uji (password: password123)
        $akun = [
            ['name' => 'Kepala Desa Sukamaju', 'email' => 'kades@sukamaju.test',   'role' => 'kepala_desa'],
            ['name' => 'Petugas Desa Sukamaju', 'email' => 'petugas@sukamaju.test', 'role' => 'petugas'],
        ];

        foreach ($akun as $a) {
            DB::table('users')->updateOrInsert(
                ['email' => $a['email']],
                [
                    'name'       => $a['name'],
                    'password'   => Hash::make('password123'),
                    'role'       => $a['role'],
                    'id_wilayah' => $wilayahId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $kadesId    = DB::table('users')->where('email', 'kades@sukamaju.test')->value('id');
        $petugasId  = DB::table('users')->where('email', 'petugas@sukamaju.test')->value('id');

        // 3. Jenis bantuan & periode
        DB::table('jenis_bantuan')->updateOrInsert(
            ['nama_bantuan' => 'Bantuan Langsung Tunai Desa'],
            [
                'nominal'    => 300000,
                'deskripsi'  => 'Bantuan tunai untuk keluarga kurang mampu (data uji).',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $bantuanId = DB::table('jenis_bantuan')->where('nama_bantuan', 'Bantuan Langsung Tunai Desa')->value('id');

        DB::table('periode')->updateOrInsert(
            ['nama_periode' => 'Periode November 2026'],
            [
                'tgl_mulai'   => '2026-11-01',
                'tgl_selesai' => '2026-11-30',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]
        );
        $periodeId = DB::table('periode')->where('nama_periode', 'Periode November 2026')->value('id');

        // 4. Penerima (NIK/KK di bawah ini hanya data contoh)
        $penerima = [
            ['3200000000000001', 'Siti Aminah',     'Dusun 1 RT 01 RW 01', '1985-03-12', 'menunggu',  'terdaftar'],
            ['3200000000000002', 'Budi Santoso',    'Dusun 1 RT 02 RW 01', '1979-07-25', 'menunggu',  'terdaftar'],
            ['3200000000000003', 'Ratna Wulandari', 'Dusun 2 RT 01 RW 01', '1990-11-02', 'menunggu',  'terdaftar'],
            ['3200000000000004', 'Agus Prasetyo',   'Dusun 2 RT 03 RW 01', '1972-01-30', 'menunggu',  'terdaftar'],
            ['3200000000000005', 'Dewi Lestari',    'Dusun 3 RT 01 RW 01', '1988-09-17', 'disetujui', 'diverifikasi'],
            ['3200000000000006', 'Hendra Gunawan',  'Dusun 3 RT 02 RW 01', '1969-05-08', 'disetujui', 'dijadwalkan'],
        ];

        foreach ($penerima as $i => [$nik, $nama, $alamat, $lahir, $statusData, $statusSalur]) {
            $disetujui = $statusData === 'disetujui';

            DB::table('penerima')->updateOrInsert(
                ['nik' => $nik],
                [
                    'no_kk'          => '3200000000009' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'nama_lengkap'   => $nama,
                    'alamat'         => $alamat,
                    'tanggal_lahir'  => $lahir,
                    'status_data'    => $statusData,
                    'tgl_disetujui'  => $disetujui ? $now : null,
                    'id_wilayah'     => $wilayahId,
                    'dibuat_oleh'    => $petugasId,
                    'disetujui_oleh' => $disetujui ? $kadesId : null,
                ]
            );

            $penerimaId = DB::table('penerima')->where('nik', $nik)->value('id');

            // 5. Penyaluran untuk tiap penerima
            DB::table('penyaluran')->updateOrInsert(
                ['id_penerima' => $penerimaId, 'id_periode' => $periodeId],
                [
                    'id_bantuan'        => $bantuanId,
                    'id_pengguna'       => $petugasId,
                    'status_penyaluran' => $statusSalur,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]
            );
        }
    }
}
