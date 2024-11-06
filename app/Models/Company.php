<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['description','nit','type','category','address'];


    //relacion uno a muchos con bank_accounts
    public function accounts(){

        return $this->hasMany(BankAccount::class);
    }

}
