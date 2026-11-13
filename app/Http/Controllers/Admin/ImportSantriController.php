<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\User;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\Ppdb;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * Import data santri dari Excel/CSV (admin jenjang & Super Admin).
 *
 * File diproses oleh App\Imports\StudentsImport. Setiap baris divalidasi
 * terpisah sehingga satu baris gagal tidak membatalkan baris lainnya.
 */
class ImportSantriController extends Controller
{
    /** Form upload + riwayat singkat hasil import. */
    public function index()
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);
        $slug = Akses::jenjangSlug($user);

        $students = Student::query()
            ->with(['user', 'kelas'])
            ->when($slug, fn ($q) => $q->whereIn('jenjang', Ppdb::variasiJenjang($slug)))
            ->latest()
            ->paginate(10);

        return view('dashboard.santri-import.index', [
            'students' => $students,
            'laporan' => session('import_laporan'),
            'jenjangSlug' => $slug,
            'targetJenjang' => $slug
                ? Jenjang::label($slug)
                : 'Semua jenjang (Super Admin)',
        ]);
    }

    /** Proses file Excel yang diunggah. */
    public function import(Request $request)
    {
        $user = auth()->user();
        $this->pastikanBolehMasuk($user);

        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file_excel.required' => 'Pilih file Excel yang akan diimpor.',
            'file_excel.file' => 'File tidak valid.',
            'file_excel.mimes' => 'Format file harus .xlsx, .xls, atau .csv.',
            'file_excel.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $slug = Akses::jenjangSlug($user);
        $import = new StudentsImport($slug);

        try {
            Excel::import($import, $request->file('file_excel'));
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal membaca file: '.$e->getMessage());
        }

        $laporan = [
            'total' => count($import->berhasil) + count($import->gagal),
            'berhasil' => count($import->berhasil),
            'dibuat' => $import->dibuat,
            'diperbarui' => $import->diperbarui,
            'gagal' => $import->gagal,
            'detail' => $import->berhasil,
        ];

        if ($laporan['total'] === 0) {
            return back()->with('error', 'Tidak ada baris data yang terbaca. Pastikan baris pertama berisi judul kolom: nisn, nama_santri, jenjang, parent_email.');
        }

        $pesan = $laporan['berhasil'].' baris berhasil diproses';

        if ($laporan['gagal'] !== []) {
            $pesan .= ', '.count($laporan['gagal']).' baris gagal (lihat rincian).';
        } else {
            $pesan .= '.';
        }

        return redirect()
            ->route('admin.santri.import.index')
            ->with('import_laporan', $laporan)
            ->with($laporan['berhasil'] > 0 ? 'success' : 'error', $pesan);
    }

    /** Unduh template Excel empat kolom. */
    public function template()
    {
        return Excel::download(
            new StudentsTemplateExport,
            'template-import-santri-'.date('Ymd').'.xlsx'
        );
    }

    private function pastikanBolehMasuk(?User $user): void
    {
        abort_unless($user, 403);
        abort_if(
            Akses::adminTanpaJenjang($user),
            403,
            'Akun admin Anda belum memiliki jenjang. Hubungi Super Admin.'
        );
    }
}
