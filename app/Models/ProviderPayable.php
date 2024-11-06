<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderPayable extends Model
{
    use HasFactory;

    protected $fillable = ['description','amount','provider_id'];


    public function provider(){

        return $this->belongsTo(Provider::class);
    }

    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
