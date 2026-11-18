<?php

use App\Models\Penerima;
use App\Models\Penyaluran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @return array{user: User, penyaluran: Penyaluran, nik: string, tanggal_lahir: string}
 */
function createPenyaluranStatusRecords(string $status = 'dijadwalkan'): array
{
    $now = now();
    $wilayahId = DB::table('wilayah')->insertGetId([
        'provinsi' => 'Jawa Barat',
        'kabupaten_kota' => 'Bandung',
        'kecamatan' => 'Coblong',
        'kelurahan_desa' => 'Dago',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $userId = DB::table('users')->insertGetId([
        'id_wilayah' => $wilayahId,
        'name' => 'Petugas Penyaluran',
        'email' => "petugas-{$wilayahId}@example.com",
        'password' => 'password',
        'role' => 'petugas',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $bantuanId = DB::table('jenis_bantuan')->insertGetId([
        'nama_bantuan' => 'Bantuan Uji',
        'nominal' => 300000,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $periodeId = DB::table('periode')->insertGetId([
        'nama_periode' => 'Periode Uji',
        'tgl_mulai' => '2026-01-01',
        'tgl_selesai' => '2026-12-31',
        'created_at' => $now,
        'updated_at' => $now,
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

    $penyaluran = Penyaluran::create([
        'id_penerima' => $penerima->id,
        'id_bantuan' => $bantuanId,
        'id_periode' => $periodeId,
        'id_pengguna' => $userId,
        'status_penyaluran' => $status,
    ]);

    return [
        'user' => User::findOrFail($userId),
        'penyaluran' => $penyaluran,
        'nik' => '3200000000000001',
        'tanggal_lahir' => '1979-07-25',
    ];
}

it('prevents moving a disbursement back to an earlier status', function () {
    $records = createPenyaluranStatusRecords();

    $response = $this->actingAs($records['user'])
        ->patchJson(route('api.penyaluran.status', $records['penyaluran']->id), [
            'status_penyaluran' => 'diverifikasi',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status_penyaluran']);

    expect($records['penyaluran']->fresh()->status_penyaluran)->toBe('dijadwalkan');
});

it('requires a proof photo before a disbursement can be marked as paid', function () {
    $records = createPenyaluranStatusRecords();

    $response = $this->actingAs($records['user'])
        ->patchJson(route('api.penyaluran.status', $records['penyaluran']->id), [
            'status_penyaluran' => 'cair',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'bukti_foto' => 'Bukti foto wajib diunggah saat status diubah menjadi Cair.',
        ]);

    expect($records['penyaluran']->fresh()->status_penyaluran)->toBe('dijadwalkan');
});

it('stores and shows the proof photo in the public status lookup', function () {
    Storage::fake('public');
    $records = createPenyaluranStatusRecords();

    $response = $this->actingAs($records['user'])->post(
        route('api.penyaluran.status', $records['penyaluran']->id),
        [
            '_method' => 'PATCH',
            'status_penyaluran' => 'cair',
            'bukti_foto' => UploadedFile::fake()->image('bukti.jpg'),
        ],
        ['Accept' => 'application/json'],
    );

    $response->assertOk()
        ->assertJsonPath('data.status_penyaluran', 'cair');

    $proof = $records['penyaluran']->fresh()->buktiPenyalurans()->firstOrFail();

    $response->assertJsonPath('data.bukti_penyalurans.0.url', $proof->url);
    Storage::disk('public')->assertExists($proof->path_file);
    $this->assertDatabaseHas('bukti_penyaluran', [
        'id_penyaluran' => $records['penyaluran']->id,
        'path_file' => $proof->path_file,
    ]);

    $this->post(route('cek-status-bantuan.cari'), [
        'nik' => $records['nik'],
        'tanggal_lahir' => $records['tanggal_lahir'],
    ])
        ->assertOk()
        ->assertSee('Bukti Foto Pencairan')
        ->assertSee($proof->path_file);
});

it('rejects a non-image as a disbursement proof', function () {
    Storage::fake('public');
    $records = createPenyaluranStatusRecords();

    $response = $this->actingAs($records['user'])->post(
        route('api.penyaluran.status', $records['penyaluran']->id),
        [
            '_method' => 'PATCH',
            'status_penyaluran' => 'cair',
            'bukti_foto' => UploadedFile::fake()->createWithContent('bukti.pdf', 'not an image'),
        ],
        ['Accept' => 'application/json'],
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['bukti_foto']);

    expect($records['penyaluran']->fresh()->status_penyaluran)->toBe('dijadwalkan');
    expect(Storage::disk('public')->allFiles('bukti-penyaluran'))->toBe([]);
});

it('allows editing a disbursement that is already paid without uploading another proof', function () {
    $records = createPenyaluranStatusRecords('cair');

    $this->actingAs($records['user'])
        ->patchJson(route('api.penyaluran.status', $records['penyaluran']->id), [
            'status_penyaluran' => 'cair',
            'catatan' => 'Catatan diperbarui.',
        ])
        ->assertOk();

    expect($records['penyaluran']->fresh()->catatan)->toBe('Catatan diperbarui.');
});
