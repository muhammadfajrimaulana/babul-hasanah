@extends('layouts.admin')

@section('title', 'Tambah Berita - Pondok Pesantren Babul Hasanah')

@section('content')
    @include('dashboard.berita._form', [
        'berita' => null,
        'action' => route('admin.berita.store'),
        'method' => 'POST',
    ])
@endsection