<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuditTrailController extends Controller
{
    private const TABLE_LABELS = [
        'penerima' => 'Data Penerima',
        'penyaluran' => 'Penyaluran Bantuan',
        'pengaduan' => 'Pengaduan',
        'tanggapan' => 'Tanggapan Pengaduan',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'kepala_desa', 403);
        abort_if($user->id_wilayah === null, 403, 'Akun Kepala Desa belum memiliki wilayah tugas.');

        $filters = $request->validate([
            'aksi' => ['nullable', 'in:create,update,delete'],
            'tabel' => ['nullable', 'in:penerima,penyaluran,pengaduan,tanggapan'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date'],
            'cari' => ['nullable', 'string', 'max:100'],
        ]);

        if (! empty($filters['dari']) && ! empty($filters['sampai'])
            && $filters['sampai'] < $filters['dari']) {
            throw ValidationException::withMessages([
                'sampai' => 'Tanggal sampai tidak boleh lebih awal dari tanggal mulai.',
            ]);
        }

        $wilayahId = (int) $user->id_wilayah;
        $penerimaIds = DB::table('penerima')
            ->where('id_wilayah', $wilayahId)
            ->select('id');
        $penyaluranIds = DB::table('penyaluran')
            ->join('penerima', 'penyaluran.id_penerima', '=', 'penerima.id')
            ->where('penerima.id_wilayah', $wilayahId)
            ->select('penyaluran.id');
        $pengaduanIds = DB::table('pengaduan')
            ->where('id_wilayah', $wilayahId)
            ->select('id');
        $tanggapanIds = DB::table('tanggapan')
            ->join('pengaduan', 'tanggapan.id_pengaduan', '=', 'pengaduan.id')
            ->where('pengaduan.id_wilayah', $wilayahId)
            ->select('tanggapan.id');

        $query = AuditTrail::query()
            ->with('pengguna:id,name')
            ->where(function (Builder $scope) use ($penerimaIds, $penyaluranIds, $pengaduanIds, $tanggapanIds, $wilayahId): void {
                $scope->where(fn (Builder $q) => $q->where('nama_tabel', 'penerima')
                    ->where(fn (Builder $recipient) => $recipient->whereIn('id_record', $penerimaIds)
                        ->orWhere('data_lama->id_wilayah', $wilayahId)))
                    ->orWhere(fn (Builder $q) => $q->where('nama_tabel', 'penyaluran')->whereIn('id_record', $penyaluranIds))
                    ->orWhere(fn (Builder $q) => $q->where('nama_tabel', 'pengaduan')->whereIn('id_record', $pengaduanIds))
                    ->orWhere(fn (Builder $q) => $q->where('nama_tabel', 'tanggapan')->whereIn('id_record', $tanggapanIds));
            })
            ->when(! empty($filters['aksi']), fn (Builder $q) => $q->where('aksi', $filters['aksi']))
            ->when(! empty($filters['tabel']), fn (Builder $q) => $q->where('nama_tabel', $filters['tabel']))
            ->when(! empty($filters['dari']), fn (Builder $q) => $q->whereDate('waktu', '>=', $filters['dari']))
            ->when(! empty($filters['sampai']), fn (Builder $q) => $q->whereDate('waktu', '<=', $filters['sampai']))
            ->when(! empty($filters['cari']), function (Builder $q) use ($filters): void {
                $cari = trim($filters['cari']);
                $q->where(function (Builder $search) use ($cari): void {
                    $search->where('nama_tabel', 'like', "%{$cari}%")
                        ->orWhere('id_record', 'like', "%{$cari}%")
                        ->orWhereHas('pengguna', fn (Builder $actor) => $actor->where('name', 'like', "%{$cari}%"));
                });
            });

        $audits = $query->orderByDesc('waktu')->orderByDesc('id')
            ->paginate(10)->withQueryString();

        $summaryQuery = (clone $query);
        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'create' => (clone $summaryQuery)->where('aksi', 'create')->count(),
            'update' => (clone $summaryQuery)->where('aksi', 'update')->count(),
            'delete' => (clone $summaryQuery)->where('aksi', 'delete')->count(),
        ];

        $wilayah = DB::table('wilayah')->where('id', $wilayahId)->first();
        $scopeName = $wilayah?->kelurahan_desa ?? 'Wilayah tugas';

        return view('pages.audit-trail.index', [
            'audits' => $audits,
            'filters' => $filters,
            'summary' => $summary,
            'scopeName' => $scopeName,
            'tableLabels' => self::TABLE_LABELS,
        ]);
    }

    public function show(Request $request, AuditTrail $auditTrail): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'kepala_desa', 403);
        abort_if($user->id_wilayah === null, 403, 'Akun Kepala Desa belum memiliki wilayah tugas.');

        $wilayahId = (int) $user->id_wilayah;
        $allowed = match ($auditTrail->nama_tabel) {
            'penerima' => DB::table('penerima')->where('id', $auditTrail->id_record)->where('id_wilayah', $wilayahId)->exists()
                || (int) ($auditTrail->data_lama['id_wilayah'] ?? $auditTrail->data_baru['id_wilayah'] ?? 0) === $wilayahId,
            'penyaluran' => DB::table('penyaluran')
                ->join('penerima', 'penyaluran.id_penerima', '=', 'penerima.id')
                ->where('penyaluran.id', $auditTrail->id_record)
                ->where('penerima.id_wilayah', $wilayahId)->exists(),
            'pengaduan' => DB::table('pengaduan')->where('id', $auditTrail->id_record)->where('id_wilayah', $wilayahId)->exists(),
            'tanggapan' => DB::table('tanggapan')
                ->join('pengaduan', 'tanggapan.id_pengaduan', '=', 'pengaduan.id')
                ->where('tanggapan.id', $auditTrail->id_record)
                ->where('pengaduan.id_wilayah', $wilayahId)->exists(),
            default => false,
        };
        abort_unless($allowed, 404);

        $auditTrail->load('pengguna:id,name');

        return view('pages.audit-trail.show', [
            'audit' => $auditTrail,
            'tableLabel' => self::TABLE_LABELS[$auditTrail->nama_tabel] ?? $auditTrail->nama_tabel,
            'dataLama' => $this->maskSensitiveData($auditTrail->data_lama ?? []),
            'dataBaru' => $this->maskSensitiveData($auditTrail->data_baru ?? []),
        ]);
    }

    /**
     * Samarkan NIK di seluruh level data snapshot sebelum ditampilkan.
     */
    private function maskSensitiveData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
                continue;
            }

            if (strtolower((string) $key) === 'nik' && $value !== null && $value !== '') {
                $digits = preg_replace('/\D+/', '', (string) $value);
                $data[$key] = strlen($digits) > 4
                    ? str_repeat('•', strlen($digits) - 4).substr($digits, -4)
                    : '••••';
            }
        }

        return $data;
    }
}
