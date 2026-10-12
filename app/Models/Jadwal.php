<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Jadwal extends Model {protected $guarded=[];public function guru(){return $this->belongsTo(Guru::class);}}
