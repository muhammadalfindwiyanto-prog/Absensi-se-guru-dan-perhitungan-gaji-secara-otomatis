@extends('layouts.app')
@section('title','Akun Pengguna')
@section('content')

<div class="page-title">
    <div>
        <div class="eyebrow">PENGATURAN SISTEM</div>
        <h1>Akun Pengguna</h1>
        <p>Kelola login untuk Admin dan Bendahara. Akun guru dibuat dari menu Data Guru.</p>
    </div>
</div>

{{-- Form Tambah Akun --}}
<div class="panel form-panel">
    <div class="section-head">
        <h3>Tambah Akun Pengelola</h3>
        <p>Kata sandi minimal 8 karakter.</p>
    </div>

    <form method="POST" action="{{ route('accounts.store') }}">
        @csrf
        <div class="form-grid">
            <label class="field">
                Nama Lengkap
                <input name="name" required placeholder="Nama lengkap" value="{{ old('name') }}">
            </label>
            <label class="field">
                Email
                <input type="email" required name="email" value="{{ old('email') }}" placeholder="email@sekolah.sch.id">
            </label>
            <label class="field">
                Kata Sandi
                <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter">
            </label>
            <label class="field">
                Peran
                <select name="role">
                    <option value="bendahara">Bendahara</option>
                    <option value="admin">Admin</option>
                </select>
            </label>
        </div>
        <div style="margin-top:20px;">
            <button class="btn primary" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Akun
            </button>
        </div>
    </form>
</div>

{{-- Daftar Akun --}}
<div class="panel">
    <div class="panel-heading">
        <div>
            <h3>Akun Terdaftar</h3>
            <p>Kelola akun admin dan bendahara</p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Perbarui Profil / Kata Sandi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="person">
                                <div class="initials">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                <strong>{{ $u->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge {{ $u->role === 'guru' ? 'izin' : 'hadir' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($u->role, ['admin','bendahara']))
                                <form method="POST" action="{{ route('accounts.update', $u) }}" class="inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input name="name" aria-label="Nama" value="{{ $u->name }}" required>
                                    <input type="email" name="email" aria-label="Email" value="{{ $u->email }}" required>
                                    <input type="password" name="password" aria-label="Kata sandi baru"
                                           placeholder="Password baru (opsional)" minlength="8">
                                    <select name="role">
                                        <option value="admin" @selected($u->role === 'admin')>Admin</option>
                                        <option value="bendahara" @selected($u->role === 'bendahara')>Bendahara</option>
                                    </select>
                                    <button class="btn secondary sm" type="submit">Simpan</button>
                                </form>
                            @else
                                <a class="link-arrow" href="{{ route('gurus.index') }}">Kelola di Data Guru →</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
