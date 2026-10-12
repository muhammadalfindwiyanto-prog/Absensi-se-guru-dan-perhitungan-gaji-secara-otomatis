<?php
namespace Tests\Feature;
use App\Models\{User,Guru,Jadwal,KomponenGaji,Absensi,Penggajian};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SeGuruFeatureTest extends TestCase {
    use RefreshDatabase;
    private function account(string $role):User {
        return User::create(['name'=>ucfirst($role),'email'=>$role.'@testing.test','password'=>'password123','role'=>$role]);
    }
    private function teacher():array {
        $user=$this->account('guru');
        $guru=Guru::create(['user_id'=>$user->id,'kode'=>'GR-T1','nama'=>'Guru Tes','jenis_kelamin'=>'L','jabatan'=>'Guru','status_pegawai'=>'Tetap','tanggal_mulai'=>'2024-01-01','aktif'=>1]);
        Jadwal::create(['guru_id'=>$guru->id,'jam_masuk'=>'07:00','jam_pulang'=>'14:00','toleransi_menit'=>10,'hari_kerja'=>'1,2,3,4,5']);
        KomponenGaji::create(['guru_id'=>$guru->id,'gaji_pokok'=>2000000,'tunjangan'=>300000,'bonus'=>100000,'honor'=>100000,'potongan_alpa'=>50000,'potongan_terlambat'=>10000,'potongan_lain'=>25000]);
        return [$user,$guru];
    }
    public function test_pengguna_harus_login():void {
        $this->get('/dashboard')->assertRedirect('/login');
    }
    public function test_guru_tidak_memiliki_akses_admin_dan_bendahara():void {
        [$user]=$this->teacher();
        $this->actingAs($user)->get('/guru')->assertForbidden();
        $this->actingAs($user)->get('/komponen-gaji')->assertForbidden();
        $this->actingAs($user)->get('/laporan')->assertForbidden();
    }
    public function test_admin_dapat_melihat_data_guru_dan_bukan_komponen_gaji():void {
        $admin=$this->account('admin');
        $this->actingAs($admin)->get('/guru')->assertOk();
        $this->actingAs($admin)->get('/komponen-gaji')->assertForbidden();
    }
    public function test_bendahara_menghitung_draft_lalu_mengesahkan():void {
        [, $guru]=$this->teacher();
        $bendahara=$this->account('bendahara');
        $bulan=(int)now()->month;$tahun=(int)now()->year;
        $response=$this->actingAs($bendahara)->post('/penggajian/hitung',['bulan'=>$bulan,'tahun'=>$tahun]);
        $response->assertRedirect();
        $slip=Penggajian::where('guru_id',$guru->id)->firstOrFail();
        $this->assertSame(2500000,(int)$slip->total_pendapatan);
        $this->assertSame((int)$slip->total_pendapatan - (int)$slip->total_potongan,(int)$slip->gaji_bersih);
        $this->actingAs($bendahara)->post('/penggajian/'.$slip->id.'/sahkan')->assertRedirect();
        $this->assertEquals('disahkan',$slip->fresh()->status);
        $this->actingAs($bendahara)->post('/penggajian/hitung',['bulan'=>$bulan,'tahun'=>$tahun]);
        $this->assertEquals('disahkan',$slip->fresh()->status);
    }
    public function test_guru_hanya_bisa_melihat_slip_milik_sendiri_yang_disahkan():void {
        [$user,$guru]=$this->teacher();
        $slip=Penggajian::create(['guru_id'=>$guru->id,'bulan'=>1,'tahun'=>2026,'status'=>'draft']);
        $this->actingAs($user)->get('/slip/'.$slip->id)->assertForbidden();
        $slip->update(['status'=>'disahkan']);
        $this->actingAs($user)->get('/slip/'.$slip->id)->assertOk();
    }
    public function test_admin_dapat_mencatat_izin_tanpa_jam():void {
        [, $guru]=$this->teacher();$admin=$this->account('admin');
        $this->actingAs($admin)->post('/absensi',['guru_id'=>$guru->id,'tanggal'=>now()->toDateString(),'status'=>'izin','jam_masuk'=>'07:00','jam_pulang'=>'14:00'])->assertRedirect('/absensi');
        $r=Absensi::where('guru_id',$guru->id)->firstOrFail();
        $this->assertNull($r->jam_masuk);$this->assertEquals('izin',$r->status);
    }
}
