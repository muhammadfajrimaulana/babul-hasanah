<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('rapor_santri')) {
            return;
        }

        Schema::create('rapor_santri', function (Blueprint $table) {
            $table->id();

            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->string('jenjang', 100);
            $table->string('kelas', 50);
            $table->string('tahun_ajaran', 20);
            $table->string('semester', 10);

            $table->decimal('rata_rata', 5, 2);
            $table->unsignedInteger('peringkat')->nullable();
            $table->unsignedInteger('jumlah_santri')->default(0);
            $table->text('catatan_wali_kelas')->nullable();

            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['santri_id', 'tahun_ajaran', 'semester'],
                'rapor_santri_unik'
            );
            $table->index(
                ['jenjang', 'kelas', 'tahun_ajaran', 'semester', 'status'],
                'rapor_scope_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_santri');
    }
};
