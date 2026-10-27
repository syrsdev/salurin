<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class JenisBantuanController extends Controller
{
    private function authorizeManagement(): void
    {
        $user = Auth::user();

        abort_unless(
            $user !== null && in_array($user->role, ['petugas', 'admin'], true),
            403
        );
    }

    public function index(Request $request): View
    {
        $this->authorizeManagement();
        $search = trim((string) $request->query('search', ''));
        $jenisBantuans = JenisBantuan::query()
            ->when($search !== '', fn($query) => $query->where('nama_bantuan', 'like', '%' . $search . '%'))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.jenis-bantuan.index', compact('jenisBantuans', 'search'));
    }

    public function create(): View
    {
        $this->authorizeManagement();
        return view('pages.jenis-bantuan.form', ['jenisBantuan' => new JenisBantuan()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManagement();
        JenisBantuan::create($this->validatedData($request));
        return redirect()->route('jenis-bantuan.index')->with('success', 'Jenis bantuan berhasil ditambahkan.');
    }

    public function edit(JenisBantuan $jenisBantuan): View
    {
        $this->authorizeManagement();
        return view('pages.jenis-bantuan.form', compact('jenisBantuan'));
    }

    public function update(Request $request, JenisBantuan $jenisBantuan): RedirectResponse
    {
        $this->authorizeManagement();
        $jenisBantuan->update($this->validatedData($request, $jenisBantuan));
        return redirect()->route('jenis-bantuan.index')->with('success', 'Jenis bantuan berhasil diperbarui.');
    }

    public function destroy(JenisBantuan $jenisBantuan): RedirectResponse
    {
        $this->authorizeManagement();
        // Penyaluran memiliki foreign key ke jenis bantuan; jangan hapus data yang sudah dipakai.
        if ($jenisBantuan->penyalurans()->exists()) {
            return back()->with('error', 'Jenis bantuan sudah digunakan pada penyaluran dan tidak dapat dihapus.');
        }
        $jenisBantuan->delete();
        return redirect()->route('jenis-bantuan.index')->with('success', 'Jenis bantuan berhasil dihapus.');
    }

    private function validatedData(Request $request, ?JenisBantuan $jenisBantuan = null): array
    {
        return $request->validate([
            'nama_bantuan' => ['required', 'string', 'max:100', Rule::unique('jenis_bantuan', 'nama_bantuan')->ignore($jenisBantuan?->id)],
            'nominal' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99', 'decimal:0,2'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ], [
            'nama_bantuan.required' => 'Nama bantuan wajib diisi.',
            'nama_bantuan.unique' => 'Nama bantuan sudah terdaftar.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
        ]);
    }
}
