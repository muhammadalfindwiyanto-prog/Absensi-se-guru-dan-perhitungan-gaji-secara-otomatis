@extends('layouts.app')
@section('title', $record->exists ? 'Edit Absensi' : 'Tambah Absensi')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">MANAJEMEN ABSENSI</div>
        <h1>{{ $record->exists ? 'Koreksi Absensi' : 'Catat Kehadiran' }}</h1>
        <p>Gunakan formulir ini untuk memperbaiki atau mencatat izin, sakit, dan alpa.</p>
    </div>
    <a class="btn secondary" href="{{ route('absensi.index') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>
</div>

<form class="panel form-panel" method="POST"
      action="{{ $record->exists ? route('absensi.update', $record) : route('absensi.store') }}">
    @csrf
    @if($record->exists) @method('PUT') @endif

    <div class="form-grid">
        <label class="field">
            Guru *
            <select name="guru_id" required>
                <option value="">Pilih Guru</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" @selected((string)old('guru_id', $record->guru_id) === (string)$g->id)>
                        {{ $g->nama }} ({{ $g->kode }})
                    </option>
                @endforeach
            </select>
        </label>

        <label class="field">
            Tanggal *
            <input type="date" name="tanggal" max="{{ date('Y-m-d') }}" required
                   value="{{ old('tanggal', $record->tanggal?->format('Y-m-d') ?? date('Y-m-d')) }}">
        </label>

        <label class="field">
            Status *
            <select name="status" required>
                @foreach(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $record->status) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="field">
            Jam Masuk
            <input type="time" name="jam_masuk"
                   value="{{ old('jam_masuk', $record->jam_masuk ? substr($record->jam_masuk,0,5) : null) }}">
        </label>

        <label class="field">
            Jam Pulang
            <input type="time" name="jam_pulang"
                   value="{{ old('jam_pulang', $record->jam_pulang ? substr($record->jam_pulang,0,5) : null) }}">
        </label>

        <label class="field span-2">
            Keterangan
            <textarea name="keterangan" rows="3" placeholder="Alasan izin, catatan koreksi, dll">{{ old('keterangan', $record->keterangan) }}</textarea>
        </label>
    </div>

    <p class="help-text">Status izin, sakit, atau alpa akan mengosongkan jam masuk dan pulang.</p>

    <div class="form-actions">
        <a class="btn secondary" href="{{ route('absensi.index') }}">Batal</a>
        <button class="btn primary" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="20 6 9 17 4 12"/></svg>
            Simpan Absensi
        </button>
    </div>
</form>

@endsection
