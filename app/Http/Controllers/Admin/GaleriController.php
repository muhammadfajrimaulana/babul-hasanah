<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;

class GaleriController extends Controller
{
    public function index()
    {
        $galeris = Galeri::latest()->paginate(10);

        return view('dashboard.galeri.index', compact('galeris'));
    }
}