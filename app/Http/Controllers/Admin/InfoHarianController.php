<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoHarian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InfoHarianController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $this->jenisDariQuery($request);

        $items = InfoHarian::where('jenis', $jenis)
            ->when($jenis === 'info',
                fn ($q) => $q->orderByDesc('tanggal')->orderByDesc('id'),
                fn ($q) => $q->orderBy('jam')->orderBy('urutan'))
            ->get();

        $jumlah = InfoHarian::selectRaw('jenis, count(*) as total')
            ->groupBy('jenis')->pluck('total', 'jenis');

        return view('dashboard.infoharian.index', compact('items', 'jenis', 'jumlah'));
    }

    public function create(Request $request)
    {
        return view('dashboard.infoharian.form', [
            'item'  => null,
            'jenis' => $this->jenisDariQuery($request),
        ]);
    }

    public function store(Request $request)
    {
        $jenis = $request->validate([
            'jenis' => ['required', Rule::in(['info', 'jadwal'])],
        ])['jenis'];

        InfoHarian::create($this->data($request, $jenis));

        return redirect()->route('admin.info.index', ['jenis' => $jenis])
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(InfoHarian $info)
    {
        return view('dashboard.infoharian.form', [
            'item'  => $info,
            'jenis' => $info->jenis,
        ]);
    }

    public function update(Request $request, InfoHarian $info)
    {
        $info->update($this->data($request, $info->jenis));

        return redirect()->route('admin.info.index', ['jenis' => $info->jenis])
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(InfoHarian $info)
    {
        $jenis = $info->jenis;
        $info->delete();

        return redirect()->route('admin.info.index', ['jenis' => $jenis])
            ->with('success', 'Data berhasil dihapus.');
    }

    /* ------------------------------------------------------------ Helper */

    private function data(Request $request, string $jenis): array
    {
        // Tanggal tayang default hari ini (juga dibutuhkan aturan after_or_equal).
        $request->merge(['tanggal' => $request->input('tanggal') ?: now()->toDateString()]);

        $v = $request->validate([
            'judul'          => ['required', 'string', 'max:150'],
            'kategori'       => [Rule::requiredIf($jenis === 'info'), 'nullable', Rule::in(array_keys(InfoHarian::KATEGORI))],
            'isi'            => ['nullable', 'string', 'max:500'],
            'jam'            => [Rule::requiredIf($jenis === 'jadwal'), 'nullable', 'regex:/^([01]?\d|2[0-3])[.:][0-5]\d$/'],
            'tanggal'        => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'urutan'         => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'jam.regex'                     => 'Format jam harus seperti 04.30 atau 18.15.',
            'berlaku_sampai.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal tayang.',
        ], [
            'judul' => $jenis === 'jadwal' ? 'kegiatan' : 'judul',
        ]);

        if ($jenis === 'jadwal') {
            [$j, $m] = preg_split('/[.:]/', $v['jam']);

            return [
                'jenis'          => 'jadwal',
                'judul'          => $v['judul'],
                'jam'            => sprintf('%02d.%02d', $j, $m),
                'kategori'       => null,
                'isi'            => null,
                'tanggal'        => $v['tanggal'],
                'berlaku_sampai' => null,
                'penting'        => false,
                'aktif'          => $request->boolean('aktif'),
                'urutan'         => $v['urutan'] ?? 0,
            ];
        }

        return [
            'jenis'          => 'info',
            'judul'          => $v['judul'],
            'kategori'       => $v['kategori'],
            'isi'            => $v['isi'] ?? null,
            'jam'            => null,
            'tanggal'        => $v['tanggal'],
            'berlaku_sampai' => $v['berlaku_sampai'] ?? null,
            'penting'        => $request->boolean('penting'),
            'aktif'          => $request->boolean('aktif'),
            'urutan'         => $v['urutan'] ?? 0,
        ];
    }

    private function jenisDariQuery(Request $request): string
    {
        return $request->query('jenis') === 'jadwal' ? 'jadwal' : 'info';
    }
}
