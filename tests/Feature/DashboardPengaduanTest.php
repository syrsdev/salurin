<?php

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @return array{user: User, wilayahId: int}
 */
function createPetugasForPengaduan(?int $wilayahId = null, string $role = 'petugas'): array
{
    if ($wilayahId === null) {
        $wilayahId = DB::table('wilayah')->insertGetId([
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Bandung',
            'kecamatan' => 'Coblong',
            'kelurahan_desa' => 'Dago',
        ]);
    }

    $userId = DB::table('users')->insertGetId([
        'name' => 'Petugas Uji',
        'email' => "pengaduan-{$role}-{$wilayahId}@example.com",
        'password' => 'password',
        'role' => $role,
        'id_wilayah' => $wilayahId,
    ]);

    return [
        'user' => User::query()->findOrFail($userId),
        'wilayahId' => $wilayahId,
    ];
}

function createPengaduanForWilayah(int $wilayahId, array $attributes = []): Pengaduan
{
    return Pengaduan::create(array_merge([
        'kode_tiket' => 'ADU-2611-'.str_pad((string) $wilayahId, 3, '0', STR_PAD_LEFT),
        'id_wilayah' => $wilayahId,
        'kategori' => 'belum_cair',
        'isi_pengaduan' => 'Bantuan saya belum cair sampai saat ini.',
        'status_pengaduan' => 'baru',
    ], $attributes));
}

it('requires a valid village when a citizen submits a complaint', function () {
    ['wilayahId' => $wilayahId] = createPetugasForPengaduan();

    $form = $this->get(route('pengaduan.create'));

    $form->assertOk()
        ->assertSee('Dago — Coblong');

    $invalidResponse = $this->from(route('pengaduan.create'))->post(route('pengaduan.store'), [
        'id_wilayah' => 999999,
        'kategori' => 'belum_cair',
        'isi_pengaduan' => 'Bantuan saya belum cair sampai hari ini.',
    ]);

    $invalidResponse->assertSessionHasErrors([
        'id_wilayah' => 'Desa/kelurahan yang dipilih tidak tersedia.',
    ]);
    $this->assertDatabaseCount('pengaduan', 0);

    $response = $this->post(route('pengaduan.store'), [
        'id_wilayah' => $wilayahId,
        'kategori' => 'belum_cair',
        'isi_pengaduan' => 'Bantuan saya belum cair sampai hari ini.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('pengaduan', [
        'id_wilayah' => $wilayahId,
        'kategori' => 'belum_cair',
    ]);
});

it('lists and opens only complaints from the signed-in petugas region', function () {
    ['user' => $petugas, 'wilayahId' => $wilayahId] = createPetugasForPengaduan();
    ['wilayahId' => $wilayahLain] = createPetugasForPengaduan();
    $pengaduanLokal = createPengaduanForWilayah($wilayahId);
    $pengaduanLain = createPengaduanForWilayah($wilayahLain, ['kode_tiket' => 'ADU-2611-999']);

    $list = $this->actingAs($petugas)->get(route('dashboard.pengaduan.index'));

    $list->assertOk()
        ->assertSee($pengaduanLokal->kode_tiket)
        ->assertDontSee($pengaduanLain->kode_tiket);

    $this->get(route('dashboard.pengaduan.show', $pengaduanLain))
        ->assertNotFound();
});

it('stores a petugas response and publishes it on the public ticket lookup', function () {
    ['user' => $petugas, 'wilayahId' => $wilayahId] = createPetugasForPengaduan();
    $pengaduan = createPengaduanForWilayah($wilayahId);

    $response = $this->actingAs($petugas)->post(route('dashboard.pengaduan.tanggapan', $pengaduan), [
        'isi_tanggapan' => "Pengaduan <script>alert('x')</script> sedang kami tindak lanjuti.",
        'status_pengaduan' => 'diproses',
    ]);

    $response->assertRedirect(route('dashboard.pengaduan.show', $pengaduan))
        ->assertSessionHas('success', 'Tanggapan berhasil dikirim.');
    $this->assertDatabaseHas('pengaduan', [
        'id' => $pengaduan->id,
        'ditangani_oleh' => $petugas->id,
        'status_pengaduan' => 'diproses',
    ]);
    $this->assertDatabaseHas('tanggapan', [
        'id_pengaduan' => $pengaduan->id,
        'id_pengguna' => $petugas->id,
        'isi_tanggapan' => "Pengaduan <script>alert('x')</script> sedang kami tindak lanjuti.",
    ]);

    $publicResponse = $this->get(route('pengaduan.cari', ['kode_tiket' => $pengaduan->kode_tiket]));

    $publicResponse
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;', false)
        ->assertDontSee("<script>alert('x')</script>", false)
        ->assertSee('Diproses');
});

it('rejects invalid replies and prevents other roles from accessing the staff queue', function () {
    ['user' => $petugas, 'wilayahId' => $wilayahId] = createPetugasForPengaduan();
    ['user' => $admin] = createPetugasForPengaduan($wilayahId, 'admin');
    $pengaduan = createPengaduanForWilayah($wilayahId);

    $response = $this->actingAs($petugas)
        ->from(route('dashboard.pengaduan.show', $pengaduan))
        ->post(route('dashboard.pengaduan.tanggapan', $pengaduan), [
            'isi_tanggapan' => 'OK',
            'status_pengaduan' => 'baru',
        ]);

    $response->assertSessionHasErrors([
        'isi_tanggapan' => 'Tanggapan minimal 5 karakter.',
        'status_pengaduan' => 'Status pengaduan tidak valid.',
    ]);
    $this->assertDatabaseCount('tanggapan', 0);

    $this->actingAs($admin)
        ->get(route('dashboard.pengaduan.index'))
        ->assertForbidden();
});

it('forbids a petugas account without an assigned region from seeing the queue', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'Petugas Tanpa Wilayah',
        'email' => 'pengaduan-no-region@example.com',
        'password' => 'password',
        'role' => 'petugas',
        'id_wilayah' => null,
    ]);

    $this->actingAs(User::query()->findOrFail($userId))
        ->get(route('dashboard.pengaduan.index'))
        ->assertForbidden();
});
