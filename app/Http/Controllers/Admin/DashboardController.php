<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\InfoHarian;
use App\Models\Kepengurusan;
use App\Models\PendaftaranOnline;
use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        $totalSantri = $this->hitung(Santri::class);
        $totalPendaftar = PendaftaranOnline::count();
        $pendaftarMenunggu = PendaftaranOnline::where(function ($q) {
            $q->whereNull('status_pendaftaran')
                ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
        })->count();
        $pendaftarTerbaru = PendaftaranOnline::latest()->take(5)->get();

        $totalBerita = Berita::published()->count();
        $beritaBulanIni = Berita::published()
            ->whereMonth('published_at', $now->month)
            ->whereYear('published_at', $now->year)
            ->count();

        $totalAsatidz = $this->hitung(Kepengurusan::class);
        $jmlGaleri = $this->hitung(\App\Models\Album::class);
        $totalAlumni = $this->hitung(Alumni::class);
        $totalPengguna = $this->hitung(User::class);
        $isSuperAdmin = in_array(strtolower((string) (auth()->user()->role ?? '')), ['super_admin', 'superadmin', 'super admin', 'super'], true);
        $jmlPrestasi = $this->hitung(PrestasiSantri::class);

        $tahunAjaran = $now->month >= 7
            ? $now->year . '/' . ($now->year + 1)
            : ($now->year - 1) . '/' . $now->year;

        // ---- Grafik: Pendaftar per Jenjang ----
        $pendaftarPerJenjang = $this->pendaftarPerJenjang();

        // ---- Grafik: Santri per Jenjang ----
        $santriPerJenjang = $this->santriPerJenjang($totalSantri);

        // ---- Grafik: Alumni per Jenjang ----
        $alumniPerJenjang = $this->alumniPerJenjang($totalAlumni);

        // ---- Grafik: Tren pendaftar 6 bulan ----
        $tren = $this->trenPendaftar();

        // ---- Info Harian (dengan guard tabel belum ada) ----
        $infoHarian = [];
        try {
            if (class_exists(InfoHarian::class) && Schema::hasTable((new InfoHarian)->getTable())) {
                $infoHarian = InfoHarian::aktif()->berlaku()->sudahTerbit()
                    ->orderByDesc('penting')->orderByDesc('tanggal')->orderByDesc('urutan')
                    ->take(8)->get();
            }
        } catch (\Throwable $e) {
            $infoHarian = [];
        }

        // ---- Kalender: event dari InfoHarian (jenis jadwal) ----
        $kalenderEvents = [];
        try {
            if (class_exists(InfoHarian::class) && Schema::hasTable((new InfoHarian)->getTable())) {
                $kalenderEvents = InfoHarian::jenis('jadwal')->aktif()->berlaku()->sudahTerbit()
                    ->orderBy('tanggal')->orderBy('jam')
                    ->take(12)->get()->map(function ($e) {
                        return [
                            'title' => $e->judul,
                            'date' => $e->tanggal ? $e->tanggal->format('Y-m-d') : now()->format('Y-m-d'),
                            'time' => $e->jam ?? '-',
                            'category' => $e->kategori ?? 'Jadwal',
                            'desc' => $e->isi ?? '',
                            'color' => $this->warnaKategori($e->kategori ?? 'Jadwal'),
                        ];
                    })->all();
            }
        } catch (\Throwable $e) {
            $kalenderEvents = [];
        }

        // ---- Agenda terdekat ----
        $agenda = $this->agendaTerdekat();

        $data = [
            'totalSantri' => $totalSantri,
            'santriPutra' => 0,
            'santriPutri' => 0,
            'totalPendaftar' => $totalPendaftar,
            'pendaftarMenunggu' => $pendaftarMenunggu,
            'pendaftarTerbaru' => $pendaftarTerbaru,
            'totalBerita' => $totalBerita,
            'beritaBulanIni' => $beritaBulanIni,
            'totalAsatidz' => $totalAsatidz,
            'jmlGaleri' => $jmlGaleri,
            'totalAlumni' => $totalAlumni,
            'totalPengguna' => $totalPengguna,
            'isSuperAdmin' => $isSuperAdmin,
            'jmlPrestasi' => $jmlPrestasi,
            'tahunAjaran' => $tahunAjaran,
            'tren' => $tren,
            'komposisi' => $santriPerJenjang,
            'agenda' => $agenda,
            'pendaftarPerJenjang' => $pendaftarPerJenjang,
            'santriPerJenjang' => $santriPerJenjang,
            'alumniPerJenjang' => $alumniPerJenjang,
            'infoHarian' => $infoHarian,
            'kalenderEvents' => $kalenderEvents,
            'userAvatar' => auth()->user()->avatar ?? null,
        ];

        return view('dashboard.index', $data);
    }

    /** Halaman pengaturan profil pengguna yang login. */
    public function profil()
    {
        return view('dashboard.profil', [
            'user' => auth()->user(),
        ]);
    }

    /** Update nama, email, dan role (hanya super_admin yang boleh ganti role). */
    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];
        if ($isSuperAdmin = in_array(strtolower((string) ($user->role ?? '')), ['super_admin', 'superadmin', 'super admin', 'super'], true)) {
            $rules['role'] = 'required|in:super_admin,admin,wali_santri';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        if (isset($rules['role'])) {
            $user->role = $request->role;
        }
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /** Ganti / reset password akun yang login. */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }

    /** Upload / ubah foto profil. */
    public function updateAvatar(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Hapus avatar lama jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /** Cek model aman. */
    private function modelAda(string $model): bool
    {
        try {
            return class_exists($model);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Hitung data dengan aman. */
    private function hitung(string $model, ?callable $filter = null): int
    {
        if (!$this->modelAda($model)) {
            return 0;
        }
        $instance = new $model;
        if (!Schema::hasTable($instance->getTable())) {
            return 0;
        }
        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }
            return $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Jumlah pendaftar per jenjang tujuan. */
    private function pendaftarPerJenjang(): array
    {
        $model = PendaftaranOnline::class;
        if (!$this->modelAda($model) || !Schema::hasTable((new $model)->getTable())) {
            return [];
        }
        try {
            return $model::query()
                ->selectRaw('jenjang_tujuan as jenjang, COUNT(*) as jml')
                ->whereNotNull('jenjang_tujuan')
                ->groupBy('jenjang_tujuan')
                ->orderByDesc('jml')
                ->get()
                ->map(fn ($r) => [
                    'jenjang' => $r->jenjang ?: '-',
                    'jml' => (int) $r->jml,
                ])->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Komposisi santri per jenjang pendidikan: [nama, persen, jumlah, warna, ikon]. */
    private function santriPerJenjang(int $total): array
    {
        $model = Santri::class;
        if ($total === 0 || !$this->modelAda($model)) {
            return [];
        }
        try {
            $rows = $model::query()
                ->selectRaw('jenjang_pendidikan as jenjang, COUNT(*) as jml')
                ->whereNotNull('jenjang_pendidikan')
                ->groupBy('jenjang_pendidikan')
                ->get();
        } catch (\Throwable $e) {
            return [];
        }

        $warna = ['#17402f', '#b08d3e', '#3b82c4', '#8b5cc6', '#d6525a', '#0e9f8e', '#9cb49d'];
        $ikon = 'fa-graduation-cap';

        return $rows->values()->map(fn ($r, $i) => [
            'jenjang' => $r->jenjang ?: 'Belum diatur',
            'persen' => round($r->jml / $total * 100),
            'jml' => (int) $r->jml,
            'warna' => $warna[$i % count($warna)],
            'ikon' => $ikon,
        ])->all();
    }

    /** Alumni per jenjang. */
    private function alumniPerJenjang(int $total): array
    {
        $model = Alumni::class;
        if ($total === 0 || !$this->modelAda($model)) {
            return [];
        }
        try {
            return $model::query()
                ->selectRaw('jenjang, COUNT(*) as jml')
                ->whereNotNull('jenjang')
                ->groupBy('jenjang')
                ->orderByDesc('jml')
                ->get()
                ->map(fn ($r) => [
                    'jenjang' => $r->jenjang ?: '-',
                    'jml' => (int) $r->jml,
                ])->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Jumlah pendaftar per bulan, 6 bulan terakhir. */
    private function trenPendaftar(): array
    {
        $tren = [];
        for ($i = 5; $i >= 0; $i--) {
            $awal = now()->subMonths($i)->startOfMonth();
            $label = $awal->copy()->locale('id')->translatedFormat('M');
            $tren[$label] = PendaftaranOnline::whereBetween('created_at', [$awal, $awal->copy()->endOfMonth()])->count();
        }
        return $tren;
    }

    /** Agenda terdekat (dari InfoHarian jadwal). */
    private function agendaTerdekat(): array
    {
        try {
            if (!class_exists(InfoHarian::class) || !Schema::hasTable((new InfoHarian)->getTable())) {
                return [];
            }
            return InfoHarian::jenis('jadwal')->aktif()->berlaku()->sudahTerbit()
                ->orderBy('tanggal')->orderBy('jam')
                ->take(3)->get()->map(function ($k) {
                    $tgl = \Illuminate\Support\Carbon::parse($k->tanggal)->locale('id');
                    return [
                        $tgl->format('d'),
                        $k->judul ?? 'Kegiatan',
                        $k->jam ?? '-',
                        $k->kategori ?? '-',
                        strtoupper($tgl->translatedFormat('M')),
                    ];
                })->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function warnaKategori(string $kategori): string
    {
        return match ($kategori) {
            'Pengumuman' => '#3b82c4',
            'Jadwal' => '#12a67a',
            'Agenda' => '#c08a1c',
            'Libur' => '#d6525a',
            default => '#8b5cc6',
        };
    }
}
