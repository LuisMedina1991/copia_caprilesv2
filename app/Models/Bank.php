<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    //relacion uno a muchos con cheks
    public function checks(){

        return $this->hasMany(CheckReceivable::class);
    }

    //relacion uno a muchos con bank_accounts
    public function accounts(){

        return $this->hasMany(BankAccount::class);
    }

}
