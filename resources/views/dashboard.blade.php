@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">OVERVIEW SISTEM</div>
        <h1>Selamat datang, {{ explode(' ', auth()->user()->name)[0] }}! <span class="wave">✦</span></h1>
        <p>Ringkasan aktivitas pada {{ now()->translatedFormat('l, d F Y') }}.</p>
    </div>
    <span class="role-chip">{{ strtoupper(auth()->user()->role) }} PANEL</span>
</div>

@if(auth()->user()->role === 'guru')

    {{-- Hero Absensi Guru --}}
    <div class="hero-panel">
        <div>
            <span class="tag-white"><span class="dot-live" style="width:6px;height:6px;display:inline-block;margin-right:6px;vertical-align:middle;"></span>ABSENSI HARI INI</span>
            <h2>{{ $todayRecord ? 'Kehadiran tercatat ✓' : 'Siap memulai hari?' }}</h2>
            <p>
                @if($todayRecord)
                    Status Anda hari ini:
                    <strong style="color:#fff;">{{ ucfirst($todayRecord->status) }}</strong>
                @else
                    Catat absensi masuk Anda untuk memulai hari kerja.
                @endif
            </p>
            <div class="hero-times">
                <span>Masuk <strong>{{ $todayRecord?->jam_masuk ? substr($todayRecord->jam_masuk,0,5) : '--:--' }}</strong></span>
                <span>Pulang <strong>{{ $todayRecord?->jam_pulang ? substr($todayRecord->jam_pulang,0,5) : '--:--' }}</strong></span>
            </div>
        </div>
        <div class="hero-buttons">
            @if(!$todayRecord)
                <button class="btn white" type="button" onclick="openAbsenModal()" id="btn-open-absen">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Catat Absensi (Hadir / Izin)
                </button>
            @elseif(in_array($todayRecord->status, ['izin', 'sakit', 'alpa']))
                <span class="badge light" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);padding:9px 15px;font-size:12px;">
                    ✓ Status: {{ ucfirst($todayRecord->status) }} tercatat
                </span>
            @elseif($todayRecord->jam_masuk && !$todayRecord->jam_pulang)
                <form action="{{ route('absensi.pulang') }}" method="POST">
                    @csrf
                    <button class="btn white" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Absen Pulang
                    </button>
                </form>
            @else
                <span class="badge light">✓ Absensi hari ini selesai</span>
            @endif
        </div>
    </div>

    {{-- MODAL PILIHAN ABSENSI (HADIR ATAU IZIN) --}}
    @if(!$todayRecord)
    <div id="modal-absen" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)closeAbsenModal()">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-absen-title">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">ABSENSI GURU</span>
                    <h3 id="modal-absen-title">Pilih Kehadiran Hari Ini</h3>
                    <p>{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <button type="button" class="modal-close" onclick="closeAbsenModal()" aria-label="Tutup">×</button>
            </div>

            <div class="modal-body">
                {{-- OPSI 1: HADIR --}}
                <div class="absen-choice-card">
                    <div class="absen-choice-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="absen-choice-content">
                        <strong>Hadir (Masuk Mengajar)</strong>
                        <p>Catat kehadiran masuk Anda di madrasah sesuai waktu saat ini.</p>
                        <form action="{{ route('absensi.masuk') }}" method="POST" style="margin-top:10px;">
                            @csrf
                            <button type="submit" class="btn primary sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Rekam Hadir Masuk
                            </button>
                        </form>
                    </div>
                </div>

                {{-- OPSI 2: IZIN --}}
                <div class="absen-choice-card">
                    <div class="absen-choice-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="absen-choice-content">
                        <strong>Izin Tidak Hadir</strong>
                        <p>Ajukan izin jika Anda berhalangan mengajar (urusan dinas, keluarga, atau keperluan lain).</p>
                        <form action="{{ route('absensi.izin') }}" method="POST" style="margin-top:10px;">
                            @csrf
                            <div class="field" style="margin-bottom:8px;">
                                <input type="text" name="keterangan" placeholder="Alasan izin (contoh: Acara keluarga, dinas luar, dll)" style="font-size:12px;padding:8px 10px;">
                            </div>
                            <button type="submit" class="btn secondary sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                Ajukan Izin Hari Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAbsenModal() {
            const m = document.getElementById('modal-absen');
            if (m) {
                m.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }
        function closeAbsenModal() {
            const m = document.getElementById('modal-absen');
            if (m) {
                m.style.display = 'none';
                document.body.style.overflow = '';
            }
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAbsenModal();
        });
    </script>
    @endif

    {{-- Statistik Bulanan --}}
    <div class="metrics four">
        <div class="metric-card">
            <div class="metric-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="metric-label">Hadir bulan ini</div>
            <div class="metric-value">{{ $monthly['hadir'] }}</div>
            <div class="metric-foot">Hari kehadiran</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="metric-label">Terlambat</div>
            <div class="metric-value">{{ $monthly['terlambat'] }}</div>
            <div class="metric-foot">Kali terlambat</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="metric-label">Izin &amp; Sakit</div>
            <div class="metric-value">{{ $monthly['izin'] + $monthly['sakit'] }}</div>
            <div class="metric-foot">Hari tidak masuk berizin</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon gold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="metric-label">Gaji terakhir</div>
            <div class="metric-value small">{{ $slip ? 'Rp '.number_format($slip->gaji_bersih,0,',','.') : '—' }}</div>
            <div class="metric-foot">
                {{ $slip ? 'Periode '.str_pad($slip->bulan,2,'0',STR_PAD_LEFT).'/'.$slip->tahun : 'Belum ada slip disahkan' }}
            </div>
        </div>
    </div>

    {{-- Tabel Absensi Terbaru --}}
    <div class="panel">
        <div class="panel-heading">
            <div>
                <h3>Aktivitas Kehadiran Terkini</h3>
                <p>Riwayat absensi 10 hari terakhir</p>
            </div>
            <a class="link-arrow" href="{{ route('absensi.index') }}">Lihat semua →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent as $r)
                        <tr>
                            <td>{{ $r->tanggal->translatedFormat('d M Y') }}</td>
                            <td>{{ $r->jam_masuk ? substr($r->jam_masuk,0,5) : '—' }}</td>
                            <td>{{ $r->jam_pulang ? substr($r->jam_pulang,0,5) : '—' }}</td>
                            <td><span class="badge {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">
                                <div class="empty-icon">📋</div>
                                Belum ada absensi tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else

    {{-- Hero Admin / Bendahara --}}
    <div class="hero-panel">
        <div>
            <span class="tag-white"><span class="dot-live" style="width:6px;height:6px;display:inline-block;margin-right:6px;vertical-align:middle;"></span>SISTEM ADMINISTRASI TERPADU</span>
            <h2>Kelola madrasah dalam satu tempat.</h2>
            <p>
                @if(auth()->user()->role === 'admin')
                    Pantau aktivitas guru, atur jadwal kerja, dan tinjau laporan kehadiran.
                @else
                    Hitung gaji berbasis data kehadiran, periksa hasil, dan terbitkan slip gaji.
                @endif
            </p>
        </div>
        <div class="hero-deco" aria-hidden="true">✦</div>
    </div>

    {{-- Statistik --}}
    <div class="metrics four">
        <div class="metric-card">
            <div class="metric-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="metric-label">Guru Aktif</div>
            <div class="metric-value">{{ $stat['total'] }}</div>
            <div class="metric-foot">Terdaftar dalam sistem</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="metric-label">Hadir Hari Ini</div>
            <div class="metric-value">{{ $stat['hadir'] }}</div>
            <div class="metric-foot">Termasuk terlambat</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon amber">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="metric-label">Terlambat Hari Ini</div>
            <div class="metric-value">{{ $stat['terlambat'] }}</div>
            <div class="metric-foot">Melewati batas toleransi</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon gold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="metric-label">Gaji Disahkan (Bulan Ini)</div>
            <div class="metric-value small">Rp {{ number_format($payrollTotal,0,',','.') }}</div>
            <div class="metric-foot">Total pengeluaran bulan ini</div>
        </div>
    </div>

    {{-- Mini Stats Kehadiran --}}
    <div class="metrics three">
        <div class="mini-stat">
            <span class="badge izin">Izin</span>
            <strong>{{ $stat['izin'] }}</strong>
            <small>Guru hari ini</small>
        </div>
        <div class="mini-stat">
            <span class="badge sakit">Sakit</span>
            <strong>{{ $stat['sakit'] }}</strong>
            <small>Guru hari ini</small>
        </div>
        <div class="mini-stat">
            <span class="badge alpa">Alpa</span>
            <strong>{{ $stat['alpa'] }}</strong>
            <small>Guru hari ini</small>
        </div>
    </div>

    {{-- Akses Cepat --}}
    <div class="panel">
        <div class="panel-heading">
            <div>
                <h3>Akses Cepat</h3>
                <p>Pilih aktivitas yang ingin Anda lakukan</p>
            </div>
        </div>
        <div class="quick-links">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('gurus.create') }}">
                    <span class="quick-ico green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    </span>
                    <strong>Tambah Guru</strong>
                    <small>Daftarkan guru baru</small>
                    <span>→</span>
                </a>
                <a href="{{ route('jadwal.index') }}">
                    <span class="quick-ico blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    <strong>Atur Jadwal</strong>
                    <small>Jam dan hari kerja</small>
                    <span>→</span>
                </a>
                <a href="{{ route('absensi.index') }}">
                    <span class="quick-ico amber">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </span>
                    <strong>Lihat Absensi</strong>
                    <small>Monitor kehadiran guru</small>
                    <span>→</span>
                </a>
            @else
                <a href="{{ route('payroll.komponen') }}">
                    <span class="quick-ico blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                    </span>
                    <strong>Komponen Gaji</strong>
                    <small>Atur gaji dan potongan</small>
                    <span>→</span>
                </a>
                <a href="{{ route('payroll.index') }}">
                    <span class="quick-ico gold">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                    <strong>Hitung Penggajian</strong>
                    <small>Buat draft dan slip</small>
                    <span>→</span>
                </a>
                <a href="{{ route('absensi.rekap') }}">
                    <span class="quick-ico green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </span>
                    <strong>Rekap Absensi</strong>
                    <small>Data dasar penggajian</small>
                    <span>→</span>
                </a>
            @endif
        </div>
    </div>

@endif

@endsection
