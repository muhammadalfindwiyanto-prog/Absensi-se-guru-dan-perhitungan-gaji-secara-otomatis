@extends('layouts.app')
@section('title','Rekap Absensi')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">LAPORAN KEHADIRAN</div>
        <h1>Rekap Absensi Bulanan</h1>
        <p>Ringkasan kehadiran dan keterlambatan berdasarkan jadwal kerja.</p>
    </div>
</div>

<div class="panel">
    <form method="GET" class="toolbar">
        <label class="field">
            Bulan
            <select name="bulan">
                @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" @selected($bulan === $b)>
                        {{ \Carbon\Carbon::create(null,$b,1)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </label>
        <label class="field">
            Tahun
            <input type="number" min="2020" max="2100" name="tahun" value="{{ $tahun }}" style="width:90px;">
        </label>
        <button class="btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Tampilkan Rekap
        </button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Guru</th>
                    <th>Hari Kerja</th>
                    <th>Hadir</th>
                    <th>Terlambat</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>
                            <div class="person">
                                <div class="initials">{{ strtoupper(substr($row['guru']->nama,0,1)) }}</div>
                                <div>
                                    <strong>{{ $row['guru']->nama }}</strong>
                                    <small>{{ $row['guru']->kode }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $row['rekap']['hari_kerja'] }}</td>
                        <td><span class="text-green">{{ $row['rekap']['hadir'] }}</span></td>
                        <td>{{ $row['rekap']['terlambat'] }}</td>
                        <td>{{ $row['rekap']['izin'] }}</td>
                        <td>{{ $row['rekap']['sakit'] }}</td>
                        <td><strong class="text-red">{{ $row['rekap']['alpa'] }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            <div class="empty-icon">📊</div>
                            Data guru belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="hint-box">
    <strong>ⓘ Ketentuan Rekap</strong>
    <p>Hari yang tidak memiliki catatan absensi dihitung sebagai alpa jika termasuk jadwal kerja dan sudah lewat. Hari ini yang belum absen tidak dihitung alpa. Hari libur sekolah di luar jadwal mingguan belum dikecualikan otomatis; admin perlu menyesuaikan jadwal dan data sebelum mengesahkan penggajian.</p>
</div>

@endsection
