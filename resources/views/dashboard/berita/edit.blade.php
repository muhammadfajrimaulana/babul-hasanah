@extends('layouts.admin')

@section('title', 'Edit Berita - Pondok Pesantren Babul Hasanah')

@section('content')
    @include('dashboard.berita._form', [
        'berita' => $berita,
        'action' => route('admin.berita.update', $berita),
        'method' => 'PUT',
    ])
@endsection