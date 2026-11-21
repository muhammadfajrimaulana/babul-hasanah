<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pendukung alur final pesantren (tanpa modul keuangan):
 * guru & wali kelas, riwayat kelas, pembinaan, izin pulang, pengumuman,
 * dokumen alumni, legalisir, tracer study, log aktivitas, dan pengajuan wali.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guru')) {
            Schema::create('guru', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('nip', 50)->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id();
                $table->string('jenjang', 100);
                $table->string('nama', 50);
                $table->unsignedTinyInteger('tingkat')->nullable();
                $table->string('tahun_ajaran', 20);
                // Satu kelas hanya memiliki satu wali kelas.
                $table->foreignId('wali_kelas_id')->nullable()->constrained('guru')->nullOnDelete();
                $table->boolean('aktif')->default(true);
                $table->timestamps();

                $table->unique(['jenjang', 'nama', 'tahun_ajaran'], 'kelas_unik');
                $table->index(['jenjang', 'tahun_ajaran'], 'kelas_jenjang_tahun_idx');
            });
        }

        if (Schema::hasTable('santri')) {
            Schema::table('santri', function (Blueprint $table) {
                if (! Schema::hasColumn('santri', 'kelas_id')) {
                    $table->foreignId('kelas_id')->nullable()->after('kelas')
                        ->constrained('kelas')->nullOnDelete();
                }

                if (! Schema::hasColumn('santri', 'kamar')) {
                    $table->string('kamar', 50)->nullable()->after('kelas_id');
                }
            });
        }

        if (! Schema::hasTable('santri_wali')) {
            Schema::create('santri_wali', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('hubungan', 20)->default('wali'); // ayah | ibu | wali
                $table->boolean('utama')->default(false);
                $table->timestamps();

                $table->unique(['santri_id', 'user_id'], 'santri_wali_unik');
                $table->index(['user_id', 'santri_id'], 'santri_wali_user_idx');
            });
        }

        if (! Schema::hasTable('riwayat_kelas')) {
            Schema::create('riwayat_kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
                $table->string('jenjang', 100);
                $table->string('kelas', 50)->nullable();
                $table->string('kamar', 50)->nullable();
                $table->string('tahun_ajaran', 20);
                $table->string('semester', 10)->default('1');
                // naik | tinggal | pindah | keluar | lulus
                $table->string('status', 20);
                $table->text('keterangan')->nullable();
                $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['santri_id', 'tahun_ajaran', 'semester'], 'riwayat_santri_idx');
            });
        }

        if (! Schema::hasTable('hafalan')) {
            Schema::create('hafalan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->date('tanggal');
                $table->string('jenis', 30)->default('setoran'); // setoran | ziyadah | murojaah
                $table->string('surat', 100)->nullable();
                $table->string('ayat', 50)->nullable();
                $table->string('juz', 10)->nullable();
                $table->decimal('nilai', 5, 2)->nullable();
                $table->string('predikat', 30)->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['santri_id', 'tanggal'], 'hafalan_santri_idx');
            });
        }

        if (! Schema::hasTable('pelanggaran')) {
            Schema::create('pelanggaran', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->date('tanggal');
                $table->string('jenis', 100);
                $table->text('deskripsi')->nullable();
                $table->unsignedSmallInteger('poin')->default(0);
                $table->text('tindakan')->nullable();
                $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['santri_id', 'tanggal'], 'pelanggaran_santri_idx');
            });
        }

        if (! Schema::hasTable('izin')) {
            Schema::create('izin', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // wali pengaju
                $table->text('alasan');
                $table->date('tanggal_pulang');
                $table->date('tanggal_kembali');
                $table->string('penjemput', 150);
                // menunggu | disetujui | ditolak | selesai | terlambat
                $table->string('status', 20)->default('menunggu');
                $table->text('alasan_penolakan')->nullable();
                $table->string('kode_izin', 40)->nullable()->unique();
                $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('disetujui_pada')->nullable();
                $table->timestamp('kembali_pada')->nullable();
                $table->timestamps();

                $table->index(['status', 'tanggal_kembali'], 'izin_status_idx');
                $table->index(['santri_id', 'status'], 'izin_santri_idx');
            });
        }

        if (! Schema::hasTable('pengumuman')) {
            Schema::create('pengumuman', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('isi');
                $table->date('tanggal');
                // null = semua jenjang
                $table->string('target_jenjang', 100)->nullable();
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['target_jenjang', 'tanggal'], 'pengumuman_target_idx');
            });
        }

        if (! Schema::hasTable('dokumen_santri')) {
            Schema::create('dokumen_santri', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                // ijazah | skl | transkrip | sertifikat_tahfidz | sertifikat_kegiatan
                $table->string('jenis', 40);
                $table->string('file_path');
                $table->string('file_name', 200);
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('status', 20)->default('published');
                $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['santri_id', 'jenis'], 'dokumen_santri_unik');
            });
        }

        if (! Schema::hasTable('tracer_study')) {
            Schema::create('tracer_study', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alumni_id')->unique()->constrained('alumni')->cascadeOnDelete();
                $table->string('kegiatan', 50)->nullable(); // Kuliah | Bekerja | Wirausaha | Mengabdi | Lainnya
                $table->string('instansi')->nullable();
                $table->text('alamat')->nullable();
                $table->string('kontak', 100)->nullable();
                $table->unsignedSmallInteger('tahun_mulai')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('legalisir')) {
            Schema::create('legalisir', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alumni_id')->constrained('alumni')->cascadeOnDelete();
                $table->string('jenis_dokumen', 100);
                $table->unsignedSmallInteger('jumlah')->default(1);
                $table->string('status', 20)->default('menunggu'); // menunggu | diproses | selesai | ditolak
                $table->string('file_hasil')->nullable();
                $table->text('catatan')->nullable();
                $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['alumni_id', 'status'], 'legalisir_alumni_idx');
            });
        }

        if (! Schema::hasTable('log_aktivitas')) {
            Schema::create('log_aktivitas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('aksi', 100);
                $table->string('model_type', 150)->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->json('data')->nullable();
                $table->text('alasan')->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id'], 'log_model_idx');
                $table->index(['user_id', 'created_at'], 'log_user_idx');
            });
        }

        if (! Schema::hasTable('pengajuan_perubahan')) {
            Schema::create('pengajuan_perubahan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('bidang', 100);
                $table->text('nilai_lama')->nullable();
                $table->text('nilai_baru');
                $table->text('alasan')->nullable();
                $table->string('status', 20)->default('menunggu'); // menunggu | disetujui | ditolak
                $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->text('catatan_admin')->nullable();
                $table->timestamps();

                $table->index(['status', 'created_at'], 'pengajuan_status_idx');
            });
        }

        if (Schema::hasTable('absensi') && ! Schema::hasColumn('absensi', 'dicatat_oleh')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->foreignId('dicatat_oleh')->nullable()->after('tanggal')
                    ->constrained('users')->nullOnDelete();
            });
        }

        // Nomor ijazah final untuk dashboard alumni.
        if (Schema::hasTable('alumni') && ! Schema::hasColumn('alumni', 'nomor_ijazah')) {
            Schema::table('alumni', function (Blueprint $table) {
                $table->string('nomor_ijazah', 100)->nullable()->after('tahun_lulus');
            });
        }

        // Migrasikan tautan wali lama (santri.user_id) ke tabel pivot.
        if (Schema::hasTable('santri_wali') && Schema::hasTable('santri')) {
            foreach (DB::table('santri')->whereNotNull('user_id')->get() as $santri) {
                DB::table('santri_wali')->insertOrIgnore([
                    'santri_id' => $santri->id,
                    'user_id' => $santri->user_id,
                    'hubungan' => 'wali',
                    'utama' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumni') && Schema::hasColumn('alumni', 'nomor_ijazah')) {
            Schema::table('alumni', fn (Blueprint $table) => $table->dropColumn('nomor_ijazah'));
        }

        if (Schema::hasTable('absensi') && Schema::hasColumn('absensi', 'dicatat_oleh')) {
            Schema::table('absensi', fn (Blueprint $table) => $table->dropConstrainedForeignId('dicatat_oleh'));
        }

        foreach ([
            'pengajuan_perubahan',
            'log_aktivitas',
            'legalisir',
            'tracer_study',
            'dokumen_santri',
            'pengumuman',
            'izin',
            'pelanggaran',
            'hafalan',
            'riwayat_kelas',
            'santri_wali',
        ] as $tabel) {
            Schema::dropIfExists($tabel);
        }

        if (Schema::hasTable('santri')) {
            Schema::table('santri', function (Blueprint $table) {
                if (Schema::hasColumn('santri', 'kamar')) {
                    $table->dropColumn('kamar');
                }

                if (Schema::hasColumn('santri', 'kelas_id')) {
                    $table->dropConstrainedForeignId('kelas_id');
                }
            });
        }

        Schema::dropIfExists('kelas');
        Schema::dropIfExists('guru');
    }
};
