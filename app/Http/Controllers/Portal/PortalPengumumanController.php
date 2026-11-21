<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Support\Jenjang;
use Illuminate\Http\Request;

/** Pengumuman yang tampil untuk jenjang anak wali. */
class PortalPengumumanController extends Controller
{
    public function index(Request $request)
    {
        $anak = $request->user()->daftarAnak();

        $slugs = $anak
            ->map(fn ($s) => Jenjang::normalisasi($s->jenjang_pendidikan))
            ->filter()
            ->unique()
            ->values();

        $query = Pengumuman::query()->with('pembuat')->orderByDesc('tanggal')->orderByDesc('id');

        $query->where(function ($q) use ($slugs) {
            $q->whereNull('target_jenjang');

            if ($slugs->isNotEmpty()) {
                $q->orWhereIn('target_jenjang', $slugs->all());
            }
        });

        if ($request->filled('jenjang')) {
            $slug = Jenjang::normalisasi($request->input('jenjang'));

            if ($slug && $slugs->contains($slug)) {
                $query->where(function ($q) use ($slug) {
                    $q->whereNull('target_jenjang')->orWhere('target_jenjang', $slug);
                });
            }
        }

        return view('portal.pengumuman.index', [
            'pengumuman' => $query->paginate(12)->withQueryString(),
            'anak' => $anak,
            'slugs' => $slugs,
            'daftarJenjang' => Jenjang::semua(),
            'filterJenjang' => $request->input('jenjang'),
        ]);
    }
}
