<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSantri;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrestasiAdminController extends Controller
{
    public const TINGKAT = ['Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];

    public function index()
    {
        $prestasi = PrestasiSantri::orderByDesc('tahun')->orderByDesc('id')->paginate(10);

        return view('dashboard.prestasi.index', compact('prestasi'));
    }

    public function create()
    {
        return view('dashboard.prestasi.form', $this->formData(null));
    }

    public function store(Request $request)
    {
        PrestasiSantri::create($this->siapkan($request));

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(PrestasiSantri $prestasi)
    {
        return view('dashboard.prestasi.form', $this->formData($prestasi));
    }

    public function update(Request $request, PrestasiSantri $prestasi)
    {
        $prestasi->update($this->siapkan($request));

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(PrestasiSantri $prestasi)
    {
        $prestasi->delete();

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi dihapus.');
    }

    /* ---------- bantu ---------- */

    private function formData(?PrestasiSantri $prestasi): array
    {
        return [
            'prestasi'    => $prestasi,
            'kategori'    => PrestasiSantri::query()->distinct()->pluck('kategori'),
            'tingkatList' => self::TINGKAT,
        ];
    }

    private function siapkan(Request $request): array
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'nama'      => 'required|string|max:100',
            'kelas'     => 'required|string|max:50',
            'kategori'  => 'required|string|max:60',
            'tingkat'   => ['required', Rule::in(self::TINGKAT)],
            'wilayah'   => 'required|string|max:100',
            'tahun'     => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'peringkat' => 'required|integer|min:0|max:99',
            'label'     => 'nullable|string|max:40',
            'deskripsi' => 'nullable|string|max:400',
        ]);

        // Label kosong → dibuat otomatis dari peringkat
        if (blank($data['label'] ?? null)) {
            $data['label'] = $data['peringkat'] > 0 ? 'Juara ' . $data['peringkat'] : 'Penghargaan';
        }

        return $data;
    }
}
