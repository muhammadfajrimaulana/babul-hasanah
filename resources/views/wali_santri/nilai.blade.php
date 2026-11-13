@extends('layouts.admin')

@section('title', 'Nilai Akademik - Pondok Pesantren Babul Hasanah')

@section('content')
<div class="container mt-4">
    <h2>Nilai Akademik</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Nilai</th>
                    <th>Keterangan</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bahasa Indonesia</td>
                    <td>-</td>
                    <td>Belum ada nilai</td>
                    <td>Ganjil 2024/2025</td>
                </tr>
                <tr>
                    <td>Matematika</td>
                    <td>-</td>
                    <td>Belum ada nilai</td>
                    <td>Ganjil 2024/2025</td>
                </tr>
                <tr>
                    <td>Ilmu Pengetahuan Alam</td>
                    <td>-</td>
                    <td>Belum ada nilai</td>
                    <td>Ganjil 2024/2025</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        <p><i>Catatan: Data nilai akan segera sepenuhnya terisi setelahperiode ujian berakhir.</i></p>
        <a href="{{ route('portal.nilai') }}" class="btn btn-link">Refresh Data</a>
    </div>
</div>
@endsection