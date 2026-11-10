<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengaduanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->authorizePetugas($request);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Pengaduan::STATUS))],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? '';

        $pengaduan = Pengaduan::query()
            ->where('id_wilayah', $user->id_wilayah)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('kode_tiket', 'like', '%'.$search.'%')
                        ->orWhere('isi_pengaduan', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== '', fn (Builder $query) => $query->where('status_pengaduan', $status))
            ->latest('tgl_pengaduan')
            ->paginate(10)
            ->withQueryString();

        return view('pages.dashboard.pengaduan.index', compact('pengaduan', 'search', 'status'));
    }

    public function show(Request $request, Pengaduan $pengaduan): View
    {
        $user = $this->authorizePetugas($request);
        $this->authorizeWilayah($pengaduan, $user);

        $pengaduan->load([
            'wilayah:id,kelurahan_desa,kecamatan',
            'tanggapans' => fn (HasMany $query) => $query->with('pengguna:id,name')->orderBy('waktu'),
        ]);

        return view('pages.dashboard.pengaduan.show', compact('pengaduan'));
    }

    public function store(Request $request, Pengaduan $pengaduan): RedirectResponse
    {
        $user = $this->authorizePetugas($request);
        $this->authorizeWilayah($pengaduan, $user);

        $data = $request->validate([
            'isi_tanggapan' => ['required', 'string', 'min:5', 'max:5000'],
            'status_pengaduan' => ['required', Rule::in(['diproses', 'eskalasi', 'selesai'])],
        ], [
            'isi_tanggapan.required' => 'Isi tanggapan wajib diisi.',
            'isi_tanggapan.min' => 'Tanggapan minimal 5 karakter.',
            'status_pengaduan.required' => 'Status pengaduan wajib dipilih.',
            'status_pengaduan.in' => 'Status pengaduan tidak valid.',
        ]);

        DB::transaction(function () use ($pengaduan, $user, $data): void {
            $lockedPengaduan = Pengaduan::query()
                ->whereKey($pengaduan->id)
                ->where('id_wilayah', $user->id_wilayah)
                ->lockForUpdate()
                ->firstOrFail();

            $waktuTanggapan = now();

            $lockedPengaduan->tanggapans()->create([
                'id_pengguna' => $user->id,
                'isi_tanggapan' => $data['isi_tanggapan'],
                'waktu' => $waktuTanggapan,
            ]);

            $lockedPengaduan->update([
                'ditangani_oleh' => $user->id,
                'status_pengaduan' => $data['status_pengaduan'],
                'tgl_tanggapan' => $waktuTanggapan,
            ]);
        });

        return redirect()->route('dashboard.pengaduan.show', $pengaduan)
            ->with('success', 'Tanggapan berhasil dikirim.');
    }

    private function authorizePetugas(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === 'petugas', 403);
        abort_if($user->id_wilayah === null, 403, 'Akun petugas belum memiliki wilayah tugas.');

        return $user;
    }

    private function authorizeWilayah(Pengaduan $pengaduan, User $user): void
    {
        abort_unless((int) $pengaduan->id_wilayah === (int) $user->id_wilayah, 404);
    }
}
