<?php
namespace App\Http\Controllers;
use App\Models\{Guru,Absensi,Penggajian};
use Illuminate\Http\Request;
class ReportController extends Controller {
    public function index(){return view('reports.index');}
    public function csv(Request $request,string $type){
        abort_unless(in_array($type,['guru','absensi','gaji']),404);
        $bulan=(int)$request->input('bulan',now()->month);$tahun=(int)$request->input('tahun',now()->year);
        abort_unless($bulan>=1&&$bulan<=12&&$tahun>=2020&&$tahun<=2100,422);
        $filename="laporan-{$type}-{$tahun}-{$bulan}.csv";
        return response()->streamDownload(function()use($type,$bulan,$tahun){
            $f=fopen('php://output','w');fwrite($f,"\xEF\xBB\xBF");
            $row=function(array $r)use($f){fputcsv($f,array_map(function($v){$s=(string)($v??'');return preg_match('/^[=+@\-\t\r]/u',$s)?"'".$s:$s;},$r),';');};
            if($type==='guru'){
                $row(['Kode','Nama','Email','Jenis Kelamin','Telepon','Jabatan','Status','Aktif']);
                Guru::with('user')->orderBy('nama')->chunk(100,function($gurus)use($row){foreach($gurus as $g)$row([$g->kode,$g->nama,$g->user?->email,$g->jenis_kelamin,$g->telepon,$g->jabatan,$g->status_pegawai,$g->aktif?'Ya':'Tidak']);});
            }elseif($type==='absensi'){
                $row(['Kode','Nama','Tanggal','Masuk','Pulang','Status','Keterangan']);
                Absensi::with('guru')->whereYear('tanggal',$tahun)->whereMonth('tanggal',$bulan)->orderBy('tanggal')->chunk(100,function($items)use($row){foreach($items as $a)$row([$a->guru?->kode,$a->guru?->nama,$a->tanggal->toDateString(),$a->jam_masuk,$a->jam_pulang,$a->status,$a->keterangan]);});
            }else{
                $row(['Kode','Nama','Bulan','Tahun','Hadir','Terlambat','Izin','Sakit','Alpa','Pendapatan','Potongan','Gaji Bersih','Status']);
                Penggajian::with('guru')->where('bulan',$bulan)->where('tahun',$tahun)->chunk(100,function($items)use($row){foreach($items as $p)$row([$p->guru?->kode,$p->guru?->nama,$p->bulan,$p->tahun,$p->hadir,$p->terlambat,$p->izin,$p->sakit,$p->alpa,$p->total_pendapatan,$p->total_potongan,$p->gaji_bersih,$p->status]);});
            }fclose($f);
        },$filename,['Content-Type'=>'text/csv; charset=UTF-8']);
    }
}
