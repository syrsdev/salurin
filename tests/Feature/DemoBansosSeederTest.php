<?php

use Database\Seeders\DemoBansosSeeder;
use Illuminate\Support\Facades\DB;

it('seeds multiple assistance types and recipients without duplicates', function () {
    $seeder = app(DemoBansosSeeder::class);

    $seeder->run();
    $seeder->run();

    expect(DB::table('jenis_bantuan')
        ->whereIn('nama_bantuan', [
            'Bantuan Langsung Tunai Desa',
            'Bantuan Pangan Desa',
            'Bantuan Pendidikan Desa',
        ])
        ->count())->toBe(3);

    expect(DB::table('penerima')
        ->whereIn('nik', [
            '3200000000000001',
            '3200000000000002',
            '3200000000000003',
            '3200000000000004',
            '3200000000000005',
            '3200000000000006',
        ])
        ->count())->toBe(6);

    $recipientIds = DB::table('penerima')
        ->whereIn('nik', [
            '3200000000000001',
            '3200000000000002',
            '3200000000000003',
            '3200000000000004',
            '3200000000000005',
            '3200000000000006',
        ])
        ->pluck('id');

    expect(DB::table('penyaluran')
        ->whereIn('id_penerima', $recipientIds)
        ->distinct()
        ->count('id_bantuan'))->toBe(3);
});
