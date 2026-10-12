<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Absensi extends Model {protected $guarded=[];protected $casts=['tanggal'=>'date'];public function guru(){return $this->belongsTo(Guru::class);}}
