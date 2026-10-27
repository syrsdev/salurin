<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenerimaController extends Controller
{
    /**
     * Issue 14: daftar, cari, filter dan pagination penerima.
     * CRUD/form penerima ditangani pada Issue 15.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'petugas', 403);
        // Petugas tidak boleh melihat penerima dari wilayah selain wilayah tugasnya.
        abort_if($user->id_wilayah === null, 403, 'Akun petugas belum memiliki wilayah tugas.');

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'wilayah' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:menunggu,disetujui,ditolak'],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? '';
        $wilayahId = isset($filters['wilayah']) ? (int) $filters['wilayah'] : null;

        $wilayahOptions = DB::table('wilayah')
            ->where('id', $user->id_wilayah)
            ->orderBy('kelurahan_desa')
            ->get(['id', 'kelurahan_desa', 'kecamatan']);

        $query = Penerima::query()
            ->leftJoin('wilayah as w', 'penerima.id_wilayah', '=', 'w.id')
            ->where('penerima.id_wilayah', '=', $user->id_wilayah)
            ->select('penerima.*', 'w.kelurahan_desa as nama_wilayah', 'w.kecamatan as nama_kecamatan');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('penerima.nama_lengkap', 'like', '%'.$search.'%')
                    ->orWhere('penerima.nik', 'like', '%'.$search.'%');
            });
        }

        if ($wilayahId !== null) {
            $query->where('penerima.id_wilayah', '=', $wilayahId);
        }

        if ($status !== '') {
            $query->where('penerima.status_data', '=', $status);
        }

        $penerimas = $query->orderBy('penerima.nama_lengkap')
            ->orderBy('penerima.id')
            ->paginate(10)
            ->withQueryString();

        // Statistik tab dihitung dalam wilayah tugas; pencarian ikut diterapkan.
        $tabQuery = DB::table('penerima')->where('id_wilayah', $user->id_wilayah);
        if ($search !== '') {
            $tabQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%'.$search.'%')
                    ->orWhere('nik', 'like', '%'.$search.'%');
            });
        }
        if ($wilayahId !== null) {
            $tabQuery->where('id_wilayah', $wilayahId);
        }
        $tabCounts = [
            'semua' => (clone $tabQuery)->count(),
            'menunggu' => (clone $tabQuery)->where('status_data', 'menunggu')->count(),
            'disetujui' => (clone $tabQuery)->where('status_data', 'disetujui')->count(),
            'ditolak' => (clone $tabQuery)->where('status_data', 'ditolak')->count(),
        ];

        return view('pages.penerima.index', compact(
            'penerimas', 'wilayahOptions', 'search', 'status', 'wilayahId', 'tabCounts'
        ));
    }
}
