<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = ['name','address','phone'];
    

    //relacion muchos a muchos con tabla products
    public function products(){

       return $this->belongsToMany(Product::class)->withPivot(['id','stock','alerts'])->withTimestamps();
    }

}
