<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Admin\PengajuanPerubahanController;
use App\Http\Controllers\Controller;
use App\Models\PengajuanPerubahan;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Pengajuan perubahan data anak oleh wali. */
class PortalPengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $anak = $user->daftarAnak();

        $pengajuan = PengajuanPerubahan::query()
            ->with(['santri', 'pemroses'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('portal.pengajuan.index', [
            'anak' => $anak,
            'pengajuan' => $pengajuan,
            'bidang' => PengajuanPerubahanController::BIDANG,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'bidang' => ['required', Rule::in(array_keys(PengajuanPerubahanController::BIDANG))],
            'nilai_baru' => ['required', 'string', 'max:1000'],
            'alasan' => ['nullable', 'string', 'max:1000'],
        ], [
            'santri_id.required' => 'Pilih anak yang datanya ingin diubah.',
            'bidang.required' => 'Pilih bagian data yang ingin diubah.',
            'nilai_baru.required' => 'Isi data yang benar.',
        ]);

        abort_unless($user->memilikiSantri((int) $data['santri_id']), 403,
            'Data santri ini bukan milik akun Anda.');

        $santri = $user->anakQuery()->with('user')->findOrFail($data['santri_id']);

        $nilaiLama = match ($data['bidang']) {
            'nama_lengkap' => $santri->nama_lengkap,
            'alamat' => $santri->alamat,
            'nama_wali' => $santri->nama_wali,
            'kamar' => $santri->kamar,
            'no_hp_wali' => $santri->user?->phone,
            'email_wali' => $santri->user?->email,
            default => null,
        };

        $pengajuan = PengajuanPerubahan::create([
            'santri_id' => $santri->id,
            'user_id' => $user->id,
            'bidang' => $data['bidang'],
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $data['nilai_baru'],
            'alasan' => $data['alasan'] ?? null,
            'status' => 'menunggu',
        ]);

        CatatanAktivitas::catat('ajukan_perubahan', $pengajuan, [
            'santri_id' => $santri->id,
            'bidang' => $pengajuan->bidang,
        ]);

        return back()->with('success', 'Pengajuan perubahan terkirim. Admin Jenjang akan memeriksa dan memberi keputusan.');
    }
}
