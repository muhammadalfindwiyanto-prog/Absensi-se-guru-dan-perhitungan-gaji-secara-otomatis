@extends('layouts.app')
@section('title','Data Absensi')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">KEHADIRAN GURU</div>
        <h1>{{ auth()->user()->role === 'guru' ? 'Riwayat Absensi' : 'Data Absensi' }}</h1>
        <p>Pencatatan masuk, pulang, dan status kehadiran guru.</p>
    </div>
    @if(auth()->user()->role === 'admin')
        <a class="btn primary" href="{{ route('absensi.create') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Catat Absensi
        </a>
    @endif
</div>

<div class="panel">
    <form class="toolbar" method="GET">
        <label class="field">
            Filter Bulan
            <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}">
        </label>
        @if(auth()->user()->role !== 'guru')
            <label class="field">
                Guru
                <select name="guru_id">
                    <option value="">Semua Guru</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" @selected((string)request('guru_id') === (string)$g->id)>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </label>
        @endif
        <button class="btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Terapkan Filter
        </button>
        <a class="btn secondary" href="{{ route('absensi.index') }}">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Guru</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    @if(auth()->user()->role === 'admin')<th>Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                    <tr>
                        <td>{{ $r->tanggal->translatedFormat('d M Y') }}</td>
                        <td><strong>{{ $r->guru?->nama ?? '—' }}</strong></td>
                        <td>{{ $r->jam_masuk ? substr($r->jam_masuk,0,5) : '—' }}</td>
                        <td>{{ $r->jam_pulang ? substr($r->jam_pulang,0,5) : '—' }}</td>
                        <td><span class="badge {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $r->keterangan ?? '—' }}</td>
                        @if(auth()->user()->role === 'admin')
                            <td>
                                <a class="btn secondary sm" href="{{ route('absensi.edit', $r) }}">Edit</a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" class="empty">
                            <div class="empty-icon">📋</div>
                            Belum ada data absensi sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $records->links() }}</div>
</div>

@endsection
