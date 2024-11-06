<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckReceivable extends Model
{
    use HasFactory;

    protected $fillable = ['description','amount','number','bank_id','costumer_id'];
    

    public function costumer(){

        return $this->belongsTo(Costumer::class);
    }

    public function bank(){

        return $this->belongsTo(Bank::class);
    }

    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
