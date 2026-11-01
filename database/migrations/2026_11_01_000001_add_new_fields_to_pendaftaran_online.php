<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Daftar kolom detail pendaftaran. Semua nullable agar form PPDB
     * boleh mengirim sebagian data tanpa melanggar constraint lama.
     */
    private array $kolom = [
        'nama_panggilan', 'nik_santri', 'nisn', 'jenis_kelamin',
        'nama_ayah', 'nama_ibu', 'nik_ortu', 'pekerjaan_ortu', 'email',
        'alamat_jalan', 'alamat_rt_rw', 'alamat_desa_kelurahan',
        'alamat_kecamatan', 'alamat_kabupaten_kota', 'alamat_provinsi',
        'berkas_pasfoto', 'berkas_kk', 'berkas_akta_kelahiran',
        'berkas_ijazah', 'berkas_ktp_ortu',
    ];

    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            foreach ($this->kolom as $nama) {
                if (! Schema::hasColumn('pendaftaran_online', $nama)) {
                    $kolom = $table->string($nama)->nullable();

                    if ($nama === 'nik_santri') {
                        $kolom->unique();
                    }
                }
            }
        });

        // Kolom lama yang tidak lagi wajib diisi.
        foreach (['nik', 'nik_santri', 'nama_ortu_wali'] as $nama) {
            if (Schema::hasColumn('pendaftaran_online', $nama)) {
                Schema::table('pendaftaran_online', function (Blueprint $table) use ($nama) {
                    $table->string($nama)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            foreach ($this->kolom as $nama) {
                if (Schema::hasColumn('pendaftaran_online', $nama)) {
                    if ($nama === 'nik_santri') {
                        $table->dropUnique(['nik_santri']);
                    }
                    $table->dropColumn($nama);
                }
            }
        });
    }
};
