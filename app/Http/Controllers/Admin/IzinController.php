<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Verifikasi izin pulang.
 *
 * Pembina atau Admin Jenjang memeriksa antrean, menyetujui (menerbitkan kode
 * izin), menolak (wajib alasan), dan menandai santri sudah kembali.
 */
class IzinController extends Controller
{
    public function index(Request $request)
    {
        Izin::tandaiTerlambat();

        $user = auth()->user();

        $query = Izin::query()
            ->with(['santri', 'wali', 'penyetuju'])
            ->whereIn('santri_id', Akademik::santriQuery($user)->select('id'))
            ->latest();

        if (in_array($request->input('status'), array_keys(Izin::STATUS), true)) {
            $query->where('status', $request->input('status'));
        }

        return view('dashboard.izin.index', [
            'izin' => $query->paginate(20)->withQueryString(),
            'status' => Izin::STATUS,
            'filterStatus' => $request->input('status'),
            'antrean' => Izin::query()
                ->where('status', Izin::MENUNGGU)
                ->whereIn('santri_id', Akademik::santriQuery($user)->select('id'))
                ->count(),
            'bolehVerifikasi' => $user->bisa('izin.verifikasi'),
        ]);
    }

    public function setujui(Request $request, Izin $izin)
    {
        $this->pastikanBoleh($izin);

        if ($izin->status === Izin::SELESAI) {
            return back()->with('error', 'Izin ini sudah selesai.');
        }

        $izin->update([
            'status' => Izin::DISETUJUI,
            'alasan_penolakan' => null,
            'kode_izin' => $izin->kode_izin ?: 'IZIN-'.strtoupper(Str::random(8)),
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        CatatanAktivitas::catat('setujui_izin', $izin, [
            'santri_id' => $izin->santri_id,
            'kode_izin' => $izin->kode_izin,
        ]);

        return back()->with('success', 'Izin disetujui. Kode izin: '.$izin->kode_izin.'.');
    }

    public function tolak(Request $request, Izin $izin)
    {
        $this->pastikanBoleh($izin);

        $data = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:1000'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi agar wali mengetahuinya.',
        ]);

        $izin->update([
            'status' => Izin::DITOLAK,
            'alasan_penolakan' => $data['alasan_penolakan'],
            'kode_izin' => null,
            'disetujui_oleh' => auth()->id(),
            'disetujui_pada' => now(),
        ]);

        CatatanAktivitas::catat('tolak_izin', $izin, [
            'alasan' => $data['alasan_penolakan'],
        ], $data['alasan_penolakan']);

        return back()->with('success', 'Izin ditolak dan alasan sudah dikirim ke portal wali.');
    }

    public function kembali(Request $request, Izin $izin)
    {
        $this->pastikanBoleh($izin);

        if ($izin->status === Izin::MENUNGGU) {
            return back()->with('error', 'Izin belum disetujui, belum bisa ditandai kembali.');
        }

        $izin->update([
            'status' => Izin::SELESAI,
            'kembali_pada' => now(),
        ]);

        CatatanAktivitas::catat('izin_selesai', $izin, [
            'santri_id' => $izin->santri_id,
            'kembali_pada' => now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Santri ditandai sudah kembali. Izin masuk ke riwayat.');
    }

    private function pastikanBoleh(Izin $izin): void
    {
        abort_unless(auth()->user()->bisa('izin.verifikasi'), 403);

        $milikJenjang = Akademik::santriQuery(auth()->user())
            ->whereKey($izin->santri_id)
            ->exists();

        abort_unless($milikJenjang, 403, 'Izin ini bukan milik santri pada jenjang yang Anda tangani.');
    }
}
