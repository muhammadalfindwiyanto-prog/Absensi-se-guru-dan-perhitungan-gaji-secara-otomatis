@extends('layouts.app')
@section('title','Jadwal Kerja')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">PENGATURAN KEHADIRAN</div>
        <h1>Jadwal Kerja Guru</h1>
        <p>Atur jam masuk, jam pulang, toleransi keterlambatan, dan hari kerja.</p>
    </div>
</div>

<div class="grid-two">
    @forelse($gurus as $guru)
        <form method="POST" action="{{ route('jadwal.update', $guru) }}" class="panel item-panel">
            @csrf
            @method('PUT')

            <div class="panel-heading">
                <div class="person">
                    <div class="initials">{{ strtoupper(substr($guru->nama,0,1)) }}</div>
                    <div>
                        <strong>{{ $guru->nama }}</strong>
                        <small>{{ $guru->kode }}</small>
                    </div>
                </div>
                <span class="badge {{ $guru->aktif ? 'hadir' : 'alpa' }}">
                    {{ $guru->aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="form-grid">
                <label class="field">
                    Jam Masuk
                    <input type="time" required name="jam_masuk"
                           value="{{ old('jam_masuk', substr($guru->jadwal?->jam_masuk ?? '07:00',0,5)) }}">
                </label>
                <label class="field">
                    Jam Pulang
                    <input type="time" required name="jam_pulang"
                           value="{{ old('jam_pulang', substr($guru->jadwal?->jam_pulang ?? '14:00',0,5)) }}">
                </label>
                <label class="field span-2">
                    Toleransi Terlambat (menit)
                    <input type="number" min="0" max="180" required name="toleransi_menit"
                           value="{{ old('toleransi_menit', $guru->jadwal?->toleransi_menit ?? 0) }}">
                </label>
            </div>

            <div class="field" style="margin-top:4px;">
                Hari Kerja
                <div class="day-checks">
                    @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $i => $nama)
                        <label>
                            <input type="checkbox" name="hari_kerja[]" value="{{ $i+1 }}"
                                   @checked(in_array($i+1, explode(',', $guru->jadwal?->hari_kerja ?? '1,2,3,4,5')))>
                            <span>{{ $nama }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="btn primary full" style="margin-top:18px;" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="20 6 9 17 4 12"/></svg>
                Simpan Jadwal
            </button>
        </form>
    @empty
        <div class="panel" style="grid-column:span 2">
            <div class="empty">
                <div class="empty-icon">📅</div>
                Belum ada guru terdaftar.
            </div>
        </div>
    @endforelse
</div>

@endsection
