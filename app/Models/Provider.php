<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ['description','phone','fax','email','country','city'];


    public function payables(){

        return $this->hasMany(ProviderPayable::class);
    }
}
