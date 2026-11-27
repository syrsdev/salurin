<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KepalaDesaPengaduanController extends Controller
{
    // Pengaduan di wilayah kepala desa yang sedang login
    private function queryWilayah(Request $request): Builder
    {
        $idWilayah = $request->user()->id_wilayah;

        return Pengaduan::query()
            ->when($idWilayah, fn ($q) => $q->where('id_wilayah', $idWilayah));
    }

    // Pengaduan yang sudah pernah diberi arahan oleh kepala desa
    private function sudahDiarahkan(Builder $query): Builder
    {
        return $query->whereHas('tanggapans', function ($t) {
            $t->whereHas('pengguna', fn ($u) => $u->where('role', 'kepala_desa'));
        });
    }

    private function pastikanBolehDiakses(Request $request, Pengaduan $pengaduan): void
    {
        $idWilayah = $request->user()->id_wilayah;

        abort_if($idWilayah && (int) $pengaduan->id_wilayah !== (int) $idWilayah, 403);

        $diteruskan = $pengaduan->status_pengaduan === 'eskalasi'
            || $this->sudahDiarahkan(Pengaduan::query()->whereKey($pengaduan->getKey()))->exists();

        abort_unless($diteruskan, 404);
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'sudah' ? 'sudah' : 'menunggu';
        $cari = strtoupper(trim((string) $request->query('cari', '')));

        $jumlahMenunggu = $this->queryWilayah($request)->where('status_pengaduan', 'eskalasi')->count();
        $jumlahSudah = $this->sudahDiarahkan($this->queryWilayah($request))->count();

        $query = $tab === 'sudah'
            ? $this->sudahDiarahkan($this->queryWilayah($request))->latest('tgl_pengaduan')
            : $this->queryWilayah($request)->where('status_pengaduan', 'eskalasi')->oldest('tgl_pengaduan');

        $pengaduans = $query
            ->with('wilayah:id,kelurahan_desa,kecamatan')
            ->when($cari !== '', fn ($q) => $q->where('kode_tiket', 'like', '%' . $cari . '%'))
            ->paginate(10)
            ->withQueryString();

        return view('pages.kepala-desa.pengaduan.index', compact(
            'pengaduans', 'tab', 'cari', 'jumlahMenunggu', 'jumlahSudah'
        ));
    }

    public function show(Request $request, Pengaduan $pengaduan): View
    {
        $this->pastikanBolehDiakses($request, $pengaduan);

        $pengaduan->load(['wilayah:id,kelurahan_desa,kecamatan', 'tanggapans.pengguna:id,name,role']);

        return view('pages.kepala-desa.pengaduan.show', compact('pengaduan'));
    }

    public function store(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $this->pastikanBolehDiakses($request, $pengaduan);

        if ($pengaduan->status_pengaduan !== 'eskalasi') {
            return back()->with('error', 'Pengaduan ini sudah tidak menunggu arahan.');
        }

        $data = $request->validate([
            'isi_tanggapan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'isi_tanggapan.required' => 'Arahan wajib diisi.',
            'isi_tanggapan.min' => 'Arahan minimal 10 karakter.',
            'isi_tanggapan.max' => 'Arahan maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use ($request, $pengaduan, $data): void {
            $lama = [
                'status_pengaduan' => $pengaduan->status_pengaduan,
                'tgl_tanggapan' => $pengaduan->tgl_tanggapan ? (string) $pengaduan->tgl_tanggapan : null,
            ];

            $tanggapan = Tanggapan::create([
                'id_pengaduan' => $pengaduan->id,
                'id_pengguna' => $request->user()->id,
                'isi_tanggapan' => $data['isi_tanggapan'],
                'waktu' => now(),
            ]);

            $pengaduan->status_pengaduan = 'diproses';
            $pengaduan->tgl_tanggapan = $pengaduan->tgl_tanggapan ?? now();
            $pengaduan->save();

            AuditTrail::catat('tanggapan', $tanggapan->id, 'create', null, [
                'id_pengaduan' => $pengaduan->id,
                'isi_tanggapan' => $data['isi_tanggapan'],
            ]);

            AuditTrail::catat('pengaduan', $pengaduan->id, 'update', $lama, [
                'status_pengaduan' => 'diproses',
                'tgl_tanggapan' => (string) $pengaduan->tgl_tanggapan,
            ]);
        });

        return redirect()->route('kades.pengaduan.index')
            ->with('success', 'Arahan untuk tiket ' . $pengaduan->kode_tiket . ' berhasil dikirim.');
    }
}
