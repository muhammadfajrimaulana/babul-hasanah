<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GaleriStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_dan_hapus_album_memakai_disk_public(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.galeri.store'), [
                'judul' => 'Album Uji Storage',
                'kategori' => 'Kegiatan',
                'sampul' => UploadedFile::fake()->image('sampul.jpg'),
                'foto' => [UploadedFile::fake()->image('foto.jpg')],
            ])
            ->assertRedirect(route('admin.galeri.index'));

        $album = Album::where('judul', 'Album Uji Storage')->firstOrFail();
        $foto = $album->media->firstWhere('tipe', 'foto');

        $this->assertStringStartsWith('galeri/sampul/', (string) $album->sampul);
        $this->assertStringStartsWith('galeri/foto/', (string) $foto?->path);
        $this->assertTrue(Storage::disk('public')->exists((string) $album->sampul));
        $this->assertTrue(Storage::disk('public')->exists((string) $foto?->path));

        $sampul = $album->sampul;
        $pathFoto = $foto->path;

        $this->actingAs($admin)
            ->delete(route('admin.galeri.destroy', $album))
            ->assertRedirect(route('admin.galeri.index'));

        $this->assertFalse(Storage::disk('public')->exists($sampul));
        $this->assertFalse(Storage::disk('public')->exists($pathFoto));
        $this->assertDatabaseMissing('albums', ['id' => $album->id]);
        $this->assertDatabaseMissing('album_media', ['album_id' => $album->id]);
    }
}
