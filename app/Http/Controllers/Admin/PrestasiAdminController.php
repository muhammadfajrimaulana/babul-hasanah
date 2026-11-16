<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSantri;
use App\Support\PrestasiJenjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrestasiAdminController extends Controller
{
    public const TINGKAT = ['Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];

    public function index()
    {
        $prestasi = PrestasiJenjang::query(auth()->user())
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->paginate(10);

        return view('dashboard.prestasi.index', compact('prestasi'));
    }

    public function create()
    {
        return view('dashboard.prestasi.form', $this->formData(null));
    }

    public function store(Request $request)
    {
        $data = $this->siapkan($request);

        PrestasiSantri::create($data);

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(PrestasiSantri $prestasi)
    {
        $this->pastikanBoleh($prestasi);

        return view('dashboard.prestasi.form', $this->formData($prestasi));
    }

    public function update(Request $request, PrestasiSantri $prestasi)
    {
        $this->pastikanBoleh($prestasi);

        $prestasi->update($this->siapkan($request));

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(PrestasiSantri $prestasi)
    {
        $this->pastikanBoleh($prestasi);

        $prestasi->delete();

        return redirect()->route('admin.prestasi.index')->with('success', 'Prestasi dihapus.');
    }

    /* ---------- bantu ---------- */

    /** Tolak bila prestasi berada di luar jenjang pengguna. */
    private function pastikanBoleh(PrestasiSantri $prestasi): void
    {
        abort_unless(
            PrestasiJenjang::cocok(auth()->user(), $prestasi),
            403,
            'Prestasi ini berada di luar jenjang yang Anda tangani.'
        );
    }

    private function formData(?PrestasiSantri $prestasi): array
    {
        return [
            'prestasi' => $prestasi,
            'kategori' => PrestasiSantri::query()->distinct()->pluck('kategori'),
            'tingkatList' => self::TINGKAT,
        ];
    }

    private function siapkan(Request $request): array
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'nama' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'kategori' => 'required|string|max:60',
            'tingkat' => ['required', Rule::in(self::TINGKAT)],
            'wilayah' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'peringkat' => 'required|integer|min:0|max:99',
            'label' => 'nullable|string|max:40',
            'deskripsi' => 'nullable|string|max:400',
        ]);

        // Admin Jenjang hanya boleh mencatat prestasi santri pada jenjangnya.
        // Tanpa aturan ini, data yang disimpan akan langsung "hilang" dari
        // daftar karena tidak lolos filter jenjang.
        if (! PrestasiJenjang::cocokNama(auth()->user(), $data['nama'])) {
            throw ValidationException::withMessages([
                'nama' => 'Nama santri tidak ditemukan pada jenjang yang Anda tangani. '
                    .'Pastikan ejaan nama sama dengan data santri.',
            ]);
        }

        // Label kosong → dibuat otomatis dari peringkat
        if (blank($data['label'] ?? null)) {
            $data['label'] = $data['peringkat'] > 0 ? 'Juara '.$data['peringkat'] : 'Penghargaan';
        }

        return $data;
    }
}
