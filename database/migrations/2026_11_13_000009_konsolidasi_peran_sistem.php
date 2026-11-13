<?php

use App\Support\Jenjang;
use App\Support\RoleSistem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Konsolidasi peran lama menjadi 6 peran resmi:
 * super-admin, admin-jenjang, pengasuh, pembina, walisantri, alumni.
 *
 * - Peran Spatie lama (admin-mdta, admin-mts, pengurus, dst.) dipindahkan ke
 *   peran resmi, lalu baris peran lama dihapus.
 * - Kolom `users.jenjang` dinormalisasi menjadi slug (mdta, mts, ...) dan hanya
 *   dipertahankan untuk Admin Jenjang.
 * - Jenjang tidak lagi ditentukan oleh nama peran; Admin Jenjang memilih satu
 *   jenjang di halaman Pengguna.
 *
 * Idempoten: aman dijalankan berulang. Dijalankan oleh manusia lewat
 * `php artisan migrate` (agen AI dilarang mengeksekusi perintah database).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tabelRole = config('permission.table_names.roles', 'roles');
        $tabelPivot = config('permission.table_names.model_has_roles', 'model_has_roles');

        if (! Schema::hasTable($tabelRole)
            || ! Schema::hasTable($tabelPivot)
            || ! Schema::hasTable('users')) {
            return;
        }

        $modelType = 'App\\Models\\User';
        $idRole = [];

        // 1. Pastikan semua peran resmi tersedia.
        foreach (RoleSistem::nama() as $nama) {
            $baris = DB::table($tabelRole)
                ->where('name', $nama)
                ->where('guard_name', RoleSistem::GUARD)
                ->first();

            $idRole[$nama] = $baris?->id ?? DB::table($tabelRole)->insertGetId([
                'name' => $nama,
                'guard_name' => RoleSistem::GUARD,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Pindahkan pengguna dari peran lama ke peran resmi.
        $peranLama = DB::table($tabelRole)
            ->where('guard_name', RoleSistem::GUARD)
            ->get();

        foreach ($peranLama as $lama) {
            $tujuan = RoleSistem::normalisasi($lama->name);

            if (! $tujuan || $tujuan === $lama->name || ! isset($idRole[$tujuan])) {
                continue;
            }

            $this->pindahkanPengguna(
                $tabelPivot,
                $modelType,
                (int) $lama->id,
                $idRole[$tujuan]
            );

            DB::table($tabelRole)->where('id', $lama->id)->delete();
        }

        // 3. Normalisasi kolom lama users.role + users.jenjang.
        DB::table('users')->select('id', 'role', 'jenjang')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $role = strtolower(str_replace([' ', '-'], '_', trim((string) $user->role)));

                $role = match ($role) {
                    'superadmin', 'super' => 'super_admin',
                    'pengurus' => 'pengasuh',
                    'wali', 'walisantri', 'santri' => 'wali_santri',
                    default => $role,
                };

                if ($role === '') {
                    continue;
                }

                if ($role === 'admin' || str_starts_with($role, 'admin_')) {
                    $role = 'admin';
                }

                $jenjang = $role === 'admin' ? Jenjang::normalisasi($user->jenjang) : null;

                if ($user->role === $role && $user->jenjang === $jenjang) {
                    continue;
                }

                DB::table('users')->where('id', $user->id)->update([
                    'role' => $role,
                    'jenjang' => $jenjang,
                ]);
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Salin penugasan peran lama ke peran baru, lalu hapus penugasan lama.
     */
    private function pindahkanPengguna(string $tabelPivot, string $modelType, int $dariRole, int $keRole): void
    {
        $pengguna = DB::table($tabelPivot)
            ->where('model_type', $modelType)
            ->where('role_id', $dariRole)
            ->pluck('model_id')
            ->unique();

        foreach ($pengguna as $modelId) {
            $sudahAda = DB::table($tabelPivot)
                ->where('role_id', $keRole)
                ->where('model_id', $modelId)
                ->where('model_type', $modelType)
                ->exists();

            if (! $sudahAda) {
                DB::table($tabelPivot)->insert([
                    'role_id' => $keRole,
                    'model_id' => $modelId,
                    'model_type' => $modelType,
                ]);
            }
        }

        DB::table($tabelPivot)
            ->where('model_type', $modelType)
            ->where('role_id', $dariRole)
            ->delete();
    }

    /**
     * Tidak ada rollback otomatis: peran lama sudah digabung dan penugasan
     * spesifik per jenjang tidak dapat direkonstruksi dengan aman.
     */
    public function down(): void
    {
        //
    }
};
