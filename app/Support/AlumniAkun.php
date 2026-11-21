<?php

namespace App\Support;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AlumniAkun
{
    /**
     * Pastikan alumni memiliki akun login.
     *
     * @return array{user: User, password: ?string} password null berarti akun sudah ada.
     */
    public static function pastikan(Alumni $alumni): array
    {
        if ($alumni->user_id && $alumni->user) {
            return ['user' => $alumni->user, 'password' => null];
        }

        $email = trim((string) $alumni->email);

        // Email alumni tidak boleh bentrok dengan akun wali/staf.
        if ($email === '' || User::where('email', $email)->exists()) {
            $dasar = $alumni->santri?->nomor_induk ?: ('ALUMNI-'.$alumni->id);
            $dasar = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $dasar);
            $email = strtolower($dasar).'@alumni.babulhasanah.sch.id';

            $urut = 1;
            while (User::where('email', $email)->exists()) {
                $email = strtolower($dasar).$urut.'@alumni.babulhasanah.sch.id';
                $urut++;
            }
        }

        $password = Str::password(12, symbols: false);

        $user = User::create([
            'name' => $alumni->nama,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'alumni',
            'phone' => $alumni->no_hp,
            'is_active' => true,
            'must_change_password' => true,
            'temp_password_expires_at' => now()->addDay(),
        ]);

        $alumni->forceFill([
            'user_id' => $user->id,
            'email' => $email,
        ])->save();

        return ['user' => $user->fresh(), 'password' => $password];
    }
}
