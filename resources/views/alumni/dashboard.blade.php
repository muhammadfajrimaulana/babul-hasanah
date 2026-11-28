@extends('layouts.alumni')

@section('title', 'Dashboard Alumni')

@section('content')
    @php
        $card = 'rounded-[22px] border border-[#e4e0d4] bg-white p-6 shadow-sm';
        $label = 'text-[10px] font-extrabold uppercase tracking-wider text-[#6b756f]';
        $box = 'rounded-2xl border border-[#e4e0d4] bg-[#fbfaf6] p-3.5';
        $kelasTerakhir = $riwayatTerakhir?->kelas ?: ($santri?->kelas ?: '-');
        $kamarTerakhir = $riwayatTerakhir?->kamar ?: ($santri?->kamar ?: '-');
        $jenjangTerakhir = $riwayatTerakhir?->jenjang ?: ($alumni->jenjang ?: ($santri?->jenjang_pendidikan ?: '-'));
    @endphp

    <div class="space-y-[22px]">
        {{-- HERO --}}
        <div class="rounded-[22px] bg-gradient-to-r from-[#17402f] via-[#1d4d3a] to-[#0f2018] p-6 sm:p-8 text-white shadow-lg">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-2xl ring-1 ring-white/20">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <div>
                        <span class="inline-block rounded-full border border-[#2fe0a0]/30 bg-[#2fe0a0]/15 px-3 py-1 text-[10px] font-extrabold uppercase tracking-wider text-[#2fe0a0]">Alumni</span>
                        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $alumni->nama ?? $santri?->nama_lengkap ?? Auth::user()->name }}</h1>
                        <p class="mt-1 text-[13px] text-white/75">
                            {{ \App\Support\Jenjang::label($jenjangTerakhir) ?: '-' }}
                            · Kelas terakhir {{ $kelasTerakhir }}
                            · Kamar {{ $kamarTerakhir }}
                            · Lulus {{ $alumni->tahun_lulus ?: '-' }}
                        </p>
                        <p class="mt-1 font-mono text-[11px] text-[#2fe0a0]">
                            <i class="fa-solid fa-certificate mr-1"></i>
                            No. Ijazah: {{ $alumni->nomor_ijazah ?: 'Belum diinput Admin Jenjang' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('alumni.dokumen') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-[#f1eee6] px-5 py-2.5 text-xs font-extrabold text-[#17402f] transition hover:bg-white">
                        <i class="fa-solid fa-download"></i> Unduh Dokumen
                    </a>
                    <a href="{{ route('alumni.profil') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/30 px-5 py-2.5 text-xs font-extrabold text-white transition hover:bg-white/10">
                        <i class="fa-solid fa-id-card"></i> Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-1 gap-[22px] sm:grid-cols-2 xl:grid-cols-4">
            <div class="{{ $card }}">
                <p class="{{ $label }}">Total Hafalan</p>
                <p class="mt-1 text-2xl font-extrabold text-[#0e9f73]">{{ number_format($totalHafalan, 0, ',', '.') }}</p>
                <p class="mt-0.5 text-[10px] text-[#6b756f]">{{ $hafalanTerakhir ? 'Terakhir: '.$hafalanTerakhir->surat : 'Belum ada catatan' }}</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Mata Pelajaran (Nilai Final)</p>
                <p class="mt-1 text-2xl font-extrabold text-[#b7791f]">{{ $nilaiAkhir->count() }}</p>
                <p class="mt-0.5 text-[10px] text-[#6b756f]">Dari nilai terbit yang terkunci</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Kehadiran</p>
                <p class="mt-1 text-2xl font-extrabold text-[#0e9f73]">{{ number_format((int) ($presensi['hadir'] ?? 0), 0, ',', '.') }}</p>
                <p class="mt-0.5 text-[10px] text-[#6b756f]">Total presensi hadir tercatat</p>
            </div>
            <div class="{{ $card }}">
                <p class="{{ $label }}">Pelanggaran</p>
                <p class="mt-1 text-2xl font-extrabold text-[#a02b2b]">{{ $pelanggaran->count() }}</p>
                <p class="mt-0.5 text-[10px] text-[#6b756f]">Tetap terlihat sebagai pengingat</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-[22px] xl:grid-cols-3">
            {{-- NILAI AKHIR --}}
            <div class="{{ $card }} xl:col-span-2">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base font-extrabold text-[#17402f]">Ringkasan Nilai Akhir</h2>
                        <p class="text-[11px] text-[#6b756f]">Nilai final yang sudah dikunci saat kelulusan.</p>
                    </div>
                    <a href="{{ route('alumni.dokumen') }}" class="text-[11px] font-bold text-[#0e9f73] hover:underline">Transkrip lengkap</a>
                </div>

                @if ($nilaiAkhir->isEmpty())
                    <p class="py-10 text-center text-[11px] text-[#6b756f]">Belum ada nilai final terbit.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-[#f1eee6] text-[10px] uppercase tracking-wider text-[#6b756f]">
                                    <th class="px-3 py-2.5 font-extrabold">Mata Pelajaran</th>
                                    <th class="px-3 py-2.5 font-extrabold">Rata-rata</th>
                                    <th class="px-3 py-2.5 font-extrabold">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e4e0d4]">
                                @foreach ($nilaiAkhir as $baris)
                                    <tr>
                                        <td class="px-3 py-2.5 font-bold text-[#1f2a24]">{{ $baris['mapel'] }}</td>
                                        <td class="px-3 py-2.5 font-extrabold text-[#0e9f73]">{{ rtrim(rtrim(number_format((float) $baris['rata'], 1, ',', '.'), '0'), ',') }}</td>
                                        <td class="px-3 py-2.5 text-[10px] text-[#6b756f]">{{ $baris['jumlah'] }} nilai</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- DOKUMEN --}}
            <div class="{{ $card }}">
                <h2 class="mb-3 text-base font-extrabold text-[#17402f]">Dokumen Tersedia</h2>
                <div class="space-y-2">
                    @foreach (\App\Models\DokumenSantri::JENIS as $kunci => $teks)
                        @php $d = $dokumen->get($kunci); @endphp
                        <div class="flex items-center justify-between gap-2 rounded-xl border {{ $d ? 'border-[#0e9f73]/30 bg-[#f1eee6]' : 'border-[#e4e0d4] bg-[#fbfaf6]' }} px-3 py-2">
                            <span class="text-[11px] font-bold text-[#1f2a24]">{{ $teks }}</span>
                            @if ($d)
                                <a href="{{ route('alumni.dokumen') }}" class="text-[10px] font-extrabold text-[#0e9f73] hover:underline"><i class="fa-solid fa-download"></i></a>
                            @else
                                <span class="text-[9px] font-bold uppercase text-[#6b756f]">Belum</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('alumni.dokumen') }}"
                    class="mt-3 block rounded-full bg-[#f1eee6] px-4 py-2.5 text-center text-[11px] font-extrabold text-[#17402f] transition hover:bg-[#e4e0d4]">
                    Buka Menu Dokumen
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-[22px] xl:grid-cols-2">
            {{-- PELANGGARAN --}}
            <div class="{{ $card }}">
                <h2 class="mb-1 text-base font-extrabold text-[#17402f]">Riwayat Pelanggaran</h2>
                <p class="mb-3 text-[11px] text-[#6b756f]">Tetap ditampilkan sebagai pengingat dan bagian dari history pesantren.</p>
                <div class="space-y-2">
                    @forelse ($pelanggaran as $pel)
                        <div class="rounded-xl border border-rose-100 bg-rose-50/50 px-3 py-2.5">
                            <p class="text-[11px] font-bold text-[#1f2a24]">{{ $pel->jenis }} <span class="ml-1 rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-extrabold text-[#a02b2b]">{{ $pel->poin }} poin</span></p>
                            <p class="text-[10px] text-[#6b756f]">{{ $pel->tanggal?->translatedFormat('d M Y') }}</p>
                            @if ($pel->deskripsi)<p class="mt-0.5 text-[10px] text-[#1f2a24]">{{ $pel->deskripsi }}</p>@endif
                        </div>
                    @empty
                        <p class="py-8 text-center text-[11px] text-[#6b756f]">Tidak ada catatan pelanggaran.</p>
                    @endforelse
                </div>
            </div>

            {{-- PENGUMUMAN --}}
            <div class="{{ $card }}">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 class="text-base font-extrabold text-[#17402f]">Pengumuman Alumni</h2>
                    <a href="{{ route('alumni.pengumuman') }}" class="text-[11px] font-bold text-[#0e9f73] hover:underline">Semua</a>
                </div>
                <div class="space-y-2">
                    @forelse ($pengumuman as $p)
                        <div class="rounded-xl border border-[#e4e0d4] bg-[#fbfaf6] px-3 py-2.5">
                            <p class="text-[11px] font-bold text-[#1f2a24]">{{ $p->judul }}</p>
                            <p class="text-[10px] text-[#6b756f]">{{ $p->tanggal?->translatedFormat('d M Y') }} · {{ $p->labelTarget() }}</p>
                        </div>
                    @empty
                        <p class="py-8 text-center text-[11px] text-[#6b756f]">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- DATA DIRI RINGKAS --}}
        <div class="{{ $card }}">
            <div class="mb-4 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-extrabold text-[#17402f]">Data Diri Saat Ini</h2>
                    <p class="text-[11px] text-[#6b756f]">Data kontak, karier, tracer study, & testimoni bisa Anda perbarui sendiri. Riwayat akademik terkunci.</p>
                </div>
                <a href="{{ route('alumni.profil') }}"
                    class="rounded-full bg-[#17402f] px-4 py-2 text-[10px] font-extrabold text-white transition hover:bg-[#1d4d3a]">Perbarui</a>
            </div>
            <dl class="grid grid-cols-1 gap-[22px] sm:grid-cols-2 lg:grid-cols-4">
                <div class="{{ $box }}"><dt class="{{ $label }}">No. HP</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->no_hp ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Email</dt><dd class="mt-0.5 break-all text-xs font-bold text-[#1f2a24]">{{ $alumni->email ?: Auth::user()->email }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Kegiatan</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->kegiatan ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Pekerjaan</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->pekerjaan ?: '-' }}</dd></div>
                <div class="{{ $box }}"><dt class="{{ $label }}">Instansi</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->instansi ?: '-' }}</dd></div>
                <div class="{{ $box }} lg:col-span-2"><dt class="{{ $label }}">Alamat Sekarang</dt><dd class="mt-0.5 text-xs font-medium text-[#1f2a24]">{{ $alumni->alamat ?: '-' }}</dd></div>
                <div class="{{ $box }} lg:col-span-2"><dt class="{{ $label }}">Tracer Study</dt><dd class="mt-0.5 text-xs font-bold text-[#1f2a24]">{{ $alumni->tracer?->kegiatan ?? 'Belum diisi' }}{{ $alumni->tracer?->instansi ? ' · '.$alumni->tracer->instansi : '' }}</dd></div>
            </dl>
        </div>
    </div>
@endsection
