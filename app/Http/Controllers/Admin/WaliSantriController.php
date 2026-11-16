<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Akademik;
use App\Support\PortalWali;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Portal Wali Santri (akun dengan peran `wali_santri`).
 *
 * Semua data anak berasal dari `PortalWali::anak()` sehingga wali hanya dapat
 * melihat santri yang terdaftar melalui PPDB (`pendaftaran_online`) dan/atau
 * ditambahkan admin ke `students`/`santri` dengan email akun ini.
 *
 * Halaman memakai layout portal (Tailwind) yang sama dengan rapor digital.
 */
class WaliSantriController extends Controller
{
    /** Pastikan hanya akun wali santri yang boleh membuka halaman ini. */
    private function pastikanWali(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->peranSistem() === 'walisantri',
            403,
            'Halaman ini hanya untuk akun Wali Santri.'
        );
    }

    /** Dashboard ringkas: anak aktif, nilai terbaru, dan status PPDB. */
    public function index(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();
        $anakList = PortalWali::anak($user);
        $anakAktif = PortalWali::aktif($request, $anakList);
        $infoNilai = PortalWali::ringkasanNilaiTerbit($anakList);
        $pendaftaran = PortalWali::pendaftaran($user);

        $adaNilaiBaru = $infoNilai->contains(
            fn ($baris) => $baris->terakhir && now()->subDays(14)->lte($baris->terakhir)
        );

        return view('wali_santri.dashboard', compact(
            'user',
            'anakList',
            'anakAktif',
            'infoNilai',
            'pendaftaran',
            'adaNilaiBaru'
        ));
    }

    /** Daftar seluruh anak yang terhubung dengan akun wali. */
    public function anak(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();
        $anakList = PortalWali::anak($user);
        $anakAktif = PortalWali::aktif($request, $anakList);
        $pendaftaran = PortalWali::pendaftaran($user);

        return view('wali_santri.anak', compact(
            'user',
            'anakList',
            'anakAktif',
            'pendaftaran'
        ));
    }

    /** Nilai/rapor anak aktif (hanya nilai yang sudah diterbitkan). */
    public function nilai(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();
        $anakList = PortalWali::anak($user);
        $anak = PortalWali::aktif($request, $anakList);
        $opsi = $anak ? PortalWali::opsiPeriode($anak) : collect();
        [$tahun, $semester] = $this->periode($request, $opsi);

        $rapor = $anak ? PortalWali::rapor($anak, $tahun, $semester) : $this->raporKosong();
        $transkrip = $anak ? PortalWali::transkrip($anak) : collect();

        return view('wali_santri.nilai', compact(
            'user',
            'anakList',
            'anak',
            'opsi',
            'tahun',
            'semester',
            'rapor',
            'transkrip'
        ));
    }

    /** Riwayat pendaftaran PPDB milik akun wali (semua status). */
    public function pendaftaran(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();
        $anakList = PortalWali::anak($user);
        $anakAktif = PortalWali::aktif($request, $anakList);
        $pendaftaran = PortalWali::pendaftaran($user);

        return view('wali_santri.pendaftaran', compact(
            'user',
            'anakList',
            'anakAktif',
            'pendaftaran'
        ));
    }

    /** Halaman profil akun wali. */
    public function profil(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();
        $anakList = PortalWali::anak($user);
        $anakAktif = PortalWali::aktif($request, $anakList);
        $pendaftaran = PortalWali::pendaftaran($user);

        return view('wali_santri.profil', compact(
            'user',
            'anakList',
            'anakAktif',
            'pendaftaran'
        ));
    }

    /** Perbarui nama & nomor HP akun wali. */
    public function updateProfil(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9\+\-\s]*$/'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka, spasi, +, atau -.',
            'phone.max' => 'Nomor HP maksimal 30 karakter.',
        ]);

        $user->forceFill([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ])->save();

        return redirect()
            ->route('wali.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /** Ganti kata sandi akun wali. */
    public function updatePassword(Request $request)
    {
        $this->pastikanWali();

        $user = Auth::user();

        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
        ]);

        if (! Hash::check($data['password_lama'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return redirect()
            ->route('wali.profil')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /* ============================================================= Helper */

    /**
     * Periode yang dipakai halaman nilai. Nilai yang tidak valid diabaikan.
     *
     * @param  Collection<int, array{tahun_ajaran:string,semester:string}>  $opsi
     * @return array{0:string,1:string}
     */
    private function periode(Request $request, Collection $opsi): array
    {
        $tahun = trim((string) $request->input('tahun_ajaran'));
        $semester = strtolower((string) $request->input('semester'));

        if ($tahun !== '' && ! preg_match('/^\d{4}\/\d{4}$/', $tahun)) {
            $tahun = '';
        }

        $daftarTahun = $opsi->pluck('tahun_ajaran')->filter()->unique()->values();

        if ($tahun !== '' && $daftarTahun->isNotEmpty() && ! $daftarTahun->contains($tahun)) {
            $tahun = '';
        }

        $pertama = $opsi->first();

        if ($tahun === '') {
            $tahun = $pertama['tahun_ajaran'] ?? Akademik::tahunAjaranSekarang();
        }

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = $pertama['semester'] ?? Akademik::semesterSekarang();
        }

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        return [$tahun, $semester];
    }

    /** @return array<string, mixed> */
    private function raporKosong(): array
    {
        return [
            'mapel' => collect(),
            'rata' => null,
            'predikat' => '-',
            'jumlah' => 0,
            'tuntas' => 0,
            'catatan' => null,
            'terakhir' => null,
            'baru' => false,
            'sumber' => 'kosong',
        ];
    }
}
