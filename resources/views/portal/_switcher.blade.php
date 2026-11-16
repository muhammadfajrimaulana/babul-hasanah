@if ($portalWaliAktif && $portalAnakList->isNotEmpty())
    <form method="POST" action="{{ route('portal.anak.pilih') }}"
        class="flex items-center gap-2 min-w-0">
        @csrf
        <input type="hidden" name="redirect" value="{{ request()->fullUrlWithoutQuery(['anak']) }}">

        <div class="hidden sm:flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-[11px] font-black shrink-0">
            {{ $portalAnakAktif?->inisial() ?? '?' }}
        </div>

        <label class="sr-only" for="pilih-anak">Pilih anak</label>
        <select id="pilih-anak" name="anak" onchange="this.form.submit()"
            class="max-w-[220px] sm:max-w-[280px] rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            @foreach ($portalAnakList as $pilihan)
                <option value="{{ $pilihan->key }}" @selected($portalAnakAktif?->key === $pilihan->key)>
                    {{ $pilihan->label() }}
                </option>
            @endforeach
        </select>
    </form>
@endif
