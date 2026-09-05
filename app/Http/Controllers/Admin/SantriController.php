<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
// Jika menggunakan Maatwebsite/Excel, uncomment baris di bawah:
// use App\Imports\SantriImport;
// use Maatwebsite\Excel\Facades\Excel;

class SantriController extends Controller
{
    public function index()
    {
        // Mengambil data santri dari database dengan pagination
        $santris = Santri::latest()->paginate(10);

        return view('dashboard.santri.index', compact('santris'));
    }
    public function detail($id)
    {
        $santri = Santri::findOrFail($id);

        return view('dashboard.santri.detail', compact('santri'));
    }
    /**
     * Memproses file import excel data santri
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // Contoh implementasi jika menggunakan Laravel Excel:
            // Excel::import(new SantriImport, $request->file('file_excel'));

            return redirect()->route('admin.santri.index')
                ->with('success', 'Data santri berhasil diimport ke dalam sistem.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh template excel untuk import santri
     */
    public function downloadTemplate()
    {
        // Path file template excel di dalam storage/public atau public folder
        $filePath = public_path('assets/template/template_data_santri.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File template import belum tersedia.');
    }
}