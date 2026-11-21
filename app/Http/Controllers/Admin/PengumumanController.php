<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pengumuman per jenjang.
 *
 * - Superadmin & Pengurus boleh membuat untuk semua jenjang.
 * - Admin Jenjang hanya untuk jenjangnya sendiri (dipaksa sistem).
 * - Wali Santri & Alumni hanya membaca dari portalnya.
 */
class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bolehUbah = $user->bisa('pengumuman.ubah');
        $jenjangAdmin = Akses::jenjangSlug($user);

        $query = Pengumuman::query()->with('pembuat');

        if (Akses::dibatasiJenjang($user)) {
            // Admin Jenjang: hanya pengumuman jenjangnya, bukan pengumuman global.
            $query->where('target_jenjang', $jenjangAdmin ?: '__tidak_ada__');
        } elseif ($request->filled('jenjang')) {
            $slug = Jenjang::normalisasi($request->input('jenjang'));
            $query->where(function ($q) use ($slug) {
                $q->whereNull('target_jenjang');
                if ($slug) {
                    $q->orWhere('target_jenjang', $slug);
                }
            });
        }

        $pengumuman = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('dashboard.pengumuman.index', [
            'pengumuman' => $pengumuman,
            'bolehUbah' => $bolehUbah,
            'jenjangAdmin' => $jenjangAdmin,
            'daftarJenjang' => Jenjang::semua(),
            'targetTerpilih' => $request->input('jenjang'),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->bisa('pengumuman.ubah'), 403);

        $data = $this->validasi($request, $user);

        $pengumuman = Pengumuman::create([
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'tanggal' => $data['tanggal'],
            'target_jenjang' => $data['target_jenjang'],
            'dibuat_oleh' => $user->id,
        ]);

        CatatanAktivitas::catat('buat_pengumuman', $pengumuman, [
            'judul' => $pengumuman->judul,
            'target' => $pengumuman->target_jenjang,
        ]);

        return back()->with('success', 'Pengumuman "'.$pengumuman->judul.'" berhasil dipublikasikan.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $user = auth()->user();
        $this->pastikanBolehKelola($user, $pengumuman);

        $data = $this->validasi($request, $user);

        $pengumuman->update([
            'judul' => $data['judul'],
            'isi' => $data['isi'],
            'tanggal' => $data['tanggal'],
            'target_jenjang' => $data['target_jenjang'],
        ]);

        CatatanAktivitas::catat('ubah_pengumuman', $pengumuman, [
            'judul' => $pengumuman->judul,
            'target' => $pengumuman->target_jenjang,
        ]);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $user = auth()->user();
        $this->pastikanBolehKelola($user, $pengumuman);

        $judul = $pengumuman->judul;
        $pengumuman->delete();

        CatatanAktivitas::catat('hapus_pengumuman', null, ['judul' => $judul]);

        return back()->with('success', 'Pengumuman "'.$judul.'" berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validasi(Request $request, $user): array
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'isi' => ['required', 'string', 'max:5000'],
            'tanggal' => ['required', 'date'],
            'target_jenjang' => ['nullable', 'string', 'max:100'],
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'tanggal.required' => 'Tanggal pengumuman wajib diisi.',
        ]);

        if (Akses::dibatasiJenjang($user)) {
            $slug = Akses::jenjangSlug($user);

            if (! $slug) {
                abort(403, 'Akun Admin Jenjang belum memiliki jenjang.');
            }

            // Target dikunci ke jenjang sendiri; input lain diabaikan.
            $data['target_jenjang'] = $slug;
        } else {
            $slug = Jenjang::normalisasi($request->input('target_jenjang'));

            if ($request->filled('target_jenjang') && ! $slug) {
                abort(422, 'Target jenjang tidak dikenali.');
            }

            $data['target_jenjang'] = $slug; // null = semua jenjang
        }

        return $data;
    }

    private function pastikanBolehKelola($user, Pengumuman $pengumuman): void
    {
        abort_unless($user->bisa('pengumuman.ubah'), 403);

        if (! Akses::dibatasiJenjang($user)) {
            return;
        }

        $slug = Akses::jenjangSlug($user);

        // Admin Jenjang tidak boleh menyentuh pengumuman global / jenjang lain.
        abort_unless($slug && $pengumuman->target_jenjang === $slug, 403,
            'Pengumuman ini bukan milik jenjang yang Anda tangani.');
    }
}
