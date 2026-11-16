<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Support\Akses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Alumni::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('nama', 'like', "%{$s}%")->orWhere('instansi', 'like', "%{$s}%");
            }))
            ->when($request->angkatan, fn ($q, $a) => $q->where('tahun_lulus', $a));

        // Admin Jenjang hanya melihat alumni pada jenjangnya.
        if (Akses::dibatasiJenjang($user)) {
            $query->whereIn(
                'jenjang',
                Akses::nilaiJenjangDb($user, Alumni::class, 'jenjang') ?: ['__tidak_ada__']
            );
        }

        $alumnis = $query->orderByDesc('tahun_lulus')->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $daftarAngkatan = Alumni::query()->select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');

        return view('dashboard.alumni.index', compact('alumnis', 'daftarAngkatan'));
    }

    public function create()
    {
        return view('dashboard.alumni.create');
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        // Admin Jenjang tidak boleh menambah alumni di luar jenjangnya.
        $this->pastikanJenjang($data['jenjang'] ?? null);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alumni', 'public');
        }

        Alumni::create($data);

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        return view('dashboard.alumni.edit', compact('alumni'));
    }

    public function update(Request $request, Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        $data = $this->validasi($request);

        // Jenjang tujuan baru juga harus tetap di dalam wewenang user.
        $this->pastikanJenjang($data['jenjang'] ?? null);

        if ($request->hasFile('foto')) {
            if ($alumni->foto) {
                Storage::disk('public')->delete($alumni->foto);
            }
            $data['foto'] = $request->file('foto')->store('alumni', 'public');
        }

        $alumni->update($data);

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        $this->pastikanJenjang($alumni->jenjang);

        if ($alumni->foto) {
            Storage::disk('public')->delete($alumni->foto);
        }
        $alumni->delete();

        return redirect()->route('admin.alumni.index')->with('success', 'Data alumni berhasil dihapus.');
    }

    /** Tolak akses bila data alumni berada di luar jenjang user. */
    private function pastikanJenjang(?string $jenjang): void
    {
        abort_unless(
            Akses::cocokJenjang(auth()->user(), $jenjang),
            403,
            'Data alumni ini berada di luar jenjang yang Anda tangani.'
        );
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tahun_lulus'   => 'required|integer|min:1998|max:' . (now()->year + 1),
            'jenjang'       => 'nullable|string|max:50',
            'kegiatan'      => 'nullable|in:' . implode(',', Alumni::KEGIATAN),
            'instansi'      => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:255',
            'alamat'        => 'nullable|string|max:1000',
            'testimoni'     => 'nullable|string|max:1000',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }
}
