<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $beritas = Berita::query()
            ->when($request->q, fn ($q, $s) => $q->where('judul', 'like', "%{$s}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('dashboard.berita.create');
    }

    public function store(Request $request)
    {
        Berita::create($this->data($request));

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function edit(Berita $berita)
    {
        return view('dashboard.berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $this->data($request, $berita);

        if (isset($data['gambar']) && $berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }
        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * Unggah gambar yang disisipkan di dalam isi berita (dipakai editor).
     */
    public function uploadGambar(Request $request)
    {
        $request->validate(['gambar' => 'required|image|max:4096']);

        $path = $request->file('gambar')->store('berita/isi', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }

    private function data(Request $request, ?Berita $berita = null): array
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:beritas,slug'.($berita ? ','.$berita->id : ''),
            'kategori' => 'required|string|max:100',
            'ringkasan' => 'nullable|string|max:300',
            'isi' => ['required', 'string', function ($attribute, $value, $fail) {
                $text = trim(html_entity_decode(preg_replace('/<[^>]+>/', ' ', $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));

                if ($words === 0 && ! str_contains($value, '<img')) {
                    $fail('Isi berita belum diisi.');
                } elseif ($words > 1000) {
                    $fail("Isi berita maksimal 1.000 kata (saat ini {$words} kata).");
                }
            }],
            'gambar' => 'nullable|image|max:2048',
            'penulis' => 'required|string|max:100',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['judul']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        } else {
            unset($data['gambar']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
