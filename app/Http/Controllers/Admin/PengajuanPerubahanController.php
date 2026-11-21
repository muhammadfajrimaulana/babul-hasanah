<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPerubahan;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengajuan perubahan data dari wali santri.
 *
 * Wali tidak bisa mengubah data langsung. Admin Jenjang memeriksa pengajuan
 * dan menyetujui/menolak. Semua keputusan masuk log aktivitas.
 */
class PengajuanPerubahanController extends Controller
{
    /** Bidang yang boleh diajukan wali (whitelist). */
    public const BIDANG = [
        'nama_lengkap' => 'Nama Lengkap Santri',
        'alamat' => 'Alamat',
        'nama_wali' => 'Nama Wali',
        'kamar' => 'Kamar',
        'no_hp_wali' => 'No. HP Wali',
        'email_wali' => 'Email Wali',
    ];

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = PengajuanPerubahan::query()
            ->with(['santri', 'wali', 'pemroses'])
            ->whereIn('santri_id', Akademik::santriQuery($user)->select('id'))
            ->latest();

        if (in_array($request->input('status'), array_keys(PengajuanPerubahan::STATUS), true)) {
            $query->where('status', $request->input('status'));
        }

        return view('dashboard.pengajuan.index', [
            'pengajuan' => $query->paginate(20)->withQueryString(),
            'status' => PengajuanPerubahan::STATUS,
            'bidang' => self::BIDANG,
            'filterStatus' => $request->input('status'),
            'bolehProses' => $user->bisa('santri.ubah'),
        ]);
    }

    public function setujui(Request $request, PengajuanPerubahan $pengajuan)
    {
        $this->pastikanBerwenang($pengajuan);

        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:1000'],
        ]);

        $santri = $pengajuan->santri;

        if (! $santri) {
            return back()->with('error', 'Data santri untuk pengajuan ini tidak ditemukan.');
        }

        $this->terapkan($pengajuan);

        $pengajuan->update([
            'status' => 'disetujui',
            'diproses_oleh' => auth()->id(),
            'catatan_admin' => $data['catatan_admin'] ?? null,
        ]);

        CatatanAktivitas::catat('setujui_pengajuan', $pengajuan, [
            'santri_id' => $santri->id,
            'bidang' => $pengajuan->bidang,
            'nilai_baru' => $pengajuan->nilai_baru,
        ], $data['catatan_admin'] ?? null);

        return back()->with('success', 'Pengajuan disetujui dan data santri sudah diperbarui.');
    }

    public function tolak(Request $request, PengajuanPerubahan $pengajuan)
    {
        $this->pastikanBerwenang($pengajuan);

        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi agar wali mengetahui penyebabnya.',
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'diproses_oleh' => auth()->id(),
            'catatan_admin' => $data['catatan_admin'],
        ]);

        CatatanAktivitas::catat('tolak_pengajuan', $pengajuan, [
            'bidang' => $pengajuan->bidang,
        ], $data['catatan_admin']);

        return back()->with('success', 'Pengajuan ditolak dan alasannya terlihat di portal wali.');
    }

    /** Terapkan nilai baru ke kolom santri/akun wali yang diizinkan. */
    private function terapkan(PengajuanPerubahan $pengajuan): void
    {
        $santri = $pengajuan->santri;
        $nilai = (string) $pengajuan->nilai_baru;

        switch ($pengajuan->bidang) {
            case 'nama_lengkap':
                $santri->update(['nama_lengkap' => $nilai]);
                break;

            case 'alamat':
                $santri->update(['alamat' => $nilai]);
                break;

            case 'nama_wali':
                $santri->update(['nama_wali' => $nilai]);
                break;

            case 'kamar':
                $santri->update(['kamar' => $nilai]);
                break;

            case 'no_hp_wali':
                $santri->user?->update(['phone' => $nilai]);
                break;

            case 'email_wali':
                if ($santri->user && filter_var($nilai, FILTER_VALIDATE_EMAIL)) {
                    $sudahDipakai = \App\Models\User::query()
                        ->where('email', $nilai)
                        ->whereKeyNot($santri->user->id)
                        ->exists();

                    if (! $sudahDipakai) {
                        $santri->user->update(['email' => $nilai]);
                    }
                }
                break;
        }
    }

    private function pastikanBerwenang(PengajuanPerubahan $pengajuan): void
    {
        abort_unless(auth()->user()->bisa('santri.ubah'), 403);

        $milikJenjang = Akademik::santriQuery(auth()->user())
            ->whereKey($pengajuan->santri_id)
            ->exists();

        abort_unless($milikJenjang, 403, 'Pengajuan ini bukan milik santri pada jenjang yang Anda tangani.');
    }
}
