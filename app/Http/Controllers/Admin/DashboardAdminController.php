<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardAdminController extends Controller
{
    public const ROLES = [
        'petugas' => 'Petugas Desa/Kelurahan',
        'kepala_desa' => 'Kepala Desa/Kelurahan',
        'admin' => 'Administrator',
    ];

    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);
        $counts = User::query()->selectRaw('role, COUNT(*) AS total')->groupBy('role')->pluck('total', 'role');
        $active = User::query()->where('aktif', true)->selectRaw('role, COUNT(*) AS total')->groupBy('role')->pluck('total', 'role');
        $accounts = User::query()->orderByDesc('created_at')->orderByDesc('id')->limit(5)
            ->get(['id', 'name', 'email', 'role', 'aktif', 'created_at']);

        return view('pages.dashboard.admin', ['counts' => $counts, 'active' => $active, 'accounts' => $accounts, 'roles' => self::ROLES]);
    }

    public function show(User $user): View
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        return view('pages.dashboard.admin-detail', ['account' => $user->load('wilayah'), 'roles' => self::ROLES]);
    }

    public function access(): View
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        return view('pages.dashboard.admin-access');
    }
}
