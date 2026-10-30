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
        'nama_panggilan',
        'nik_santri',
        'nisn',
        'jenis_kelamin',
        'tempat_tanggal_lahir',
        'asal_sekolah',
        'nik_ortu',
        'pekerjaan_ortu',
        'alamat_jalan',
        'alamat_rt_rw',
        'alamat_desa_kelurahan',
        'alamat_kecamatan',
        'alamat_kabupaten_kota',
        'alamat_provinsi',
        'nama_ayah',
        'nama_ibu',
        'email',
        'no_hp_ortu',
        'berkas_pasfoto',
        'berkas_kk',
        'berkas_akta_kelahiran',
        'berkas_ijazah',
        'berkas_ktp_ortu',
        'status_pendaftaran',
    ];
}