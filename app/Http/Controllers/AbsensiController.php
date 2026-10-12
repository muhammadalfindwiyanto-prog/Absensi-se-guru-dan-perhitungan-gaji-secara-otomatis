<?php
namespace App\Http\Controllers;
use App\Models\{Absensi,Guru};
use App\Services\RekapService;
use Illuminate\Http\Request;
use Carbon\Carbon;
class AbsensiController extends Controller {
    public function index(Request $request){
        $query=Absensi::with('guru')->orderByDesc('tanggal')->orderByDesc('id');
        if($request->user()->role==='guru'){
            $guru=$request->user()->guru;abort_unless($guru,403,'Akun belum terhubung dengan data guru.');
            $query->where('guru_id',$guru->id);
        }else if($request->filled('guru_id')){$query->where('guru_id',$request->integer('guru_id'));}
        if($request->filled('bulan') && preg_match('/^\d{4}-\d{2}$/',$request->bulan))$query->whereYear('tanggal',substr($request->bulan,0,4))->whereMonth('tanggal',substr($request->bulan,5,2));
        return view('absensi.index',['records'=>$query->paginate(15)->withQueryString(),'gurus'=>Guru::orderBy('nama')->get()]);
    }
    public function masuk(Request $request){
        $guru=$request->user()->guru;abort_unless($guru && $guru->aktif,403,'Akun guru tidak aktif.');
        $now=now();$tanggal=$now->toDateString();$jam=$now->format('H:i:s');
        if(Absensi::where('guru_id',$guru->id)->whereDate('tanggal',$tanggal)->exists())return back()->with('error','Absensi hari ini sudah tercatat.');
        $jadwal=$guru->jadwal;
        $batas=Carbon::parse($tanggal.' '.($jadwal?->jam_masuk ?? '07:00:00'))->addMinutes($jadwal?->toleransi_menit??0);
        Absensi::create(['guru_id'=>$guru->id,'tanggal'=>$tanggal,'jam_masuk'=>$jam,'status'=>$now->greaterThan($batas)?'terlambat':'hadir']);
        return back()->with('success','Absensi masuk berhasil direkam.');
    }
    public function pulang(Request $request){
        $guru=$request->user()->guru;abort_unless($guru && $guru->aktif,403);
        $absensi=Absensi::where('guru_id',$guru->id)->whereDate('tanggal',today())->first();
        if(!$absensi || !$absensi->jam_masuk || $absensi->jam_pulang)return back()->with('error','Absensi masuk belum ada, atau absensi pulang sudah tercatat.');
        $absensi->update(['jam_pulang'=>now()->format('H:i:s')]);
        return back()->with('success','Absensi pulang berhasil direkam.');
    }
    public function create(){return view('absensi.form',['record'=>new Absensi,'gurus'=>Guru::orderBy('nama')->get()]);}
    private function rules(?Absensi $record=null):array{return [
        'guru_id'=>'required|exists:gurus,id','tanggal'=>'required|date|before_or_equal:today',
        'status'=>'required|in:hadir,terlambat,izin,sakit,alpa',
        'jam_masuk'=>'nullable|date_format:H:i','jam_pulang'=>'nullable|date_format:H:i',
        'keterangan'=>'nullable|string|max:500'];}
    public function store(Request $request){
        $data=$request->validate($this->rules());
        if(Absensi::where('guru_id',$data['guru_id'])->whereDate('tanggal',$data['tanggal'])->exists())return back()->withInput()->with('error','Guru tersebut sudah memiliki absensi pada tanggal ini.');
        Absensi::create($this->data($data));return redirect()->route('absensi.index')->with('success','Absensi ditambahkan.');
    }
    public function edit(Absensi $record){return view('absensi.form',['record'=>$record,'gurus'=>Guru::orderBy('nama')->get()]);}
    public function update(Request $request, Absensi $record){
        $data=$request->validate($this->rules($record));
        if(Absensi::where('guru_id',$data['guru_id'])->whereDate('tanggal',$data['tanggal'])->where('id','!=',$record->id)->exists())return back()->withInput()->with('error','Duplikasi guru dan tanggal.');
        $record->update($this->data($data));return redirect()->route('absensi.index')->with('success','Absensi diperbaiki. Penggajian draft dapat dihitung ulang.');
    }
    private function data(array $data):array{
        if(in_array($data['status'],['izin','sakit','alpa'])){$data['jam_masuk']=null;$data['jam_pulang']=null;}
        if(empty($data['jam_masuk']))$data['jam_pulang']=null;
        return $data;
    }
    public function rekap(Request $request,RekapService $rekap){
        $bulan=(int)$request->input('bulan',now()->month);$tahun=(int)$request->input('tahun',now()->year);
        abort_unless($bulan>=1&&$bulan<=12&&$tahun>=2020&&$tahun<=2100,422);
        $gurus=Guru::with('jadwal')->orderBy('nama')->get();
        if($request->user()->role==='guru')$gurus=$gurus->where('user_id',$request->user()->id);
        $rows=$gurus->map(fn($guru)=>['guru'=>$guru,'rekap'=>$rekap->hitung($guru,$bulan,$tahun)]);
        return view('absensi.rekap',compact('rows','bulan','tahun'));
    }
}
