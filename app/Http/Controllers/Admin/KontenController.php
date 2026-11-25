<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Konten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenController extends Controller
{
    public function index()
    {
        $stored = Konten::query()->get()->keyBy('key');

        // Baris bawaan (selalu tampil, walau belum pernah disimpan).
        $rows = [];
        foreach (Konten::FIELDS as $key => $meta) {
            $rows[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'group' => $meta['group'],
                'section' => Konten::sectionFor($key),
                'value' => $stored[$key]->value ?? (Konten::DEFAULTS[$key] ?? ''),
                'is_default' => array_key_exists($key, Konten::DEFAULTS),
            ];
        }

        // Konten tambahan buatan admin.
        foreach ($stored as $key => $item) {
            if (isset($rows[$key]) || ! str_starts_with($key, 'custom_')) {
                continue;
            }

            $rows[$key] = [
                'key' => $key,
                'label' => Str::headline(Str::after($key, 'custom_')),
                'group' => 'Konten Tambahan',
                'section' => 'Konten Tambahan',
                'value' => $item->value,
                'is_default' => false,
            ];
        }

        $fotoItem = $stored->get('sejarah_foto');
        $foto = $fotoItem ? $fotoItem->value : null;

        $captionItem = $stored->get('sejarah_foto_caption');
        $caption = $captionItem ? $captionItem->value : (Konten::DEFAULTS['sejarah_foto_caption'] ?? '');

        // Foto fasilitas: pakai data baru, lalu cadangan data lama (JSON).
        $legacyFasilitas = json_decode((string) ($stored->get('fasilitas_photos')->value ?? '[]'), true);
        $legacyFasilitas = is_array($legacyFasilitas) ? array_values($legacyFasilitas) : [];

        $fasilitasFoto = [];
        for ($i = 1; $i <= 4; $i++) {
            $item = $stored->get("fasilitas_{$i}_foto");
            $path = $item ? (string) $item->value : '';

            if ($path === '' && isset($legacyFasilitas[$i - 1])) {
                $path = (string) $legacyFasilitas[$i - 1];
            }

            $fasilitasFoto[$i] = $path;
        }

        return view('dashboard.konten.index', compact('rows', 'foto', 'caption', 'fasilitasFoto'));
    }

    public function updateProfil(Request $request)
    {
        // Tombol hapus / reset mengirim delete_key.
        if ($request->filled('delete_key')) {
            $key = (string) $request->input('delete_key');

            if (preg_match('/^[a-z0-9_]+$/', $key)) {
                Konten::where('key', $key)->delete();
            }

            return redirect()->back()->with('success', 'Konten berhasil dihapus / dikembalikan ke nilai bawaan.');
        }

        $rules = [
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'string', 'max:20000'],
            'layout' => ['nullable', 'array'],
            'layout.*' => ['nullable', 'string'],
            'sejarah_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        foreach (range(1, 4) as $i) {
            $rules["fasilitas_{$i}_foto"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $request->validate($rules);

        foreach ((array) $request->input('items', []) as $key => $value) {
            if (! preg_match('/^[a-z0-9_]+$/', (string) $key)) {
                continue;
            }

            $value = (string) $value;

            Konten::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $this->tipeUntuk($value)]
            );
        }

        // Simpan susunan & visibilitas section.
        foreach (['sejarah', 'visi', 'pembelajaran', 'fasilitas'] as $page) {
            $raw = $request->input("layout.{$page}");

            if (! is_string($raw)) {
                continue;
            }

            $decoded = json_decode($raw, true);

            if (! is_array($decoded)) {
                continue;
            }

            $valid = collect(Konten::LAYOUTS[$page] ?? [])->pluck('id')->all();
            $clean = [];

            foreach ($decoded as $row) {
                $id = $row['id'] ?? null;

                if (! in_array($id, $valid, true) || isset($clean[$id])) {
                    continue;
                }

                $clean[$id] = ['id' => $id, 'visible' => (bool) ($row['visible'] ?? true)];
            }

            Konten::updateOrCreate(
                ['key' => 'layout_'.$page],
                ['value' => json_encode(array_values($clean)), 'type' => 'json']
            );
        }

        if ($request->hasFile('sejarah_foto')) {
            $lama = Konten::getVal('sejarah_foto');
            if ($lama) {
                Storage::disk('public')->delete(preg_replace('#^storage/#', '', $lama));
            }

            $path = $request->file('sejarah_foto')->store('konten', 'public');
            Konten::updateOrCreate(
                ['key' => 'sejarah_foto'],
                ['value' => $path, 'type' => 'image']
            );
        }

        // Foto 4 fasilitas utama.
        foreach (range(1, 4) as $i) {
            $field = "fasilitas_{$i}_foto";

            if (! $request->hasFile($field)) {
                continue;
            }

            $lama = Konten::getVal($field);
            if ($lama) {
                Storage::disk('public')->delete(preg_replace('#^storage/#', '', $lama));
            }

            $path = $request->file($field)->store('konten/fasilitas', 'public');
            Konten::updateOrCreate(
                ['key' => $field],
                ['value' => $path, 'type' => 'image']
            );
        }

        return redirect()->back()->with('success', 'Perubahan konten berhasil disimpan.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'value' => ['nullable', 'string', 'max:20000'],
        ]);

        $key = 'custom_'.Str::slug($data['label'], '_');

        if ($key === 'custom_' || Konten::where('key', $key)->exists()) {
            return redirect()->back()->withErrors([
                'label' => 'Nama konten sudah dipakai atau tidak valid. Gunakan nama lain.',
            ]);
        }

        $value = (string) ($data['value'] ?? '');

        Konten::create([
            'key' => $key,
            'value' => $value,
            'type' => $this->tipeUntuk($value),
        ]);

        return redirect()->back()->with('success', 'Konten baru berhasil ditambahkan.');
    }

    /** Tentukan tipe penyimpanan dari isi konten. */
    private function tipeUntuk(string $value): string
    {
        return (str_contains($value, "\n") || mb_strlen($value) > 120) ? 'textarea' : 'text';
    }
}
