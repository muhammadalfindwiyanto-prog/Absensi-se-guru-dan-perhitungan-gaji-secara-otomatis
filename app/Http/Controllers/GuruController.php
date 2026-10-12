<?php
namespace App\Http\Controllers;
use App\Models\{Guru,User,Jadwal,KomponenGaji};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class GuruController extends Controller {
    public function index(){return view('gurus.index',['gurus'=>Guru::with(['user','jadwal'])->orderBy('nama')->paginate(12)]);}
    public function create(){return view('gurus.form',['guru'=>new Guru]);}
    private function rules(?Guru $guru=null):array{return [
        'kode'=>['required','max:30',Rule::unique('gurus','kode')->ignore($guru?->id)],
        'nama'=>'required|string|max:150','email'=>['required','email','max:200',Rule::unique('users','email')->ignore($guru?->user_id)],
        'password'=>$guru?'nullable|string|min:8':'required|string|min:8',
        'jenis_kelamin'=>'required|in:L,P','telepon'=>'nullable|string|max:25','alamat'=>'nullable|string',
        'jabatan'=>'required|string|max:100','status_pegawai'=>'required|string|max:60',
        'tanggal_mulai'=>'nullable|date','aktif'=>'required|boolean'
    ];}
    public function store(Request $request){
        $data=$request->validate($this->rules());
        DB::transaction(function()use($data){
            $user=User::create(['name'=>$data['nama'],'email'=>$data['email'],'password'=>$data['password'],'role'=>'guru']);
            $guru=Guru::create($this->guruValues($data)+['user_id'=>$user->id]);
            Jadwal::create(['guru_id'=>$guru->id]);KomponenGaji::create(['guru_id'=>$guru->id]);
        });
        return redirect()->route('gurus.index')->with('success','Data guru dan akun login berhasil ditambahkan.');
    }
    public function edit(Guru $guru){$guru->load('user');return view('gurus.form',compact('guru'));}
    public function update(Request $request,Guru $guru){
        $data=$request->validate($this->rules($guru));
        DB::transaction(function()use($data,$guru){
            $guru->update($this->guruValues($data));
            $update=['name'=>$data['nama'],'email'=>$data['email']];
            if(!empty($data['password']))$update['password']=$data['password'];
            $guru->user?->update($update);
        });
        return redirect()->route('gurus.index')->with('success','Data guru diperbarui.');
    }
    private function guruValues(array $data):array{return collect($data)->only(['kode','nama','jenis_kelamin','telepon','alamat','jabatan','status_pegawai','tanggal_mulai','aktif'])->all();}
    public function toggle(Guru $guru){$guru->update(['aktif'=>!$guru->aktif]);return back()->with('success','Status guru berhasil diubah.');}
}
