<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;

class StrukturOrganisasiController extends Controller
{
    public function index()
    {
        return view('pages.struktur-organisasi', [
            'bagan'          => StrukturOrganisasi::baganTree(),
            'jenjang'        => StrukturOrganisasi::aktif()->diKategori('jenjang')->urut()->get(),
            'bidang'         => StrukturOrganisasi::aktif()->diKategori('bidang')->urut()->get(),
            'jumlahPengurus' => StrukturOrganisasi::aktif()->diKategori('bagan')->count(),
        ]);
    }
}
