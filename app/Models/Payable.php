<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;

    protected $fillable = ['reference','description','amount'];


    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
