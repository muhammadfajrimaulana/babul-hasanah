<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporSantri extends Model
{
    protected $table = 'rapor_santri';

    protected $fillable = [
        'santri_id', 'jenjang', 'kelas', 'tahun_ajaran', 'semester',
        'rata_rata', 'peringkat', 'jumlah_santri', 'catatan_wali_kelas',
        'status', 'created_by', 'diperbarui_oleh', 'diterbitkan_oleh', 'published_at',
    ];

    protected $casts = [
        'rata_rata' => 'decimal:2',
        'peringkat' => 'integer',
        'jumlah_santri' => 'integer',
        'published_at' => 'datetime',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public static function siap(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('rapor_santri');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
