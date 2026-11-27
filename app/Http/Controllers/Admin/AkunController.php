<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends Controller
{
    private const ROLES = [
        'petugas' => 'Petugas Desa/Kelurahan',
        'kepala_desa' => 'Kepala Desa/Kelurahan',
        'admin' => 'Administrator',
    ];

    private function wilayahOptions()
    {
        return DB::table('wilayah')->orderBy('kelurahan_desa')->get(['id', 'kelurahan_desa', 'kecamatan']);
    }

    // Data akun untuk audit trail (tanpa password)
    private function snapshot(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'id_wilayah' => $user->id_wilayah,
            'aktif' => (bool) $user->aktif,
        ];
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'id_wilayah' => [
                Rule::requiredIf(fn () => in_array($request->input('role'), ['petugas', 'kepala_desa'], true)),
                'nullable', 'integer', Rule::exists('wilayah', 'id'),
            ],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Peran wajib dipilih.',
            'id_wilayah.required' => 'Wilayah wajib dipilih untuk petugas dan kepala desa.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);
    }

    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $role = (string) $request->query('role', '');
        $status = (string) $request->query('status', '');

        $users = User::query()
            ->leftJoin('wilayah', 'users.id_wilayah', '=', 'wilayah.id')
            ->select('users.*', 'wilayah.kelurahan_desa', 'wilayah.kecamatan')
            ->when($cari !== '', fn ($q) => $q->where(function ($w) use ($cari) {
                $w->where('users.name', 'like', '%' . $cari . '%')
                    ->orWhere('users.email', 'like', '%' . $cari . '%');
            }))
            ->when(array_key_exists($role, self::ROLES), fn ($q) => $q->where('users.role', $role))
            ->when($status === 'aktif', fn ($q) => $q->where('users.aktif', true))
            ->when($status === 'nonaktif', fn ($q) => $q->where('users.aktif', false))
            ->orderBy('users.name')
            ->paginate(10)
            ->withQueryString();

        $roles = self::ROLES;

        return view('pages.akun.index', compact('users', 'cari', 'role', 'status', 'roles'));
    }

    public function create(): View
    {
        return view('pages.akun.form', [
            'user' => new User(),
            'roles' => self::ROLES,
            'wilayahOptions' => $this->wilayahOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = new User();
        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'id_wilayah' => $data['id_wilayah'] ?? null,
            'aktif' => true,
            'password' => $data['password'], // di-hash otomatis oleh cast 'hashed'
        ])->save();

        AuditTrail::catat('users', $user->id, 'create', null, $this->snapshot($user));

        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('pages.akun.form', [
            'user' => $user,
            'roles' => self::ROLES,
            'wilayahOptions' => $this->wilayahOptions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        $lama = $this->snapshot($user);

        // Admin tidak boleh mengubah perannya sendiri agar tidak terkunci
        if ($user->is($request->user())) {
            $data['role'] = $user->role;
        }

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'id_wilayah' => $data['id_wilayah'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $baru = $this->snapshot($user);
        if (! empty($data['password'])) {
            $baru['password_diubah'] = true;
        }

        AuditTrail::catat('users', $user->id, 'update', $lama, $baru);

        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function ubahStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $lama = ['aktif' => (bool) $user->aktif];
        $user->forceFill(['aktif' => ! $user->aktif])->save();

        AuditTrail::catat('users', $user->id, 'update', $lama, ['aktif' => (bool) $user->aktif]);

        return back()->with('success', $user->aktif ? 'Akun berhasil diaktifkan.' : 'Akun berhasil dinonaktifkan.');
    }
}
