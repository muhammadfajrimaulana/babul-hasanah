<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Izin pulang dari portal wali. */
class PortalIzinController extends Controller
{
    public function index(Request $request)
    {
        Izin::tandaiTerlambat();

        $user = $request->user();
        $anak = $user->daftarAnak();

        $izin = Izin::query()
            ->with(['santri', 'penyetuju'])
            ->whereIn('santri_id', $anak->pluck('id'))
            ->latest()
            ->paginate(15);

        return view('portal.izin.index', [
            'anak' => $anak,
            'izin' => $izin,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'alasan' => ['required', 'string', 'max:1000'],
            'tanggal_pulang' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pulang'],
            'penjemput' => ['required', 'string', 'max:150'],
        ], [
            'santri_id.required' => 'Pilih anak yang akan pulang.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'tanggal_pulang.after_or_equal' => 'Tanggal pulang tidak boleh sebelum hari ini.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pulang.',
            'penjemput.required' => 'Nama penjemput wajib diisi.',
        ]);

        abort_unless($user->memilikiSantri((int) $data['santri_id']), 403,
            'Data santri ini bukan milik akun Anda.');
        Gate::authorize('portal.ajukanIzin', (int) $data['santri_id']);

        $masihAktif = Izin::query()
            ->where('santri_id', $data['santri_id'])
            ->whereIn('status', [Izin::MENUNGGU, Izin::DISETUJUI, Izin::TERLAMBAT])
            ->exists();

        if ($masihAktif) {
            return back()->with('error', 'Masih ada izin yang belum selesai untuk anak ini. Tunggu hingga selesai sebelum mengajukan lagi.');
        }

        $izin = Izin::create([
            'santri_id' => $data['santri_id'],
            'user_id' => $user->id,
            'alasan' => $data['alasan'],
            'tanggal_pulang' => $data['tanggal_pulang'],
            'tanggal_kembali' => $data['tanggal_kembali'],
            'penjemput' => $data['penjemput'],
            'status' => Izin::MENUNGGU,
        ]);

        CatatanAktivitas::catat('ajukan_izin', $izin, [
            'santri_id' => $izin->santri_id,
            'tanggal_pulang' => $izin->tanggal_pulang?->toDateString(),
            'tanggal_kembali' => $izin->tanggal_kembali?->toDateString(),
        ]);

        return back()->with('success', 'Pengajuan izin terkirim. Status awal MENUNGGU dan akan diperiksa Pembina/Admin Jenjang.');
    }
}
