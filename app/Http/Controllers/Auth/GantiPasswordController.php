<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GantiPasswordController extends Controller
{
    public function show()
    {
        return view('auth.ganti-password');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:password_lama'],
        ], [
            'password_lama.required' => 'Password saat ini wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
            'password.different' => 'Password baru harus berbeda dari password sebelumnya.',
        ]);

        if (! Hash::check($request->password_lama, $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'temp_password_expires_at' => null,
        ])->save();

        CatatanAktivitas::catat('ganti_password', $user, ['role' => $user->role]);

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Password berhasil diganti. Selamat menggunakan sistem.');
    }
}
