<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User,Guru,Jadwal,KomponenGaji,Absensi};
use Carbon\CarbonImmutable;
class DatabaseSeeder extends Seeder {
    public function run():void {
        $password='SeguruDemo!2026';
        User::firstOrCreate(['email'=>'admin@seguru.test'],['name'=>'Administrator','password'=>$password,'role'=>'admin']);
        User::firstOrCreate(['email'=>'bendahara@seguru.test'],['name'=>'Bendahara Sekolah','password'=>$password,'role'=>'bendahara']);
        $samples=[
            ['GR-001','Ahmad Fauzi','L','Guru Matematika',2500000],
            ['GR-002','Siti Aisyah','P','Guru Bahasa Indonesia',2300000],
            ['GR-003','Rizky Pratama','L','Guru IPA',2200000],
            ['GR-004','Nurul Hidayah','P','Guru Bahasa Inggris',2400000]
        ];
        foreach($samples as $i=>[$kode,$nama,$jk,$jabatan,$pokok]){
            $email=$i===0?'guru@seguru.test':'guru'.($i+1).'@seguru.test';
            $user=User::firstOrCreate(['email'=>$email],['name'=>$nama,'password'=>$password,'role'=>'guru']);
            $g=Guru::firstOrCreate(['kode'=>$kode],[
                'user_id'=>$user->id,'nama'=>$nama,'jenis_kelamin'=>$jk,
                'jabatan'=>$jabatan,'status_pegawai'=>'Tetap','aktif'=>true,
                'telepon'=>'08XXXXXXXXXX','tanggal_mulai'=>now()->subYears(2)->toDateString()
            ]);
            Jadwal::firstOrCreate(['guru_id'=>$g->id],['jam_masuk'=>'07:00','jam_pulang'=>'14:00','toleransi_menit'=>10,'hari_kerja'=>'1,2,3,4,5']);
            KomponenGaji::firstOrCreate(['guru_id'=>$g->id],[
                'gaji_pokok'=>$pokok,'tunjangan'=>350000,'bonus'=>100000,'honor'=>150000,
                'potongan_alpa'=>50000,'potongan_terlambat'=>10000,'potongan_lain'=>25000
            ]);
            $start=CarbonImmutable::today()->startOfMonth();
            for($d=$start;$d->lessThan(CarbonImmutable::today());$d=$d->addDay()){
                if($d->dayOfWeekIso>5)continue;
                $day=(int)$d->day;
                $status=($day+$i)%13===0?'izin':(($day+$i)%17===0?'sakit':(($day+$i)%7===0?'terlambat':'hadir'));
                Absensi::firstOrCreate(['guru_id'=>$g->id,'tanggal'=>$d->toDateString()],[
                    'status'=>$status,
                    'jam_masuk'=>in_array($status,['hadir','terlambat'])?($status==='hadir'?'07:00:00':'07:18:00'):null,
                    'jam_pulang'=>in_array($status,['hadir','terlambat'])?'14:00:00':null,
                    'keterangan'=>in_array($status,['izin','sakit'])?'Data contoh untuk demo':null
                ]);
            }
        }
    }
}
