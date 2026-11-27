<?php

namespace Tests\Feature;

use App\Models\Izin;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use App\Support\KodeUnik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Menjaga identifier tetap unik, termasuk terhadap baris yang dihapus lunak.
 * Format kode sengaja tidak berubah agar data/test lama tetap cocok.
 */
class IdentifierUnikTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Str::createRandomStringsNormally();
        parent::tearDown();
    }

    public function test_nomor_induk_melewati_santri_yang_dihapus_lunak(): void
    {
        $terhapus = $this->buatSantri('SantriTerhapus', 'SANTRI-2026-ABCD');
        $terhapus->delete();

        Str::createRandomStringsUsingSequence(['ABCD', 'EFGH']);

        $this->assertSame('SANTRI-2026-EFGH', KodeUnik::nomorInduk('2026'));
    }

    public function test_no_pendaftaran_melewati_kode_yang_sudah_dipakai(): void
    {
        $this->buatPendaftaran('PPDB-2026-ABCDE');

        Str::createRandomStringsUsingSequence(['ABCDE', 'FGHIJ']);

        $this->assertSame('PPDB-2026-FGHIJ', KodeUnik::pendaftaran('2026'));
    }

    public function test_kode_izin_melewati_kode_yang_sudah_dipakai(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $santri = $this->buatSantri('AnakIzin', 'SANTRI-2026-MNOP');

        Izin::create([
            'santri_id' => $santri->id,
            'user_id' => $wali->id,
            'alasan' => 'Uji kode unik',
            'tanggal_pulang' => '2026-12-01',
            'tanggal_kembali' => '2026-12-02',
            'penjemput' => 'Wali Uji',
            'status' => Izin::MENUNGGU,
            'kode_izin' => 'IZIN-ABCDEFGH',
        ]);

        Str::createRandomStringsUsingSequence(['ABCDEFGH', 'IJKLMNOP']);

        $this->assertSame('IZIN-IJKLMNOP', KodeUnik::kodeIzin());
    }

    private function buatSantri(string $nama, string $nomorInduk): Santri
    {
        return Santri::create([
            'nomor_induk' => $nomorInduk,
            'nama_lengkap' => $nama,
            'jenjang_pendidikan' => 'MTs',
            'kelas' => '7A',
            'alamat' => 'Alamat uji',
            'nama_wali' => 'Wali Uji',
            'status_kelulusan' => Santri::STATUS_AKTIF,
        ]);
    }

    private function buatPendaftaran(string $nomor): PendaftaranOnline
    {
        $unik = strtolower(Str::random(6));

        return PendaftaranOnline::create([
            'no_pendaftaran' => $nomor,
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
        ]);
    }
}
