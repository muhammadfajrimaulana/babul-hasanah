<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah jenis kelamin ke data santri agar komposisi putra/putri pada
     * dashboard tidak lagi nol. Data lama diisi dari arsip PPDB bila ada.
     */
    public function up(): void
    {
        if (! Schema::hasTable('santri')) {
            return;
        }

        if (! Schema::hasColumn('santri', 'jenis_kelamin')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->enum('jenis_kelamin', ['L', 'P'])
                    ->nullable()
                    ->after('nama_lengkap');
            });
        }

        if (Schema::hasTable('pendaftaran_online')
            && Schema::hasColumn('pendaftaran_online', 'jenis_kelamin')
            && Schema::hasColumn('santri', 'pendaftaran_online_id')) {
            // SQLite tidak menerima UPDATE ... JOIN seperti MySQL (klausa SET
            // tidak bisa menyebut tabel hasil join). Subquery berkorelasi ini
            // berjalan di kedua driver dan hasilnya identik.
            DB::table('santri')
                ->whereNull('santri.jenis_kelamin')
                ->whereNotNull('santri.pendaftaran_online_id')
                ->update([
                    'jenis_kelamin' => DB::raw(
                        "(select po.jenis_kelamin from pendaftaran_online po "
                        ."where po.id = santri.pendaftaran_online_id "
                        ."and po.jenis_kelamin in ('L', 'P') limit 1)"
                    ),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('santri') && Schema::hasColumn('santri', 'jenis_kelamin')) {
            Schema::table('santri', function (Blueprint $table) {
                $table->dropColumn('jenis_kelamin');
            });
        }
    }
};
