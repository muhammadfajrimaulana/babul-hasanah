{{-- Satu node bagan, memanggil dirinya sendiri untuk bawahan (rekursif). --}}
@php
    $kelas = $level === 0 ? 'top' : ($level === 1 ? 'gold' : '');
    $ikon  = $node->ikon ?: ($level === 0 ? 'fa-user-tie' : 'fa-user');
@endphp
<li>
    <div class="person {{ $kelas }}">
        <div class="avatar">
            <i class="fa-solid {{ $ikon }}"></i>
            @if ($node->foto)
                <img src="{{ $node->foto_url }}" alt="{{ $node->nama }}" @if ($level > 1) loading="lazy" @endif onerror="this.remove()">
            @endif
        </div>
        <h3>{{ $node->nama }}</h3>
        <span>{{ $node->jabatan }}</span>
    </div>

    @if ($node->anak->isNotEmpty())
        <ul>
            @foreach ($node->anak as $anak)
                @include('pages.partials.struktur-node', ['node' => $anak, 'level' => $level + 1])
            @endforeach
        </ul>
    @endif
</li>
