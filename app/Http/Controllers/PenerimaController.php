<?php

namespace App\Http\Controllers;

use App\Models\Penerima;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenerimaController extends Controller
{
    /**
     * Issue 14: daftar, cari, filter dan pagination penerima.
     * Issue 15: CRUD penerima.
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

        $penerima = $query->orderBy('penerima.nama_lengkap')
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
            'penerima',
            'wilayahOptions',
            'search',
            'status',
            'wilayahId',
            'tabCounts'
        ));
    }

    public function create(): View
    {
        $user = $this->authorizePetugas();
        $wilayahOptions = DB::table('wilayah')
            ->where('id', $user->id_wilayah)
            ->orderBy('kelurahan_desa')
            ->get(['id', 'kelurahan_desa', 'kecamatan']);

        $penerima = new Penerima;

        return view('pages.penerima.form', compact('penerima', 'wilayahOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->authorizePetugas();
        $data = $this->validatedData($request, $user);

        Penerima::create(array_merge($data, [
            'id_wilayah' => $user->id_wilayah,
            'dibuat_oleh' => $user->id,
        ]));

        return redirect()->route('penerima.index')->with('success', 'Data penerima berhasil ditambahkan.');
    }

    public function edit(Penerima $penerima): View
    {
        $user = $this->authorizePetugas();
        $this->authorizePenerimaAccess($penerima, $user);

        $wilayahOptions = DB::table('wilayah')
            ->where('id', $user->id_wilayah)
            ->orderBy('kelurahan_desa')
            ->get(['id', 'kelurahan_desa', 'kecamatan']);

        return view('pages.penerima.form', compact('penerima', 'wilayahOptions'));
    }

    public function update(Request $request, Penerima $penerima): RedirectResponse
    {
        $user = $this->authorizePetugas();
        $this->authorizePenerimaAccess($penerima, $user);
        $data = $this->validatedData($request, $user, $penerima);

        $penerima->update(array_merge($data, [
            'id_wilayah' => $user->id_wilayah,
        ]));

        return redirect()->route('penerima.index')->with('success', 'Data penerima berhasil diperbarui.');
    }

    public function destroy(Penerima $penerima): RedirectResponse
    {
        $user = $this->authorizePetugas();
        $this->authorizePenerimaAccess($penerima, $user);

        if (DB::table('penyaluran')->where('id_penerima', $penerima->id)->exists()) {
            return redirect()->route('penerima.index')
                ->with('error', 'Data penerima sudah digunakan pada penyaluran dan tidak dapat dihapus.');
        }

        $penerima->delete();

        return redirect()->route('penerima.index')->with('success', 'Data penerima berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, User $user, ?Penerima $penerima = null): array
    {
        return $request->validate([
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique('penerima', 'nik')->ignore($penerima?->id),
            ],
            'no_kk' => ['nullable', 'string', 'digits:16'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'alamat' => ['nullable', 'string', 'max:5000'],
            'id_wilayah' => ['required', 'integer', Rule::in([$user->id_wilayah])],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'no_kk.digits' => 'Nomor KK harus terdiri dari 16 digit angka.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            'id_wilayah.required' => 'Wilayah wajib dipilih.',
            'id_wilayah.in' => 'Wilayah harus sesuai dengan wilayah tugas Anda.',
        ]);
    }

    private function authorizePetugas(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->role === 'petugas', 403);
        abort_if($user->id_wilayah === null, 403, 'Akun petugas belum memiliki wilayah tugas.');

        return $user;
    }

    private function authorizePenerimaAccess(Penerima $penerima, User $user): void
    {
        abort_unless((int) $penerima->id_wilayah === (int) $user->id_wilayah, 404);
    }
}
