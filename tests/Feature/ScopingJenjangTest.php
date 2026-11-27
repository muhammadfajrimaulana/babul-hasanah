<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akademik;
use App\Support\Akses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScopingJenjangTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_jenjang_hanya_melihat_santri_pada_jenjangnya(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();

        $santriMts = $this->buatSantri('SantriMtsScoping', 'MTs');
        $santriMa = $this->buatSantri('SantriMaScoping', 'MA');

        $this->actingAs($admin)
            ->get(route('admin.santri.index'))
            ->assertOk()
            ->assertSee('SantriMtsScoping')
            ->assertDontSee('SantriMaScoping');
    }

    public function test_detail_santri_jenjang_lain_ditolak(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();

        $santriMts = $this->buatSantri('SantriMtsDetail', 'MTs');
        $santriMa = $this->buatSantri('SantriMaDetail', 'MA');

        $this->actingAs($admin)
            ->get(route('admin.santri.detail', $santriMa->id))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.santri.detail', $santriMts->id))
            ->assertOk()
            ->assertSee('SantriMtsDetail');
    }

    public function test_policy_santri_dan_nilai_mengikuti_jenjang_admin(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();

        $santriMts = $this->buatSantri('SantriMtsPolicy', 'MTs');
        $santriMa = $this->buatSantri('SantriMaPolicy', 'MA');
        $nilaiMts = $this->buatNilai($santriMts, 'MTs');
        $nilaiMa = $this->buatNilai($santriMa, 'MA');

        $gate = Gate::forUser($admin);

        $this->assertTrue($gate->allows('view', $santriMts));
        $this->assertFalse($gate->allows('view', $santriMa));
        $this->assertTrue($gate->allows('update', $santriMts));
        $this->assertFalse($gate->allows('update', $santriMa));
        $this->assertTrue($gate->allows('update', $nilaiMts));
        $this->assertFalse($gate->allows('update', $nilaiMa));
        $this->assertTrue($gate->allows('bukaKembali', $nilaiMts));
        $this->assertFalse($gate->allows('bukaKembali', $nilaiMa));
    }

    public function test_admin_tanpa_jenjang_fail_closed(): void
    {
        $admin = User::factory()->adminJenjang()->create(['jenjang' => null]);
        $santri = $this->buatSantri('SantriAdminTanpaJenjang', 'MTs');

        $this->assertTrue(Akses::adminTanpaJenjang($admin));

        $this->actingAs($admin)
            ->get(route('admin.santri.index'))
            ->assertOk()
            ->assertDontSee('SantriAdminTanpaJenjang');

        $this->assertFalse(Gate::forUser($admin)->allows('view', $santri));
    }

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

    private function buatNilai(Santri $santri, string $jenjang): NilaiSantri
    {
        $mapel = MataPelajaran::create([
            'jenjang' => $jenjang,
            'kode' => 'MP-'.Str::upper(Str::random(8)),
            'nama' => 'Mapel '.Str::upper(Str::random(6)),
            'kkm' => 70,
            'aktif' => true,
        ]);

        return NilaiSantri::create([
            'santri_id' => $santri->id,
            'mata_pelajaran_id' => $mapel->id,
            'jenjang' => $jenjang,
            'kelas' => $santri->kelas,
            'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
            'semester' => Akademik::semesterSekarang(),
            'kategori' => 'uts',
            'nilai' => 88,
            'status' => 'draft',
        ]);
    }
}
