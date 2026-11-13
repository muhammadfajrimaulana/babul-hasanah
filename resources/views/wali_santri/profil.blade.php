@extends('layouts.admin')

@section('title', 'Profil Wali Santri - Pondok Pesantren Babul Hasanah')

@section('content')
<div class="container mt-4">
    <h2>Profil Saya</h2>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Akun</h5>
                    <hr>
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-success">Aktif</span></p>
                    <hr>
                    <h5 class="card-title">Anak Terkait</h5>
                    <p>Data anak yang terkait dengan akun wali ini akan ditampilkan di sini.</p>
                    <a href="{{ route('wali.anak') }}" class="btn btn-primary">Lihat Data Anak</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection