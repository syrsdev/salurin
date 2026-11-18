<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

function createDashboardRegion(): int
{
    return DB::table('wilayah')->insertGetId([
        'provinsi' => 'Jawa Barat',
        'kabupaten_kota' => 'Bandung',
        'kecamatan' => 'Coblong',
        'kelurahan_desa' => 'Dago',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createDashboardUser(string $role, ?int $wilayahId): User
{
    $userId = DB::table('users')->insertGetId([
        'id_wilayah' => $wilayahId,
        'name' => ucfirst(str_replace('_', ' ', $role)),
        'email' => "dashboard-{$role}-".uniqid().'@example.test',
        'password' => 'password',
        'role' => $role,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::query()->findOrFail($userId);
}

function createDashboardRecipient(int $creatorId, int $wilayahId, int $number, string $status): int
{
    return DB::table('penerima')->insertGetId([
        'nik' => str_pad((string) $number, 16, '0', STR_PAD_LEFT),
        'nama_lengkap' => "Penerima {$number}",
        'status_data' => $status,
        'id_wilayah' => $wilayahId,
        'dibuat_oleh' => $creatorId,
    ]);
}

function createDashboardDistribution(int $recipientId, int $assistanceId, int $periodId, int $userId, string $status): void
{
    DB::table('penyaluran')->insert([
        'id_penerima' => $recipientId,
        'id_bantuan' => $assistanceId,
        'id_periode' => $periodId,
        'id_pengguna' => $userId,
        'status_penyaluran' => $status,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createDashboardComplaint(int $wilayahId, string $ticketCode): void
{
    DB::table('pengaduan')->insert([
        'kode_tiket' => $ticketCode,
        'id_wilayah' => $wilayahId,
        'kategori' => 'belum_cair',
        'isi_pengaduan' => 'Pengaduan uji dashboard',
        'status_pengaduan' => 'baru',
        'tgl_pengaduan' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function createDashboardAudit(int $userId, int $recipientId): void
{
    DB::table('audit_trail')->insert([
        'id_pengguna' => $userId,
        'nama_tabel' => 'penerima',
        'id_record' => $recipientId,
        'aksi' => 'create',
        'data_baru' => json_encode(['status_data' => 'menunggu']),
        'waktu' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

it('shows live petugas metrics and only data from its assigned region', function () {
    $wilayahId = createDashboardRegion();
    $otherWilayahId = createDashboardRegion();
    $petugas = createDashboardUser('petugas', $wilayahId);
    $otherPetugas = createDashboardUser('petugas', $otherWilayahId);

    $pendingRecipientId = createDashboardRecipient($petugas->id, $wilayahId, 1, 'menunggu');
    createDashboardRecipient($petugas->id, $wilayahId, 2, 'disetujui');
    createDashboardRecipient($petugas->id, $wilayahId, 3, 'ditolak');
    $otherRecipientId = createDashboardRecipient($otherPetugas->id, $otherWilayahId, 4, 'menunggu');

    $periodId = DB::table('periode')->insertGetId([
        'nama_periode' => 'Periode Aktif',
        'tgl_mulai' => today()->subDay()->toDateString(),
        'tgl_selesai' => today()->addDay()->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $assistanceId = DB::table('jenis_bantuan')->insertGetId([
        'nama_bantuan' => 'Bantuan Uji',
        'nominal' => 100000,
        'deskripsi' => 'Data uji',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    createDashboardDistribution($pendingRecipientId, $assistanceId, $periodId, $petugas->id, 'cair');
    createDashboardDistribution($pendingRecipientId, $assistanceId, $periodId, $petugas->id, 'dijadwalkan');
    createDashboardComplaint($wilayahId, 'ADU-TEST-001');
    createDashboardComplaint($otherWilayahId, 'ADU-TEST-002');
    createDashboardAudit($petugas->id, $pendingRecipientId);
    createDashboardAudit($otherPetugas->id, $otherRecipientId);

    $response = $this->actingAs($petugas)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewIs('pages.dashboard')
        ->assertViewHas('recipientTotal', 3)
        ->assertViewHas('recipientPending', 1)
        ->assertViewHas('recipientApproved', 1)
        ->assertViewHas('recipientRejected', 1)
        ->assertViewHas('distributionsTotal', 2)
        ->assertViewHas('distributionsPaid', 1)
        ->assertViewHas('distributionsPaidPercentage', 50)
        ->assertViewHas('complaintsWaiting', 1)
        ->assertViewHas('complaintsToday', 1)
        ->assertViewHas('scopeName', 'Dago')
        ->assertViewHas('recentActivities', fn ($activities): bool => $activities->contains(
            fn (array $activity): bool => str_contains($activity['detail'], 'Penerima 1')
        ) && ! $activities->contains(
            fn (array $activity): bool => str_contains($activity['detail'], 'Penerima 4')
                || str_contains($activity['detail'], 'ADU-TEST-002')
        ));
});

it('shows the kepala desa approval queue and counts only its assigned region', function () {
    $wilayahId = createDashboardRegion();
    $otherWilayahId = createDashboardRegion();
    $kepalaDesa = createDashboardUser('kepala_desa', $wilayahId);
    $petugas = createDashboardUser('petugas', $wilayahId);
    $otherPetugas = createDashboardUser('petugas', $otherWilayahId);

    createDashboardRecipient($petugas->id, $wilayahId, 11, 'menunggu');
    createDashboardRecipient($petugas->id, $wilayahId, 12, 'menunggu');
    createDashboardRecipient($petugas->id, $wilayahId, 13, 'disetujui');
    createDashboardRecipient($otherPetugas->id, $otherWilayahId, 14, 'menunggu');

    $response = $this->actingAs($kepalaDesa)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewIs('pages.dashboard.kepala-desa')
        ->assertViewHas('recipientTotal', 3)
        ->assertViewHas('recipientPending', 2)
        ->assertViewHas('recipientApproved', 1)
        ->assertViewHas('pendingRecipients', fn ($recipients): bool => $recipients->count() === 2
            && $recipients->pluck('nama_lengkap')->contains('Penerima 11')
            && ! $recipients->pluck('nama_lengkap')->contains('Penerima 14'))
        ->assertSee('Penerima 11')
        ->assertDontSee('Penerima 14');
});

it('keeps the existing global dashboard access for administrators', function () {
    $firstWilayahId = createDashboardRegion();
    $secondWilayahId = createDashboardRegion();
    $firstPetugas = createDashboardUser('petugas', $firstWilayahId);
    $secondPetugas = createDashboardUser('petugas', $secondWilayahId);
    $admin = createDashboardUser('admin', null);

    createDashboardRecipient($firstPetugas->id, $firstWilayahId, 21, 'menunggu');
    createDashboardRecipient($secondPetugas->id, $secondWilayahId, 22, 'disetujui');

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewIs('pages.dashboard')
        ->assertViewHas('recipientTotal', 2)
        ->assertViewHas('scopeName', 'Seluruh wilayah');
});
