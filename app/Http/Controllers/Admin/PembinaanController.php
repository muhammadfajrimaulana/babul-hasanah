<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Hafalan;
use App\Models\Pelanggaran;
use App\Models\Santri;
use App\Support\Akademik;
use App\Support\Akses;
use App\Support\CatatanAktivitas;
use App\Support\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pembinaan harian: hafalan/setoran, pelanggaran, dan presensi kegiatan.
 *
 * Pembina dan Admin Jenjang boleh menginput. Admin Jenjang tetap dibatasi
 * ke jenjangnya sendiri; Pembina melayani seluruh pesantren.
 */
class PembinaanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $jenjang = Akses::dibatasiJenjang($user) ? null : $request->input('jenjang');

        $santriQuery = Akademik::santriQuery($user, $jenjang)
            ->where('status_kelulusan', Santri::STATUS_AKTIF)
            ->orderBy('nama_lengkap');

        $santris = $santriQuery->get();

        $santri = null;

        if ($request->filled('santri')) {
            $santri = Akademik::santriQuery($user)
                ->with([
                    'kelasModel.waliKelas',
                    'hafalan' => fn ($q) => $q->latest('tanggal')->limit(25),
                    'pelanggaran' => fn ($q) => $q->latest('tanggal')->limit(25),
                    'absensi' => fn ($q) => $q->latest('tanggal')->limit(25),
                ])
                ->find($request->integer('santri'));

            abort_unless($santri, 403, 'Santri ini bukan bagian dari kewenangan Anda.');
        }

        return view('dashboard.pembinaan.index', [
            'santris' => $santris,
            'santri' => $santri,
            'jenisHafalan' => Hafalan::JENIS,
            'statusAbsensi' => ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'],
            'jenisKegiatan' => ['Sholat Berjamaah', 'Tahfidz', 'Sekolah Formal', 'Kegiatan Asrama', 'Piket Kamar'],
            'daftarJenjang' => Jenjang::semua(),
            'filterJenjang' => $jenjang,
            'bisaPilihJenjang' => ! Akses::dibatasiJenjang($user),
            'bolehUbah' => $user->bisa('pembinaan.ubah'),
        ]);
    }

    public function simpanHafalan(Request $request)
    {
        abort_unless(auth()->user()->bisa('pembinaan.ubah'), 403);

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::in(array_keys(Hafalan::JENIS))],
            'surat' => ['nullable', 'string', 'max:100'],
            'ayat' => ['nullable', 'string', 'max:50'],
            'juz' => ['nullable', 'string', 'max:10'],
            'nilai' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'predikat' => ['nullable', 'string', 'max:30'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $santri = $this->santriBerwenang($data['santri_id']);

        $hafalan = Hafalan::create([
            'santri_id' => $santri->id,
            'tanggal' => $data['tanggal'],
            'jenis' => $data['jenis'],
            'surat' => $data['surat'] ?? null,
            'ayat' => $data['ayat'] ?? null,
            'juz' => $data['juz'] ?? null,
            'nilai' => $data['nilai'] ?? null,
            'predikat' => $data['predikat'] ?? null,
            'catatan' => $data['catatan'] ?? null,
            'dicatat_oleh' => auth()->id(),
        ]);

        CatatanAktivitas::catat('input_hafalan', $hafalan, [
            'santri_id' => $santri->id,
            'jenis' => $hafalan->jenis,
            'surat' => $hafalan->surat,
        ]);

        return back()->with('success', 'Setoran hafalan '.$santri->nama_lengkap.' berhasil disimpan.');
    }

    public function hapusHafalan(Hafalan $hafalan)
    {
        abort_unless(auth()->user()->bisa('pembinaan.ubah'), 403);
        $this->santriBerwenang($hafalan->santri_id);

        $hafalan->delete();

        CatatanAktivitas::catat('hapus_hafalan', null, ['santri_id' => $hafalan->santri_id]);

        return back()->with('success', 'Catatan hafalan berhasil dihapus.');
    }

    public function simpanPelanggaran(Request $request)
    {
        abort_unless(auth()->user()->bisa('pembinaan.ubah'), 403);

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'poin' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'tindakan' => ['nullable', 'string', 'max:1000'],
        ]);

        $santri = $this->santriBerwenang($data['santri_id']);

        $pelanggaran = Pelanggaran::create([
            'santri_id' => $santri->id,
            'tanggal' => $data['tanggal'],
            'jenis' => $data['jenis'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'poin' => $data['poin'] ?? 0,
            'tindakan' => $data['tindakan'] ?? null,
            'dicatat_oleh' => auth()->id(),
        ]);

        CatatanAktivitas::catat('input_pelanggaran', $pelanggaran, [
            'santri_id' => $santri->id,
            'jenis' => $pelanggaran->jenis,
            'poin' => $pelanggaran->poin,
        ]);

        return back()->with('success', 'Pelanggaran '.$santri->nama_lengkap.' berhasil dicatat.');
    }

    public function hapusPelanggaran(Pelanggaran $pelanggaran)
    {
        abort_unless(auth()->user()->bisa('pembinaan.ubah'), 403);
        $this->santriBerwenang($pelanggaran->santri_id);

        $pelanggaran->delete();

        CatatanAktivitas::catat('hapus_pelanggaran', null, ['santri_id' => $pelanggaran->santri_id]);

        return back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }

    public function simpanAbsensi(Request $request)
    {
        abort_unless(auth()->user()->bisa('pembinaan.ubah'), 403);

        $data = $request->validate([
            'santri_id' => ['required', 'integer'],
            'tanggal' => ['required', 'date'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'status_kehadiran' => ['required', Rule::in(['hadir', 'izin', 'sakit', 'alpa'])],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $santri = $this->santriBerwenang($data['santri_id']);

        $absensi = Absensi::firstOrNew([
            'santri_id' => $santri->id,
            'tanggal' => $data['tanggal'],
            'jenis_kegiatan' => $data['jenis_kegiatan'],
        ]);

        $absensi->fill([
            'status_kehadiran' => $data['status_kehadiran'],
            'keterangan' => $data['keterangan'] ?? null,
            'dicatat_oleh' => auth()->id(),
        ])->save();

        CatatanAktivitas::catat('input_absensi', $absensi, [
            'santri_id' => $santri->id,
            'kegiatan' => $absensi->jenis_kegiatan,
            'status' => $absensi->status_kehadiran,
        ]);

        return back()->with('success', 'Presensi '.$santri->nama_lengkap.' ('.$absensi->jenis_kegiatan.') tersimpan.');
    }

    /** Pastikan santri berada dalam kewenangan user dan masih aktif. */
    private function santriBerwenang(int $santriId): Santri
    {
        $santri = Akademik::santriQuery(auth()->user())->find($santriId);

        abort_unless($santri, 403, 'Santri ini bukan bagian dari kewenangan Anda.');

        return $santri;
    }
}
