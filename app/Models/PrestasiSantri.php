<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiSantri extends Model
{
    protected $table = 'prestasi_santri';

    protected $fillable = [
        'santri_id', 'jenjang', 'judul', 'nama', 'kelas', 'kategori', 'tingkat',
        'wilayah', 'tahun', 'peringkat', 'label', 'deskripsi', 'dicatat_oleh',
    ];

    protected $casts = [
        'tahun'     => 'integer',
        'peringkat' => 'integer',
    ];

    /** Santri pemilik prestasi (bila dicatat dari dashboard jenjang). */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /** Admin yang mencatat prestasi. */
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
