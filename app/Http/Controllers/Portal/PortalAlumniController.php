<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\TracerStudy;
use App\Support\TranskripAlumni;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Portal Alumni: transkrip lengkap, ijazah digital, dan tracer study.
 *
 * Identitas alumni diselesaikan oleh `TranskripAlumni::konteks()` dari akun
 * yang login (alumni.user_id, email, atau students.user_id).
 */
class PortalAlumniController extends Controller
{
    public function index(Request $request)
    {
        $konteks = TranskripAlumni::konteks($request->user());
        $transkrip = TranskripAlumni::untuk($konteks);

        return view('portal.alumni.dashboard', [
            'konteks' => $konteks,
            'transkrip' => $transkrip,
            'tracer' => $this->tracer($request->user()),
            'nomorIjazah' => TranskripAlumni::nomorIjazah($konteks),
            'kodeVerifikasi' => TranskripAlumni::kodeVerifikasi($konteks),
            'siapIjazah' => TranskripAlumni::siapIjazah($konteks),
            'daftarKegiatan' => Alumni::KEGIATAN,
        ]);
    }

    /** Unduh transkrip nilai lengkap (semua semester terbit). */
    public function transkripPdf(Request $request)
    {
        $konteks = TranskripAlumni::konteks($request->user());
        $transkrip = TranskripAlumni::untuk($konteks);

        return $this->pdf('portal.alumni.pdf-transkrip', [
            'konteks' => $konteks,
            'transkrip' => $transkrip,
            'nomorIjazah' => TranskripAlumni::nomorIjazah($konteks),
            'kodeVerifikasi' => TranskripAlumni::kodeVerifikasi($konteks),
        ], 'transkrip-'.Str::slug($konteks['nama']).'.pdf');
    }

    /** Unduh ijazah digital (hanya untuk alumni yang sudah dinyatakan lulus). */
    public function ijazahPdf(Request $request)
    {
        $konteks = TranskripAlumni::konteks($request->user());

        if (! TranskripAlumni::siapIjazah($konteks)) {
            return redirect()
                ->route('alumni.dashboard')
                ->with('error', 'Ijazah digital belum tersedia. Hubungi sekretariat untuk memastikan status kelulusan Anda.');
        }

        return $this->pdf('portal.alumni.pdf-ijazah', [
            'konteks' => $konteks,
            'nomorIjazah' => TranskripAlumni::nomorIjazah($konteks),
            'kodeVerifikasi' => TranskripAlumni::kodeVerifikasi($konteks),
        ], 'ijazah-'.Str::slug($konteks['nama']).'.pdf');
    }

    /** Simpan atau perbarui jawaban tracer study milik akun alumni. */
    public function simpanTracer(Request $request)
    {
        if (! Schema::hasTable('tracer_study')) {
            return redirect()
                ->route('alumni.dashboard')
                ->with('error', 'Formulir tracer study belum tersedia. Hubungi administrator pesantren.');
        }

        $konteks = TranskripAlumni::konteks($request->user());

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'tahun_lulus' => ['nullable', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'jenjang' => ['nullable', 'string', 'max:50'],
            'kegiatan' => ['required', 'string', Rule::in(Alumni::KEGIATAN)],
            'instansi' => ['nullable', 'string', 'max:150'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'mulai_tahun' => ['nullable', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'testimoni' => ['nullable', 'string', 'max:1000'],
            'bersedia_dihubungi' => ['nullable', 'boolean'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'kegiatan.required' => 'Pilih kegiatan utama Anda saat ini.',
            'kegiatan.in' => 'Pilihan kegiatan tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'tahun_lulus.min' => 'Tahun lulus tidak valid.',
            'mulai_tahun.min' => 'Tahun mulai tidak valid.',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['student_id'] = $konteks['student']?->id;
        $data['alumni_id'] = $konteks['alumni']?->id;
        $data['tahun_lulus'] = ($data['tahun_lulus'] ?? null) ?: ($konteks['tahun_lulus'] ?? null);
        $data['jenjang'] = ($data['jenjang'] ?? null) ?: ($konteks['jenjang'] ?? null);
        $data['email'] = ($data['email'] ?? null) ?: $konteks['email'];
        $data['bersedia_dihubungi'] = $request->boolean('bersedia_dihubungi');

        TracerStudy::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return redirect()
            ->to(route('alumni.dashboard').'#tracer')
            ->with('success', 'Terima kasih! Data tracer study Anda berhasil disimpan.');
    }

    /* ============================================================= Helper */

    private function tracer($user): ?TracerStudy
    {
        if (! Schema::hasTable('tracer_study')) {
            return null;
        }

        return TracerStudy::query()->where('user_id', $user->id)->first();
    }

    /**
     * Render satu view Blade menjadi PDF memakai Dompdf.
     *
     * @param  array<string, mixed>  $data
     */
    private function pdf(string $view, array $data, string $nama)
    {
        $html = view($view, $data)->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nama.'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
