<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Penyaluran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenyaluranTambahController extends Controller
{
    public function pilihan(Request $request)
    {
        $wilayah = $request->user()->id_wilayah;
        abort_if($wilayah === null, 403, 'Akun belum memiliki wilayah tugas.');
        return response()->json([
            'penerima' => DB::table('penerima')->where('id_wilayah', $wilayah)->where('status_data', 'disetujui')->select('id', 'nama_lengkap', 'nik')->orderBy('nama_lengkap')->get(),
            'bantuan' => DB::table('jenis_bantuan')->select('id', 'nama_bantuan', 'nominal')->orderBy('nama_bantuan')->get(),
            'periode' => DB::table('periode')->select('id', 'nama_periode', 'tgl_mulai', 'tgl_selesai')->orderByDesc('tgl_mulai')->get()
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate(['id_penerima' => 'required|integer|exists:penerima,id', 'id_bantuan' => 'required|integer|exists:jenis_bantuan,id', 'id_periode' => 'required|integer|exists:periode,id', 'catatan' => 'nullable|string|max:1000']);
        $wilayah = $request->user()->id_wilayah;
        abort_if($wilayah === null, 403, 'Akun belum memiliki wilayah tugas.');
        $penerima = DB::table('penerima')->where('id', $data['id_penerima'])->where('id_wilayah', $wilayah)->where('status_data', 'disetujui')->first();
        if (!$penerima) throw ValidationException::withMessages(['id_penerima' => 'Penerima harus sudah disetujui dan berada di wilayah tugas.']);
        $penyaluran = DB::transaction(function () use ($request, $data) {
            $exists = Penyaluran::where('id_penerima', $data['id_penerima'])->where('id_bantuan', $data['id_bantuan'])->where('id_periode', $data['id_periode'])->exists();
            if ($exists) throw ValidationException::withMessages(['id_penerima' => 'Penyaluran untuk kombinasi penerima, bantuan, dan periode sudah ada.']);
            $p = Penyaluran::create(['id_penerima' => $data['id_penerima'], 'id_bantuan' => $data['id_bantuan'], 'id_periode' => $data['id_periode'], 'id_pengguna' => $request->user()->id, 'status_penyaluran' => 'terdaftar', 'catatan' => $data['catatan'] ?? null]);
            AuditTrail::catat('penyaluran', $p->id, 'create', null, $p->toArray());
            return $p;
        });
        return response()->json(['message' => 'Penyaluran berhasil ditambahkan.', 'data' => $penyaluran], 201);
    }
}
