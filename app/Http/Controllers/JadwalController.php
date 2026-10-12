<?php
namespace App\Http\Controllers;
use App\Models\{Guru,Jadwal};
use Illuminate\Http\Request;
class JadwalController extends Controller {
    public function index(){return view('jadwal.index',['gurus'=>Guru::with('jadwal')->orderBy('nama')->get()]);}
    public function update(Request $request,Guru $guru){
        $data=$request->validate(['jam_masuk'=>'required|date_format:H:i','jam_pulang'=>'required|date_format:H:i','toleransi_menit'=>'required|integer|min:0|max:180','hari_kerja'=>'required|array|min:1','hari_kerja.*'=>'integer|between:1,7']);
        Jadwal::updateOrCreate(['guru_id'=>$guru->id],['jam_masuk'=>$data['jam_masuk'],'jam_pulang'=>$data['jam_pulang'],'toleransi_menit'=>$data['toleransi_menit'],'hari_kerja'=>implode(',',array_unique($data['hari_kerja']))]);
        return back()->with('success','Jadwal kerja diperbarui.');
    }
}
