<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\Santri;
use App\Models\User;
use App\Support\Akademik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortalKepemilikanAnakTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_hanya_melihat_anak_sendiri_di_portal_nilai(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $waliLain = User::factory()->waliSantri()->create();

        $anakSaya = $this->buatSantri('AnakSayaPortal', 'MTs', $wali);
        $anakLain = $this->buatSantri('AnakLainPortal', 'MA', $waliLain);

        $this->actingAs($wali)
            ->get(route('portal.nilai', ['anak' => $anakSaya->id]))
            ->assertOk()
            ->assertSee('AnakSayaPortal')
            ->assertDontSee('AnakLainPortal');
    }

    public function test_anak_orang_lain_tidak_bisa_dibuka_lewat_url(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $waliLain = User::factory()->waliSantri()->create();
        $anakSaya = $this->buatSantri('AnakSayaUrl', 'MTs', $wali);
        $anakLain = $this->buatSantri('AnakLainUrl', 'MA', $waliLain);

        // Pastikan daftar anak tidak kosong: kalau kosong, pilihAnak()
        // return lebih awal dan tidak sempat menolak parameter ?anak=.
        $this->assertTrue($wali->memilikiSantri($anakSaya->id));

        $this->actingAs($wali)
            ->get(route('portal.nilai', ['anak' => $anakLain->id]))
            ->assertForbidden();

        $this->actingAs($wali)
            ->get(route('portal.anak.show', $anakLain))
            ->assertForbidden();

        $this->assertFalse(Gate::forUser($wali)->allows('portal.lihatAnak', $anakLain));
        $this->assertFalse(Gate::forUser($wali)->allows('portal.lihatAnakId', $anakLain->id));
    }

    public function test_portal_hanya_menampilkan_nilai_published(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $anak = $this->buatSantri('AnakNilaiPortal', 'MTs', $wali);

        $mapelTerbit = $this->buatMapel('MTs', 'MapelTerbitPortal');
        $mapelDraft = $this->buatMapel('MTs', 'MapelDraftPortal');

        $this->buatNilai($anak, $mapelTerbit, 'published', 92);
        $this->buatNilai($anak, $mapelDraft, 'draft', 40);

        $this->actingAs($wali)
            ->get(route('portal.nilai', ['anak' => $anak->id]))
            ->assertOk()
            ->assertSee('MapelTerbitPortal')
            ->assertDontSee('MapelDraftPortal');

        $this->actingAs($wali)
            ->get(route('portal.anak.show', $anak))
            ->assertOk()
            ->assertSee('MapelTerbitPortal')
            ->assertDontSee('MapelDraftPortal');
    }

    private function buatSantri(string $nama, string $jenjang, User $wali): Santri
    {
        return Santri::create([
            'user_id' => $wali->id,
            'nomor_induk' => 'NIS-'.Str::upper(Str::random(12)),
            'nama_lengkap' => $nama,
            'jenjang_pendidikan' => $jenjang,
            'kelas' => '7A',
            'alamat' => 'Alamat uji',
            'nama_wali' => 'Wali Uji',
            'status_kelulusan' => 'aktif',
        ]);
    }

    private function buatMapel(string $jenjang, string $nama): MataPelajaran
    {
        return MataPelajaran::create([
            'jenjang' => $jenjang,
            'kode' => 'MP-'.Str::upper(Str::random(8)),
            'nama' => $nama,
            'kkm' => 70,
            'aktif' => true,
        ]);
    }

    private function buatNilai(
        Santri $santri,
        MataPelajaran $mapel,
        string $status,
        int $angka
    ): NilaiSantri {
        return NilaiSantri::create([
            'santri_id' => $santri->id,
            'mata_pelajaran_id' => $mapel->id,
            'jenjang' => $santri->jenjang_pendidikan,
            'kelas' => $santri->kelas,
            'tahun_ajaran' => Akademik::tahunAjaranSekarang(),
            'semester' => Akademik::semesterSekarang(),
            'kategori' => 'uts',
            'nilai' => $angka,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }
}
