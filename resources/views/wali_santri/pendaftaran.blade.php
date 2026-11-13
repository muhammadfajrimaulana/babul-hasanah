@extends('layouts.admin')

@section('title', 'Riwayat Pendaftaran - Pondok Pesantren Babul Hasanah')

@section('content')
<div class="container mt-4">
    <h2>Riwayat Pendaftaran</h2>
    
    @if($riwayatPendaftaran)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatPendaftaran as $pendaftaran)
                        <tr>
                            <td>{{ $pendaftaran->tahun_ajaran }}</td>
                            <td>{{ $pendaftaran->jenjang }}</td>
                            <td><span class="badge bg-{{ $pendaftaran->status == 'lulus' ? 'success' : 'warning' }}">{{ $pendaftaran->status }}</span></td>
                            <td>
                                <span class="text-muted">-</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            <h5>Belum ada riwayat pendaftaran</h5>
            <p>Anda belum memiliki riwayat pendaftaran di sistem. Data akan tampil setelah proses verifikasi selesai.</p>
        </div>
    @endif
</div>
@endsection