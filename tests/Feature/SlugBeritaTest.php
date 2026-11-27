<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugBeritaTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_dibuat_otomatis_dari_judul_saat_berita_dibuat(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.berita.store'), $this->dataBerita([
                'judul' => 'Kegiatan Pondok Baru 2026',
            ]))
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('beritas', [
            'judul' => 'Kegiatan Pondok Baru 2026',
            'slug' => 'kegiatan-pondok-baru-2026',
        ]);
    }

    public function test_slug_duplikat_ditolak_saat_membuat_berita(): void
    {
        $admin = User::factory()->superAdmin()->create();
        Berita::create($this->dataBerita([
            'judul' => 'Berita Pertama',
            'slug' => 'judul-sama',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.berita.store'), $this->dataBerita([
                'judul' => 'Berita Kedua',
                'slug' => 'judul-sama',
            ]))
            ->assertSessionHasErrors('slug');

        $this->assertSame(1, Berita::where('slug', 'judul-sama')->count());
    }

    public function test_edit_judul_tidak_mengubah_slug_walaupun_slug_dikirim(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $berita = Berita::create($this->dataBerita([
            'judul' => 'Judul Lama Berita',
            'slug' => 'judul-lama-berita',
        ]));

        $this->actingAs($admin)
            ->put(route('admin.berita.update', $berita), $this->dataBerita([
                'judul' => 'Judul Baru Berita',
                'slug' => 'judul-baru-berita',
            ]))
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('beritas', [
            'id' => $berita->id,
            'judul' => 'Judul Baru Berita',
            'slug' => 'judul-lama-berita',
        ]);
        $this->get(route('berita.show', 'judul-lama-berita'))
            ->assertOk()
            ->assertSee('Judul Baru Berita');
        $this->get(route('berita.show', 'judul-baru-berita'))->assertNotFound();
    }

    public function test_edit_judul_tanpa_slug_tetap_mempertahankan_permalink(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $berita = Berita::create($this->dataBerita([
            'judul' => 'Judul Awal Berita',
            'slug' => 'permalink-awal',
        ]));

        $data = $this->dataBerita(['judul' => 'Judul Sesudah Diubah']);
        unset($data['slug']);

        $this->actingAs($admin)
            ->put(route('admin.berita.update', $berita), $data)
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('beritas', [
            'id' => $berita->id,
            'judul' => 'Judul Sesudah Diubah',
            'slug' => 'permalink-awal',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function dataBerita(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Judul Berita Uji',
            'slug' => '',
            'kategori' => 'Kegiatan',
            'ringkasan' => 'Ringkasan uji.',
            'isi' => 'Isi berita uji yang memuat beberapa kata agar lolos validasi.',
            'penulis' => 'Admin',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
        ], $overrides);
    }
}
