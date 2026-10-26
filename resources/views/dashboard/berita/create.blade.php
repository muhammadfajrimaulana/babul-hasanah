@extends('layouts.admin')
@section('title', 'Tambah Berita')

@section('content')
    <div class="space-y-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-white">Tambah Berita Baru</h2>
        <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('dashboard.berita._form')
        </form>
    </div>
@endsection
