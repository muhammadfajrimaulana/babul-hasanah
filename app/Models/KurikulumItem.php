<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KurikulumItem extends Model
{
    protected $table = 'kurikulum_item';

    protected $fillable = [
        'kurikulum_id', 'mata_pelajaran_id', 'kkm', 'target_kd', 'urutan',
    ];

    protected $casts = [
        'kkm' => 'integer',
        'urutan' => 'integer',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}
