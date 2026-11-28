<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Konten;

class ProfilController extends Controller
{
    /**
     * Halaman induk profil. Teks profil diambil di sini, bukan di Blade,
     * agar Blade tidak memanggil kelas model secara langsung.
     */
    public function index()
    {
        $konten = Konten::allWithDefaults();

        return view('pages.profil', compact('konten'));
    }

    /**
     * Konten & susunan section diambil di sini, bukan di Blade.
     * Nilai bawaan tetap berasal dari Konten::DEFAULTS, jadi halaman
     * tetap tampil sebelum admin mengedit konten.
     */
    public function sejarah()
    {
        $konten = Konten::allWithDefaults();
        $layout = Konten::layoutFor('sejarah');

        return view('pages.sejarah', compact('konten', 'layout'));
    }

    public function visiMisi()
    {
        $konten = Konten::allWithDefaults();
        $layout = Konten::layoutFor('visi');

        return view('pages.visi-misi', compact('konten', 'layout'));
    }

    public function pembelajaran()
    {
        $konten = Konten::allWithDefaults();
        $layout = Konten::layoutFor('pembelajaran');

        return view('pages.pembelajaran', compact('konten', 'layout'));
    }

    public function fasilitas()
    {
        $konten = Konten::allWithDefaults();
        $layout = Konten::layoutFor('fasilitas');
        $semuaKartu = Konten::fasilitasCards();

        return view('pages.fasilitas', compact('konten', 'layout', 'semuaKartu'));
    }
}
