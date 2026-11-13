<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Support\Akademik;
use App\Support\PortalWali;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PortalAkademikController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $anakList = PortalWali::anak($user);
        $anak = PortalWali::aktif($request, $anakList);
        $opsi = $anak ? PortalWali::opsiPeriode($anak) : collect();
        [$tahun, $semester] = $this->periode($request, $opsi);

        $tab = (string) $request->input('tab', 'rapor');
        if (! in_array($tab, ['rapor', 'transkrip', 'tahfidz', 'absensi', 'keuangan'], true)) {
            $tab = 'rapor';
        }

        $rapor = $anak
            ? PortalWali::rapor($anak, $tahun, $semester)
            : $this->raporKosong();

        return view('portal.akademik.index', [
            'anakList' => $anakList,
            'anak' => $anak,
            'filter' => ['tahun' => $tahun, 'semester' => $semester],
            'opsi' => $opsi,
            'tabAktif' => $tab,
            'rapor' => $rapor,
            'transkrip' => $anak ? PortalWali::transkrip($anak) : collect(),
            'tahfidz' => $anak ? PortalWali::tahfidz($anak, $tahun, $semester) : collect(),
            'absensi' => $anak ? PortalWali::absensi($anak, $tahun, $semester) : $this->absensiKosong(),
            'keuangan' => $anak ? PortalWali::keuangan($anak) : $this->keuanganKosong(),
        ]);
    }

    /** Unduh rapor resmi dalam bentuk PDF. */
    public function pdf(Request $request)
    {
        return $this->buatPdf($request, false);
    }

    /** Tampilan cetak/PDF di tab baru (kompatibilitas route lama). */
    public function cetak(Request $request)
    {
        return $this->buatPdf($request, true);
    }

    private function buatPdf(Request $request, bool $inline)
    {
        $user = $request->user();
        $anakList = PortalWali::anak($user);
        $anak = PortalWali::aktif($request, $anakList);

        abort_unless($anak, 404, 'Pilih anak terlebih dahulu.');

        $opsi = PortalWali::opsiPeriode($anak);
        [$tahun, $semester] = $this->periode($request, $opsi);
        $rapor = PortalWali::rapor($anak, $tahun, $semester);

        $html = view('portal.akademik.pdf', [
            'anak' => $anak,
            'filter' => ['tahun' => $tahun, 'semester' => $semester],
            'rapor' => $rapor,
        ])->render();

        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nama = 'rapor-'.Str::slug($anak->nama).'-'.$tahun.'-'.$semester.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($inline ? 'inline' : 'attachment').'; filename="'.$nama.'"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    /**
     * Tentukan periode. Bila tidak diminta, pakai nilai terbit terbaru;
     * bila belum ada nilai, pakai semester berjalan.
     *
     * @param  Collection<int, array{tahun_ajaran:string,semester:string}>  $opsi
     * @return array{0:string,1:string}
     */
    private function periode(Request $request, Collection $opsi): array
    {
        $tahun = trim((string) $request->input('tahun_ajaran'));
        $semester = strtolower((string) $request->input('semester'));

        $pertama = $opsi->first();

        if ($tahun === '') {
            $tahun = $pertama['tahun_ajaran'] ?? Akademik::tahunAjaranSekarang();
        }

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = $pertama['semester'] ?? Akademik::semesterSekarang();
        }

        if (! array_key_exists($semester, Akademik::SEMESTER)) {
            $semester = Akademik::semesterSekarang();
        }

        return [$tahun, $semester];
    }

    /** @return array<string, mixed> */
    private function raporKosong(): array
    {
        return [
            'mapel' => collect(),
            'rata' => null,
            'predikat' => '-',
            'jumlah' => 0,
            'tuntas' => 0,
            'catatan' => null,
            'terakhir' => null,
            'baru' => false,
            'sumber' => 'kosong',
        ];
    }

    /** @return array{rows:Collection,rekap:Collection,total:int} */
    private function absensiKosong(): array
    {
        return ['rows' => collect(), 'rekap' => collect(), 'total' => 0];
    }

    /** @return array{rows:Collection,total:float,dibayar:float,sisa:float} */
    private function keuanganKosong(): array
    {
        return ['rows' => collect(), 'total' => 0.0, 'dibayar' => 0.0, 'sisa' => 0.0];
    }
}
