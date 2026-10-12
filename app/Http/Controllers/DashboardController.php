<?php
namespace App\Http\Controllers;
use App\Models\{Guru,Absensi,Penggajian};
use App\Services\RekapService;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller {
    public function index(RekapService $rekap){
        $user=Auth::user(); $guru=$user->guru;
        $today=now()->toDateString();
        $stat=[
            'total'=>Guru::where('aktif',true)->count(),
            'hadir'=>Absensi::whereDate('tanggal',$today)->whereIn('status',['hadir','terlambat'])->count(),
            'terlambat'=>Absensi::whereDate('tanggal',$today)->where('status','terlambat')->count(),
            'izin'=>Absensi::whereDate('tanggal',$today)->where('status','izin')->count(),
            'sakit'=>Absensi::whereDate('tanggal',$today)->where('status','sakit')->count(),
            'alpa'=>Absensi::whereDate('tanggal',$today)->where('status','alpa')->count(),
        ];
        $todayRecord=$guru?->absensis()->whereDate('tanggal',$today)->first();
        $recent=$guru?->absensis()->orderByDesc('tanggal')->limit(7)->get()??collect();
        $slip=$guru?->penggajians()->where('status','disahkan')->orderByDesc('tahun')->orderByDesc('bulan')->first();
        $monthly=$guru?$rekap->hitung($guru,(int)now()->month,(int)now()->year):null;
        $payrollTotal=Penggajian::where('bulan',now()->month)->where('tahun',now()->year)->where('status','disahkan')->sum('gaji_bersih');
        return view('dashboard',compact('stat','guru','todayRecord','recent','slip','monthly','payrollTotal'));
    }
}
