
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') · SE-GURU</title>

    {{-- Vite CSS & JavaScript --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
</head>

<body>

    {{-- Overlay Mobile Sidebar --}}
    <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true" onclick="closeSidebar()"></div>

    <div class="app-shell">

        {{-- SIDEBAR --}}
        <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">

            {{-- Brand --}}
            <div class="brand">
                <div class="brand-mark" aria-hidden="true">
                    <span class="brand-mark-letter">S</span>
                    <span class="brand-mark-star">✦</span>
                </div>
                <div>
                    <strong>SE-GURU</strong>
                    <small>Manajemen Madrasah</small>
                </div>
            </div>

            {{-- Menu Utama --}}
            <div class="side-caption">MENU UTAMA</div>

            <nav class="nav" role="navigation">

                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('gurus.index') }}"
                       class="{{ request()->routeIs('gurus.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Data Guru
                    </a>

                    <a href="{{ route('jadwal.index') }}"
                       class="{{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Jadwal Kerja
                    </a>
                @endif

                <a href="{{ route('absensi.index') }}"
                   class="{{ request()->routeIs('absensi.index', 'absensi.create', 'absensi.edit') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    {{ auth()->user()->role === 'guru' ? 'Riwayat Absensi' : 'Data Absensi' }}
                </a>

                <a href="{{ route('absensi.rekap') }}"
                   class="{{ request()->routeIs('absensi.rekap') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Rekap Bulanan
                </a>

                @if(auth()->user()->role === 'bendahara')
                    <a href="{{ route('payroll.komponen') }}"
                       class="{{ request()->routeIs('payroll.komponen*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                        Komponen Gaji
                    </a>
                @endif

                <a href="{{ route('payroll.index') }}"
                   class="{{ request()->routeIs('payroll.index', 'payroll.slip') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    {{ auth()->user()->role === 'guru' ? 'Slip Gaji' : 'Penggajian' }}
                </a>

                @if(in_array(auth()->user()->role, ['admin', 'bendahara']))
                    <a href="{{ route('reports.index') }}"
                       class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Laporan &amp; Ekspor
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <div class="side-caption sub">PENGATURAN</div>

                    <a href="{{ route('accounts.index') }}"
                       class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Akun Pengguna
                    </a>
                @endif

            </nav>

            {{-- Sidebar Bottom --}}
            <div class="sidebar-bottom">
                <div class="side-info">
                    <span class="dot-live" aria-hidden="true"></span>
                    Sistem aktif
                </div>
                <div class="version">SE-GURU v1.0 · Laravel 12</div>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <div class="main-wrap">

            {{-- TOPBAR --}}
            <header class="topbar" role="banner">

                <button
                    class="hamburger"
                    type="button"
                    id="hamburger-btn"
                    aria-label="Buka menu navigasi"
                    aria-expanded="false"
                    aria-controls="sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:18px;height:18px"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>

                <div class="breadcrumb" aria-label="Lokasi saat ini">
                    SE-GURU <span aria-hidden="true">/</span>
                    <span class="breadcrumb-current">@yield('title', 'Dashboard')</span>
                </div>

                <div class="top-actions">

                    <span class="today-label" aria-label="Tanggal hari ini">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>

                    <div class="avatar" aria-hidden="true">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="user-tag">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ ucfirst(auth()->user()->role) }}</small>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout" title="Keluar dari sistem" type="submit" aria-label="Keluar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        </button>
                    </form>

                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="content" role="main">

                @if(session('success'))
                    <div class="notice success" role="alert">
                        <span class="notice-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <div class="notice-body">{{ session('success') }}</div>
                        <button onclick="this.parentElement.remove()" aria-label="Tutup pesan">×</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="notice error" role="alert">
                        <span class="notice-icon" aria-hidden="true">!</span>
                        <div class="notice-body">{{ session('error') }}</div>
                        <button onclick="this.parentElement.remove()" aria-label="Tutup pesan">×</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="notice error" role="alert">
                        <span class="notice-icon" aria-hidden="true">!</span>
                        <div class="notice-body">
                            <strong>Periksa kembali data yang dimasukkan:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.remove()" aria-label="Tutup pesan">×</button>
                    </div>
                @endif

                @yield('content')

            </main>

            {{-- FOOTER --}}
            <footer class="footer" role="contentinfo">
                <span>© {{ date('Y') }} SE-GURU · Sistem Informasi Absensi &amp; Penggajian</span>
                <span>Madrasah Ibtidaiyah</span>
            </footer>

        </div>
    </div>

    {{-- SIDEBAR MOBILE SCRIPT --}}
    <script>
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebar-overlay');
        const hamburger = document.getElementById('hamburger-btn');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            hamburger.setAttribute('aria-expanded', 'true');
            hamburger.setAttribute('aria-label', 'Tutup menu navigasi');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.setAttribute('aria-label', 'Buka menu navigasi');
            document.body.style.overflow = '';
        }

        hamburger.addEventListener('click', function() {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });

        // Tutup dengan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
        });
    </script>

    @stack('scripts')

</body>
</html>
