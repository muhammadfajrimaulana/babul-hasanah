{{--
    Panel Dashboard Jenjang yang bisa dipakai ulang.

    Dipakai oleh dashboard Super Admin agar seluruh komponen Dashboard Jenjang
    (statistik, input nilai, input prestasi, dan Data Alumni) tersedia untuk
    semua jenjang dari satu halaman.

    Parameter: $panel (array data dari RoleDashboardController::dataJenjang()).
--}}
@php
    extract($panel ?? [], EXTR_OVERWRITE);
@endphp

<div class="space-y-4">
    @include('dashboard.roles._pilih-jenjang')

    @if (! empty($tanpaJenjang))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-xs text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
            <p class="font-black"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Akun belum ditautkan ke jenjang.</p>
            <p class="mt-1 leading-relaxed">Hubungi Super Admin agar akun ini diberi jenjang melalui menu <b>Pengguna</b>.</p>
        </div>
    @elseif (! empty($modeAgregat))
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-xs text-sky-800 dark:border-sky-900/50 dark:bg-sky-950/30 dark:text-sky-200">
            <p class="font-black"><i class="fa-solid fa-circle-info mr-1"></i> Mode gabungan seluruh jenjang.</p>
            <p class="mt-1 leading-relaxed">Angka di bawah adalah total 7 jenjang. Pilih salah satu jenjang pada menu di atas untuk melihat data dan mengisi nilai/prestasi jenjang tersebut.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @include('dashboard.roles._stat', [
            'label' => 'Santri',
            'nilai' => number_format($totalSantri ?? 0, 0, ',', '.'),
            'sub' => $jenjang['nama'] ?? 'Seluruh jenjang',
            'ikon' => 'fa-people-group',
            'href' => $jenjang ? route('admin.jenjang.show', $jenjang['slug']) : route('admin.santri.index'),
        ])
        @include('dashboard.roles._stat', [
            'label' => 'Pendaftar',
            'nilai' => number_format($totalPendaftar ?? 0, 0, ',', '.'),
            'sub' => $jenjang ? 'Total PPDB jenjang ini' : 'Total PPDB semua jenjang',
            'ikon' => 'fa-address-book',
            'ikonBg' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/50 dark:text-sky-400',
            'href' => route('admin.pendaftaran.index'),
        ])
        @include('dashboard.roles._stat', [
            'label' => 'Menunggu Verifikasi',
            'nilai' => number_format($pendaftarMenunggu ?? 0, 0, ',', '.'),
            'sub' => 'Perlu ditindaklanjuti',
            'ikon' => 'fa-clock',
            'ikonBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400',
            'href' => route('admin.pendaftaran.index'),
        ])
        @include('dashboard.roles._stat', [
            'label' => 'Alumni',
            'nilai' => number_format($totalAlumni ?? 0, 0, ',', '.'),
            'sub' => ($totalAlumni ?? 0) > 0 ? ($alumniPunyaNilai ?? 0).' punya nilai/transkrip' : 'Riwayat lulusan',
            'ikon' => 'fa-user-graduate',
            'ikonBg' => 'bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-400',
            'href' => route('admin.alumni.index', $jenjangPilihan ? ['jenjang' => $jenjangPilihan] : []),
        ])
        @include('dashboard.roles._stat', [
            'label' => 'Prestasi',
            'nilai' => number_format($totalPrestasi ?? 0, 0, ',', '.'),
            'sub' => $jenjang ? 'Capaian jenjang ini' : 'Capaian semua jenjang',
            'ikon' => 'fa-trophy',
            'ikonBg' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400',
            'href' => route('admin.prestasi.index'),
        ])
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @include('dashboard.roles._form-nilai-jenjang')
        @include('dashboard.roles._form-prestasi-jenjang')
    </div>

    @include('dashboard.roles._alumni-jenjang')
</div>
