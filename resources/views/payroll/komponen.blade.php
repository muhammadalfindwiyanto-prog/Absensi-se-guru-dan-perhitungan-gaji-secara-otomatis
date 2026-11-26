@extends('layouts.app')
@section('title','Komponen Gaji')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">KONFIGURASI PENGGAJIAN</div>
        <h1>Komponen Gaji Guru</h1>
        <p>Atur nominal penghasilan dan potongan untuk setiap guru (dalam Rupiah).</p>
    </div>
</div>

<div class="hint-box">
    <strong>ⓘ Rumus Penggajian</strong>
    <p>Gaji bersih = (Gaji Pokok + Tunjangan + Bonus + Honor) − (Potongan Alpa × Jumlah Alpa + Potongan Terlambat × Jumlah Terlambat + Potongan Lain).</p>
</div>

<div class="grid-two">
    @forelse($gurus as $g)
        <form class="panel item-panel" method="POST" action="{{ route('payroll.komponen.update', $g) }}">
            @csrf
            @method('PUT')

            <div class="panel-heading">
                <div class="person">
                    <div class="initials">{{ strtoupper(substr($g->nama,0,1)) }}</div>
                    <div>
                        <strong>{{ $g->nama }}</strong>
                        <small>{{ $g->kode }} · {{ $g->jabatan }}</small>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                @foreach([
                    'gaji_pokok'          => 'Gaji Pokok',
                    'tunjangan'           => 'Tunjangan',
                    'bonus'               => 'Bonus',
                    'honor'               => 'Honor Tambahan',
                    'potongan_alpa'       => 'Potongan / Alpa',
                    'potongan_terlambat'  => 'Potongan / Terlambat',
                    'potongan_lain'       => 'Potongan Lain',
                ] as $field => $label)
                    <label class="field">
                        {{ $label }} (Rp)
                        <input type="number" required min="0" max="100000000000"
                               name="{{ $field }}"
                               value="{{ old($field, $g->komponen?->$field ?? 0) }}">
                    </label>
                @endforeach
            </div>

            <button class="btn primary full" style="margin-top:16px;" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="20 6 9 17 4 12"/></svg>
                Simpan Komponen
            </button>
        </form>
    @empty
        <div class="panel" style="grid-column:span 2">
            <div class="empty">
                <div class="empty-icon">💼</div>
                Belum ada guru terdaftar.
            </div>
        </div>
    @endforelse
</div>

@endsection
