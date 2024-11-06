<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //relacion muchos a muchos con tabla subcategories
    public function subcategories(){
        
       return $this->belongsToMany(Subcategory::class)->withPivot(['id'])->withTimestamps();
    }
}
