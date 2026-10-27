<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicDashboardController extends Controller
{
    public function index(Request $request)
    {
        $wilayahOptions = DB::table('wilayah')
            ->select('id', 'kelurahan_desa')
            ->distinct()
            ->orderBy('kelurahan_desa')
            ->get();

        $bantuanOptions = DB::table('jenis_bantuan')
            ->select('id', 'nama_bantuan')
            ->orderBy('nama_bantuan')
            ->get();

        $periodeOptions = DB::table('periode')
            ->select('id', 'nama_periode', 'tgl_mulai')
            ->orderByDesc('tgl_mulai')
            ->get();

        $filters = [
            'wilayah' => $request->input('wilayah', ''),
            'bantuan' => $request->input('bantuan', ''),
            'periode' => $request->input('periode', $periodeOptions->first()->id ?? ''),
        ];

        $applyFilters = function ($query) use ($filters) {
            if ($filters['wilayah'] !== '') {
                $query->where('w.id', $filters['wilayah']);
            }
            if ($filters['bantuan'] !== '') {
                $query->where('jb.id', $filters['bantuan']);
            }
            if ($filters['periode'] !== '') {
                $query->where('pr.id', $filters['periode']);
            }

            return $query;
        };

        $base = fn () => $applyFilters(
            DB::table('penyaluran as ps')
                ->join('penerima as p', 'p.id', '=', 'ps.id_penerima')
                ->join('wilayah as w', 'w.id', '=', 'p.id_wilayah')
                ->join('jenis_bantuan as jb', 'jb.id', '=', 'ps.id_bantuan')
                ->join('periode as pr', 'pr.id', '=', 'ps.id_periode')
                ->where('p.status_data', 'disetujui')
        );

        $summary = (clone $base())
            ->selectRaw('COUNT(DISTINCT p.id) as total_penerima')
            ->selectRaw("COUNT(DISTINCT CASE WHEN ps.status_penyaluran = 'cair' THEN p.id END) as total_cair")
            ->selectRaw("COUNT(DISTINCT CASE WHEN ps.status_penyaluran = 'dijadwalkan' THEN p.id END) as total_dijadwalkan")
            ->selectRaw("COALESCE(SUM(CASE WHEN ps.status_penyaluran = 'cair' THEN jb.nominal ELSE 0 END), 0) as total_dana")
            ->first();

        $totalPenerima = (int) ($summary->total_penerima ?? 0);
        $totalCair = (int) ($summary->total_cair ?? 0);
        $persentase = $totalPenerima > 0 ? (int) round(($totalCair / $totalPenerima) * 100) : 0;

        $distribusi = (clone $base())
            ->where('ps.status_penyaluran', 'cair')
            ->select('w.kelurahan_desa as wilayah')
            ->selectRaw('COUNT(DISTINCT p.id) as jumlah')
            ->groupBy('w.kelurahan_desa')
            ->orderByDesc('jumlah')
            ->get();

        $penerima = (clone $base())
            ->select([
                'p.id',
                'p.nama_lengkap',
                'w.kelurahan_desa as wilayah',
                'jb.nama_bantuan',
                'jb.nominal',
                'pr.nama_periode',
                'ps.status_penyaluran',
                'ps.tgl_penyaluran',
            ])
            ->orderByDesc('ps.updated_at')
            ->paginate(6)
            ->withQueryString();

        return view('pages.public-dashboard', compact(
            'wilayahOptions',
            'bantuanOptions',
            'periodeOptions',
            'filters',
            'totalPenerima',
            'totalCair',
            'persentase',
            'summary',
            'distribusi',
            'penerima'
        ));
    }
}
