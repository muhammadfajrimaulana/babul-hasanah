@extends('layouts.alumni')

@section('title', 'Dashboard Alumni')

@section('content')
    @php
        $card = 'rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm';
        $label = 'text-[10px] font-black uppercase tracking-wider text-slate-400';
        $box = 'rounded-2xl border border-slate-100 bg-slate-50/60 p-3.5';
        $kelasTerakhir = $riwayatTerakhir?->kelas ?: ($santri?->kelas ?: '-');
        $kamarTerakhir = $riwayatTerakhir?->kamar ?: ($santri?->kamar ?: '-');
        $jenjangTerakhir = $riwayatTerakhir?->jenjang ?: ($alumni->jenjang ?: ($santri?->jenjang_pendidikan ?: '-'));
    @endphp

    <div class="space-y-6">
        {{-- HERO --}}
        <div class="rounded-3xl bg-gradient-to-r from-indigo-900 via-emerald-950 to-slate-950 p-8 text-white shadow-lg">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-2xl ring-1 ring-white/20">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <div>
                        <span class="inline-block rounded-full border border-emerald-400/30 bg-emerald-500/20 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-300">Alumni</span>
                        <h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ $alumni->nama ?? $santri?->nama_lengkap ?? Auth::user()->name }}</h1>
                        <p class="mt-1 text-xs text-slate-300">
                            {{ \App\Support\Jenjang::label($jenjangTerakhir) ?: '-' }}
                            · Kelas terakhir {{ $kelasTerakhir }}
                            · Kamar {{ $kamarTerakhir }}
                            · Lulus {{ $alumni->tahun_lulus ?: '-' }}
                        </p>
                        <p class="mt-1 font-mono text-[11px] text-emerald-300">
                            <i class="fa-solid fa-certificate mr-1"></i>
                            No. Ijazah: {{ $alumni->nomor_ijazah ?: 'Belum diinput Admin Jenjang' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('alumni.dokumen') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2.5 text-xs font-black text-slate-900 transition hover:bg-emerald-400">
                        <i class="fa-solid fa-download"></i> Unduh Dokumen
                    </a>
                    <a href="{{ route('alumni.profil') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/25 px-4 py-2.5 text-xs font-black text-white transition hover:bg-white/10">
                        <i class="fa-solid fa-id-card"></i> Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="{{ $card }}">
                <p class="{{ $label }}">Total Hafalan</p>
                <p class="mt-1 text-2xl font-black text-emerald-700">{{ number_format($totalHafalan, 0, ',', '.') }}</p>
                <p class="mt-0.5 text-[10px] text-slate-400">{{ $hafalanTerakhir ? 'Terakhir: '.$hafalanTerakhir->surat : 'Belum ada catatan' }}</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Mata Pelajaran (Nilai Final)</p>
                <p class="mt-1 text-2xl font-black text-indigo-700">{{ $nilaiAkhir->count() }}</p>
                <p class="mt-0.5 text-[10px] text-slate-400">Dari nilai terbit yang terkunci</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Kehadiran</p>
                <p class="mt-1 text-2xl font-black text-sky-700">{{ number_format((int) ($presensi['hadir'] ?? 0), 0, ',', '.') }}</p>
                <p class="mt-0.5 text-[10px] text-slate-400">Total presensi hadir tercatat</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Pelanggaran</p>
                <p class="mt-1 text-2xl font-black text-rose-700">{{ $pelanggaran->count() }}</p>
                <p class="mt-0.5 text-[10px] text-slate-400">Tetap terlihat sebagai pengingat</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- NILAI AKHIR --}}
            <div class="{{ $card }} xl:col-span-2">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Ringkasan Nilai Akhir</h2>
                        <p class="text-[10px] text-slate-400">Nilai final yang sudah dikunci saat kelulusan.</p>
                    </div>
                    <a href="{{ route('alumni.dokumen') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Transkrip lengkap</a>
                </div>

                @if ($nilaiAkhir->isEmpty())
                    <p class="py-10 text-center text-[11px] text-slate-400">Belum ada nilai final terbit.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-400">
                                    <th class="px-3 py-2.5 font-extrabold">Mata Pelajaran</th>
                                    <th class="px-3 py-2.5 font-extrabold">Rata-rata</th>
                                    <th class="px-3 py-2.5 font-extrabold">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($nilaiAkhir as $baris)
                                    <tr>
                                        <td class="px-3 py-2.5 font-bold text-slate-700">{{ $baris['mapel'] }}</td>
                                        <td class="px-3 py-2.5 font-black text-emerald-700">{{ rtrim(rtrim(number_format((float) $baris['rata'], 1, ',', '.'), '0'), ',') }}</td>
                                        <td class="px-3 py-2.5 text-[10px] text-slate-400">{{ $baris['jumlah'] }} nilai</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- DOKUMEN --}}
            <div class="{{ $card }}">
                <h2 class="mb-3 text-sm font-black text-slate-900">Dokumen Tersedia</h2>
                <div class="space-y-2">
                    @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $teks)
                        @php $d = $dokumen->get($kunci); @endphp
                        <div class="flex items-center justify-between gap-2 rounded-xl border {{ $d ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-100 bg-slate-50/60' }} px-3 py-2">
                            <span class="text-[11px] font-bold text-slate-700">{{ $teks }}</span>
                            @if ($d)
                                <a href="{{ route('alumni.dokumen') }}" class="text-[10px] font-black text-emerald-700 hover:underline"><i class="fa-solid fa-download"></i></a>
                            @else
                                <span class="text-[9px] font-bold uppercase text-slate-400">Belum</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('alumni.dokumen') }}" class="mt-3 block rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-[11px] font-black text-white hover:bg-indigo-700">
                    Buka Menu Dokumen
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            {{-- PELANGGARAN --}}
            <div class="{{ $card }}">
                <h2 class="mb-1 text-sm font-black text-slate-900">Riwayat Pelanggaran</h2>
                <p class="mb-3 text-[10px] text-slate-400">Tetap ditampilkan sebagai pengingat dan bagian dari history pesantren.</p>
                <div class="space-y-2">
                    @forelse ($pelanggaran as $pel)
                        <div class="rounded-xl border border-rose-100 bg-rose-50/50 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-slate-800">{{ $pel->jenis }} <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-black text-rose-700">{{ $pel->poin }} poin</span></p>
                            <p class="text-[10px] text-slate-400">{{ $pel->tanggal?->translatedFormat('d M Y') }}</p>
                            @if ($pel->deskripsi)<p class="mt-0.5 text-[10px] text-slate-600">{{ $pel->deskripsi }}</p>@endif
                        </div>
                    @empty
                        <p class="py-8 text-center text-[11px] text-slate-400">Tidak ada catatan pelanggaran.</p>
                    @endforelse
                </div>
            </div>

            {{-- PENGUMUMAN --}}
            <div class="{{ $card }}">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 class="text-sm font-black text-slate-900">Pengumuman Alumni</h2>
                    <a href="{{ route('alumni.pengumuman') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Semua</a>
                </div>
                <div class="space-y-2">
                    @forelse ($pengumuman as $p)
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-slate-800">{{ $p->judul }}</p>
                            <p class="text-[10px] text-slate-400">{{ $p->tanggal?->translatedFormat('d M Y') }} · {{ $p->labelTarget() }}</p>
                        </div>
                    @empty
                        <p class="py-8 text-center text-[11px] text-slate-400">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- DATA DIRI RINGKAS --}}
        <div class="{{ $card }}">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-900">Data Diri Saat Ini</h2>
                    <p class="text-[10px] text-slate-400">Data kontak & karier bisa Anda perbarui sendiri. Riwayat akademik terkunci.</p>
                </div>
                <a href="{{ route('alumni.profil') }}" class="rounded-xl bg-slate-900 px-3.5 py-2 text-[10px] font-bold text-white">Perbarui</a>
            </div>
            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="{{ $box }}"><dt class="{{ $label }}">No. HP</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->no_hp ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Email</dt><dd class="mt-0.5 break-all text-xs font-bold text-slate-800">{{ $alumni->email ?: Auth::user()->email }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Kegiatan</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->kegiatan ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Pekerjaan</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->pekerjaan ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Instansi</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->instansi ?: '-' }}</dd></div>
                <div class="{{ $box }} lg:col-span-2"><dt class="{{ $label }}">Alamat Sekarang</dt><dd class="mt-0.5 text-xs font-medium text-slate-700">{{ $alumni->alamat ?: '-' }}</dd></div>
                <div class="{{ $box }} lg:col-span-2"><dt class="{{ $label }}">Tracer Study</dt><dd class="mt-0.5 text-xs font-bold text-slate-800">{{ $alumni->tracer?->kegiatan ?? 'Belum diisi' }}{{ $alumni->tracer?->instansi ? ' · '.$alumni->tracer->instansi : '' }}</dd></div>
            </dl>
        </div>
    </div>
@endsection
