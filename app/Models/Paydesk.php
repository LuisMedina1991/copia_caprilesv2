<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paydesk extends Model
{
    use HasFactory;

    protected $fillable = ['description','action','type','relation','amount','created_at','updated_at'];
}
