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

        // Kartu fasilitas (bisa ditambah / dihapus dari admin).
        $fasilitasCards = Konten::fasilitasCards();
        $fasilitasNext = ($fasilitasCards ? max(array_column($fasilitasCards, 'index')) : 0) + 1;

        return view('dashboard.konten.index', compact('rows', 'foto', 'caption', 'fasilitasCards', 'fasilitasNext'));
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

        // Tombol "Hapus" pada tiap bagian mengirim daftar key (dipisah koma).
        $deleteKeys = (string) $request->input('delete_keys', '');

        if ($deleteKeys !== '') {
            foreach (array_filter(array_map('trim', explode(',', $deleteKeys))) as $key) {
                if (preg_match('/^[a-z0-9_]+$/', $key)) {
                    Konten::where('key', $key)->delete();
                }
            }

            return redirect()->back()->with('success', 'Isi bagian berhasil dikembalikan ke nilai bawaan.');
        }

        // Tombol "Hapus" pada kartu fasilitas: kosongkan key + hapus fotonya.
        $emptyKeys = (string) $request->input('empty_keys', '');

        if ($emptyKeys !== '') {
            foreach (array_filter(array_map('trim', explode(',', $emptyKeys))) as $key) {
                if (! preg_match('/^[a-z0-9_]+$/', $key)) {
                    continue;
                }

                if (str_ends_with($key, '_foto')) {
                    $lama = Konten::getVal($key);

                    if ($lama) {
                        Storage::disk('public')->delete(preg_replace('#^storage/#', '', $lama));
                    }
                }

                Konten::updateOrCreate(
                    ['key' => $key],
                    ['value' => '', 'type' => str_ends_with($key, '_foto') ? 'image' : 'text']
                );
            }

            return redirect()->back()->with('success', 'Kartu fasilitas berhasil dihapus.');
        }

        $rules = [
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'string', 'max:20000'],
            'layout' => ['nullable', 'array'],
            'layout.*' => ['nullable', 'string'],
            'sejarah_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        // Nomor kartu fasilitas bisa bertambah dari admin, jadi aturan
        // validasi foto dibuat mengikuti nomor yang benar-benar dikirim.
        $fotoIndices = [];

        foreach (array_keys((array) $request->input('items', [])) as $key) {
            if (preg_match('/^fasilitas_(\d{1,3})_(title|desc)$/', (string) $key, $m)
                && (int) $m[1] >= 1 && (int) $m[1] <= 60) {
                $fotoIndices[(int) $m[1]] = true;
            }
        }

        foreach (array_keys($request->allFiles()) as $key) {
            if (preg_match('/^fasilitas_(\d{1,3})_foto$/', (string) $key, $m)
                && (int) $m[1] >= 1 && (int) $m[1] <= 60) {
                $fotoIndices[(int) $m[1]] = true;
            }
        }

        foreach (array_keys($fotoIndices) as $i) {
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

        // Tombol "Hapus foto" pada kartu foto sejarah / fasilitas.
        $hapusFoto = (string) $request->input('delete_foto', '');

        if ($hapusFoto !== '' && preg_match('/^(sejarah_foto|fasilitas_\d{1,3}_foto)$/', $hapusFoto)) {
            $lama = Konten::getVal($hapusFoto);

            if ($lama) {
                Storage::disk('public')->delete(preg_replace('#^storage/#', '', $lama));
            }

            Konten::updateOrCreate(
                ['key' => $hapusFoto],
                ['value' => '', 'type' => 'image']
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

        // Foto kartu fasilitas (jumlah mengikuti kartu yang dikirim).
        foreach (array_keys($fotoIndices) as $i) {
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
