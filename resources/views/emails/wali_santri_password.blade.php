@component('mail::message')
# {{ $akunBaru ? 'Akun Portal Wali Santri Telah Aktif' : 'Kata Sandi Portal Wali Santri Direset' }}

Halo **{{ $wali->name }}**,

{{ $akunBaru
    ? 'Pendaftaran santri Anda telah diverifikasi dan disetujui oleh Admin Jenjang. Akun portal wali santri Anda sudah aktif.'
    : 'Kata sandi akun portal wali santri Anda telah direset oleh administrator pesantren.' }}

**Data Login**

@component('mail::table')
| Keterangan | Nilai |
| :--------- | :---- |
| Email | {{ $wali->email }} |
| Kata Sandi | {{ $password }} |
@endcomponent

@component('mail::button', ['url' => $urlLogin, 'color' => 'success'])
Masuk ke Portal Wali Santri
@endcomponent

Demi keamanan, segera ganti kata sandi Anda setelah login pertama melalui menu **Profil Saya**.

Jika Anda merasa tidak melakukan pendaftaran atau tidak mengenali email ini, silakan hubungi administrasi Pondok Pesantren Babul Hasanah.

Terima kasih,
**Tim Administrasi Pondok Pesantren Babul Hasanah**
@endcomponent
