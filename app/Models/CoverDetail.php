<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverDetail extends Model
{
    use HasFactory;

    protected $fillable = ['cover_id','type','previus_day_balance','ingress','egress','actual_balance','created_at','updated_at'];


    //relacion muchos a uno con covers
    public function cover(){

        return $this->belongsTo(Cover::class);
    }
}
