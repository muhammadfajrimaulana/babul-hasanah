<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Legalisir;
use App\Support\Akademik;
use App\Support\CatatanAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Proses pengajuan legalisir alumni oleh Admin Jenjang. */
class LegalisirController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Legalisir::query()
            ->with(['alumni.santri', 'pemroses'])
            ->whereHas('alumni', fn ($a) => $a->whereIn(
                'santri_id',
                Akademik::santriQuery($user)->select('id')
            ))
            ->latest();

        if (in_array($request->input('status'), array_keys(Legalisir::STATUS), true)) {
            $query->where('status', $request->input('status'));
        }

        return view('dashboard.legalisir.index', [
            'legalisir' => $query->paginate(20)->withQueryString(),
            'status' => Legalisir::STATUS,
            'filterStatus' => $request->input('status'),
        ]);
    }

    public function proses(Request $request, Legalisir $legalisir)
    {
        abort_unless(auth()->user()->bisa('alumni.ubah'), 403);
        $this->pastikanBerwenang($legalisir);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Legalisir::STATUS))],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'file_hasil' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $path = $legalisir->file_hasil;

        if ($request->hasFile('file_hasil')) {
            if (filled($path)) {
                Storage::disk('public')->delete($path);
            }

            $path = $request->file('file_hasil')->store('legalisir/'.$legalisir->alumni_id, 'public');
        }

        $legalisir->update([
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
            'file_hasil' => $path,
            'diproses_oleh' => auth()->id(),
        ]);

        CatatanAktivitas::catat('proses_legalisir', $legalisir, [
            'alumni_id' => $legalisir->alumni_id,
            'status' => $legalisir->status,
        ], $data['catatan'] ?? null);

        return back()->with('success', 'Pengajuan legalisir diperbarui menjadi '.$legalisir->labelStatus().'.');
    }

    private function pastikanBerwenang(Legalisir $legalisir): void
    {
        $santriId = $legalisir->alumni?->santri_id;

        abort_unless($santriId, 404, 'Data santri alumni tidak ditemukan.');

        abort_unless(
            Akademik::santriQuery(auth()->user())->whereKey($santriId)->exists(),
            403,
            'Legalisir ini bukan milik jenjang yang Anda tangani.'
        );
    }
}
