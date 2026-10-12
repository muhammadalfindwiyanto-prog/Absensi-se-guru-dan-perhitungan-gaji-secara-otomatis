<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class AccountController extends Controller {
    public function index(){return view('accounts.index',['users'=>User::orderBy('name')->get()]);}
    public function store(Request $request){
        $data=$request->validate(['name'=>'required|string|max:150','email'=>'required|email|unique:users,email','password'=>'required|string|min:8','role'=>'required|in:admin,bendahara']);
        User::create($data);return back()->with('success','Akun pengelola ditambahkan.');
    }
    public function update(Request $request,User $user){
        abort_unless(in_array($user->role,['admin','bendahara']),403);
        $data=$request->validate(['name'=>'required|string|max:150','email'=>['required','email',Rule::unique('users')->ignore($user->id)],'password'=>'nullable|string|min:8','role'=>'required|in:admin,bendahara']);
        if($user->id===$request->user()->id&&$data['role']!=='admin')return back()->with('error','Anda tidak boleh melepas hak admin dari akun sendiri.');
        if(empty($data['password']))unset($data['password']);
        $user->update($data);return back()->with('success','Akun diperbarui.');
    }
}
