<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KomponenGaji extends Model {protected $table='komponengajis';protected $guarded=[];public function guru(){return $this->belongsTo(Guru::class);}}
