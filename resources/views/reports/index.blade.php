@extends('layouts.app')
@section('title','Laporan & Ekspor')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">PUSAT LAPORAN</div>
        <h1>Laporan &amp; Ekspor</h1>
        <p>Unduh data administrasi dalam format CSV yang dapat dibuka di Excel.</p>
    </div>
</div>

<div class="panel form-panel">
    <div class="section-head">
        <h3>Pilih Periode Laporan</h3>
        <p>Filter berlaku untuk seluruh laporan di bawah.</p>
    </div>
    <form method="GET">
        <div class="form-grid">
            <label class="field">
                Bulan
                <select name="bulan">
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" @selected((int)request('bulan', now()->month) === $b)>
                            {{ \Carbon\Carbon::create(null,$b,1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                Tahun
                <input type="number" name="tahun" min="2020" max="2100"
                       value="{{ request('tahun', now()->year) }}" style="width:110px;">
            </label>
        </div>
        <button class="btn secondary" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Terapkan Periode
        </button>
    </form>
</div>

<div class="report-grid">
    <div class="panel report-card">
        <div class="report-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>Laporan Data Guru</h3>
        <p>Identitas, status kepegawaian, dan informasi seluruh guru terdaftar.</p>
        <a class="btn secondary full"
           href="{{ route('reports.csv', ['type' => 'guru', 'bulan' => request('bulan', now()->month), 'tahun' => request('tahun', now()->year)]) }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh CSV Guru
        </a>
    </div>

    <div class="panel report-card">
        <div class="report-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <h3>Laporan Absensi</h3>
        <p>Data kehadiran, jam masuk, jam pulang, dan status per periode yang dipilih.</p>
        <a class="btn secondary full"
           href="{{ route('reports.csv', ['type' => 'absensi', 'bulan' => request('bulan', now()->month), 'tahun' => request('tahun', now()->year)]) }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh CSV Absensi
        </a>
    </div>

    <div class="panel report-card">
        <div class="report-icon gold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <h3>Laporan Penggajian</h3>
        <p>Rincian perhitungan gaji, potongan kehadiran, dan total gaji bersih guru.</p>
        <a class="btn secondary full"
           href="{{ route('reports.csv', ['type' => 'gaji', 'bulan' => request('bulan', now()->month), 'tahun' => request('tahun', now()->year)]) }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Unduh CSV Gaji
        </a>
    </div>
</div>

@endsection
