<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //relacion muchos a muchos con tabla categories
    public function categories(){
        
       return $this->belongsToMany(Category::class)->withPivot(['id'])->withTimestamps();
    }
}
