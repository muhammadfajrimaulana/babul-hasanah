<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $data = [
        ];

        return view('dashboard.kegiatan.index', $data);
    }
}