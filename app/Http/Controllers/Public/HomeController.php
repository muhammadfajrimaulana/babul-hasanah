<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Album;
use App\Models\Berita;
use App\Models\InfoHarian;
use App\Models\Konten;
use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Models\StrukturOrganisasi;
use App\Support\GambarPublik;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Halaman beranda publik.
     *
     * Semua pengambilan data ada di sini agar Blade hanya bertugas menampilkan.
     * Guard Schema::hasTable dipertahankan supaya beranda tetap tampil sebelum
     * migrasi tabel pendukung dijalankan.
     */
    public function index()
    {
        // Nama tabel hasil migrasi adalah `beritas` (bukan `berita`).
        $beritaTerbaru = Schema::hasTable('beritas')
            ? Berita::published()->terbaru()->take(3)->get()
            : collect();

        $pengurus = StrukturOrganisasi::where('aktif', 1)
            ->where('kategori', 'bagan')
            ->orderBy('urutan')->orderBy('id')
            ->take(4)->get();

        // Info Harian: konten section "Kabar & jadwal pesantren hari ini",
        // dikelola lewat dashboard super admin (Pengelolaan > Info Harian).
        $infoHarian = collect();
        $jadwalHariIni = collect();

        if (Schema::hasTable('info_harian')) {
            $infoHarian = InfoHarian::info()->aktif()->berlaku()->sudahTerbit()
                ->orderByDesc('penting')->orderByDesc('tanggal')->orderByDesc('id')
                ->take(4)->get();

            $jadwalHariIni = InfoHarian::jadwal()->aktif()->berlaku()
                ->orderBy('jam')->orderBy('urutan')->orderBy('id')->get();
        }

        $teksInfo = $infoHarian->where('penting', true)->pluck('judul')->implode('   •   ');

        // Angka statistik dihitung langsung dari data admin.
        $jumlahSantri = Schema::hasTable('santri')
            ? Santri::count() : 0;
        $jumlahPrestasi = Schema::hasTable('prestasi_santri')
            ? PrestasiSantri::count() : 0;
        $jumlahBerita = Schema::hasTable('beritas')
            ? Berita::published()->count() : 0;
        $jumlahAlumni = Schema::hasTable('alumni')
            ? Alumni::count() : 0;

        // Urutan sama dengan halaman /prestasi: tahun terbaru, juara 1-3 dulu,
        // penghargaan di akhir.
        $prestasiTerbaru = PrestasiSantri::query()
            ->orderByDesc('tahun')
            ->orderByRaw('peringkat = 0, peringkat')
            ->orderByDesc('id')
            ->take(3)->get();

        // Galeri: slot besar diprioritaskan untuk album yang memuat video,
        // sisanya 2 album terbaru.
        $albumTerbaru = Album::with('media')
            ->orderByDesc('tanggal')->orderByDesc('id')->take(6)->get();
        $albumUtama = $albumTerbaru->first(
            fn ($a) => $a->media->where('tipe', '!=', 'foto')->isNotEmpty()
        ) ?? $albumTerbaru->first();
        $albumLain = $albumTerbaru->reject(
            fn ($a) => $albumUtama && $a->id === $albumUtama->id
        )->take(2);
        $galeriBeranda = $albumUtama ? collect([$albumUtama])->merge($albumLain) : collect();
        $gambarDefault = asset('assets/img/banner/banner.png');

        $sampulAlbum = function ($a) use ($gambarDefault) {
            $foto = $a->media->first(fn ($m) => $m->tipe === 'foto' && $m->path);
            $poster = $a->media->first(fn ($m) => ! empty($m->poster));

            // Cari berkas pertama yang benar-benar ada di disk.
            foreach ([$a->sampul, $foto?->path, $poster?->poster] as $path) {
                $url = GambarPublik::dariStorage($path);
                if ($url) {
                    return $url;
                }
            }

            return $gambarDefault;
        };

        // Testimoni alumni: section disembunyikan bila belum ada testimoni.
        $testimoniAlumni = collect();
        if (Schema::hasTable('alumni')) {
            $testimoniAlumni = Alumni::query()
                ->whereNotNull('testimoni')
                ->where('testimoni', '<>', '')
                ->orderByDesc('tahun_lulus')
                ->orderBy('nama')
                ->take(12)
                ->get();
        }

        // Fasilitas unggulan di beranda, maksimal 3 kartu.
        $fasilitasBeranda = Schema::hasTable('konten')
            ? collect(Konten::fasilitasCards())->take(3)
            : collect();

        return view('pages.beranda', compact(
            'beritaTerbaru', 'pengurus', 'infoHarian', 'jadwalHariIni', 'teksInfo',
            'jumlahSantri', 'jumlahPrestasi', 'jumlahBerita', 'jumlahAlumni',
            'prestasiTerbaru', 'galeriBeranda', 'gambarDefault', 'sampulAlbum',
            'testimoniAlumni', 'fasilitasBeranda'
        ));
    }
}
