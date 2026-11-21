<?php

namespace App\Support;

use App\Models\LogAktivitas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CatatanAktivitas
{
    /**
     * Catat perubahan penting (nilai, kelas, role, dokumen, dsb).
     *
     * @param  array<string, mixed>  $data
     */
    public static function catat(
        string $aksi,
        ?Model $model = null,
        array $data = [],
        ?string $alasan = null,
        ?int $userId = null
    ): void {
        try {
            LogAktivitas::create([
                'user_id' => $userId ?? Auth::id(),
                'aksi' => $aksi,
                'model_type' => $model ? $model::class : null,
                'model_id' => $model?->getKey(),
                'data' => $data ?: null,
                'alasan' => $alasan,
                'ip' => request()?->ip(),
                'user_agent' => substr((string) request()?->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Log tidak boleh menggagalkan aksi utama.
            report($e);
        }
    }
}
