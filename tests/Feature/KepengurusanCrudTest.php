<?php

namespace Tests\Feature;

use App\Models\Kepengurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KepengurusanCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_menambah_pengurus_dan_tampil_di_profil(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.kepengurusan.store'), [
                'nama' => 'Ustadz Zaki',
                'jabatan' => 'Pimpinan Pesantren',
                'urutan' => 1,
            ])
            ->assertRedirect(route('admin.kepengurusan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kepengurusan', [
            'nama' => 'Ustadz Zaki',
            'jabatan' => 'Pimpinan Pesantren',
            'urutan' => 1,
        ]);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'kepengurusan_tambah']);

        $this->get(route('profil'))
            ->assertOk()
            ->assertViewHas('kepengurusans')
            ->assertSee('Ustadz Zaki')
            ->assertSee('Pimpinan Pesantren');
    }

    public function test_nama_dan_jabatan_wajib_diisi(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.kepengurusan.store'), [
                'nama' => '',
                'jabatan' => '',
            ])
            ->assertSessionHasErrors(['nama', 'jabatan']);

        $this->assertDatabaseCount('kepengurusan', 0);
    }

    public function test_superadmin_mengubah_data_dan_urutan(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $pertama = $this->buat('PengurusAwal', 'JabatanAwal', 1);
        $this->buat('PengurusKedua', 'JabatanKedua', 2);

        $this->actingAs($admin)
            ->put(route('admin.kepengurusan.update', $pertama), [
                'nama' => 'PengurusUbah',
                'jabatan' => 'JabatanUbah',
                'urutan' => 9,
            ])
            ->assertRedirect(route('admin.kepengurusan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kepengurusan', [
            'id' => $pertama->id,
            'nama' => 'PengurusUbah',
            'jabatan' => 'JabatanUbah',
            'urutan' => 9,
        ]);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'kepengurusan_ubah']);

        $this->get(route('profil'))
            ->assertOk()
            ->assertSeeInOrder(['PengurusKedua', 'PengurusUbah']);
    }

    public function test_superadmin_menghapus_pengurus(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $item = $this->buat('PengurusHapus', 'JabatanHapus', 1);

        $this->actingAs($admin)
            ->delete(route('admin.kepengurusan.destroy', $item))
            ->assertRedirect(route('admin.kepengurusan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('kepengurusan', ['id' => $item->id]);
        $this->assertDatabaseHas('log_aktivitas', ['aksi' => 'kepengurusan_hapus']);
        $this->get(route('profil'))->assertOk()->assertDontSee('PengurusHapus');
    }

    public function test_peran_non_superadmin_tidak_bisa_mengelola(): void
    {
        $wali = User::factory()->waliSantri()->create();

        $this->actingAs($wali)
            ->get(route('admin.kepengurusan.index'))
            ->assertForbidden();

        $this->actingAs($wali)
            ->post(route('admin.kepengurusan.store'), [
                'nama' => 'PengurusDitolak',
                'jabatan' => 'JabatanDitolak',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('kepengurusan', ['nama' => 'PengurusDitolak']);
    }

    public function test_foto_legacy_tetap_bisa_ditampilkan(): void
    {
        $item = $this->buat('PengurusLegacy', 'JabatanLegacy', 1, 'upload/kepengurusan/contoh.png');

        $this->assertNotNull($item->foto_url);
        $this->assertStringContainsString('upload/kepengurusan/contoh.png', (string) $item->foto_url);
    }

    public function test_foto_baru_disimpan_di_disk_public(): void
    {
        Storage::fake('public');

        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.kepengurusan.store'), [
                'nama' => 'PengurusBerfoto',
                'jabatan' => 'JabatanBerfoto',
                'foto' => UploadedFile::fake()->image('foto.png', 120, 120),
            ])
            ->assertRedirect(route('admin.kepengurusan.index'));

        $item = Kepengurusan::where('nama', 'PengurusBerfoto')->firstOrFail();

        $this->assertStringStartsWith('kepengurusan/', (string) $item->foto);
        $this->assertTrue(Storage::disk('public')->exists((string) $item->foto));
    }

    private function buat(string $nama, string $jabatan, int $urutan, ?string $foto = null): Kepengurusan
    {
        return Kepengurusan::create([
            'nama' => $nama,
            'jabatan' => $jabatan,
            'urutan' => $urutan,
            'foto' => $foto,
        ]);
    }
}
