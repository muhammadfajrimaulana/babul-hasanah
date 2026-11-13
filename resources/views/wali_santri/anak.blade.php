@extends('layouts.admin')

@section('title', 'Data Anak - Pondok Pesantren Babul Hasanah')

@section('content')
<div class="container mt-4">
    <h2>Data Anak Saya</h2>
    
    @if($anakSaya)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Anak</h5>
                <hr>
                <p><strong>Nama Anak:</strong> {{ $anakSaya->nama_lengkap }}</p>
                <p><strong>Jenjang:</strong> {{ $anakSaya->jenjang_pendidikan }}</p>
                <p><strong>Kelas:</strong> {{ $anakSaya->kelas }}</p>
                <p><strong>Status:</strong> {{ $anakSaya->status_pendaftaran }}</p>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h5>Belum ada data anak</h5>
            <p>Anda belum terdaftar sebagai wali santri untuk anak- anak tertentu. Silakan hubungi Admin Jenjang untuk mendaftarkan data anak.</p>
            <a href="{{ route('pendaftaran.index') }}" class="btn btn-primary">Daftarkan Anak</a>
        </div>
    @endif
</div>
@endsection