@if (\App\Support\Akses::adminTanpaJenjang(auth()->user()))
    <div class="rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50 dark:bg-rose-500/10 px-4 py-3.5 flex items-start gap-2.5">
        <i class="fa-solid fa-user-lock text-rose-600 dark:text-rose-400 mt-0.5"></i>
        <div>
            <p class="text-xs font-black text-rose-800 dark:text-rose-300">Akun admin ini belum memiliki jenjang</p>
            <p class="text-[11px] text-rose-700/90 dark:text-rose-300/80 leading-relaxed">
                Demi keamanan, data akademik tidak ditampilkan sampai Super Admin mengatur jenjang pada akun Anda.
                Hubungi Super Admin untuk melengkapi kolom jenjang pengguna.
            </p>
        </div>
    </div>
@endif
