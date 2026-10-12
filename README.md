# SE-GURU — Sistem Absensi dan Penggajian Guru

Aplikasi Laravel **12** berbasis PRD *Sistem Informasi Absensi Se-Guru dan Perhitungan Gaji Otomatis Berbasis Web*. Dibangun di atas project `se_guru` yang dilampirkan, bukan mengganti Laravel dengan framework lain.

## Fitur

| Pengguna | Hak Akses |
|---|---|
| Admin | Dashboard; CRUD guru dan akun guru; aktif/nonaktif; jadwal (jam masuk, pulang, toleransi dan hari); catatan & koreksi absensi; rekap; lihat payroll; akun admin/bendahara; laporan CSV |
| Guru | Login; absen masuk & pulang; pantau absensi dan rekap sendiri; slip gaji disahkan |
| Bendahara | Dashboard; rekap; pengaturan komponen gaji per guru; hitung draft gaji; tinjau & sahkan; slip cetak/PDF via browser; laporan CSV |

Tampilan adaptif desktop dan ponsel; styling lokal di `public/css/app.css` **tidak membutuhkan npm/Vite** untuk dapat tampil.

## Opsi cepat: instal database dari file SQL (db_se_guru.sql)

Paket ini menyertakan `database/sql/db_se_guru.sql` dan file SQL terpisah untuk diimpor di phpMyAdmin/HeidiSQL. SQL membuat database bernama **`db_se_guru`**, semua tabel Laravel dan SE-GURU, serta 6 akun demo, 4 profil guru, jadwal, dan komponen gaji. Import **hanya ke database baru/kosong** (backup bila sudah ada data).

1. Laragon → Start All → phpMyAdmin / HeidiSQL → menu **Import**, pilih `db_se_guru.sql`, lalu eksekusi. Script sudah menjalankan `CREATE DATABASE IF NOT EXISTS db_se_guru` dan `USE db_se_guru`. Bila UI meminta database terlebih dahulu, buat/klik `db_se_guru` sebelum impor.
2. Salin `.env.example` ke `.env`, pastikan `DB_DATABASE=db_se_guru`, `DB_USERNAME=root`, `DB_PASSWORD=` (sesuaikan password lokal Anda).
3. Jalankan `php artisan key:generate`, lalu `php artisan optimize:clear` dan `php artisan serve`.
4. **Jangan menjalankan `migrate:fresh`**, karena akan menghapus tabel beserta data demo. `php artisan migrate` opsional untuk migrasi **baru** di masa depan; empat migrasi bawaan project sudah tercatat di tabel `migrations` pada SQL.
5. **Tidak perlu `php artisan db:seed`**, karena akun demo dan data guru sudah ada dari file SQL. Jika Anda tetap menjalankan seeder, `firstOrCreate` akan melewati data master yang sama, tetapi dapat menambah riwayat absensi contoh bulan berjalan.

Password demo untuk Admin/Bendahara/Guru: `SeguruDemo!2026`. Segera ganti untuk penggunaan nyata. **Pilih salah satu metode setup database:** (A) import SQL, atau (B) database kosong + `php artisan migrate --seed`. Jangan menggabungkan keduanya saat setup awal.

## Cara instal di Laragon Windows

