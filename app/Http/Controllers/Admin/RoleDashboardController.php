<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Alumni;
use App\Models\Berita;
use App\Models\Kepengurusan;
use App\Models\PendaftaranOnline;
use App\Models\PrestasiSantri;
use App\Models\Santri;
use App\Support\Akses;
use App\Support\RekapJenjang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Dashboard khusus per role.
 *
 * - admin   -> dashboard-jenjang (data dibatasi ke jenjang yang ditangani)
 * - pengasuh-> dashboard-pengasuh (monitoring seluruh pesantren, hanya baca)
 * - pembina -> dashboard-pembina (fokus asrama & kehadiran, hanya baca)
 *
 * Super Admin tetap memakai DashboardController@index (dashboard lengkap).
 */
class RoleDashboardController extends Controller
{
    /** Dashboard Admin Jenjang: hanya data jenjang miliknya. */
    public function adminJenjang()
    {
        $user = auth()->user();
        $info = Akses::jenjangInfo($user);
        $slug = $info['slug'] ?? null;

        $nilaiSantri = $slug ? RekapJenjang::nilaiDb(Santri::class, 'jenjang_pendidikan', $slug) : [];
        $nilaiPendaftar = $slug ? RekapJenjang::nilaiDb(PendaftaranOnline::class, 'jenjang_tujuan', $slug) : [];
        $nilaiAlumni = $slug ? RekapJenjang::nilaiDb(Alumni::class, 'jenjang', $slug) : [];

        $santri = fn ($q) => $nilaiSantri ? $q->whereIn('jenjang_pendidikan', $nilaiSantri) : $q->whereRaw('1 = 0');
        $pendaftar = fn ($q) => $nilaiPendaftar ? $q->whereIn('jenjang_tujuan', $nilaiPendaftar) : $q->whereRaw('1 = 0');
        $alumni = fn ($q) => $nilaiAlumni ? $q->whereIn('jenjang', $nilaiAlumni) : $q->whereRaw('1 = 0');

        $menunggu = function ($q) {
            $q->where(function ($w) {
                $w->whereNull('status_pendaftaran')
                    ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
            });
        };

        $pendaftarMenunggu = function ($q) use ($nilaiPendaftar, $menunggu) {
            if (! $nilaiPendaftar) {
                $q->whereRaw('1 = 0');

                return;
            }
            $q->whereIn('jenjang_tujuan', $nilaiPendaftar);
            $menunggu($q);
        };

        return view('dashboard.roles.admin-jenjang', [
            'jenjang' => $info,
            'totalSantri' => $this->hitung(Santri::class, $santri),
            'totalPendaftar' => $this->hitung(PendaftaranOnline::class, $pendaftar),
            'pendaftarMenunggu' => $this->hitung(PendaftaranOnline::class, $pendaftarMenunggu),
            'totalAlumni' => $this->hitung(Alumni::class, $alumni),
            'totalPrestasi' => $this->hitung(PrestasiSantri::class),
            'pendaftarTerbaru' => $this->terbaru(PendaftaranOnline::class, $pendaftar, 5),
            'santriTerbaru' => $this->terbaru(Santri::class, $santri, 5),
        ]);
    }

    /** Dashboard Pengasuh: ringkasan seluruh pesantren (hanya baca). */
    public function pengasuh()
    {
        $menunggu = function ($q) {
            $q->where(function ($w) {
                $w->whereNull('status_pendaftaran')
                    ->orWhereNotIn('status_pendaftaran', ['diterima', 'ditolak', 'Diterima', 'Ditolak']);
            });
        };

        return view('dashboard.roles.pengasuh', [
            'totalSantri' => $this->hitung(Santri::class),
            'totalPendaftar' => $this->hitung(PendaftaranOnline::class),
            'pendaftarMenunggu' => $this->hitung(PendaftaranOnline::class, $menunggu),
            'totalAlumni' => $this->hitung(Alumni::class),
            'totalPrestasi' => $this->hitung(PrestasiSantri::class),
            'totalAsatidz' => $this->hitung(Kepengurusan::class),
            'totalBerita' => $this->hitung(Berita::class, fn ($q) => $q->published()),
            'jenjangStats' => RekapJenjang::ringkasan(),
            'pendaftarTerbaru' => $this->terbaru(PendaftaranOnline::class, null, 6),
        ]);
    }

    /** Dashboard Pembina: fokus santri & kehadiran asrama (hanya baca). */
    public function pembina()
    {
        return view('dashboard.roles.pembina', [
            'totalSantri' => $this->hitung(Santri::class),
            'totalPrestasi' => $this->hitung(PrestasiSantri::class),
            'totalAlumni' => $this->hitung(Alumni::class),
            'absensiHariIni' => $this->hitung(Absensi::class, fn ($q) => $q->whereDate('tanggal', now()->toDateString())),
            'jenjangStats' => RekapJenjang::ringkasan(),
            'santriTerbaru' => $this->terbaru(Santri::class, null, 6),
            'prestasiTerbaru' => $this->terbaru(PrestasiSantri::class, null, 4),
        ]);
    }

    /** Hitung baris dengan aman (tabel/kolom belum ada tidak membuat error). */
    private function hitung(string $model, ?callable $filter = null): int
    {
        if (! $this->siap($model)) {
            return 0;
        }

        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }

            return (int) $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Ambil beberapa baris terbaru dengan aman. */
    private function terbaru(string $model, ?callable $filter = null, int $limit = 5)
    {
        if (! $this->siap($model)) {
            return collect();
        }

        try {
            $query = $model::query();
            if ($filter) {
                $filter($query);
            }

            return $query->latest()->take($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function siap(string $model): bool
    {
        try {
            if (! class_exists($model)) {
                return false;
            }

            /** @var Model $instance */
            $instance = new $model;

            return Schema::hasTable($instance->getTable());
        } catch (\Throwable $e) {
            return false;
        }
    }
}
