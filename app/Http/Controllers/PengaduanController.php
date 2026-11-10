<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    public function create(): View
    {
        $wilayahOptions = DB::table('wilayah')
            ->orderBy('kelurahan_desa')
            ->get(['id', 'kelurahan_desa', 'kecamatan']);

        return view('pages.pengaduan.create', compact('wilayahOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_wilayah' => ['required', 'integer', Rule::exists('wilayah', 'id')],
            'kategori' => ['required', Rule::in(array_keys(Pengaduan::KATEGORI))],
            'isi_pengaduan' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'id_wilayah.required' => 'Desa/kelurahan wajib dipilih.',
            'id_wilayah.exists' => 'Desa/kelurahan yang dipilih tidak tersedia.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'isi_pengaduan.required' => 'Isi pengaduan wajib diisi.',
            'isi_pengaduan.min' => 'Isi pengaduan minimal 10 karakter.',
        ]);

        $pengaduan = null;

        // Ulangi jika dua pengaduan masuk bersamaan dan kode bentrok
        for ($i = 0; $i < 3 && $pengaduan === null; $i++) {
            try {
                $pengaduan = Pengaduan::create([
                    'kode_tiket' => Pengaduan::buatKodeTiket(),
                    'id_wilayah' => $data['id_wilayah'],
                    'kategori' => $data['kategori'],
                    'isi_pengaduan' => $data['isi_pengaduan'],
                    'status_pengaduan' => 'baru',
                    'tgl_pengaduan' => now(),
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // coba lagi dengan kode berikutnya
            }
        }

        if ($pengaduan === null) {
            return back()->withInput()->with('error', 'Gagal membuat tiket, silakan coba lagi.');
        }

        return redirect()->route('pengaduan.sukses', $pengaduan->kode_tiket);
    }

    public function cari(Request $request): View
    {
        $kode = strtoupper(trim((string) $request->query('kode_tiket', '')));
        $pengaduan = null;

        if ($kode !== '') {
            $pengaduan = Pengaduan::query()
                ->with(['wilayah:id,kelurahan_desa,kecamatan', 'tanggapans' => function (HasMany $query): void {
                    $query->select('id', 'id_pengaduan', 'isi_tanggapan', 'waktu')
                        ->orderBy('waktu');
                }])
                ->where('kode_tiket', $kode)
                ->first();
        }

        return view('pages.pengaduan.cari', compact('kode', 'pengaduan'));
    }

    public function sukses(string $kode): View
    {
        $pengaduan = Pengaduan::where('kode_tiket', $kode)->firstOrFail();

        return view('pages.pengaduan.sukses', compact('pengaduan'));
    }
}
