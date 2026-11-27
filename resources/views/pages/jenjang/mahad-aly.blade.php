@extends('layouts.app')

@section('title', $p['judul_halaman'].' - Pondok Pesantren Babul Hasanah')

@section('content')

@include('pages.jenjang._detail', ['p' => $p])

@endsection
