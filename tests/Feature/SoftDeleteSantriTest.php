<?php

namespace Tests\Feature;

use App\Models\Izin;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akademik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SoftDeleteSantriTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_menghapus_santri_menjadi_soft_delete(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $pendaftaran = $this->buatPendaftaran(['status_pendaftaran' => 'diterima']);
        $santri = $this->buatSantri('SantriSoftDelete', 'MTs', null, $pendaftaran->id);

        $this->actingAs($admin)
            ->delete(route('admin.santri.destroy', $santri->id))
            ->assertRedirect(route('admin.santri.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('santri', ['id' => $santri->id]);
        $this->assertDatabaseHas('santri', [
            'id' => $santri->id,
            'nama_lengkap' => 'SantriSoftDelete',
        ]);
        $this->assertSame('diterima', $pendaftaran->fresh()->status_pendaftaran);
        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'hapus_santri',
            'model_id' => $santri->id,
        ]);
    }

    public function test_admin_jenjang_tidak_bisa_menghapus_santri(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriAksesHapus', 'MTs');

        $this->actingAs($admin)
            ->delete(route('admin.santri.destroy', $santri->id))
            ->assertForbidden();

        $this->assertNotSoftDeleted('santri', ['id' => $santri->id]);
    }

    public function test_santri_terhapus_hilang_dari_daftar_dan_detail(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $terhapus = $this->buatSantri('SantriSudahDihapus', 'MTs');
        $tersisa = $this->buatSantri('SantriMasihAda', 'MTs');

        $this->actingAs($admin)
            ->delete(route('admin.santri.destroy', $terhapus->id))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.santri.index'))
            ->assertOk()
            ->assertDontSee('SantriSudahDihapus')
            ->assertSee('SantriMasihAda');

        $this->actingAs($admin)
            ->get(route('admin.santri.detail', $terhapus->id))
            ->assertNotFound();
    }

    public function test_superadmin_memulihkan_santri_dari_tempat_sampah(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('SantriDipulihkan', 'MTs');

        $this->actingAs($admin)
            ->delete(route('admin.santri.destroy', $santri->id))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.santri.terhapus'))
            ->assertOk()
            ->assertSee('SantriDipulihkan');

        $this->actingAs($admin)
            ->post(route('admin.santri.pulihkan', $santri->id))
            ->assertSessionHas('success');

        $this->assertNotSoftDeleted('santri', ['id' => $santri->id]);
        $this->assertDatabaseHas('log_aktivitas', [
            'aksi' => 'pulihkan_santri',
            'model_id' => $santri->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.santri.index'))
            ->assertOk()
            ->assertSee('SantriDipulihkan');
    }

    public function test_admin_jenjang_tidak_bisa_mengakses_tempat_sampah_atau_memulihkan(): void
    {
        $pemilik = User::factory()->superAdmin()->create();
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriPulihkanDitolak', 'MTs');

        // Superadmin yang menghapus; Admin Jenjang hanya mencoba membaca/memulihkan.
        $this->actingAs($pemilik)
            ->delete(route('admin.santri.destroy', $santri->id))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.santri.terhapus'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.santri.pulihkan', $santri->id))
            ->assertForbidden();

        $this->assertSoftDeleted('santri', ['id' => $santri->id]);
    }

    public function test_penghapusan_tidak_menghilangkan_nilai_dan_izin(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('SantriRiwayatUtuh', 'MTs');

        $mapel = MataPelajaran::create([
            'jenjang' => 'mts',
            'kode' => 'MP-'.Str::upper(Str::random(8)),
            'nama' => 'Mapel '.Str::upper(Str::random(6)),
            'kkm' => 70,
            'aktif' => true,
        ]);

        $nilai = NilaiSantri::create([
            'santri_id' => $santri->id,
            'mata_pelajaran_id' => $mapel->id,
            'jenjang' => 'mts',
            'kelas' => $santri->kelas,
            'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
            'semester' => Akademik::semesterSekarang(),
            'kategori' => 'uts',
            'nilai' => 88,
            'status' => 'draft',
        ]);

        $izin = Izin::create([
            'santri_id' => $santri->id,
            'user_id' => User::factory()->waliSantri()->create()->id,
            'alasan' => 'Keperluan keluarga',
            'tanggal_pulang' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'penjemput' => 'Ayah Santri',
            'status' => Izin::SELESAI,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.santri.destroy', $santri->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('nilai_santri', ['id' => $nilai->id, 'santri_id' => $santri->id]);
        $this->assertDatabaseHas('izin', ['id' => $izin->id, 'santri_id' => $santri->id]);

        $this->actingAs($admin)
            ->post(route('admin.santri.pulihkan', $santri->id))
            ->assertSessionHas('success');

        $this->assertSame($nilai->id, (int) $nilai->fresh()->id);
        $this->assertSame($izin->id, (int) $izin->fresh()->id);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function buatPendaftaran(array $overrides = []): PendaftaranOnline
    {
        $unik = strtolower(Str::random(8));

        return PendaftaranOnline::create(array_merge([
            'no_pendaftaran' => 'PPDB-'.date('Y').'-'.Str::upper(Str::random(5)),
            'status_pendaftaran' => 'pending',
            'nama_lengkap' => 'Pendaftar '.$unik,
            'tempat_tanggal_lahir' => 'Padang, 1 Januari 2010',
            'jenjang_tujuan' => 'MTs',
            'asal_sekolah' => 'MTs Uji',
            'nama_ortu_wali' => 'Wali '.$unik,
            'no_hp_ortu' => '081234567890',
            'email' => 'wali-'.$unik.'@example.test',
            'email_santri' => 'santri-'.$unik.'@example.test',
            'no_hp_aktif' => '081234567891',
            'alamat_domisili' => 'Jl. Uji No. 1',
            'alamat_sekolah' => 'Jl. Sekolah No. 2',
            'tahun_lulus' => '2026',
            'nisn' => (string) mt_rand(1000000000, 9999999999),
        ], $overrides));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function buatSantri(
        string $nama,
        string $jenjang,
        ?User $wali = null,
        ?int $pendaftaranId = null
    ): Santri {
        return Santri::create([
            'user_id' => $wali?->id,
            'pendaftaran_online_id' => $pendaftaranId,
            'nomor_induk' => 'NIS-'.Str::upper(Str::random(12)),
            'nama_lengkap' => $nama,
            'jenjang_pendidikan' => $jenjang,
            'kelas' => '7A',
            'alamat' => 'Alamat uji',
            'nama_wali' => 'Wali Uji',
            'status_kelulusan' => Santri::STATUS_AKTIF,
        ]);
    }
}
