<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $table = 'pelanggaran';

    protected $fillable = [
        'santri_id', 'tanggal', 'jenis', 'deskripsi', 'poin', 'tindakan', 'dicatat_oleh',
    ];

    protected $casts = ['tanggal' => 'date', 'poin' => 'integer'];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
