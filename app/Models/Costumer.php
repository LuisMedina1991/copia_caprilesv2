<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costumer extends Model
{
    use HasFactory;

    protected $fillable = ['description','phone','fax','email','nit'];

    public function debts(){

        return $this->hasMany(CostumerReceivable::class);
    }

    public function checks(){

        return $this->hasMany(CheckReceivable::class);
    }
}
