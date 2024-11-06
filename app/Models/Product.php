<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['description','brand','ring','threshing','tarp','code','cost','price','category_subcategory_id','state_id'];


    //relacion muchos a uno con states
    public function state(){

        return $this->belongsTo(State::class);
    }

    //relacion muchos a muchos con offices
    public function offices(){

        return $this->belongsToMany(Office::class)->withPivot(['id','stock','alerts'])->withTimestamps();
    }

    //relacion uno a uno polimorfica con images
    public function image(){

        return $this->morphOne('App\Models\Image','imageable');
    }

    //relacion uno a muchos con sales
    public function sales(){

        return $this->hasMany(Sale::class);
    }

    //relacion uno a muchos con incomes
    public function incomes(){

        return $this->hasMany(Income::class);
    }

    //relacion uno a muchos con transfers
    public function transfers(){

        return $this->hasMany(Transfer::class);
    }

    //nombre del accesor es imagen
    public function getImagenAttribute(){   //metodo accesor para mostrar imagen por defecto en caso de no registrarle ninguna
        
        if($this->image == null)    //validar si la columna image no tiene nada registrado en la base de datos
            return '../noimg.jpg'; //retornar imagen por defecto

        if(file_exists('storage/products/' . $this->image)) //validar si archivo existe fisicarmente en el almacenamiento interno
            return $this->image;    //retornar imagen registrada en la base de datos
        else
            return '../noimg.jpg'; //caso contrario retornar imagen por defecto
    }

}
