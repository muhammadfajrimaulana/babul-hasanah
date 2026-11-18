<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrestasiSantri;
use App\Support\Akses;
use App\Support\Jenjang;
use App\Support\RekapJenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PrestasiAdminController extends Controller
{
    public const TINGKAT = ['Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'];

    public function index(Request $request)
    {
        $user = auth()->user();
        $batasiJenjang = Akses::dibatasiJenjang($user);

        // Admin Jenjang terkunci pada jenjangnya; role global bisa memilih
        // satu jenjang atau melihat gabungan seluruh jenjang.
        $filterJenjang = $batasiJenjang
            ? (string) (Akses::jenjangSlug($user) ?? '')
            : (string) (Jenjang::normalisasi($request->input('jenjang')) ?? '');

        $nilaiJenjang = $batasiJenjang
            ? (Akses::nilaiJenjangDb($user, PrestasiSantri::class, 'jenjang') ?: ['__tidak_ada__'])
            : ($filterJenjang !== ''
                ? (RekapJenjang::nilaiDb(PrestasiSantri::class, 'jenjang', $filterJenjang) ?: ['__tidak_ada__'])
                : []);

        $prestasi = PrestasiSantri::query()
            ->when($nilaiJenjang !== [], fn ($q) => $q->whereIn('jenjang', $nilaiJenjang))
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.prestasi.index', [
            'prestasi' => $prestasi,
            'filterJenjang' => $filterJenjang,
            'daftarJenjang' => $batasiJenjang ? [] : Jenjang::semua(),
            'bisaFilterJenjang' => ! $batasiJenjang,
            'namaJenjangAdmin' => $batasiJenjang ? Akses::namaJenjang($user) : null,
        ]);
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
        $user = auth()->user();

        return [
            'prestasi'    => $prestasi,
            'kategori'    => PrestasiSantri::query()->distinct()->pluck('kategori'),
            'tingkatList' => self::TINGKAT,
            'daftarJenjang' => Akses::dibatasiJenjang($user) ? [] : Jenjang::semua(),
            'bisaPilihJenjang' => ! Akses::dibatasiJenjang($user),
            'jenjangTerkunci' => Akses::dibatasiJenjang($user) ? Akses::namaJenjang($user) : null,
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
            'jenjang'   => 'nullable|string|max:60',
        ]);

        // Label kosong → dibuat otomatis dari peringkat
        if (blank($data['label'] ?? null)) {
            $data['label'] = $data['peringkat'] > 0 ? 'Juara ' . $data['peringkat'] : 'Penghargaan';
        }

        // Admin Jenjang selalu mencatat prestasi untuk jenjangnya sendiri.
        // Role global boleh memilih jenjang lewat form.
        if (! Schema::hasColumn('prestasi_santri', 'jenjang')) {
            unset($data['jenjang']);
        } elseif (Akses::dibatasiJenjang($request->user())) {
            $data['jenjang'] = Akses::jenjangSlug($request->user());
        } else {
            $slug = Jenjang::normalisasi($data['jenjang'] ?? null);

            if ($slug) {
                $data['jenjang'] = $slug;
            } else {
                unset($data['jenjang']);
            }
        }

        return $data;
    }
}
