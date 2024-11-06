<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostumerReceivable extends Model
{
    use HasFactory;

    protected $fillable = ['description','amount','costumer_id'];


    public function costumer(){

        return $this->belongsTo(Costumer::class);
    }

    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
