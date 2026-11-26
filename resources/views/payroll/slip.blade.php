@extends('layouts.app')
@section('title','Slip Gaji')
@section('content')

<div class="page-title no-print">
    <div>
        <div class="eyebrow">DOKUMEN PENGGAJIAN</div>
        <h1>Slip Gaji</h1>
        <p>Rincian penghasilan dan potongan guru.</p>
    </div>
    <div class="actions">
        <a href="{{ route('payroll.index', ['bulan' => $record->bulan, 'tahun' => $record->tahun]) }}"
           class="btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali
        </a>
        <button onclick="window.print()" class="btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>
</div>

<article class="slip" id="slip">

    {{-- Header Slip --}}
    <div class="slip-top">
        <div class="slip-logo-wrap">
            <div class="slip-logo">
                <span class="slip-logo-letter">S</span>
            </div>
            <div>
                <h2>SE-GURU</h2>
                <small>Sistem Informasi Absensi &amp; Penggajian · MI</small>
            </div>
        </div>
        <div class="slip-right">
            <h2>SLIP GAJI</h2>
            <strong>{{ \Carbon\Carbon::create($record->tahun, $record->bulan, 1)->translatedFormat('F Y') }}</strong>
            <span class="badge {{ $record->status === 'disahkan' ? 'disahkan' : 'draft' }}">
                {{ $record->status === 'disahkan' ? 'Disahkan' : 'Draft' }}
            </span>
        </div>
    </div>

    {{-- Info Guru --}}
    <div class="slip-info">
        <div>
            <small>Nama Guru</small>
            <strong>{{ $record->guru?->nama }}</strong>
        </div>
        <div>
            <small>Kode Guru</small>
            <strong>{{ $record->guru?->kode }}</strong>
        </div>
        <div>
            <small>Jabatan</small>
            <strong>{{ $record->guru?->jabatan }}</strong>
        </div>
        <div>
            <small>Periode</small>
            <strong>{{ str_pad($record->bulan,2,'0',STR_PAD_LEFT) }}/{{ $record->tahun }}</strong>
        </div>
    </div>

    {{-- Rincian Pendapatan & Potongan --}}
    <div class="slip-columns">
        <div>
            <h3>PENDAPATAN</h3>
            <div class="slip-line">
                <span>Gaji Pokok</span>
                <strong>Rp {{ number_format($record->gaji_pokok,0,',','.') }}</strong>
            </div>
            <div class="slip-line">
                <span>Tunjangan</span>
                <strong>Rp {{ number_format($record->tunjangan,0,',','.') }}</strong>
            </div>
            <div class="slip-line">
                <span>Bonus</span>
                <strong>Rp {{ number_format($record->bonus,0,',','.') }}</strong>
            </div>
            <div class="slip-line">
                <span>Honor Tambahan</span>
                <strong>Rp {{ number_format($record->honor,0,',','.') }}</strong>
            </div>
            <div class="slip-line total">
                <span>Total Pendapatan</span>
                <strong>Rp {{ number_format($record->total_pendapatan,0,',','.') }}</strong>
            </div>
        </div>

        <div>
            <h3>POTONGAN</h3>
            <div class="slip-line">
                <span>Potongan Kehadiran</span>
                <strong>Rp {{ number_format($record->potongan_kehadiran,0,',','.') }}</strong>
            </div>
            <div class="slip-line">
                <span>Potongan Lainnya</span>
                <strong>Rp {{ number_format($record->potongan_lain,0,',','.') }}</strong>
            </div>
            <div class="slip-line total">
                <span>Total Potongan</span>
                <strong>Rp {{ number_format($record->total_potongan,0,',','.') }}</strong>
            </div>
        </div>
    </div>

    {{-- Gaji Bersih --}}
    <div class="slip-bottom">
        <div>
            <small>GAJI BERSIH DITERIMA</small>
            <strong>Rp {{ number_format($record->gaji_bersih,0,',','.') }}</strong>
        </div>
        <div class="slip-bottom-deco" aria-hidden="true">✦</div>
    </div>

    {{-- Rekap Kehadiran --}}
    <div class="slip-attendance">
        Rekap kehadiran:
        <strong class="text-green">Hadir {{ $record->hadir }}</strong> ·
        <span class="text-amber">Terlambat {{ $record->terlambat }}</span> ·
        Izin {{ $record->izin }} · Sakit {{ $record->sakit }} ·
        <strong class="text-red">Alpa {{ $record->alpa }}</strong>
    </div>

    <div class="slip-footer">
        Slip dihasilkan oleh SE-GURU ·
        {{ $record->disahkan_pada ? 'Disahkan '.$record->disahkan_pada->translatedFormat('d M Y, H:i') : 'Belum disahkan oleh bendahara' }}
    </div>

</article>

@endsection
