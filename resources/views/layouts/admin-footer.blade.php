    @php
        $u = auth()->user();
        $dashRoute = \App\Support\Akses::dashboardRoute($u);
    @endphp

    <!-- BOTTOM NAVIGATION -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 bg-gradient-to-r from-emerald-800 via-emerald-900 to-emerald-950 backdrop-blur-md border-t border-emerald-700/50 px-6 py-2 flex items-center justify-around z-30 shadow-2xl">
        <a href="{{ route($dashRoute) }}"
            class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('admin.dashboard*') ? 'text-amber-300' : 'text-emerald-100/70 hover:text-white' }} transition">
            <i class="fa-solid fa-chart-pie text-base"></i>
            <span class="text-[10px] font-bold">Dashboard</span>
        </a>
        @if ($u?->bisa('pendaftaran.lihat'))
            <a href="{{ route('admin.pendaftaran.index') }}"
                class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('admin.pendaftaran.*') ? 'text-amber-300' : 'text-emerald-100/70 hover:text-white' }} transition">
                <i class="fas fa-address-book text-base"></i>
                <span class="text-[10px] font-bold">Pendaftaran</span>
            </a>
        @endif
        <!-- Tombol Menu Tambahan untuk buka Sidebar Drawer -->
        <button @click="sidebarOpen = true"
            class="flex flex-col items-center gap-1 py-1 text-emerald-100/70 hover:text-white transition">
            <i class="fa-solid fa-bars text-base"></i>
            <span class="text-[10px] font-bold">Menu Lain</span>
        </button>
    </div>
