<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Kelola akun pengguna dashboard (khusus Super Admin lewat route).
 * Sesuaikan isi ROLES dengan nilai kolom "role" di tabel users kamu.
 */
class PenggunaController extends Controller
{
    public const ROLES = [
        'super_admin' => 'Super Admin',
        'admin'       => 'Admin',
        'wali_santri' => 'Wali Santri',
    ];

    public function index(Request $request)
    {
        $penggunas = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            }))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.pengguna.index', ['penggunas' => $penggunas, 'roles' => self::ROLES]);
    }

    public function create()
    {
        return view('dashboard.pengguna.create', ['roles' => self::ROLES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'role'     => ['required', Rule::in(array_keys(self::ROLES))],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('super.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        return view('dashboard.pengguna.edit', ['pengguna' => $pengguna, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $pengguna)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'role'     => ['required', Rule::in(array_keys(self::ROLES))],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Jangan turunkan peran Super Admin terakhir
        if ($pengguna->role === 'super_admin' && $data['role'] !== 'super_admin'
            && User::where('role', 'super_admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Tidak bisa mengubah peran Super Admin terakhir.'])->withInput();
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $pengguna->update($data);

        return redirect()->route('super.pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($pengguna->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Super Admin terakhir tidak bisa dihapus.');
        }

        $pengguna->delete();

        return redirect()->route('super.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
