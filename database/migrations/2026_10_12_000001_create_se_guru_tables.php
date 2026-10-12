<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('guru')->after('password');
        });
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('kode', 30)->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('telepon', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan')->default('Guru');
            $table->string('status_pegawai')->default('Tetap');
            $table->date('tanggal_mulai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->unique()->constrained('gurus')->cascadeOnDelete();
            $table->time('jam_masuk')->default('07:00');
            $table->time('jam_pulang')->default('14:00');
            $table->unsignedSmallInteger('toleransi_menit')->default(0);
            $table->string('hari_kerja')->default('1,2,3,4,5');
            $table->timestamps();
        });
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['hadir','terlambat','izin','sakit','alpa']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['guru_id', 'tanggal']);
            $table->index(['tanggal','status']);
        });
        Schema::create('komponengajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->unique()->constrained('gurus')->cascadeOnDelete();
            foreach (['gaji_pokok','tunjangan','bonus','honor','potongan_lain','potongan_alpa','potongan_terlambat'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }
            $table->timestamps();
        });
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->unsignedSmallInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('hadir')->default(0);
            $table->unsignedInteger('terlambat')->default(0);
            $table->unsignedInteger('izin')->default(0);
            $table->unsignedInteger('sakit')->default(0);
            $table->unsignedInteger('alpa')->default(0);
            foreach (['gaji_pokok','tunjangan','bonus','honor','potongan_kehadiran','potongan_lain','total_pendapatan','total_potongan','gaji_bersih'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }
            $table->string('status')->default('draft');
            $table->timestamp('disahkan_pada')->nullable();
            $table->timestamps();
            $table->unique(['guru_id','bulan','tahun']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('penggajians');
        Schema::dropIfExists('komponengajis');
        Schema::dropIfExists('absensis');
        Schema::dropIfExists('jadwals');
        Schema::dropIfExists('gurus');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};
