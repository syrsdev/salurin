<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Penerima;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kepala Desa: lihat data penerima, setujui, atau tolak (alasan wajib).
 */
class PenerimaPersetujuanController extends Controller
{
    // Kolom yang dicatat ke audit trail.
    private const KOLOM_AUDIT = ['status_data', 'alasan_penolakan', 'tgl_disetujui', 'disetujui_oleh'];

    public function index(Request $request): JsonResponse
    {
        $wilayahId = $this->wilayahId($request);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:menunggu,disetujui,ditolak'],
        ]);

        $search = trim($filters['search'] ?? '');

        $query = Penerima::query()->where('id_wilayah', $wilayahId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status_data', $filters['status']);
        }

        $penerimas = $query->orderBy('nama_lengkap')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return response()->json($penerimas);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $penerima = Penerima::where('id', $id)
            ->where('id_wilayah', $this->wilayahId($request))
            ->firstOrFail();

        return response()->json(['data' => $penerima]);
    }

    public function setujui(Request $request, int $id): JsonResponse
    {
        $penerima = $this->proses($request, $id, [
            'status_data'      => 'disetujui',
            'alasan_penolakan' => null,
            'tgl_disetujui'    => now(),
            'disetujui_oleh'   => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Data penerima disetujui.',
            'data'    => $penerima,
        ]);
    }

    public function tolak(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $penerima = $this->proses($request, $id, [
            'status_data'      => 'ditolak',
            'alasan_penolakan' => $data['alasan_penolakan'],
            'tgl_disetujui'    => null,
            'disetujui_oleh'   => null,
        ]);

        return response()->json([
            'message' => 'Data penerima ditolak.',
            'data'    => $penerima,
        ]);
    }

    /**
     * Ubah status + catat audit trail dalam satu transaksi.
     * Hanya data berstatus "menunggu" di wilayah kepala desa yang boleh diproses.
     */
    private function proses(Request $request, int $id, array $perubahan): Penerima
    {
        $wilayahId = $this->wilayahId($request);

        return DB::transaction(function () use ($id, $wilayahId, $perubahan) {
            $penerima = Penerima::where('id', $id)
                ->where('id_wilayah', $wilayahId)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($penerima->status_data !== 'menunggu', 409, 'Data penerima ini sudah diproses.');

            $lama = $penerima->only(self::KOLOM_AUDIT);

            $penerima->update($perubahan);

            AuditTrail::catat('penerima', $penerima->id, 'update', $lama, $penerima->only(self::KOLOM_AUDIT));

            return $penerima;
        });
    }

    private function wilayahId(Request $request): int
    {
        $user = $request->user();
        abort_if($user->id_wilayah === null, 403, 'Akun kepala desa belum memiliki wilayah tugas.');

        return (int) $user->id_wilayah;
    }
}
