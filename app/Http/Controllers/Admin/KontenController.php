<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Konten;
use App\Models\Kepengurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class KontenController extends Controller
{
    public function index()
    {
        $kontens = Konten::all()->pluck('value', 'key');
        $kepengurusans = Kepengurusan::orderBy('urutan', 'asc')->get();

        return view('dashboard.konten.index', compact('kontens', 'kepengurusans'));
    }

    public function updateProfil(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('konten', 'public');
                Konten::updateOrCreate(['key' => $key], ['value' => $path, 'type' => 'image']);
            } else {
                Konten::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Profil pesantren berhasil diperbarui!');
    }

    public function storeKepengurusan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Pindahkan langsung ke folder public/kepengurusan di root project
            $file->move(public_path('upload/kepengurusan'), $filename);

            // Path yang disimpan ke database jadi relatif (misal: kepengurusan/12345.jpg)
            $path = 'upload/kepengurusan/' . $filename;
        }

        Kepengurusan::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'foto' => $path,
            'urutan' => Kepengurusan::count() + 1,
        ]);

        return redirect()->back()->with('success', 'Data kepengurusan berhasil ditambahkan!');
    }

    public function destroyKepengurusan($id)
    {
        $kepengurusan = Kepengurusan::findOrFail($id);

        // Hapus file fisik langsung dari folder public
        if ($kepengurusan->foto && file_exists(public_path($kepengurusan->foto))) {
            unlink(public_path($kepengurusan->foto));
        }

        $kepengurusan->delete();

        return redirect()->back()->with('success', 'Data kepengurusan berhasil ditambahkan!'); // (opsional: pesan sukses hapus)
    }
}