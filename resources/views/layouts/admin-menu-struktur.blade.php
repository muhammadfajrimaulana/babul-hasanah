{{-- Item menu "Struktur" untuk sidebar admin. Dipanggil dari admin-sidebar.blade.php --}}
@if (\Illuminate\Support\Facades\Route::has('admin.struktur.index'))
    @php $strukturAktif = request()->routeIs('admin.struktur.*'); @endphp
    <a href="{{ route('admin.struktur.index') }}"
        class="flex items-center gap-4 px-4 py-2.5 rounded-xl text-sm transition-colors
        {{ $strukturAktif ? 'text-emerald-400 font-semibold' : 'text-slate-400 hover:text-white' }}">
        <i class="fa-solid fa-sitemap w-5 text-center"></i>
        <span>Struktur</span>
    </a>
@endif
