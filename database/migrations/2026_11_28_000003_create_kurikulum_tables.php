<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('kurikulum')) {
            Schema::create('kurikulum', function (Blueprint $table) {
                $table->id();

                $table->string('jenjang', 100);
                $table->string('tahun_ajaran', 20);
                $table->string('semester', 10);
                $table->string('nama', 150);
                $table->text('deskripsi')->nullable();
                $table->string('status', 20)->default('draft');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();

                $table->unique(
                    ['jenjang', 'tahun_ajaran', 'semester', 'nama'],
                    'kurikulum_unik'
                );
            });
        }

        if (! Schema::hasTable('kurikulum_item')) {
            Schema::create('kurikulum_item', function (Blueprint $table) {
                $table->id();

                $table->foreignId('kurikulum_id')->constrained('kurikulum')->cascadeOnDelete();
                $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
                $table->unsignedInteger('kkm')->default(70);
                $table->text('target_kd')->nullable();
                $table->unsignedInteger('urutan')->default(0);

                $table->timestamps();

                $table->unique(
                    ['kurikulum_id', 'mata_pelajaran_id'],
                    'kurikulum_item_unik'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum_item');
        Schema::dropIfExists('kurikulum');
    }
};
