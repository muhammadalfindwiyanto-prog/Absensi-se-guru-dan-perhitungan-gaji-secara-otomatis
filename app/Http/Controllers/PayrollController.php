<?php
namespace App\Http\Controllers;
use App\Models\{Guru,KomponenGaji,Penggajian};
use App\Services\RekapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PayrollController extends Controller {
    public function komponen(){return view('payroll.komponen',['gurus'=>Guru::with('komponen')->orderBy('nama')->get()]);}
    public function saveKomponen(Request $request,Guru $guru){
        $keys=['gaji_pokok','tunjangan','bonus','honor','potongan_lain','potongan_alpa','potongan_terlambat'];
        $rules=[];foreach($keys as $key)$rules[$key]='required|integer|min:0|max:100000000000';
        $data=$request->validate($rules);
        KomponenGaji::updateOrCreate(['guru_id'=>$guru->id],$data);
        return back()->with('success','Komponen gaji '.$guru->nama.' diperbarui.');
    }
    public function index(Request $request){
        $bulan=(int)$request->input('bulan',now()->month);$tahun=(int)$request->input('tahun',now()->year);
        abort_unless($bulan>=1&&$bulan<=12&&$tahun>=2020&&$tahun<=2100,422);
        $query=Penggajian::with('guru')->where('bulan',$bulan)->where('tahun',$tahun)->orderBy('guru_id');
        if($request->user()->role==='guru'){
            abort_unless($request->user()->guru,403);
            $query->where('guru_id',$request->user()->guru->id)->where('status','disahkan');
        }
        $records=$query->get();
        $gurus=Guru::where('aktif',true)->orderBy('nama')->get();
        return view('payroll.index',compact('records','gurus','bulan','tahun'));
    }
    public function hitung(Request $request,RekapService $rekap){
        $data=$request->validate(['bulan'=>'required|integer|between:1,12','tahun'=>'required|integer|between:2020,2100']);
        $bulan=(int)$data['bulan'];$tahun=(int)$data['tahun'];
        if($tahun>now()->year||($tahun===now()->year&&$bulan>now()->month))return back()->with('error','Periode mendatang belum dapat dihitung.');
        $count=0;
        DB::transaction(function()use($bulan,$tahun,$rekap,&$count){
            foreach(Guru::with(['jadwal','komponen'])->where('aktif',true)->get() as $guru){
                if ($guru->tanggal_mulai && $guru->tanggal_mulai->startOfDay()->greaterThan(\Carbon\Carbon::create($tahun,$bulan,1)->endOfMonth())) continue;
                $previous=Penggajian::where('guru_id',$guru->id)->where('bulan',$bulan)->where('tahun',$tahun)->first();
                if($previous?->status==='disahkan')continue;
                $r=$rekap->hitung($guru,$bulan,$tahun);$k=$guru->komponen;
                if(!$k)continue;
                $pendapatan=(int)$k->gaji_pokok+(int)$k->tunjangan+(int)$k->bonus+(int)$k->honor;
                $potonganKehadiran=(int)$r['alpa']*(int)$k->potongan_alpa+(int)$r['terlambat']*(int)$k->potongan_terlambat;
                $potongan=$potonganKehadiran+(int)$k->potongan_lain;
                Penggajian::updateOrCreate(['guru_id'=>$guru->id,'bulan'=>$bulan,'tahun'=>$tahun],[
                    ...collect($r)->only(['hadir','terlambat','izin','sakit','alpa'])->all(),
                    'gaji_pokok'=>$k->gaji_pokok,'tunjangan'=>$k->tunjangan,'bonus'=>$k->bonus,'honor'=>$k->honor,
                    'potongan_kehadiran'=>$potonganKehadiran,'potongan_lain'=>$k->potongan_lain,
                    'total_pendapatan'=>$pendapatan,'total_potongan'=>$potongan,
                    'gaji_bersih'=>max(0,$pendapatan-$potongan),'status'=>'draft','disahkan_pada'=>null]);
                $count++;
            }
        });
        return redirect()->route('payroll.index',['bulan'=>$bulan,'tahun'=>$tahun])->with('success',"Berhasil menghitung {$count} draft gaji. Data yang sudah disahkan tidak diubah.");
    }
    public function sahkan(Penggajian $record){
        abort_unless($record->status==='draft',422,'Slip sudah disahkan.');
        $record->update(['status'=>'disahkan','disahkan_pada'=>now()]);
        return back()->with('success','Penggajian disahkan. Slip sekarang terlihat oleh guru.');
    }
    public function slip(Request $request,Penggajian $record){
        if($request->user()->role==='guru')abort_unless($record->guru?->user_id===$request->user()->id&&$record->status==='disahkan',403);
        return view('payroll.slip',['record'=>$record->load('guru')]);
    }
}
