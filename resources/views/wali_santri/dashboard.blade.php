@extends('layouts.admin')

@section('title', 'Dashboard Wali Santri - Pondok Pesantren Babul Hasanah')

@section('content')
<div class="container mt-4">
    <h2>Selamat Datang, Wali Santri</h2>
    <p>Selamat datang di portal wali santri Pondok Pesantren Babul Hasanah.</p>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Anak Saya</h5>
                    <p class="card-text">Lihat perkembangan akademik anak Anda.</p>
                    <a href="{{ route('wali.anak') }}" class="btn btn-primary">Lihat Data Anak</a>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nilai & Akademik</h5>
                    <p class="card-text">Cek nilai dan raport anak Anda.</p>
                    <a href="{{ route('portal.nilai') }}" class="btn btn-primary">Lihat Nilai</a>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Profil Saya</h5>
                    <p class="card-text">Lihat dan profil akun wali santri.</p>
                    <a href="{{ route('profil') }}" class="btn btn-primary">Profil Saya</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection