<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Penggajian extends Model {protected $guarded=[];protected $casts=['disahkan_pada'=>'datetime'];public function guru(){return $this->belongsTo(Guru::class);}}
