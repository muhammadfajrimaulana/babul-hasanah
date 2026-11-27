<?php

namespace Tests\Feature;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pesan flash harus mengikuti aksi yang benar-benar dilakukan.
 *
 * Form arsip memakai satu endpoint untuk status "keluar" dan "arsip", sehingga
 * pesan lama yang selalu berkata "diarsipkan" menyesatkan saat wali memilih
 * "Keluar / Berhenti".
 */
class PesanFlashTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesan_arsipkan_mengikuti_status_keluar(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('SantriKeluar');

        $this->actingAs($admin)
            ->post(route('admin.santri.arsipkan', $santri->id), [
                'status' => Santri::STATUS_KELUAR,
                'alasan' => 'Berpindah ke pesantren lain',
            ])
            ->assertSessionHas('success');

        $pesan = (string) session('success');

        $this->assertStringContainsString('Keluar / Berhenti', $pesan);
        $this->assertStringNotContainsString('diarsipkan', strtolower($pesan));
        $this->assertSame(Santri::STATUS_KELUAR, $santri->fresh()->status_kelulusan);
    }

    public function test_pesan_arsipkan_mengikuti_status_arsip(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $santri = $this->buatSantri('SantriArsip');

        $this->actingAs($admin)
            ->post(route('admin.santri.arsipkan', $santri->id), [
                'status' => Santri::STATUS_ARSIP,
                'alasan' => 'Tidak melanjutkan pendidikan',
            ])
            ->assertSessionHas('success');

        $pesan = (string) session('success');

        $this->assertStringContainsString('berhasil diarsipkan', strtolower($pesan));
        $this->assertStringNotContainsString('keluar', strtolower($pesan));
        $this->assertSame(Santri::STATUS_ARSIP, $santri->fresh()->status_kelulusan);
    }

    private function buatSantri(string $nama): Santri
    {
        return Santri::create([
            'nomor_induk' => 'NIS-'.Str::upper(Str::random(12)),
            'nama_lengkap' => $nama,
            'jenjang_pendidikan' => 'MTs',
            'kelas' => '7A',
            'alamat' => 'Alamat uji',
            'nama_wali' => 'Wali Uji',
            'status_kelulusan' => Santri::STATUS_AKTIF,
        ]);
    }
}
