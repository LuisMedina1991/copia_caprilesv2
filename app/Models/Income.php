<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = ['quantity','total','office','pf','type','relation','state_id','user_id','product_id','created_at','updated_at'];

    //relacion muchos a uno con states
    public function state(){

        return $this->belongsTo(State::class);
    }

    //relacion muchos a uno con users
    public function user(){

        return $this->belongsTo(User::class);
    }

    //relacion muchos a uno con products
    public function product(){

        return $this->belongsTo(Product::class);
    }
}
