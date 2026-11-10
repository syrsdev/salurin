<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CekStatusBantuanController extends Controller
{
    public function index(): View
    {
        return view('pages.cek-status-bantuan.index');
    }

    public function cari(Request $request): View|RedirectResponse
    {
        $validator = Validator::make($request->only(['nik', 'no_kk', 'tanggal_lahir']), [
            'nik' => ['required', 'string', 'digits:16'],
            'no_kk' => ['required', 'string', 'digits:16'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'no_kk.required' => 'Nomor KK wajib diisi.',
            'no_kk.digits' => 'Nomor KK harus terdiri dari 16 digit angka.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Tanggal lahir harus menggunakan format yang valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('cek-status-bantuan.index')
                ->withErrors($validator)
                ->withInput([]);
        }

        $data = $validator->validated();

        $penerima = Penerima::query()
            ->select('id', 'nama_lengkap', 'status_data', 'tgl_disetujui', 'id_wilayah')
            ->with([
                'wilayah:id,kelurahan_desa,kecamatan',
                'penyalurans' => function (HasMany $query): void {
                    $query->select(
                        'id',
                        'id_penerima',
                        'id_bantuan',
                        'id_periode',
                        'status_penyaluran',
                        'tgl_penyaluran',
                        'created_at',
                        'updated_at'
                    )
                        ->with([
                            'jenisBantuan:id,nama_bantuan,nominal',
                            'periode:id,nama_periode,tgl_mulai,tgl_selesai',
                        ])
                        ->orderByDesc('id');
                },
            ])
            ->matchingIdentity($data['nik'], $data['no_kk'], $data['tanggal_lahir'])
            ->first();

        return view('pages.cek-status-bantuan.show', compact('penerima'));
    }
}
