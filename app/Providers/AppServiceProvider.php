<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Hafalan;
use App\Models\Izin;
use App\Models\Legalisir;
use App\Models\NilaiSantri;
use App\Models\Pelanggaran;
use App\Models\Santri;
use App\Policies\IzinPolicy;
use App\Policies\LegalisirPolicy;
use App\Policies\NilaiPolicy;
use App\Policies\PembinaanPolicy;
use App\Policies\PortalPolicy;
use App\Policies\SantriPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policy memakai App\Support\Akses sebagai satu-satunya sumber keputusan.
        Gate::policy(Santri::class, SantriPolicy::class);
        Gate::policy(NilaiSantri::class, NilaiPolicy::class);

        // Pembinaan memakai satu policy untuk tiga model sejenis.
        Gate::policy(Hafalan::class, PembinaanPolicy::class);
        Gate::policy(Pelanggaran::class, PembinaanPolicy::class);
        Gate::policy(Absensi::class, PembinaanPolicy::class);
        Gate::policy(Izin::class, IzinPolicy::class);
        Gate::policy(Legalisir::class, LegalisirPolicy::class);

        // Portal wali/alumni: ability tidak terikat satu model policy.
        Gate::define('portal.lihatAnak', [PortalPolicy::class, 'lihatAnak']);
        Gate::define('portal.lihatAnakId', [PortalPolicy::class, 'lihatAnakId']);
        Gate::define('portal.lihatDokumen', [PortalPolicy::class, 'lihatDokumen']);
        Gate::define('portal.lihatDokumenSantri', [PortalPolicy::class, 'lihatDokumenSantri']);
        Gate::define('portal.ajukanIzin', [PortalPolicy::class, 'ajukanIzin']);
        Gate::define('portal.lihatIzin', [PortalPolicy::class, 'lihatIzin']);
        Gate::define('portal.lihatAlumni', [PortalPolicy::class, 'lihatAlumni']);
        Gate::define('portal.lihatDokumenAlumni', [PortalPolicy::class, 'lihatDokumenAlumni']);
        Gate::define('portal.ajukanLegalisir', [PortalPolicy::class, 'ajukanLegalisir']);
        Gate::define('portal.unduhLegalisir', [PortalPolicy::class, 'unduhLegalisir']);

        // Jika web diakses lewat alamat yang mengandung kata 'ngrok'
        if (str_contains(request()->getHttpHost(), 'ngrok-free.dev') || str_contains(request()->getHttpHost(), 'ngrok.io')) {
            URL::forceScheme('https');
        }
    }
}
