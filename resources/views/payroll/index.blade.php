@extends('layouts.app')
@section('title', auth()->user()->role === 'guru' ? 'Slip Gaji' : 'Penggajian')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">{{ auth()->user()->role === 'guru' ? 'RIWAYAT GAJI' : 'MANAJEMEN KEUANGAN' }}</div>
        <h1>{{ auth()->user()->role === 'guru' ? 'Slip Gaji Saya' : 'Penggajian Guru' }}</h1>
        <p>{{ auth()->user()->role === 'guru' ? 'Lihat slip yang sudah disahkan bendahara.' : 'Hitung otomatis, tinjau draft, dan sahkan slip gaji.' }}</p>
    </div>
    @if(auth()->user()->role === 'bendahara')
        <a class="btn secondary" href="{{ route('payroll.komponen') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Atur Komponen
        </a>
    @endif
</div>

<div class="panel">
    <div class="toolbar-between">
        <form class="toolbar" method="GET">
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
            <button class="btn secondary">Tampilkan</button>
        </form>

        @if(auth()->user()->role === 'bendahara')
            <form method="POST" action="{{ route('payroll.hitung') }}"
                  onsubmit="return confirm('Hitung ulang semua draft periode ini? Slip yang sudah disahkan tidak berubah.')">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button class="btn primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                    Hitung Gaji Otomatis
                </button>
            </form>
        @endif
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Guru</th>
                    <th>Periode</th>
                    <th>Pendapatan</th>
                    <th>Potongan</th>
                    <th>Gaji Bersih</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                    <tr>
                        <td>
                            <strong>{{ $r->guru?->nama }}</strong>
                            <span class="table-sub">{{ $r->guru?->kode }}</span>
                        </td>
                        <td>{{ str_pad($r->bulan,2,'0',STR_PAD_LEFT) }}/{{ $r->tahun }}</td>
                        <td>Rp {{ number_format($r->total_pendapatan,0,',','.') }}</td>
                        <td>Rp {{ number_format($r->total_potongan,0,',','.') }}</td>
                        <td><strong>Rp {{ number_format($r->gaji_bersih,0,',','.') }}</strong></td>
                        <td>
                            <span class="badge {{ $r->status === 'disahkan' ? 'disahkan' : 'draft' }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn secondary sm" href="{{ route('payroll.slip', $r) }}">
                                    Lihat Slip
                                </a>
                                @if(auth()->user()->role === 'bendahara' && $r->status === 'draft')
                                    <form method="POST" action="{{ route('payroll.sahkan', $r) }}"
                                          onsubmit="return confirm('Sahkan penggajian ini? Setelah disahkan slip tidak bisa dihitung ulang.')">
                                        @csrf
                                        <button class="btn primary sm" type="submit">Sahkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            <div class="empty-icon">💰</div>
                            Belum ada data penggajian periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->role === 'bendahara')
    <div class="hint-box">
        <strong>ⓘ Alur Penggajian</strong>
        <p>1. Periksa jadwal dan rekap absensi → 2. Atur komponen gaji → 3. Hitung gaji otomatis → 4. Periksa slip → 5. Klik <strong>Sahkan</strong>. Slip yang disahkan tidak akan berubah ketika dihitung ulang, dan dapat dilihat oleh guru.</p>
    </div>
@endif

@endsection
