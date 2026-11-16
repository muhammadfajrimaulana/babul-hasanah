@extends('layouts.portal')

@section('title', 'Portal Alumni')

@use('App\Support\Akademik')

@section('content')
    @php
        $periode = $transkrip['periode'];
        $nilaiTracer = fn (string $kunci, $bawaan = null) => old($kunci, $tracer?->{$kunci} ?? $bawaan);
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        @foreach (['success' => 'emerald', 'error' => 'rose', 'info' => 'sky'] as $jenis => $warna)
            @if (session($jenis))
                <div class="rounded-2xl border border-{{ $warna }}-200 bg-{{ $warna }}-50 px-4 py-3.5 text-xs font-bold text-{{ $warna }}-800 flex items-start gap-2.5">
                    <i class="fa-solid {{ $jenis === 'success' ? 'fa-circle-check' : ($jenis === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info') }} mt-0.5"></i>
                    <span>{{ session($jenis) }}</span>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-xs text-rose-800 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation mt-0.5"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Identitas alumni --}}
        <div class="rounded-3xl bg-gradient-to-br from-violet-700 via-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-900/20 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-violet-100">Portal Alumni</p>
                        <h1 class="text-xl sm:text-2xl font-black leading-tight">{{ $konteks['nama'] }}</h1>
                        <p class="text-xs text-violet-100/90 mt-1">
                            {{ $konteks['jenjang'] ?: 'Jenjang belum tercatat' }}
                            · NIS {{ $konteks['nis'] }}
                            @if ($konteks['tahun_lulus'])
                                · Lulus {{ $konteks['tahun_lulus'] }}
                            @endif
                        </p>
                        <p class="text-[11px] text-violet-100/70 mt-0.5">{{ $konteks['email'] }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('alumni.transkrip.pdf') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white text-violet-700 hover:bg-violet-50 px-4 py-2.5 text-xs font-black transition shadow-sm">
                        <i class="fa-solid fa-file-arrow-down"></i> Transkrip Lengkap
                    </a>
                    @if ($siapIjazah)
                        <a href="{{ route('alumni.ijazah.pdf') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-violet-900/40 border border-white/25 text-white hover:bg-violet-900/60 px-4 py-2.5 text-xs font-black transition">
                            <i class="fa-solid fa-certificate"></i> Ijazah Digital
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-xl bg-violet-900/30 border border-white/15 text-violet-200 px-4 py-2.5 text-xs font-bold cursor-not-allowed"
                            title="Ijazah tersedia setelah status kelulusan tercatat.">
                            <i class="fa-solid fa-certificate"></i> Ijazah belum tersedia
                        </span>
                    @endif
                    <a href="#tracer"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/20 text-white hover:bg-white/20 px-4 py-2.5 text-xs font-black transition">
                        <i class="fa-solid fa-clipboard-question"></i> Tracer Study
                    </a>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ([
                    ['label' => 'Rata-rata', 'nilai' => Akademik::formatNilai($transkrip['rata']), 'ikon' => 'fa-chart-simple'],
                    ['label' => 'Mata pelajaran', 'nilai' => $transkrip['jumlah_mapel'], 'ikon' => 'fa-book-open'],
                    ['label' => 'Periode nilai', 'nilai' => $transkrip['jumlah_periode'], 'ikon' => 'fa-calendar-days'],
                    ['label' => 'Predikat', 'nilai' => $transkrip['predikat'], 'ikon' => 'fa-award'],
                ] as $kartu)
                    <div class="rounded-2xl bg-white/10 border border-white/15 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-violet-100/80">
                            <i class="fa-solid {{ $kartu['ikon'] }} mr-1"></i> {{ $kartu['label'] }}
                        </p>
                        <p class="mt-1 text-lg font-black">{{ $kartu['nilai'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($transkrip['sumber'] === 'kosong')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-800">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Belum ada nilai berstatus terbit pada akun ini. Transkrip akan terisi otomatis setelah
                nilai dari pesantren diterbitkan.
            </div>
        @endif

        {{-- Transkrip nilai lengkap --}}
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-900">Transkrip Nilai Lengkap</h2>
                    <p class="text-[11px] text-slate-500">
                        Seluruh semester terbit, dikelompokkan per tahun ajaran.
                    </p>
                </div>
                <a href="{{ route('alumni.transkrip.pdf') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-violet-200 bg-violet-50 px-3 py-1.5 text-[11px] font-black text-violet-700 hover:bg-violet-100 transition">
                    <i class="fa-solid fa-download"></i> Unduh PDF
                </a>
            </div>

            @if ($periode->isEmpty())
                <div class="p-8 text-center text-xs text-slate-500">
                    Belum ada data nilai yang dapat ditampilkan.
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($periode as $blok)
                        <div class="px-5 sm:px-6 py-4 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-xs font-black text-slate-800">
                                        Tahun Ajaran {{ $blok['tahun'] }} · Semester {{ Akademik::labelSemester($blok['semester']) }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">{{ count($blok['mapel']) }} mata pelajaran</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-600">
                                    <i class="fa-solid fa-chart-line"></i> Rata {{ Akademik::formatNilai($blok['rata']) }}
                                    · {{ $blok['predikat'] }}
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="min-w-full text-[11px]">
                                    <thead class="bg-slate-50 text-slate-500">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-black uppercase tracking-wider text-[9px]">Mata Pelajaran</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">KKM</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">Tugas</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">UTS</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">UAS</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">Hafalan</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">Akhir</th>
                                            <th class="px-3 py-2 text-center font-black uppercase tracking-wider text-[9px]">Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($blok['mapel'] as $mapel)
                                            <tr class="hover:bg-slate-50/70">
                                                <td class="px-3 py-2 font-semibold text-slate-700">{{ $mapel['nama'] }}</td>
                                                <td class="px-3 py-2 text-center text-slate-500">{{ Akademik::formatNilai($mapel['kkm']) }}</td>
                                                <td class="px-3 py-2 text-center">{{ Akademik::formatNilai($mapel['tugas']) }}</td>
                                                <td class="px-3 py-2 text-center">{{ Akademik::formatNilai($mapel['uts']) }}</td>
                                                <td class="px-3 py-2 text-center">{{ Akademik::formatNilai($mapel['uas']) }}</td>
                                                <td class="px-3 py-2 text-center">{{ Akademik::formatNilai($mapel['hafalan']) }}</td>
                                                <td class="px-3 py-2 text-center font-black text-slate-800">{{ Akademik::formatNilai($mapel['akhir']) }}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <span class="inline-flex rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-black text-slate-600">{{ $mapel['predikat'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Ijazah digital --}}
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Ijazah Digital</h2>
                        <p class="text-[11px] text-slate-500">
                            Nomor: <b>{{ $nomorIjazah }}</b> · Kode verifikasi: <b>{{ $kodeVerifikasi }}</b>
                        </p>
                        @if ($siapIjazah)
                            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">
                                <i class="fa-solid fa-circle-check"></i> Status kelulusan tercatat.
                            </p>
                        @else
                            <p class="text-[11px] text-amber-600 font-semibold mt-0.5">
                                <i class="fa-solid fa-clock"></i> Menunggu pencatatan kelulusan oleh pesantren.
                            </p>
                        @endif
                    </div>
                </div>
                @if ($siapIjazah)
                    <a href="{{ route('alumni.ijazah.pdf') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-xs font-black text-white transition">
                        <i class="fa-solid fa-file-arrow-down"></i> Unduh Ijazah
                    </a>
                @endif
            </div>
        </div>

        {{-- Form tracer study --}}
        <div id="tracer" class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-900">Formulir Tracer Study</h2>
                <p class="text-[11px] text-slate-500">
                    Bantu pesantren memantau kegiatan alumni. Anda dapat memperbarui data ini kapan saja.
                </p>
            </div>

            <form method="POST" action="{{ route('alumni.tracer.simpan') }}" class="p-5 sm:p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Nama lengkap</label>
                        <input id="nama" name="nama" type="text" required value="{{ $nilaiTracer('nama', $konteks['nama']) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="kegiatan" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Kegiatan saat ini</label>
                        <select id="kegiatan" name="kegiatan" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                            <option value="">— Pilih kegiatan —</option>
                            @foreach ($daftarKegiatan as $kegiatan)
                                <option value="{{ $kegiatan }}" @selected($nilaiTracer('kegiatan') === $kegiatan)>{{ $kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tahun_lulus" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Tahun lulus</label>
                        <input id="tahun_lulus" name="tahun_lulus" type="number" min="1980" max="{{ now()->year + 1 }}"
                            value="{{ $nilaiTracer('tahun_lulus', $konteks['tahun_lulus']) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="jenjang" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Jenjang</label>
                        <input id="jenjang" name="jenjang" type="text" value="{{ $nilaiTracer('jenjang', $konteks['jenjang']) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="instansi" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Instansi / tempat kerja</label>
                        <input id="instansi" name="instansi" type="text" value="{{ $nilaiTracer('instansi') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="jabatan" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Jabatan / program studi</label>
                        <input id="jabatan" name="jabatan" type="text" value="{{ $nilaiTracer('jabatan') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="bidang" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Bidang</label>
                        <input id="bidang" name="bidang" type="text" value="{{ $nilaiTracer('bidang') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="mulai_tahun" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Mulai tahun</label>
                        <input id="mulai_tahun" name="mulai_tahun" type="number" min="1980" max="{{ now()->year + 1 }}"
                            value="{{ $nilaiTracer('mulai_tahun') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="no_hp" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">No. HP / WhatsApp</label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ $nilaiTracer('no_hp') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" value="{{ $nilaiTracer('email', $konteks['email']) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="alamat" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="2"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">{{ $nilaiTracer('alamat') }}</textarea>
                    </div>
                    <div>
                        <label for="testimoni" class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1.5">Testimoni / pesan untuk pesantren</label>
                        <textarea id="testimoni" name="testimoni" rows="3"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">{{ $nilaiTracer('testimoni') }}</textarea>
                    </div>
                </div>

                <label class="flex items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                    <input type="checkbox" name="bersedia_dihubungi" value="1"
                        @checked((bool) $nilaiTracer('bersedia_dihubungi'))
                        class="mt-0.5 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-[11px] font-semibold text-slate-600">
                        Saya bersedia dihubungi pesantren untuk kegiatan alumni.
                    </span>
                </label>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <p class="text-[11px] text-slate-400">
                        @if ($tracer)
                            Terakhir diperbarui {{ $tracer->updated_at?->translatedFormat('d F Y H:i') }}.
                        @else
                            Belum pernah diisi.
                        @endif
                    </p>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-violet-700 hover:bg-violet-800 px-5 py-2.5 text-xs font-black text-white transition">
                        <i class="fa-solid fa-paper-plane"></i> {{ $tracer ? 'Perbarui Tracer Study' : 'Kirim Tracer Study' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
