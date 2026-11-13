<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranOnline;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Ppdb;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Modul PPDB: verifikasi kelulusan dan plotting santri ke kelas.
 *
 * Semua akses dibatasi oleh `Akses::jenjangSlug()`:
 * - Admin jenjang hanya melihat/mengubah data pada jenjangnya.
 * - Admin jenjang tanpa jenjang ditolak (fail-closed).
 * - Super Admin melihat semua jenjang.
 */
class PpdbController extends Controller
{
    /** Halaman verifikasi kelulusan PPDB. */
    public function kelulusan(Request $request)
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);
        $slug = Akses::jenjangSlug($user);

        $dasar = $this->queryPendaftaran($user);

        $statistik = [
            'total' => (clone $dasar)->count(),
            'pending' => (clone $dasar)->where(fn (Builder $q) => $this->wherePending($q))->count(),
            'diterima' => (clone $dasar)->where('status_pendaftaran', 'diterima')->count(),
            'ditolak' => (clone $dasar)->where('status_pendaftaran', 'ditolak')->count(),
        ];

        $pendaftar = (clone $dasar)
            ->when($request->filled('status'), function (Builder $q) use ($request) {
                $status = (string) $request->input('status');

                if ($status === 'pending') {
                    $q->where(fn (Builder $w) => $this->wherePending($w));
                } else {
                    $q->where('status_pendaftaran', $status);
                }
            })
            ->when(! $slug && $request->filled('jenjang'), function (Builder $q) use ($request) {
                $q->whereIn('jenjang_tujuan', Ppdb::variasiJenjang((string) $request->input('jenjang')));
            })
            ->when($request->filled('q'), function (Builder $q) use ($request) {
                $cari = '%'.$request->input('q').'%';
                $q->where(function (Builder $w) use ($cari) {
                    $w->where('nama_lengkap', 'like', $cari)
                        ->orWhere('no_pendaftaran', 'like', $cari)
                        ->orWhere('nisn', 'like', $cari)
                        ->orWhere('email', 'like', $cari)
                        ->orWhere('parent_email', 'like', $cari)
                        ->orWhere('nama_ortu_wali', 'like', $cari)
                        ->orWhere('asal_sekolah', 'like', $cari);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.ppdb.kelulusan', [
            'pendaftar' => $pendaftar,
            'statistik' => $statistik,
            'jenjangSlug' => $slug,
            'jenjangList' => $this->opsiJenjang(),
            'filter' => $request->only(['status', 'jenjang', 'q']),
        ]);
    }

    /** Terima atau tolak satu pendaftar PPDB. */
    public function verifikasi(Request $request, PendaftaranOnline $pendaftaran)
    {
        $request->validate([
            'status_pendaftaran' => ['required', Rule::in(['diterima', 'ditolak'])],
        ], [
            'status_pendaftaran.required' => 'Pilih status verifikasi terlebih dahulu.',
            'status_pendaftaran.in' => 'Status verifikasi tidak valid.',
        ]);

        $this->pastikanJenjang($pendaftaran->jenjang_tujuan);

        $status = (string) $request->input('status_pendaftaran');

        try {
            if ($status === 'ditolak') {
                Ppdb::tolak($pendaftaran);

                return back()->with('success', 'Status pendaftaran berhasil diperbarui menjadi Ditolak.');
            }

            $hasil = Ppdb::terima($pendaftaran);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $email = Ppdb::emailWali($pendaftaran);

        $infoLogin = $hasil['rawPassword']
            ? "Email: {$email}\nPassword: {$hasil['rawPassword']}"
            : "Email: {$email}\nPassword: sama seperti akun portal Anda sebelumnya";

        $pesanWa = "Alhamdulillah, Pendaftaran santri atas nama *{$pendaftaran->nama_lengkap}* telah *DITERIMA* di Pesantren Babul Hasanah.\n\n"
            ."Silakan login ke Portal Wali Santri:\n{$infoLogin}\n\n"
            .'Silakan login melalui link berikut: '.route('login');

        $phone = preg_replace('/\D/', '', (string) ($pendaftaran->no_hp_ortu ?: $pendaftaran->no_hp_aktif));

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $pesan = $hasil['akunBaru']
            ? 'Status berhasil diubah menjadi Diterima! Akun wali santri telah dibuat.'
            : 'Status berhasil diubah menjadi Diterima! Santri ditautkan ke akun wali yang sudah ada.';

        if ($hasil['diaktifkan']) {
            $pesan .= $hasil['emailTerkirim']
                ? ' Akun wali diaktifkan dan kata sandi dikirim ke '.$email.'.'
                : ' Akun wali diaktifkan, tetapi email gagal dikirim. Sampaikan kata sandi melalui WhatsApp.';
        }

        return back()->with([
            'success' => $pesan,
            'wa_link' => $phone !== '' ? 'https://wa.me/'.$phone.'?text='.urlencode($pesanWa) : null,
            'nama_santri' => $pendaftaran->nama_lengkap,
        ]);
    }

    /** Halaman plotting santri ke kelas. */
    public function plotting(Request $request)
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);
        $slug = Akses::jenjangSlug($user);

        $dasar = $this->queryStudent($user);

        $statistik = [
            'total' => (clone $dasar)->count(),
            'belum' => (clone $dasar)->where(fn (Builder $q) => $q->whereNull('class_id')->whereDoesntHave('classes'))->count(),
            'sudah' => (clone $dasar)->where(fn (Builder $q) => $q->whereNotNull('class_id')->orWhereHas('classes'))->count(),
            'calon' => (clone $dasar)->where('status', 'calon')->count(),
        ];

        $students = (clone $dasar)
            ->when(! $slug && $request->filled('jenjang'), function (Builder $q) use ($request) {
                $q->whereIn('jenjang', Ppdb::variasiJenjang((string) $request->input('jenjang')));
            })
            ->when($request->filled('class_id'), function (Builder $q) use ($request) {
                $id = (int) $request->input('class_id');

                $q->where(function (Builder $w) use ($id) {
                    $w->where('class_id', $id)
                        ->orWhereHas('classes', fn (Builder $p) => $p->where('classes.id', $id));
                });
            })
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), function (Builder $q) use ($request) {
                $cari = '%'.$request->input('q').'%';
                $q->where(function (Builder $w) use ($cari) {
                    $w->where('name', 'like', $cari)
                        ->orWhere('nisn', 'like', $cari)
                        ->orWhere('parent_email', 'like', $cari);
                });
            })
            ->with(['user', 'kelas', 'classes', 'pendaftaranOnline'])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $kelasList = Ppdb::kelasUntuk($slug);

        return view('dashboard.ppdb.plotting', [
            'students' => $students,
            'statistik' => $statistik,
            'kelasList' => $kelasList,
            'kelasPerJenjang' => $kelasList->groupBy(fn (SchoolClass $k) => Jenjang::label($k->jenjang) ?: $k->jenjang),
            'jenjangSlug' => $slug,
            'jenjangList' => $this->opsiJenjang(),
            'filter' => $request->only(['jenjang', 'class_id', 'status', 'q']),
        ]);
    }

    /** Plot satu atau banyak santri ke kelas tujuan. */
    public function plot(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', 'integer', Rule::exists('classes', 'id')],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', Rule::exists('students', 'id')],
        ], [
            'class_id.required' => 'Pilih kelas tujuan terlebih dahulu.',
            'class_id.exists' => 'Kelas tujuan tidak ditemukan.',
            'student_ids.required' => 'Pilih minimal satu santri yang akan diplot.',
            'student_ids.min' => 'Pilih minimal satu santri yang akan diplot.',
            'student_ids.*.exists' => 'Ada data santri yang tidak ditemukan.',
        ]);

        $kelas = SchoolClass::findOrFail($data['class_id']);
        $this->pastikanJenjang($kelas->jenjang);

        $hasil = Ppdb::plotBanyak($data['student_ids'], $kelas);

        $pesan = $hasil['berhasil'].' santri berhasil diplot ke kelas '.$kelas->label().'.';

        if ($hasil['dilewati'] !== []) {
            return back()
                ->with('success', $pesan)
                ->with('plot_gagal', $hasil['dilewati']);
        }

        return back()->with('success', $pesan);
    }

    /** Tambah kelas sederhana dari halaman plotting. */
    public function storeKelas(Request $request)
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);
        $slugAdmin = Akses::jenjangSlug($user);

        $data = $request->validate([
            'jenjang' => ['required', 'string', Rule::in(array_column(Jenjang::semua(), 'slug'))],
            'name' => ['required', 'string', 'max:50'],
            'tingkat' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'jenjang.required' => 'Pilih jenjang kelas.',
            'jenjang.in' => 'Jenjang kelas tidak valid.',
            'name.required' => 'Nama kelas wajib diisi.',
            'name.max' => 'Nama kelas maksimal 50 karakter.',
        ]);

        $slug = Ppdb::jenjangSlug($data['jenjang']);
        abort_unless($slug, 422, 'Jenjang kelas tidak valid.');

        if ($slugAdmin && $slug !== $slugAdmin) {
            abort(403, 'Anda hanya boleh membuat kelas pada jenjang yang Anda tangani.');
        }

        // `classes` memiliki unique (jenjang, name). Baris yang soft-deleted
        // tidak terlihat oleh firstOrCreate, jadi pulihkan lebih dulu agar
        // pembuatan ulang kelas yang pernah dihapus tidak melanggar unique.
        $kelas = SchoolClass::withTrashed()
            ->where('jenjang', $slug)
            ->where('name', $data['name'])
            ->first();

        if ($kelas && $kelas->trashed()) {
            $kelas->restore();
        }

        $kelas ??= new SchoolClass(['jenjang' => $slug, 'name' => $data['name']]);

        $kelas->fill([
            'jenjang' => $slug,
            'name' => $data['name'],
            'tingkat' => $data['tingkat'] ?? $kelas->tingkat,
            'keterangan' => $data['keterangan'] ?? $kelas->keterangan,
        ])->save();

        return back()->with('success', 'Kelas '.$data['name'].' ('.Jenjang::label($slug).') siap dipakai.');
    }

    /** Query pendaftar yang terlihat oleh pengguna. */
    private function queryPendaftaran(User $user): Builder
    {
        $query = PendaftaranOnline::query();
        $slug = Akses::jenjangSlug($user);

        if ($slug) {
            $query->whereIn('jenjang_tujuan', Ppdb::variasiJenjang($slug));
        }

        return $query;
    }

    /** Query santri (tabel students) yang terlihat oleh pengguna. */
    private function queryStudent(User $user): Builder
    {
        $query = Student::query();
        $slug = Akses::jenjangSlug($user);

        if ($slug) {
            $query->whereIn('jenjang', Ppdb::variasiJenjang($slug));
        }

        return $query;
    }

    private function wherePending(Builder $query): void
    {
        $query->whereNull('status_pendaftaran')
            ->orWhereIn('status_pendaftaran', ['', 'pending', 'menunggu']);
    }

    /** Tolak admin jenjang yang belum memiliki jenjang (fail-closed). */
    private function pastikanBolehMasuk(?User $user): void
    {
        abort_unless($user, 403);
        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin Anda belum memiliki jenjang. Hubungi Super Admin.'
        );
    }

    /** Pastikan satu nilai jenjang boleh diakses pengguna aktif. */
    private function pastikanJenjang(?string $nilai): void
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);

        abort_unless(
            Akses::cocokJenjang($user, $nilai),
            403,
            'Data ini berada di luar jenjang yang Anda tangani.'
        );
    }

    /** @return array<string, string> */
    private function opsiJenjang(): array
    {
        return collect(Jenjang::semua())
            ->mapWithKeys(fn ($j) => [$j['slug'] => $j['kode'].' — '.$j['nama']])
            ->all();
    }
}
