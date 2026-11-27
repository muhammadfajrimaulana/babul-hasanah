<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kepengurusan;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD pengurus yang ditampilkan pada section "Kepengurusan" halaman /profil.
 *
 * Tabel ini sengaja dipertahankan terpisah dari `struktur_organisasi`:
 * `struktur_organisasi` adalah bagan lengkap di /struktur-organisasi,
 * sedangkan `kepengurusan` adalah kartu pimpinan ringkas di /profil.
 */
class KepengurusanController extends Controller
{
    public function index()
    {
        $items = Kepengurusan::orderBy('urutan')->orderBy('id')->get();

        return view('dashboard.kepengurusan.index', compact('items'));
    }

    public function create()
    {
        return view('dashboard.kepengurusan.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['urutan'] = $data['urutan'] ?? $this->urutanBerikut();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kepengurusan', 'public');
        } else {
            unset($data['foto']);
        }

        unset($data['hapus_foto']);

        $item = Kepengurusan::create($data);

        CatatanAktivitas::catat('kepengurusan_tambah', $item, [
            'nama' => $item->nama,
            'jabatan' => $item->jabatan,
        ]);

        return redirect()
            ->route('admin.kepengurusan.index')
            ->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function edit(Kepengurusan $kepengurusan)
    {
        return view('dashboard.kepengurusan.form', ['item' => $kepengurusan]);
    }

    public function update(Request $request, Kepengurusan $kepengurusan)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $this->hapusFotoLama($kepengurusan->foto);
            $data['foto'] = $request->file('foto')->store('kepengurusan', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            $this->hapusFotoLama($kepengurusan->foto);
            $data['foto'] = null;
        } else {
            unset($data['foto']);
        }

        unset($data['hapus_foto']);

        $kepengurusan->update($data);

        CatatanAktivitas::catat('kepengurusan_ubah', $kepengurusan, [
            'nama' => $kepengurusan->nama,
            'jabatan' => $kepengurusan->jabatan,
            'urutan' => $kepengurusan->urutan,
        ]);

        return redirect()
            ->route('admin.kepengurusan.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(Kepengurusan $kepengurusan)
    {
        $this->hapusFotoLama($kepengurusan->foto);

        CatatanAktivitas::catat('kepengurusan_hapus', $kepengurusan, [
            'nama' => $kepengurusan->nama,
            'jabatan' => $kepengurusan->jabatan,
        ]);

        $kepengurusan->delete();

        return redirect()
            ->route('admin.kepengurusan.index')
            ->with('success', 'Data pengurus berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jabatan' => ['required', 'string', 'max:150'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ], [
            'nama' => 'nama pengurus',
            'jabatan' => 'jabatan',
        ]);
    }

    private function urutanBerikut(): int
    {
        return ((int) Kepengurusan::max('urutan')) + 1;
    }

    /**
     * Hapus hanya foto yang diunggah aplikasi ini. Berkas legacy di
     * `public/upload/kepengurusan/` sengaja tidak disentuh.
     */
    private function hapusFotoLama(?string $path): void
    {
        $path = trim((string) $path);

        if ($path !== '' && str_starts_with($path, 'kepengurusan/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
