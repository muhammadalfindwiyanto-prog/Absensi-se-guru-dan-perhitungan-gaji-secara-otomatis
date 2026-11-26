@extends('layouts.app')
@section('title','Data Guru')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">MANAJEMEN SDM</div>
        <h1>Data Guru</h1>
        <p>Kelola profil dan akun tenaga pendidik madrasah.</p>
    </div>
    <a class="btn primary" href="{{ route('gurus.create') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Guru
    </a>
</div>

<div class="panel">
    <div class="panel-heading">
        <div>
            <h3>Daftar Guru</h3>
            <p>{{ $gurus->total() }} guru terdaftar dalam sistem</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Guru</th>
                    <th>Kode</th>
                    <th>Jabatan</th>
                    <th>Status Pegawai</th>
                    <th>Keaktifan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $guru)
                    <tr>
                        <td>
                            <div class="person">
                                <div class="initials">{{ strtoupper(substr($guru->nama,0,1)) }}</div>
                                <div>
                                    <strong>{{ $guru->nama }}</strong>
                                    <small>{{ $guru->user?->email ?? 'Tanpa akun' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $guru->kode }}</td>
                        <td>{{ $guru->jabatan }}</td>
                        <td>{{ $guru->status_pegawai }}</td>
                        <td>
                            <span class="badge {{ $guru->aktif ? 'hadir' : 'alpa' }}">
                                {{ $guru->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary sm" href="{{ route('gurus.edit', $guru) }}">Edit</a>
                                <form method="POST" action="{{ route('gurus.toggle', $guru) }}"
                                      onsubmit="return confirm('Ubah status aktif guru {{ addslashes($guru->nama) }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn {{ $guru->aktif ? 'ghost' : 'secondary' }} sm" type="submit">
                                        {{ $guru->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            <div class="empty-icon">👩‍🏫</div>
                            Belum ada guru terdaftar.
                            <p><a href="{{ route('gurus.create') }}" class="text-green">Tambahkan guru pertama →</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $gurus->links() }}</div>
</div>

@endsection
