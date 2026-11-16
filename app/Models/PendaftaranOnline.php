<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PendaftaranOnline extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_online';

    /** Kolom yang wajib ada agar form PPDB bisa menyimpan data. */
    public const KOLOM_FORM = [
        'nama_lengkap',
        'tempat_tanggal_lahir',
        'no_hp_aktif',
        'alamat_domisili',
        'email',
        'nama_ortu_wali',
        'no_hp_ortu',
        'asal_sekolah',
        'alamat_sekolah',
        'tahun_lulus',
        'nisn',
        'jenjang_tujuan',
    ];

    /**
     * Kolom form PPDB yang belum ada di tabel (biasanya karena migrasi
     * penyelaras belum dijalankan). Kosong berarti skema sudah siap.
     *
     * @return array<int, string>
     */
    public static function kolomFormHilang(): array
    {
        if (! Schema::hasTable((new static)->getTable())) {
            return self::KOLOM_FORM;
        }

        return array_values(array_filter(
            self::KOLOM_FORM,
            fn (string $kolom) => ! Schema::hasColumn((new static)->getTable(), $kolom)
        ));
    }

    /** Pesan bantuan saat skema database belum lengkap. */
    public static function pesanSkemaBelumSiap(array $kolomHilang): string
    {
        return 'Struktur database belum lengkap. Kolom yang belum ada: '
            . implode(', ', $kolomHilang)
            . '. Jalankan "php artisan migrate" lalu coba simpan kembali.';
    }

    protected $fillable = [
        'no_pendaftaran',
        'nama_lengkap',
        'nama_panggilan',
        'nik_santri',
        'nisn',
        'jenis_kelamin',
        'tempat_tanggal_lahir',
        'jenjang_tujuan',
        'asal_sekolah',
        'alamat_sekolah',
        'tahun_lulus',
        'no_hp_aktif',
        'alamat_domisili',
        'nama_ortu_wali',
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