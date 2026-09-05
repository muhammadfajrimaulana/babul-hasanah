<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranOnline extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_online';

    protected $fillable = [
        'no_pendaftaran',
        'nama_lengkap',
        'nik',
        'tempat_tanggal_lahir',
        'jenjang_tujuan',
        'asal_sekolah',
        'nama_ortu_wali',
        'email',
        'no_hp_ortu',
        'status_pendaftaran',
    ];
}