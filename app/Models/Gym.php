<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory;

    protected $fillable = ['description','amount'];

    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
