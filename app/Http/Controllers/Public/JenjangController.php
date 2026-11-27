<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DataJenjang;

class JenjangController extends Controller
{
    /**
     * Tampilkan salah satu dari tujuh halaman jenjang.
     *
     * Data diambil dari App\Support\DataJenjang, bukan dari Blade,
     * sehingga perubahan konten cukup dilakukan di satu tempat.
     */
    public function show(string $slug)
    {
        $p = DataJenjang::untuk($slug);

        abort_unless($p, 404);

        return view('pages.jenjang.'.$slug, compact('p'));
    }
}
