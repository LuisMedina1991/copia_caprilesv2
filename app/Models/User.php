<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    protected $fillable = ['name','profile','status','phone','email','password'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //relacion uno a muchos con incomes
    public function incomes(){

        return $this->hasMany(Income::class);
    }

    //relacion uno a muchos con transfers
    public function transfers(){

        return $this->hasMany(Transfer::class);
    }

    //relacion uno a uno polimorfica
    public function image(){

        return $this->morphOne('App\Models\Image','imageable');
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
