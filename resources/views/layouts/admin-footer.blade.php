    <!-- BOTTOM NAVIGATION -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 bg-slate-900/90 backdrop-blur-md border-t border-slate-800 px-6 py-2 flex items-center justify-around z-30 shadow-2xl">
        <a href="{{ route('admin.dashboard') }}"
            class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-400' : 'text-slate-400 hover:text-white' }} transition">
            <i class="fa-solid fa-chart-pie text-base"></i>
            <span class="text-[10px] font-bold">Dashboard</span>
        </a>
        <a href="{{ route('admin.pendaftaran.index') }}"
            class="flex flex-col items-center gap-1 py-1 {{ request()->routeIs('admin.pendaftaran.*') ? 'text-emerald-400' : 'text-slate-400 hover:text-white' }} transition">
            <i class="fas fa-address-book text-base"></i>
            <span class="text-[10px] font-bold">Pendaftaran</span>
        </a>
        <!-- Tombol Menu Tambahan untuk buka Sidebar Drawer -->
        <button @click="sidebarOpen = true"
            class="flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-bars text-base"></i>
            <span class="text-[10px] font-bold">Menu Lain</span>
        </button>
    </div>
