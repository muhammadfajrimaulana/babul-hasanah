<?php

namespace Tests\Feature;

use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PencarianSantriTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencarian_menemukan_santri_berdasarkan_nama(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->buatSantri('Ahmad Fauzan', 'MTs');
        $this->buatSantri('Siti Aminah', 'MTs');

        $this->actingAs($admin)
            ->get(route('admin.santri.index', ['q' => 'Fauzan']))
            ->assertOk()
            ->assertSee('Ahmad Fauzan')
            ->assertDontSee('Siti Aminah');
    }

    public function test_pencarian_menemukan_santri_berdasarkan_nomor_induk(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('Santri NIS', 'MTs');
        $this->buatSantri('Santri Lain', 'MA');

        $this->actingAs($admin)
            ->get(route('admin.santri.index', ['q' => $santri->nomor_induk]))
            ->assertOk()
            ->assertSee('Santri NIS')
            ->assertDontSee('Santri Lain');
    }

    public function test_pencarian_menemukan_email_wali_dari_pendaftaran(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $unik = strtolower(Str::random(8));
        $pendaftaran = $this->buatPendaftaran([
            'email' => 'wali.'.$unik.'@example.test',
        ]);
        $santri = $this->buatSantri('Santri Email Wali', 'MTs', null, $pendaftaran->id);
        $this->buatSantri('Santri Tanpa Email Itu', 'MTs');

        $this->actingAs($admin)
            ->get(route('admin.santri.index', ['q' => 'wali.'.$unik]))
            ->assertOk()
            ->assertSee('Santri Email Wali')
            ->assertDontSee('Santri Tanpa Email Itu');
    }

    public function test_pencarian_dan_filter_jenjang_saling_menyaring(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $this->buatSantri('Sama di MTs', 'MTs');
        $this->buatSantri('Sama di MA', 'MA');

        $this->actingAs($admin)
            ->get(route('admin.santri.index', ['q' => 'Sama di', 'jenjang' => 'mts']))
            ->assertOk()
            ->assertSee('Sama di MTs')
            ->assertDontSee('Sama di MA');
    }

    public function test_admin_jenjang_tetap_fail_closed_saat_mencari(): void
    {
        $admin = User::factory()->adminJenjang('mts')->create();
        $this->buatSantri('Santri MTs Search', 'MTs');
        $this->buatSantri('Santri MA Search', 'MA');

        // Admin Jenjang mencoba menimpa filter lewat query string `jenjang=ma`.
        $this->actingAs($admin)
            ->get(route('admin.santri.index', ['q' => 'Search', 'jenjang' => 'ma']))
            ->assertOk()
            ->assertSee('Santri MTs Search')
            ->assertDontSee('Santri MA Search');
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function buatPendaftaran(array $overrides = []): PendaftaranOnline
    {
        $unik = strtolower(Str::random(8));

        return PendaftaranOnline::create(array_merge([
            'no_pendaftaran' => 'PPDB-'.date('Y').'-'.Str::upper(Str::random(5)),
            'status_pendaftaran' => 'diterima',
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
