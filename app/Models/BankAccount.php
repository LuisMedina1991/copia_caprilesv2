<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = ['type','currency','amount','bank_id','company_id'];


    //relacion uno a muchos inversa con banks
    public function bank(){

        return $this->belongsTo(Bank::class);
    }

    //relacion uno a muchos inversa con companies
    public function company(){

        return $this->belongsTo(Company::class);
    }

    //relacion uno a muchos polimorfica
    public function details(){

        return $this->morphMany('App\Models\Detail','detailable');
    }
}
