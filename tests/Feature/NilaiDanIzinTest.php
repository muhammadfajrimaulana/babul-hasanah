<?php

namespace Tests\Feature;

use App\Models\Izin;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akademik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NilaiDanIzinTest extends TestCase
{
    use RefreshDatabase;

    /* ============================================================== Nilai */

    public function test_admin_jenjang_menyimpan_nilai_sebagai_draft(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriNilaiDraft', 'MTs');
        $mapel = $this->buatMapel('mts');

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.store'), $this->konteks($mapel) + [
                'nilai' => [$santri->id => '87.5'],
                'catatan' => [$santri->id => 'Rajin membaca'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $nilai = NilaiSantri::where('santri_id', $santri->id)->firstOrFail();

        $this->assertSame('draft', $nilai->status);
        $this->assertSame('mts', $nilai->jenjang);
        $this->assertSame('7A', $nilai->kelas);
        $this->assertSame('Rajin membaca', $nilai->catatan);
        $this->assertSame($mapel->id, $nilai->mata_pelajaran_id);
        $this->assertSame($admin->id, $nilai->created_by);
        $this->assertNull($nilai->published_at);
        $this->assertEqualsWithDelta(87.5, (float) $nilai->nilai, 0.001);
    }

    public function test_mengubah_nilai_yang_sudah_terbit_mengembalikannya_ke_draft(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriNilaiTerbit', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'published', 90);

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.store'), $this->konteks($mapel) + [
                'nilai' => [$santri->id => '95'],
            ])
            ->assertSessionHas('success');

        $nilai->refresh();

        $this->assertSame('draft', $nilai->status);
        $this->assertNull($nilai->published_at);
        $this->assertNull($nilai->diterbitkan_oleh);
        $this->assertStringContainsString('dikembalikan', session('success'));
    }

    public function test_nilai_draft_yang_dikosongkan_dihapus(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriNilaiKosong', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'draft', 80);

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.store'), $this->konteks($mapel) + [
                'nilai' => [$santri->id => ''],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('nilai_santri', ['id' => $nilai->id]);
    }

    public function test_admin_tanpa_jenjang_tidak_bisa_input_nilai(): void
    {
        $admin = User::factory()->adminJenjang()->create(['jenjang' => null]);
        $santri = $this->buatSantri('SantriAdminTanpaJenjangNilai', 'MTs');
        $mapel = $this->buatMapel('mts');

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.store'), $this->konteks($mapel) + [
                'nilai' => [$santri->id => '85'],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('nilai_santri', 0);
    }

    public function test_mata_pelajaran_dari_jenjang_lain_ditolak(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriSalahMapel', 'MTs');
        $mapel = $this->buatMapel('ma');

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.store'), $this->konteks($mapel) + [
                'nilai' => [$santri->id => '85'],
            ])
            ->assertSessionHasErrors('mata_pelajaran_id');

        $this->assertDatabaseCount('nilai_santri', 0);
    }

    public function test_superadmin_menerbitkan_nilai_draft(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('SantriTerbitNilai', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'draft', 88);

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.terbitkan'), $this->konteks($mapel))
            ->assertSessionHas('success');

        $nilai->refresh();

        $this->assertSame('published', $nilai->status);
        $this->assertNotNull($nilai->published_at);
        $this->assertSame($admin->id, $nilai->diterbitkan_oleh);
    }

    public function test_pengurus_tidak_bisa_menerbitkan_nilai(): void
    {
        $pengurus = User::factory()->pengurus()->create();
        $santri = $this->buatSantri('SantriTerbitDitolak', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'draft', 88);

        $this->actingAs($pengurus)
            ->post(route('admin.akademik.nilai.terbitkan'), $this->konteks($mapel))
            ->assertForbidden();

        $this->assertSame('draft', $nilai->fresh()->status);
    }

    public function test_buka_kembali_nilai_wajib_alasan_dan_dicatat(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('SantriBukaKembali', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'published', 92);

        $this->actingAs($admin)
            ->get(route('admin.akademik.nilai.buka-kembali.form', $nilai))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.buka-kembali', $nilai), ['alasan' => 'sala'])
            ->assertSessionHasErrors('alasan');

        $this->assertSame('published', $nilai->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.buka-kembali', $nilai), [
                'alasan' => 'Nilai salah input',
            ])
            ->assertSessionHas('success');

        $nilai->refresh();

        $this->assertSame('draft', $nilai->status);
        $this->assertNull($nilai->published_at);
        $this->assertNull($nilai->diterbitkan_oleh);
        $this->assertSame($admin->id, $nilai->diperbarui_oleh);
    }

    public function test_admin_jenjang_lain_tidak_bisa_membuka_kembali_nilai(): void
    {
        $admin = User::factory()->adminJenjang('ma')->create();
        $santri = $this->buatSantri('SantriNilaiJenjangLain', 'MTs');
        $mapel = $this->buatMapel('mts');
        $nilai = $this->buatNilai($santri, $mapel, 'published', 92);

        $this->actingAs($admin)
            ->post(route('admin.akademik.nilai.buka-kembali', $nilai), [
                'alasan' => 'Nilai perlu diperbaiki',
            ])
            ->assertForbidden();

        $this->assertSame('published', $nilai->fresh()->status);
    }

    /* =============================================================== Izin */

    public function test_wali_mengajukan_izin_untuk_anak_sendiri(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $santri = $this->buatSantri('AnakIzinSendiri', 'MTs', $wali);

        $this->actingAs($wali)
            ->post(route('portal.izin.store'), $this->dataIzin($santri->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('izin', [
            'santri_id' => $santri->id,
            'user_id' => $wali->id,
            'status' => Izin::MENUNGGU,
            'alasan' => 'Keperluan keluarga',
            'penjemput' => 'Ayah Santri',
        ]);
    }

    public function test_wali_tidak_bisa_mengajukan_izin_anak_orang_lain(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $santri = $this->buatSantri('AnakIzinOrangLain', 'MTs');

        $this->actingAs($wali)
            ->post(route('portal.izin.store'), $this->dataIzin($santri->id))
            ->assertForbidden();

        $this->assertDatabaseCount('izin', 0);
    }

    public function test_izin_yang_masih_aktif_mencegah_pengajuan_baru(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $santri = $this->buatSantri('AnakIzinAktif', 'MTs', $wali);
        $this->buatIzin($santri, Izin::MENUNGGU, $wali);

        $this->actingAs($wali)
            ->post(route('portal.izin.store'), $this->dataIzin($santri->id))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('izin', 1);
    }

    public function test_admin_menyetujui_izin_dan_menerbitkan_kode(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('AnakIzinDisetujui', 'MTs');
        $izin = $this->buatIzin($santri, Izin::MENUNGGU);

        $this->actingAs($admin)
            ->patch(route('admin.izin.setujui', $izin))
            ->assertSessionHas('success');

        $izin->refresh();

        $this->assertSame(Izin::DISETUJUI, $izin->status);
        $this->assertMatchesRegularExpression('/^IZIN-[A-Z0-9]{8}$/', $izin->kode_izin);
        $this->assertSame($admin->id, $izin->disetujui_oleh);
        $this->assertNotNull($izin->disetujui_pada);
    }

    public function test_tolak_izin_wajib_alasan(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('AnakIzinDitolak', 'MTs');
        $izin = $this->buatIzin($santri, Izin::MENUNGGU);

        $this->actingAs($admin)
            ->patch(route('admin.izin.tolak', $izin), [])
            ->assertSessionHasErrors('alasan_penolakan');

        $this->assertSame(Izin::MENUNGGU, $izin->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('admin.izin.tolak', $izin), [
                'alasan_penolakan' => 'Jadwal pulang bertabrakan',
            ])
            ->assertSessionHas('success');

        $izin->refresh();

        $this->assertSame(Izin::DITOLAK, $izin->status);
        $this->assertSame('Jadwal pulang bertabrakan', $izin->alasan_penolakan);
        $this->assertNull($izin->kode_izin);
    }

    public function test_tanda_kembali_hanya_untuk_izin_yang_disetujui(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $santri = $this->buatSantri('AnakIzinKembali', 'MTs');
        $izin = $this->buatIzin($santri, Izin::MENUNGGU);

        $this->actingAs($admin)
            ->patch(route('admin.izin.kembali', $izin))
            ->assertSessionHas('error');

        $this->assertSame(Izin::MENUNGGU, $izin->fresh()->status);

        $this->actingAs($admin)
            ->patch(route('admin.izin.setujui', $izin))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->patch(route('admin.izin.kembali', $izin))
            ->assertSessionHas('success');

        $izin->refresh();

        $this->assertSame(Izin::SELESAI, $izin->status);
        $this->assertNotNull($izin->kembali_pada);
    }

    public function test_admin_jenjang_lain_tidak_bisa_memverifikasi_izin(): void
    {
        $admin = User::factory()->adminJenjang('ma')->create();
        $santri = $this->buatSantri('AnakIzinJenjangLain', 'MTs');
        $izin = $this->buatIzin($santri, Izin::MENUNGGU);

        $this->actingAs($admin)
            ->patch(route('admin.izin.setujui', $izin))
            ->assertForbidden();

        $this->assertSame(Izin::MENUNGGU, $izin->fresh()->status);
    }

    /* ============================================================= Helper */

    private function buatSantri(string $nama, string $jenjang, ?User $wali = null): Santri
    {
        return Santri::create([
            'user_id' => $wali?->id,
            'nomor_induk' => 'NIS-'.Str::upper(Str::random(12)),
            'nama_lengkap' => $nama,
            'jenjang_pendidikan' => $jenjang,
            'kelas' => '7A',
            'alamat' => 'Alamat uji',
            'nama_wali' => 'Wali Uji',
            'status_kelulusan' => 'aktif',
        ]);
    }

    private function buatMapel(string $jenjang): MataPelajaran
    {
        return MataPelajaran::create([
            'jenjang' => $jenjang,
            'kode' => 'MP-'.Str::upper(Str::random(8)),
            'nama' => 'Mapel '.Str::upper(Str::random(6)),
            'kkm' => 70,
            'aktif' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function konteks(MataPelajaran $mapel): array
    {
        return [
            'jenjang' => $mapel->jenjang,
            'kelas' => '7A',
            'mata_pelajaran_id' => $mapel->id,
            'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
            'semester' => Akademik::semesterSekarang(),
            'kategori' => 'tugas',
        ];
    }

    private function buatNilai(
        Santri $santri,
        MataPelajaran $mapel,
        string $status = 'draft',
        float $nilai = 85
    ): NilaiSantri {
        return NilaiSantri::create([
            'santri_id' => $santri->id,
            'mata_pelajaran_id' => $mapel->id,
            'jenjang' => $mapel->jenjang,
            'kelas' => $santri->kelas,
            'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
            'semester' => Akademik::semesterSekarang(),
            'kategori' => 'tugas',
            'nilai' => $nilai,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }

    private function buatIzin(
        Santri $santri,
        string $status = Izin::MENUNGGU,
        ?User $wali = null
    ): Izin {
        return Izin::create([
            'santri_id' => $santri->id,
            'user_id' => $wali?->id ?? User::factory()->waliSantri()->create()->id,
            'alasan' => 'Keperluan keluarga',
            'tanggal_pulang' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'penjemput' => 'Ayah Santri',
            'status' => $status,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataIzin(int $santriId): array
    {
        return [
            'santri_id' => $santriId,
            'alasan' => 'Keperluan keluarga',
            'tanggal_pulang' => now()->toDateString(),
            'tanggal_kembali' => now()->addDay()->toDateString(),
            'penjemput' => 'Ayah Santri',
        ];
    }
}
