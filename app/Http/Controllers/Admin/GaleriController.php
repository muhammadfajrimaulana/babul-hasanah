<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $albums = Album::with('media')
            ->withCount([
                'media as foto_count'  => fn ($q) => $q->where('tipe', 'foto'),
                'media as video_count' => fn ($q) => $q->where('tipe', '!=', 'foto'),
            ])
            ->latest()
            ->paginate(10);

        return view('dashboard.galeri.index', compact('albums'));
    }

    public function create()
    {
        return view('dashboard.galeri.form', [
            'kategori' => Album::query()->distinct()->pluck('kategori'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|string|max:150',
            'kategori'  => 'required|string|max:60',
            'tanggal'   => 'nullable|date',
            'deskripsi' => 'nullable|string|max:300',
            'sampul'    => 'nullable|image|max:4096',
            'foto'      => 'nullable|array',
            'foto.*'    => 'image|max:4096',

            'media'              => 'nullable|array',
            'media.*.tipe'       => 'required|in:upload,link',
            'media.*.file'       => 'nullable|required_if:media.*.tipe,upload|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200', // 50 MB
            'media.*.url'        => 'nullable|required_if:media.*.tipe,link|url|max:500',
            'media.*.poster'     => 'nullable|image|max:4096',
            'media.*.keterangan' => 'nullable|string|max:120',
        ], [
            'media.*.file.mimetypes' => 'File video harus berformat mp4, webm, atau mov.',
            'media.*.file.max'       => 'Ukuran video maksimal 50 MB.',
            'media.*.url.url'        => 'Link video harus diawali https://',
        ]);

        $album = Album::create([
            'judul'     => $data['judul'],
            'kategori'  => $data['kategori'],
            'tanggal'   => $data['tanggal'] ?? null,
            'deskripsi' => $data['deskripsi'] ?? null,
            'sampul'    => $request->file('sampul')?->store('galeri/sampul', 'public'),
        ]);

        $urutan = 0;

        foreach ($request->file('foto', []) as $file) {
            $album->media()->create([
                'tipe'   => 'foto',
                'path'   => $file->store('galeri/foto', 'public'),
                'urutan' => $urutan++,
            ]);
        }

        foreach ($request->input('media', []) as $i => $row) {
            $poster = $request->file("media.$i.poster")?->store('galeri/poster', 'public');

            if ($row['tipe'] === 'upload') {
                $album->media()->create([
                    'tipe'       => 'video',
                    'path'       => $request->file("media.$i.file")->store('galeri/video', 'public'),
                    'poster'     => $poster,
                    'keterangan' => $row['keterangan'] ?? null,
                    'urutan'     => $urutan++,
                ]);
            } else {
                $album->media()->create([
                    'tipe'       => 'link',
                    'url'        => $row['url'],
                    'poster'     => $poster,
                    'keterangan' => $row['keterangan'] ?? null,
                    'urutan'     => $urutan++,
                ]);
            }
        }

        return redirect()->route('admin.galeri.index')->with('success', 'Album berhasil disimpan.');
    }

    public function destroy(Album $galeri)
    {
        // hapus semua file milik album agar storage tidak menumpuk
        $files = $galeri->media->flatMap(fn ($m) => [$m->path, $m->poster])->push($galeri->sampul)->filter()->all();
        Storage::disk('public')->delete($files);

        $galeri->delete(); // baris album_media ikut terhapus (cascade)

        return redirect()->route('admin.galeri.index')->with('success', 'Album dihapus.');
    }
}