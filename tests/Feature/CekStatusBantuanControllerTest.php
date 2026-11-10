<?php

use App\Models\Penerima;
use App\Models\Penyaluran;
use Illuminate\Support\Facades\DB;

/**
 * @return array{nik: string, no_kk: string, tanggal_lahir: string}
 */
function createStatusLookupRecords(): array
{
    $wilayahId = DB::table('wilayah')->insertGetId([
        'provinsi' => 'Jawa Barat',
        'kabupaten_kota' => 'Bandung',
        'kecamatan' => 'Coblong',
        'kelurahan_desa' => 'Dago',
    ]);

    $userId = DB::table('users')->insertGetId([
        'name' => 'Petugas Status',
        'email' => "status-{$wilayahId}@example.com",
        'password' => 'password',
        'role' => 'petugas',
        'id_wilayah' => $wilayahId,
    ]);

    $jenisBantuanId = DB::table('jenis_bantuan')->insertGetId([
        'nama_bantuan' => 'Bantuan Uji',
        'nominal' => 300000,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $periodeId = DB::table('periode')->insertGetId([
        'nama_periode' => 'Juni 2026',
        'tgl_mulai' => '2026-06-01',
        'tgl_selesai' => '2026-06-30',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $penerima = Penerima::create([
        'nik' => '3200000000000001',
        'no_kk' => '3200000000000002',
        'nama_lengkap' => 'Budi Santoso',
        'tanggal_lahir' => '1979-07-25',
        'status_data' => 'disetujui',
        'id_wilayah' => $wilayahId,
        'dibuat_oleh' => $userId,
    ]);

    Penyaluran::create([
        'id_penerima' => $penerima->id,
        'id_bantuan' => $jenisBantuanId,
        'id_periode' => $periodeId,
        'id_pengguna' => $userId,
        'status_penyaluran' => 'dijadwalkan',
        'tgl_penyaluran' => '2026-06-30',
    ]);

    return [
        'nik' => '3200000000000001',
        'no_kk' => '3200000000000002',
        'tanggal_lahir' => '1979-07-25',
    ];
}

it('matches a recipient using all three identity fields', function () {
    $identity = createStatusLookupRecords();

    $penerima = Penerima::query()
        ->matchingIdentity($identity['nik'], $identity['no_kk'], $identity['tanggal_lahir'])
        ->with(['wilayah', 'penyalurans.jenisBantuan', 'penyalurans.periode'])
        ->first();

    expect($penerima?->nama_lengkap)->toBe('Budi Santoso')
        ->and($penerima->wilayah->kelurahan_desa)->toBe('Dago')
        ->and($penerima->penyalurans->first()->status_penyaluran)->toBe('dijadwalkan')
        ->and($penerima->penyalurans->first()->jenisBantuan->nominal)->toBe('300000.00')
        ->and($penerima->penyalurans->first()->periode->nama_periode)->toBe('Juni 2026');
});

it('does not match a recipient when an identity field does not match', function () {
    $identity = createStatusLookupRecords();
    $identity['no_kk'] = '3200000000000099';

    $penerima = Penerima::query()
        ->matchingIdentity($identity['nik'], $identity['no_kk'], $identity['tanggal_lahir'])
        ->first();

    expect($penerima)->toBeNull();
});

it('rejects invalid identity fields without flashing them to the session', function () {
    $response = $this->post(route('cek-status-bantuan.cari'), [
        'nik' => '123',
    ]);

    $response->assertRedirect(route('cek-status-bantuan.index'))
        ->assertSessionHasErrors(['nik', 'no_kk', 'tanggal_lahir'])
        ->assertSessionHas('_old_input', fn (array $input): bool => $input === []);
});
