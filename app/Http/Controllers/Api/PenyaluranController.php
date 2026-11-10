<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Penyaluran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Petugas Desa: lihat daftar penyaluran dan ubah status tiap penerima.
 */
class PenyaluranController extends Controller
{
    private const KOLOM_AUDIT = ['status_penyaluran', 'tgl_penyaluran', 'catatan'];

    public function index(Request $request): JsonResponse
    {
        $wilayahId = $this->wilayahId($request);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Penyaluran::STATUS))],
        ]);

        $search = trim($filters['search'] ?? '');

        $query = Penyaluran::query()
            ->with([
                'penerima:id,nik,nama_lengkap,status_data,alamat',
                'jenisBantuan:id,nama_bantuan,nominal,deskripsi',
                'periode:id,nama_periode,tgl_mulai,tgl_selesai',
            ])
            ->whereHas('penerima', function ($q) use ($wilayahId, $search) {
                $q->where('id_wilayah', $wilayahId);

                if ($search !== '') {
                    $q->where(function ($w) use ($search) {
                        $w->where('nama_lengkap', 'like', '%' . $search . '%')
                            ->orWhere('nik', 'like', '%' . $search . '%');
                    });
                }
            });

        if (! empty($filters['status'])) {
            $query->where('status_penyaluran', $filters['status']);
        }

        return response()->json(
            $query->orderByDesc('id')->paginate(10)->withQueryString()
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $penyaluran = $this->querySewilayah($this->wilayahId($request))
            ->with([
                'penerima:id,nik,nama_lengkap,status_data,alamat',
                'jenisBantuan:id,nama_bantuan,nominal,deskripsi',
                'periode:id,nama_periode,tgl_mulai,tgl_selesai',
            ])
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'data' => $penyaluran,
        ]);
    }

    public function riwayat(Request $request, int $id): JsonResponse
    {
        // Pastikan penyaluran berada di wilayah tugas petugas.
        $penyaluran = $this->querySewilayah($this->wilayahId($request))
            ->where('id', $id)
            ->firstOrFail();

        // Ambil riwayat perubahan dari Audit Trail.
        $riwayat = AuditTrail::query()
            ->with('pengguna:id,name')
            ->where('nama_tabel', 'penyaluran')
            ->where('id_record', $penyaluran->id)
            ->orderBy('waktu')
            ->orderBy('id')
            ->get();

        $data = $riwayat->map(function ($audit) {
            return [
                'id' => $audit->id,
                'status_sebelumnya' => $audit->data_lama['status_penyaluran'] ?? null,
                'status_baru' => $audit->data_baru['status_penyaluran'] ?? null,
                'tgl_penyaluran' => $audit->data_baru['tgl_penyaluran'] ?? null,
                'catatan' => $audit->data_baru['catatan'] ?? null,
                'waktu' => $audit->waktu,
                'pengguna' => $audit->pengguna?->name,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    public function ubahStatus(Request $request, int $id): JsonResponse
    {
        $wilayahId = $this->wilayahId($request);

        $data = $request->validate([
            'status_penyaluran' => ['required', Rule::in(array_keys(Penyaluran::STATUS))],
            'tgl_penyaluran'    => ['nullable', 'date'],
            'catatan'           => ['nullable', 'string', 'max:1000'],
        ], [
            'status_penyaluran.required' => 'Status penyaluran wajib dipilih.',
            'status_penyaluran.in'       => 'Status penyaluran tidak valid.',
        ]);

        $penyaluran = DB::transaction(function () use ($id, $wilayahId, $data) {
            $penyaluran = $this->querySewilayah($wilayahId)
                ->where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $lama = $penyaluran->only(self::KOLOM_AUDIT);

            $penyaluran->update(
                Arr::only($data, ['status_penyaluran', 'tgl_penyaluran', 'catatan'])
            );

            AuditTrail::catat(
                'penyaluran',
                $penyaluran->id,
                'update',
                $lama,
                $penyaluran->only(self::KOLOM_AUDIT)
            );

            return $penyaluran;
        });

        return response()->json([
            'message' => 'Status penyaluran diperbarui.',
            'data' => $penyaluran->load([
                'penerima:id,nik,nama_lengkap,status_data,alamat',
                'jenisBantuan:id,nama_bantuan,nominal,deskripsi',
                'periode:id,nama_periode,tgl_mulai,tgl_selesai',
            ]),
        ]);
    }

    // Penyaluran hanya boleh diakses jika penerimanya berada di wilayah petugas.
    private function querySewilayah(int $wilayahId)
    {
        return Penyaluran::query()->whereHas('penerima', function ($q) use ($wilayahId) {
            $q->where('id_wilayah', $wilayahId);
        });
    }

    private function wilayahId(Request $request): int
    {
        $user = $request->user();
        abort_if($user->id_wilayah === null, 403, 'Akun petugas belum memiliki wilayah tugas.');

        return (int) $user->id_wilayah;
    }
}
