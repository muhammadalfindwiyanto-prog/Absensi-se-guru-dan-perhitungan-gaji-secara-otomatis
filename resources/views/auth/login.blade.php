<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · SE-GURU</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
</head>
<body class="login-body">

<div class="login-wrap">

    {{-- PANEL KIRI — Identitas Sekolah --}}
    <section class="login-hero" aria-hidden="true">
        <div class="login-hero-deco"></div>

        <div class="login-brand">
            <div class="login-brand-mark">
                <span class="login-brand-mark-letter">S</span>
            </div>
            <div>
                <div class="login-brand-name">SE-GURU</div>
                <div class="login-brand-sub">Sistem Informasi Terintegrasi</div>
            </div>
        </div>

        <div class="login-copy">
            <span class="pill-label">✦ MADRASAH MODERN</span>
            <h1>Administrasi <em>madrasah</em>,<br>lebih teratur.</h1>
            <p>Satu platform untuk kehadiran guru, rekap bulanan otomatis, dan penggajian yang transparan dan akuntabel.</p>

            <div class="login-features">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Absensi real-time
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Penggajian otomatis
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Laporan terintegrasi
                </div>
            </div>
        </div>

        <div class="login-foot">SE-GURU · MI — Sistem Informasi Absensi &amp; Penggajian Guru</div>
    </section>

    {{-- PANEL KANAN — Formulir Login --}}
    <section class="login-panel">
        <div class="login-form">

            <div class="mobile-brand">
                <div class="login-brand-mark" style="width:38px;height:38px;">
                    <span class="login-brand-mark-letter" style="font-size:19px;">S</span>
                </div>
                SE-GURU
            </div>

            <div class="login-heading">
                <span class="eyebrow">SELAMAT DATANG</span>
                <h2>Masuk ke akun Anda</h2>
                <p>Silakan masukkan kredensial yang terdaftar untuk melanjutkan.</p>
            </div>

            @if($errors->any())
                <div class="notice error" role="alert">
                    <span class="notice-icon">!</span>
                    <div class="notice-body">{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="login-form">
                @csrf

                <div class="field">
                    <label for="email">Alamat Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        autofocus
                        value="{{ old('email') }}"
                        placeholder="nama@sekolah.sch.id"
                        autocomplete="username"
                        aria-describedby="email-hint"
                    >
                </div>

                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <div class="input-password-wrap">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" aria-label="Tampilkan kata sandi" onclick="togglePassword(this)">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn primary full lg" id="submit-btn">
                    Masuk ke Dashboard
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>

            <p class="login-note">Akses hanya untuk Admin, Guru, dan Bendahara terdaftar.</p>
            <p class="login-copyright">© {{ date('Y') }} SE-GURU. Seluruh hak dilindungi.</p>
        </div>
    </section>

</div>

<script>
function togglePassword(btn) {
    const input = document.getElementById('password');
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.setAttribute('aria-label', isText ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
    btn.querySelector('svg').innerHTML = isText
        ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
        : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
}

// Loading state tombol submit
document.getElementById('login-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Memproses...';
});
</script>

</body>
</html>
