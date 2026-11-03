<?php

use App\Models\Penerima;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @return array{0: User, 1: int}
 */
function createPetugasWithWilayah(string $role = 'petugas'): array
{
    $wilayahId = DB::table('wilayah')->insertGetId([
        'provinsi' => 'Jawa Barat',
        'kabupaten_kota' => 'Bandung',
        'kecamatan' => 'Coblong',
        'kelurahan_desa' => 'Dago',
    ]);

    $userId = DB::table('users')->insertGetId([
        'name' => 'Petugas Uji',
        'email' => "petugas-{$role}-{$wilayahId}@example.com",
        'password' => 'password',
        'role' => $role,
        'id_wilayah' => $wilayahId,
    ]);
    $user = User::query()->findOrFail($userId);

    return [$user, $wilayahId];
}

function createPenerima(User $user, int $wilayahId, array $attributes = []): Penerima
{
    return Penerima::create(array_merge([
        'nik' => '3200000000000001',
        'no_kk' => null,
        'nama_lengkap' => 'Penerima Uji',
        'alamat' => null,
        'tanggal_lahir' => null,
        'status_data' => 'menunggu',
        'id_wilayah' => $wilayahId,
        'dibuat_oleh' => $user->id,
    ], $attributes));
}

describe('recipient CRUD', function () {
    it('shows the create form with its store endpoint', function () {
        [$petugas] = createPetugasWithWilayah();

        $response = $this->actingAs($petugas)->get(route('penerima.create'));

        $response->assertOk()
            ->assertSee('Tambah Data Penerima')
            ->assertSee(route('penerima.store'));
    });

    it('shows the edit form for a recipient in the assigned region', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($petugas, $wilayahId);

        $response = $this->actingAs($petugas)->get(route('penerima.edit', $penerima));

        $response->assertOk()
            ->assertSee('Edit Data Penerima')
            ->assertSee(route('penerima.update', $penerima))
            ->assertSee('3200000000000001');
    });

    it('stores a recipient in the petugas region with its default status', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        [$otherUser] = createPetugasWithWilayah('admin');

        $response = $this->actingAs($petugas)->post(route('penerima.store'), [
            'nik' => '3200000000000002',
            'no_kk' => '3200000000000099',
            'nama_lengkap' => 'Siti Penerima',
            'tanggal_lahir' => '1990-04-15',
            'alamat' => 'Jalan Merdeka',
            'id_wilayah' => $wilayahId,
            'status_data' => 'disetujui',
            'dibuat_oleh' => $otherUser->id,
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('penerima.index'));

        $this->assertDatabaseHas('penerima', [
            'nik' => '3200000000000002',
            'nama_lengkap' => 'Siti Penerima',
            'id_wilayah' => $wilayahId,
            'dibuat_oleh' => $petugas->id,
            'status_data' => 'menunggu',
        ]);
    });

    it('rejects a duplicate NIK when storing a recipient', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        createPenerima($petugas, $wilayahId, ['nik' => '3200000000000003']);

        $response = $this->actingAs($petugas)
            ->from(route('penerima.create'))
            ->post(route('penerima.store'), [
                'nik' => '3200000000000003',
                'nama_lengkap' => 'Duplikat',
                'id_wilayah' => $wilayahId,
            ]);

        $response->assertSessionHasErrors([
            'nik' => 'NIK sudah terdaftar.',
        ]);
        $this->assertDatabaseCount('penerima', 1);
    });

    it('rejects an NIK that is not exactly 16 digits', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();

        $response = $this->actingAs($petugas)
            ->from(route('penerima.create'))
            ->post(route('penerima.store'), [
                'nik' => '12345',
                'nama_lengkap' => 'NIK Tidak Valid',
                'id_wilayah' => $wilayahId,
            ]);

        $response->assertSessionHasErrors([
            'nik' => 'NIK harus terdiri dari 16 digit angka.',
        ]);
        $this->assertDatabaseCount('penerima', 0);
    });

    it('rejects a submitted region outside the petugas assignment', function () {
        [$petugas] = createPetugasWithWilayah();
        [, $otherWilayahId] = createPetugasWithWilayah();

        $response = $this->actingAs($petugas)
            ->from(route('penerima.create'))
            ->post(route('penerima.store'), [
                'nik' => '3200000000000004',
                'nama_lengkap' => 'Wilayah Lain',
                'id_wilayah' => $otherWilayahId,
            ]);

        $response->assertSessionHasErrors([
            'id_wilayah' => 'Wilayah harus sesuai dengan wilayah tugas Anda.',
        ]);
        $this->assertDatabaseMissing('penerima', ['nik' => '3200000000000004']);
    });

    it('updates recipient details without changing its status or creator', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($petugas, $wilayahId, [
            'status_data' => 'disetujui',
            'nik' => '3200000000000005',
        ]);

        $response = $this->actingAs($petugas)->put(route('penerima.update', $penerima), [
            'nik' => '3200000000000005',
            'nama_lengkap' => 'Nama Diperbarui',
            'id_wilayah' => $wilayahId,
            'status_data' => 'ditolak',
            'dibuat_oleh' => 999,
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('penerima.index'));

        $this->assertDatabaseHas('penerima', [
            'id' => $penerima->id,
            'nama_lengkap' => 'Nama Diperbarui',
            'status_data' => 'disetujui',
            'dibuat_oleh' => $petugas->id,
        ]);
    });

    it('rejects an update that uses another recipient NIK', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($petugas, $wilayahId, ['nik' => '3200000000000006']);
        createPenerima($petugas, $wilayahId, ['nik' => '3200000000000007']);

        $response = $this->actingAs($petugas)
            ->from(route('penerima.edit', $penerima))
            ->put(route('penerima.update', $penerima), [
                'nik' => '3200000000000007',
                'nama_lengkap' => 'Nama Tidak Tersimpan',
                'id_wilayah' => $wilayahId,
            ]);

        $response->assertSessionHasErrors([
            'nik' => 'NIK sudah terdaftar.',
        ]);
        $this->assertDatabaseHas('penerima', [
            'id' => $penerima->id,
            'nama_lengkap' => 'Penerima Uji',
            'nik' => '3200000000000006',
        ]);
    });

    it('does not expose recipients from another region for editing', function () {
        [$petugas] = createPetugasWithWilayah();
        [$otherPetugas, $otherWilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($otherPetugas, $otherWilayahId);

        $response = $this->actingAs($petugas)->get(route('penerima.edit', $penerima));

        $response->assertNotFound();
    });

    it('does not update a recipient from another region', function () {
        [$petugas] = createPetugasWithWilayah();
        [$otherPetugas, $otherWilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($otherPetugas, $otherWilayahId);

        $response = $this->actingAs($petugas)->put(route('penerima.update', $penerima), [
            'nik' => '3200000000000008',
            'nama_lengkap' => 'Tidak Boleh Diubah',
            'id_wilayah' => $otherWilayahId,
        ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('penerima', [
            'id' => $penerima->id,
            'nama_lengkap' => 'Penerima Uji',
        ]);
    });

    it('does not delete a recipient from another region', function () {
        [$petugas] = createPetugasWithWilayah();
        [$otherPetugas, $otherWilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($otherPetugas, $otherWilayahId);

        $response = $this->actingAs($petugas)->delete(route('penerima.destroy', $penerima));

        $response->assertNotFound();
        $this->assertDatabaseHas('penerima', ['id' => $penerima->id]);
    });

    it('deletes an unused recipient', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($petugas, $wilayahId);

        $response = $this->actingAs($petugas)->delete(route('penerima.destroy', $penerima));

        $response->assertRedirect(route('penerima.index'));
        $this->assertDatabaseMissing('penerima', ['id' => $penerima->id]);
    });

    it('keeps a recipient that is already used in a distribution', function () {
        [$petugas, $wilayahId] = createPetugasWithWilayah();
        $penerima = createPenerima($petugas, $wilayahId);
        $jenisBantuanId = DB::table('jenis_bantuan')->insertGetId([
            'nama_bantuan' => 'Bantuan Uji',
            'nominal' => 100000,
        ]);
        $periodeId = DB::table('periode')->insertGetId([
            'nama_periode' => 'Periode Uji',
            'tgl_mulai' => '2026-01-01',
            'tgl_selesai' => '2026-12-31',
        ]);

        DB::table('penyaluran')->insert([
            'id_penerima' => $penerima->id,
            'id_bantuan' => $jenisBantuanId,
            'id_periode' => $periodeId,
            'id_pengguna' => $petugas->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($petugas)->delete(route('penerima.destroy', $penerima));

        $response->assertRedirect(route('penerima.index'))
            ->assertSessionHas('error', 'Data penerima sudah digunakan pada penyaluran dan tidak dapat dihapus.');
        $this->assertDatabaseHas('penerima', ['id' => $penerima->id]);
    });

    it('forbids users without the petugas role from managing recipients', function () {
        [$kepalaDesa] = createPetugasWithWilayah('kepala_desa');

        $response = $this->actingAs($kepalaDesa)->get(route('penerima.create'));

        $response->assertForbidden();
    });

    it('redirects guests to login before recipient management', function () {
        $response = $this->get(route('penerima.create'));

        $response->assertRedirect(route('login'));
    });
});
