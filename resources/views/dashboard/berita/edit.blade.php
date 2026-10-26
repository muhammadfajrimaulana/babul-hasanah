@extends('layouts.admin')
@section('title', 'Edit Berita')

@section('content')
    <div class="space-y-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-white">Edit Berita</h2>
        <form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('dashboard.berita._form', ['berita' => $berita])
        </form>
    </div>
@endsection
