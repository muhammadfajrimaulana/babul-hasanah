<?php

namespace Tests\Feature;

use App\Mail\KredensialMail;
use App\Mail\PendaftaranMasukMail;
use App\Models\PendaftaranOnline;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class VerifikasiPPDBTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_ppdb_berhasil_menyimpan_pendaftaran_dan_mengirim_email(): void
    {
        Mail::fake();
        $data = $this->dataPendaftaran();

        $this->post(route('pendaftaran.store'), $data)->assertSessionHas('success');

        $pendaftaran = PendaftaranOnline::where('email', $data['email'])->firstOrFail();

        $this->assertSame('pending', $pendaftaran->status_pendaftaran);
        $this->assertSame($data['email_santri'], $pendaftaran->email_santri);
        $this->assertSame($data['jenjang_tujuan'], $pendaftaran->jenjang_tujuan);
        $this->assertMatchesRegularExpression(
            '/^PPDB-\d{4}-[A-Z0-9]{5}$/',
            $pendaftaran->no_pendaftaran
        );
        $this->assertStringContainsString($pendaftaran->no_pendaftaran, session('success'));
        $this->assertDatabaseMissing('users', ['email' => $data['email']]);

        Mail::assertSent(PendaftaranMasukMail::class);
    }

    public function test_honeypot_website_ditolak(): void
    {
        $this->post(route('pendaftaran.store'), $this->dataPendaftaran([
            'website' => 'https://spam.example.test',
        ]))->assertSessionHasErrors('website');

        $this->assertDatabaseCount('pendaftaran_online', 0);
    }

    public function test_email_santri_harus_beda_dengan_email_wali(): void
    {
        $data = $this->dataPendaftaran();
        $data['email_santri'] = $data['email'];

        $this->post(route('pendaftaran.store'), $data)
            ->assertSessionHasErrors('email_santri');

        $this->assertDatabaseCount('pendaftaran_online', 0);
    }

    public function test_email_santri_yang_sudah_dipakai_akun_ditolak(): void
    {
        $data = $this->dataPendaftaran();
        User::factory()->alumni()->create(['email' => $data['email_santri']]);

        $this->post(route('pendaftaran.store'), $data)
            ->assertSessionHasErrors('email_santri');

        $this->assertDatabaseCount('pendaftaran_online', 0);
    }

    public function test_email_santri_yang_sudah_dipakai_pendaftaran_lain_ditolak(): void
    {
        $data = $this->dataPendaftaran();
        $this->buatPendaftaran(['email_santri' => $data['email_santri']]);

        $this->post(route('pendaftaran.store'), $data)
            ->assertSessionHasErrors('email_santri');

        $this->assertDatabaseCount('pendaftaran_online', 1);
    }

    public function test_email_wali_yang_dipakai_akun_staf_ditolak(): void
    {
        $data = $this->dataPendaftaran();
        User::factory()->superAdmin()->create(['email' => $data['email']]);

        $this->post(route('pendaftaran.store'), $data)
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('pendaftaran_online', 0);
    }

    public function test_jenjang_tujuan_di_luar_daftar_resmi_ditolak(): void
    {
        $this->post(route('pendaftaran.store'), $this->dataPendaftaran([
            'jenjang_tujuan' => 'SMA',
        ]))->assertSessionHasErrors('jenjang_tujuan');

        $this->assertDatabaseCount('pendaftaran_online', 0);
    }

    public function test_superadmin_menerima_ppdb_membuat_akun_wali_santri_santri_dan_email(): void
    {
        Mail::fake();
        $admin = User::factory()->superAdmin()->create();
        $pendaftaran = $this->buatPendaftaran();

        $this->actingAs($admin)
            ->patch(route('admin.pendaftaran.verifikasi', $pendaftaran->id), [
                'status_pendaftaran' => 'diterima',
            ])
            ->assertSessionHas('success');

        $this->assertSame('diterima', $pendaftaran->fresh()->status_pendaftaran);

        $wali = User::where('email', $pendaftaran->email)->firstOrFail();
        $this->assertSame('wali_santri', $wali->role);
        $this->assertTrue($wali->must_change_password);
        $this->assertNotNull($wali->temp_password_expires_at);

        $santri = Santri::where('pendaftaran_online_id', $pendaftaran->id)->firstOrFail();
        $this->assertSame($pendaftaran->nama_lengkap, $santri->nama_lengkap);
        $this->assertSame($pendaftaran->email_santri, $santri->email_santri);
        $this->assertTrue($wali->anak()->whereKey($santri->id)->exists());

        Mail::assertSent(
            KredensialMail::class,
            fn (KredensialMail $mail) => $mail->hasTo($pendaftaran->email)
        );
    }

    public function test_admin_menolak_ppdb_tanpa_membuat_akun_atau_santri(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $pendaftaran = $this->buatPendaftaran();

        $this->actingAs($admin)
            ->patch(route('admin.pendaftaran.verifikasi', $pendaftaran->id), [
                'status_pendaftaran' => 'ditolak',
            ])
            ->assertSessionHas('success');

        $this->assertSame('ditolak', $pendaftaran->fresh()->status_pendaftaran);
        $this->assertDatabaseMissing('users', ['email' => $pendaftaran->email]);
        $this->assertDatabaseMissing('santri', [
            'pendaftaran_online_id' => $pendaftaran->id,
        ]);
    }

    public function test_pendaftar_yang_sudah_diterima_tidak_bisa_diterima_ulang(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $pendaftaran = $this->buatPendaftaran(['status_pendaftaran' => 'diterima']);

        $this->actingAs($admin)
            ->patch(route('admin.pendaftaran.verifikasi', $pendaftaran->id), [
                'status_pendaftaran' => 'diterima',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => $pendaftaran->email]);
        $this->assertDatabaseMissing('santri', [
            'pendaftaran_online_id' => $pendaftaran->id,
        ]);
    }

    public function test_admin_jenjang_lain_tidak_bisa_memverifikasi_pendaftar(): void
    {
        $admin = User::factory()->adminJenjang('ma')->create();
        $pendaftaran = $this->buatPendaftaran(['jenjang_tujuan' => 'MTs']);

        $this->actingAs($admin)
            ->patch(route('admin.pendaftaran.verifikasi', $pendaftaran->id), [
                'status_pendaftaran' => 'diterima',
            ])
            ->assertForbidden();

        $this->assertSame('pending', $pendaftaran->fresh()->status_pendaftaran);
        $this->assertDatabaseMissing('users', ['email' => $pendaftaran->email]);
    }

    /**
     * Payload lengkap sesuai aturan validasi PendaftaranOnlineController@store.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function dataPendaftaran(array $overrides = []): array
    {
        $unik = strtolower(Str::random(8));

        return array_merge([
            'nama_lengkap' => 'Santri Uji '.$unik,
            'tempat_tanggal_lahir' => 'Padang, 1 Januari 2010',
            'no_hp_aktif' => '081234567890',
            'alamat_domisili' => 'Jl. Uji No. 1',
            'email' => 'wali-'.$unik.'@example.test',
            'email_santri' => 'santri-'.$unik.'@example.test',
            'nama_ortu_wali' => 'Wali Uji '.$unik,
            'no_hp_ortu' => '081234567891',
            'asal_sekolah' => 'MTs Uji',
            'alamat_sekolah' => 'Jl. Sekolah No. 2',
            'tahun_lulus' => '2026',
            'nisn' => (string) mt_rand(1000000000, 9999999999),
            'jenjang_tujuan' => 'MTs',
        ], $overrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function buatPendaftaran(array $overrides = []): PendaftaranOnline
    {
        return PendaftaranOnline::create(array_merge([
            'no_pendaftaran' => 'PPDB-'.date('Y').'-'.Str::upper(Str::random(5)),
            'status_pendaftaran' => 'pending',
        ], $this->dataPendaftaran($overrides)));
    }
}