1. **Backup** project/database `se_guru` lama jika berisi data. Jika ingin memakai paket baru, ekstrak isi ZIP sehingga terdapat `D:\laragon\laragon\www\se_guru\artisan`. Hindari folder ganda `se_guru\se_guru`.
2. Buka Laragon, **Start All**, pastikan PHP >= 8.2 dan MySQL aktif.
3. Buat database MySQL `db_se_guru` melalui phpMyAdmin / HeidiSQL / terminal MySQL.
4. Di VS Code → Terminal → PowerShell atau CMD:

   ```powershell
   cd D:\laragon\laragon\www\se_guru
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   Jika ada `.env` sendiri yang sudah berjalan, jangan ditimpa. Sesuaikan koneksi database dan `APP_TIMEZONE=Asia/Jakarta`.
5. File `vendor/` disertakan untuk memudahkan percobaan lokal. Bila Composer tersedia dan perlu sinkronisasi dependency, jalankan `composer install` (jika vendor belum lengkap atau PHP berbeda).
6. Jalankan:

   ```powershell
   php artisan optimize:clear
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```

7. Buka **http://127.0.0.1:8000**.

**Catatan:** Jika Anda memakai database yang sudah memiliki tabel Laravel `users`, perintah `migrate` menambahkan kolom `role` dan tabel SE-GURU baru. Jangan menghapus data pengguna lama tanpa mencadangkan. Jangan menjalankan `migrate:fresh` jika ingin mempertahankan data.

### Jika PHP/Node tidak dikenali

Pakai terminal Laragon yang sudah memakai PHP Laragon; jalankan `php -v`. Node/NPM **tidak wajib** untuk tampilan SE-GURU ini (CSS dimuat langsung dari `public/css/app.css`).

### Alternatif instal dari nol tanpa `vendor/`

`composer install`, lalu `php artisan key:generate`, `php artisan migrate --seed`, `php artisan serve`. Tidak ada plugin frontend tambahan.

## Akun demo (hanya untuk komputer lokal)

Setelah `php artisan db:seed`:

| Peran | Email | Password |
|---|---|---|
| Admin | admin@seguru.test | SeguruDemo!2026 |
| Bendahara | bendahara@seguru.test | SeguruDemo!2026 |
| Guru contoh | guru@seguru.test | SeguruDemo!2026 |

Seeder menambahkan 4 guru dan data absensi dummy pada bulan berjalan. Seeder memakai `firstOrCreate`, sehingga data utama tidak ditimpa pada eksekusi ulang. **Ganti seluruh kata sandi demo melalui menu Akun/Guru sebelum dipakai selain demo lokal.** Akun demo bukan untuk deployment publik.

## Alur uji aplikasi

1. **Admin:** masuk, menu *Data Guru* → *Tambah Guru*, lalu *Jadwal Kerja*. Koreksi/isi kehadiran izin, sakit, alpa lewat *Data Absensi*; periksa *Rekap Bulanan*.
2. **Guru:** masuk dan klik *Absen Masuk*, kemudian *Absen Pulang* (masing-masing maksimal sekali per hari).
3. **Bendahara:** masuk, *Komponen Gaji* → isi gaji pokok, tunjangan, bonus, honor, potongan; lalu *Penggajian* → *Hitung Gaji Otomatis*. Tinjau slip sebelum klik *Sahkan*.
4. **Guru:** logout/login lagi, lihat *Slip Gaji* setelah bendahara mengesahkan.
5. **Admin/Bendahara:** *Laporan & Ekspor* → unduh CSV, buka lewat Excel (pemisah titik koma `;`, UTF-8).

## Logika absensi dan gaji

- Jam masuk dibandingkan jam jadwal + toleransi menit, dengan timezone `Asia/Jakarta`.
- Rekap bulanan menghitung hari kerja sesuai jadwal mingguan. Hari kerja yang telah lewat **tanpa record** dihitung sebagai `alpa`; hari ini yang belum absen belum alpa. Sabtu/Minggu tidak wajib jika jadwal hanya Senin–Jumat.
- Status `izin` dan `sakit` hanya bisa dimasukkan/dikoreksi oleh admin, karena PRD tidak merinci alur pengajuan guru.
- **Total pendapatan** = gaji pokok + tunjangan + bonus + honor tambahan.
- **Potongan kehadiran** = (alpa × potongan per alpa) + (terlambat × potongan per terlambat).
- **Total potongan** = potongan kehadiran + potongan lain.
- **Gaji bersih** = max(0, total pendapatan − total potongan). Nilai minimum Rp0 merupakan kebijakan pengaman untuk demo; tetapkan kebijakan gaji sekolah sebenarnya sebelum produksi.
- Penggajian periode berjalan adalah **provisional** dan dapat dihitung ulang selama `draft`; yang sudah `disahkan` tidak dihitung ulang, untuk menjaga histori slip.
- Sistem **belum otomatis mengenali libur nasional/cuti bersama/kalender akademik**. Admin harus memastikan jadwal dan data absensi periode benar sebelum mengesahkan slip; tanpa itu, hari libur di hari kerja dapat dihitung alpa. Untuk operasional nyata diperlukan modul kalender libur/pengecualian.

## Lingkup dan keterbatasan

Berdasarkan PRD kuliah: belum mencakup biometrik, aplikasi Android native, transfer bank, perpajakan, integrasi alat absensi. Slip dibuat menggunakan HTML yang bisa dicetak/simpan PDF dari browser; laporan diekspor CSV, bukan XLSX. Tidak termasuk klaim keamanan enterprise, audit trail koreksi, sinkronisasi data eksternal, atau verifikasi libur akademik; fitur tersebut perlu dikembangkan bila dipakai untuk gaji riil.

## Struktur sumber

- `app/Http/Controllers`: autentikasi, guru, jadwal, absensi, rekap, penggajian, laporan, pengelola akun.
- `app/Services/RekapService.php`: perhitungan rekap bulanan.
- `app/Http/Middleware/RequireRole.php`: proteksi hak akses endpoint.
- `database/migrations/2026_10_12_000001_create_se_guru_tables.php`: skema MySQL.
- `database/seeders/DatabaseSeeder.php`: akun & data demo.
- `resources/views/`: semua UI Blade.
- `public/css/app.css`: UI responsif tanpa dependency frontend.

## Jika muncul masalah

- `Access denied for user root`: cek `DB_USERNAME`, `DB_PASSWORD`, database, serta MySQL Laragon.
- `No application encryption key`: jalankan `php artisan key:generate`.
- `Nothing to migrate`: semua migrasi sudah dijalankan, bukan error.
- `Class ... not found`: jalankan `composer install` dan `php artisan optimize:clear`.
- Halaman tidak bergaya: pastikan `public/css/app.css` ada dan URL dibuka melalui `php artisan serve`.
