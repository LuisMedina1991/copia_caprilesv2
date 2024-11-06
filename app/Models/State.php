<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['name','type'];

    //relacion uno a muchos con incomes
    public function incomes(){

        return $this->hasMany(Income::class);
    }

    //relacion uno a muchos con transfers
    public function transfers(){

        return $this->hasMany(Transfer::class);
    }

    //relacion uno a muchos con sales
    public function sales(){

        return $this->hasMany(Sale::class);
    }

    //relacion uno a muchos con products
    public function products(){

        return $this->hasMany(Product::class);
    }
}
