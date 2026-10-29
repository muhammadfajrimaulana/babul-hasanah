<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StrukturOrganisasiController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $this->kategoriDariQuery($request);

        $items = $kategori === 'bagan'
            ? StrukturOrganisasi::baganBerurutan()
            : StrukturOrganisasi::diKategori($kategori)->urut()->get();

        $jumlah = StrukturOrganisasi::selectRaw('kategori, count(*) as total')
            ->groupBy('kategori')->pluck('total', 'kategori');

        return view('dashboard.strukturorganisasi.index', compact('items', 'kategori', 'jumlah'));
    }

    public function create(Request $request)
    {
        return view('dashboard.strukturorganisasi.form', [
            'item'     => null,
            'kategori' => $this->kategoriDariQuery($request),
            'indukOpsi' => StrukturOrganisasi::baganBerurutan(),
        ]);
    }

    public function store(Request $request)
    {
        $kategori = $request->validate([
            'kategori' => ['required', Rule::in(array_keys(StrukturOrganisasi::KATEGORI))],
        ])['kategori'];

        $data = $this->validasi($request, $kategori);
        $data['kategori'] = $kategori;
        $data['aktif']    = $request->boolean('aktif');

        if ($kategori !== 'bagan') {
            $data['parent_id'] = null;
        }

        if ($request->hasFile('foto') && $this->bolehFoto($kategori)) {
            $data['foto'] = $request->file('foto')->store('struktur-organisasi', 'public');
        } else {
            unset($data['foto']);
        }

        if (!isset($data['urutan'])) {
            $data['urutan'] = $this->urutanBerikut($kategori, $data['parent_id'] ?? null);
        }

        unset($data['hapus_foto']);
        StrukturOrganisasi::create($data);

        return redirect()
            ->route('admin.struktur.index', ['kategori' => $kategori])
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(StrukturOrganisasi $struktur)
    {
        return view('dashboard.strukturorganisasi.form', [
            'item'      => $struktur,
            'kategori'  => $struktur->kategori,
            'indukOpsi' => StrukturOrganisasi::baganBerurutan(),
        ]);
    }

    public function update(Request $request, StrukturOrganisasi $struktur)
    {
        $kategori = $struktur->kategori;

        $data = $this->validasi($request, $kategori, $struktur);
        $data['aktif'] = $request->boolean('aktif');

        if ($kategori !== 'bagan') {
            $data['parent_id'] = null;
        }

        if ($this->bolehFoto($kategori)) {
            if ($request->hasFile('foto')) {
                $this->hapusFoto($struktur->foto);
                $data['foto'] = $request->file('foto')->store('struktur-organisasi', 'public');
            } elseif ($request->boolean('hapus_foto')) {
                $this->hapusFoto($struktur->foto);
                $data['foto'] = null;
            } else {
                unset($data['foto']);
            }
        } else {
            unset($data['foto']);
        }

        if (!isset($data['urutan'])) {
            $data['urutan'] = $struktur->urutan;
        }

        unset($data['hapus_foto']);
        $struktur->update($data);

        return redirect()
            ->route('admin.struktur.index', ['kategori' => $kategori])
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(StrukturOrganisasi $struktur)
    {
        $kategori = $struktur->kategori;

        if ($struktur->anak()->exists()) {
            return back()->with('error', 'Tidak bisa dihapus karena masih punya bawahan. Pindahkan atau hapus bawahannya terlebih dahulu.');
        }

        $this->hapusFoto($struktur->foto);
        $struktur->delete();

        return redirect()
            ->route('admin.struktur.index', ['kategori' => $kategori])
            ->with('success', 'Data berhasil dihapus.');
    }

    /* ------------------------------------------------------------ Helper */

    private function validasi(Request $request, string $kategori, ?StrukturOrganisasi $item = null): array
    {
        // Atasan tidak boleh dirinya sendiri atau bawahannya (mencegah putaran).
        $tolakInduk = $item
            ? array_merge([$item->id], StrukturOrganisasi::idKeturunan($item->id))
            : [];

        return $request->validate([
            'nama'       => ['required', 'string', 'max:150'],
            'jabatan'    => [Rule::requiredIf($kategori === 'bagan'), 'nullable', 'string', 'max:150'],
            'kode'       => [Rule::requiredIf($kategori === 'jenjang'), 'nullable', 'string', 'max:30'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'ikon'       => ['nullable', Rule::in(array_keys(StrukturOrganisasi::IKON))],
            'urutan'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'parent_id'  => [
                'nullable', 'integer',
                Rule::exists('struktur_organisasi', 'id')->where('kategori', 'bagan'),
                Rule::notIn($tolakInduk),
            ],
            'foto'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max'   => 'Ukuran foto maksimal 2 MB.',
            'parent_id.not_in' => 'Atasan tidak boleh dirinya sendiri atau bawahannya.',
        ], [
            'nama' => 'nama', 'jabatan' => 'jabatan', 'kode' => 'kode jenjang',
        ]);
    }

    private function kategoriDariQuery(Request $request): string
    {
        $k = $request->query('kategori');

        return array_key_exists($k, StrukturOrganisasi::KATEGORI) ? $k : 'bagan';
    }

    private function bolehFoto(string $kategori): bool
    {
        return in_array($kategori, ['bagan', 'jenjang'], true);
    }

    private function hapusFoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function urutanBerikut(string $kategori, ?int $parentId): int
    {
        $q = StrukturOrganisasi::diKategori($kategori);
        $parentId === null ? $q->whereNull('parent_id') : $q->where('parent_id', $parentId);

        return ((int) $q->max('urutan')) + 1;
    }
}