<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Guru extends Model {
    protected $guarded=[];
    protected $casts=['aktif'=>'boolean','tanggal_mulai'=>'date'];
    public function user(){return $this->belongsTo(User::class);}
    public function jadwal(){return $this->hasOne(Jadwal::class);}
    public function absensis(){return $this->hasMany(Absensi::class);}
    public function komponen(){return $this->hasOne(KomponenGaji::class);}
    public function penggajians(){return $this->hasMany(Penggajian::class);}
}
