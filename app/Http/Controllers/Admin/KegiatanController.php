<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\CatatanKalender;
use App\Models\InfoHarian;
use App\Support\Akses;
use App\Support\Akademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KegiatanController extends Controller
{
    /**
     * Monitoring kegiatan: agenda pesantren, catatan kalender pribadi, dan
     * aktivitas harian santri. Bersifat baca saja dan mengikuti batas jenjang.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $slugJenjang = Akses::jenjangSlug($user);

        $idSantri = Akademik::santriQuery($user, $slugJenjang)->pluck('id');

        $jadwal = collect();
        if (Schema::hasTable('info_harian')) {
            $jadwal = InfoHarian::jadwal()->aktif()->berlaku()
                ->orderBy('jam')
                ->orderBy('urutan')
                ->orderBy('id')
                ->limit(10)
                ->get();
        }

        $catatan = collect();
        if (Schema::hasTable('catatan_kalender')) {
            $catatan = CatatanKalender::where('user_id', $user->id)
                ->whereDate('tanggal', '>=', today())
                ->orderBy('tanggal')
                ->limit(5)
                ->get();
        }

        $absensi = collect();
        if (Schema::hasTable('absensi') && $idSantri->isNotEmpty()) {
            $absensi = Absensi::query()
                ->whereIn('santri_id', $idSantri)
                ->with('santri:id,nama_lengkap,kelas,jenjang_pendidikan')
                ->latest('tanggal')
                ->limit(12)
                ->get();
        }

        return view('dashboard.kegiatan.index', [
            'jadwal'     => $jadwal,
            'catatan'    => $catatan,
            'absensi'    => $absensi,
            'namaJenjang' => Akses::namaJenjang($user),
        ]);
    }
}
