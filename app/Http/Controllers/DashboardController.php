<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Penerima;
use App\Models\Pengaduan;
use App\Models\Penyaluran;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless(
            $user instanceof User && in_array($user->role, ['petugas', 'kepala_desa', 'admin'], true),
            403
        );

        $wilayahId = $user->role === 'admin'
            ? null
            : ($user->id_wilayah === null ? null : (int) $user->id_wilayah);
        abort_if($user->role !== 'admin' && $wilayahId === null, 403, 'Akun belum memiliki wilayah tugas.');

        $wilayah = $wilayahId === null
            ? null
            : DB::table('wilayah')->where('id', $wilayahId)->first();
        abort_if($wilayahId !== null && $wilayah === null, 403, 'Wilayah tugas tidak ditemukan.');

        $scopeName = $wilayah === null
            ? 'Seluruh wilayah'
            : implode(' · ', array_filter([
                $wilayah->kelurahan_desa,
                $wilayah->rw ? 'RW '.$wilayah->rw : null,
                $wilayah->rt ? 'RT '.$wilayah->rt : null,
            ]));
        $recipientStatusCounts = DB::table('penerima')
            ->when($wilayahId !== null, fn ($query) => $query->where('id_wilayah', $wilayahId))
            ->select('status_data')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('status_data')
            ->pluck('total', 'status_data')
            ->map(fn ($total): int => (int) $total);

        $totalRecipients = $recipientStatusCounts->sum();
        $pendingRecipientsCount = $recipientStatusCounts->get('menunggu', 0);
        $approvedRecipientsCount = $recipientStatusCounts->get('disetujui', 0);
        $rejectedRecipientsCount = $recipientStatusCounts->get('ditolak', 0);

        $today = today()->toDateString();
        $activePeriod = Periode::query()
            ->whereDate('tgl_mulai', '<=', $today)
            ->whereDate('tgl_selesai', '>=', $today)
            ->orderByDesc('tgl_mulai')
            ->first();

        $distributionQuery = Penyaluran::query()
            ->when($wilayahId !== null, function ($query) use ($wilayahId): void {
                $query->whereHas('penerima', function ($recipientQuery) use ($wilayahId): void {
                    $recipientQuery->where('id_wilayah', $wilayahId);
                });
            });
        $totalDistributions = $activePeriod === null
            ? 0
            : (clone $distributionQuery)->where('id_periode', $activePeriod->id)->count();
        $paidDistributions = $activePeriod === null
            ? 0
            : (clone $distributionQuery)
                ->where('id_periode', $activePeriod->id)
                ->where('status_penyaluran', 'cair')
                ->count();

        $complaintQuery = Pengaduan::query()
            ->when($wilayahId !== null, fn ($query) => $query->where('id_wilayah', $wilayahId));
        $waitingComplaints = (clone $complaintQuery)
            ->where('status_pengaduan', 'baru')
            ->count();
        $complaintsToday = (clone $complaintQuery)
            ->whereDate('tgl_pengaduan', $today)
            ->count();

        $data = [
            'dashboardTitle' => match ($user->role) {
                'kepala_desa' => 'Dashboard Kepala Desa',
                'admin' => 'Dashboard Administrator',
                default => 'Dashboard Petugas',
            },
            'dashboardRole' => $user->role,
            'scopeName' => $scopeName,
            'updatedAt' => now()->locale('id'),
            'recipientTotal' => $totalRecipients,
            'recipientPending' => $pendingRecipientsCount,
            'recipientApproved' => $approvedRecipientsCount,
            'recipientRejected' => $rejectedRecipientsCount,
            'distributionsTotal' => $totalDistributions,
            'distributionsPaid' => $paidDistributions,
            'distributionsPaidPercentage' => $totalDistributions === 0
                ? 0
                : (int) round($paidDistributions / $totalDistributions * 100),
            'activePeriodName' => $activePeriod?->nama_periode ?? 'Tidak ada periode aktif',
            'complaintsWaiting' => $waitingComplaints,
            'complaintsToday' => $complaintsToday,
            'recentActivities' => $this->recentActivities($wilayahId, $scopeName),
        ];

        if ($user->role === 'kepala_desa') {
            $data['pendingRecipients'] = Penerima::query()
                ->where('id_wilayah', $wilayahId)
                ->where('status_data', 'menunggu')
                ->orderByDesc('id')
                ->limit(5)
                ->get(['id', 'nama_lengkap', 'alamat']);

            return view('pages.dashboard.kepala-desa', $data);
        }

        return view('pages.dashboard', $data);
    }

    /**
     * @return Collection<int, array{waktu: mixed, title: string, detail: string, area: string, actor: string, status: string}>
     */
    private function recentActivities(?int $wilayahId, string $scopeName): Collection
    {
        $recipientIds = DB::table('penerima')->select('id');
        $distributionIds = DB::table('penyaluran')
            ->join('penerima', 'penyaluran.id_penerima', '=', 'penerima.id')
            ->select('penyaluran.id');

        if ($wilayahId !== null) {
            $recipientIds->where('id_wilayah', $wilayahId);
            $distributionIds->where('penerima.id_wilayah', $wilayahId);
        }

        $audits = AuditTrail::query()
            ->with('pengguna:id,name')
            ->whereIn('nama_tabel', ['penerima', 'penyaluran'])
            ->where(function ($query) use ($recipientIds, $distributionIds): void {
                $query->where(function ($recipientQuery) use ($recipientIds): void {
                    $recipientQuery->where('nama_tabel', 'penerima')
                        ->whereIn('id_record', $recipientIds);
                })->orWhere(function ($distributionQuery) use ($distributionIds): void {
                    $distributionQuery->where('nama_tabel', 'penyaluran')
                        ->whereIn('id_record', $distributionIds);
                });
            })
            ->select('id', 'id_pengguna', 'nama_tabel', 'id_record', 'aksi', 'data_baru', 'waktu')
            ->orderByDesc('waktu')
            ->limit(12)
            ->get();

        $recipientNames = DB::table('penerima')
            ->whereIn('id', $audits->where('nama_tabel', 'penerima')->pluck('id_record')->unique())
            ->pluck('nama_lengkap', 'id');
        $distributions = Penyaluran::query()
            ->with(['penerima:id,nama_lengkap', 'jenisBantuan:id,nama_bantuan'])
            ->whereIn('id', $audits->where('nama_tabel', 'penyaluran')->pluck('id_record')->unique())
            ->get()
            ->keyBy('id');

        $auditActivities = $audits->map(function (AuditTrail $audit) use ($recipientNames, $distributions, $scopeName): array {
            $newData = $audit->data_baru ?? [];

            if ($audit->nama_tabel === 'penerima') {
                $status = [
                    'menunggu' => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                ][$newData['status_data'] ?? ''] ?? 'Diperbarui';
                $name = $recipientNames->get($audit->id_record, 'Penerima');

                return [
                    'waktu' => $audit->waktu,
                    'title' => $audit->aksi === 'create' ? 'Data penerima ditambahkan' : 'Data penerima diperbarui',
                    'detail' => "{$name} · {$status}",
                    'area' => $scopeName,
                    'actor' => $audit->pengguna?->name ?? 'Sistem',
                    'status' => $status,
                ];
            }

            $distribution = $distributions->get($audit->id_record);
            $status = Penyaluran::STATUS[$newData['status_penyaluran'] ?? ''] ?? 'Diperbarui';
            $detail = implode(' · ', array_filter([
                $distribution?->penerima?->nama_lengkap,
                $distribution?->jenisBantuan?->nama_bantuan,
                $status,
            ]));

            return [
                'waktu' => $audit->waktu,
                'title' => $audit->aksi === 'create' ? 'Penyaluran dicatat' : 'Status penyaluran diperbarui',
                'detail' => $detail !== '' ? $detail : 'Perubahan penyaluran dicatat',
                'area' => $scopeName,
                'actor' => $audit->pengguna?->name ?? 'Sistem',
                'status' => $status,
            ];
        });

        $complaintActivities = Pengaduan::query()
            ->with('wilayah:id,kelurahan_desa')
            ->when($wilayahId !== null, fn ($query) => $query->where('id_wilayah', $wilayahId))
            ->orderByDesc('tgl_pengaduan')
            ->limit(6)
            ->get(['id', 'kode_tiket', 'id_wilayah', 'kategori', 'status_pengaduan', 'tgl_pengaduan'])
            ->map(fn (Pengaduan $complaint): array => [
                'waktu' => $complaint->tgl_pengaduan,
                'title' => 'Pengaduan masuk',
                'detail' => $complaint->kode_tiket.' · '.(Pengaduan::KATEGORI[$complaint->kategori] ?? 'Pengaduan'),
                'area' => $complaint->wilayah?->kelurahan_desa ?? $scopeName,
                'actor' => 'Masyarakat',
                'status' => Pengaduan::STATUS[$complaint->status_pengaduan] ?? 'Baru',
            ]);

        return $auditActivities
            ->concat($complaintActivities)
            ->sortByDesc('waktu')
            ->take(6)
            ->values();
    }
}
