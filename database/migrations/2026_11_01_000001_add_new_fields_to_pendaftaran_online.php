<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            // Menambah kolom baru
            $table->string('nama_panggilan')->nullable()->after('nama_lengkap');
            $table->string('nik_santri')->unique()->nullable()->after('nama_panggilan');
            $table->string('nisn')->nullable()->after('nik_santri');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('nisn');
            
            // Alamat lengkap
            $table->string('alamat_jalan')->nullable()->after('jenis_kelamin');
            $table->string('alamat_rt_rw')->nullable()->after('alamat_jalan');
            $table->string('alamat_desa_kelurahan')->nullable()->after('alamat_rt_rw');
            $table->string('alamat_kecamatan')->nullable()->after('alamat_desa_kelurahan');
            $table->string('alamat_kabupaten_kota')->nullable()->after('alamat_kecamatan');
            $table->string('alamat_provinsi')->nullable()->after('alamat_kabupaten_kota');
            
            // Data Orang Tua/Wali
            $table->string('nik_ortu')->nullable()->after('alamat_provinsi');
            $table->string('pekerjaan_ortu')->nullable()->after('nik_ortu');
            $table->string('nama_ayah')->nullable()->after('pekerjaan_ortu');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
            
            // Berkas digital (nama file yang diupload)
            $table->string('berkas_pasfoto')->nullable()->after('nama_ibu');
            $table->string('berkas_kk')->nullable()->after('berkas_pasfoto');
            $table->string('berkas_akta_kelahiran')->nullable()->after('berkas_kk');
            $table->string('berkas_ijazah')->nullable()->after('berkas_akta_kelahiran');
            $table->string('berkas_ktp_ortu')->nullable()->after('berkas_ijazah');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_online', function (Blueprint $table) {
            // Mengembalikan perubahan (hapus kolom baru)
            $table->dropColumn([
                'nama_panggilan',
                'nik_santri',
                'nisn',
                'jenis_kelamin',
                'alamat_jalan',
                'alamat_rt_rw',
                'alamat_desa_kelurahan',
                'alamat_kecamatan',
                'alamat_kabupaten_kota',
                'alamat_provinsi',
                'nik_ortu',
                'pekerjaan_ortu',
                'nama_ayah',
                'nama_ibu',
                'berkas_pasfoto',
                'berkas_kk',
                'berkas_akta_kelahiran',
                'berkas_ijazah',
                'berkas_ktp_ortu',
            ]);
        });
    }
}