<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'fa_persona';
    
    public $timestamps = false;
    
    protected $fillable = [
        'dni',
        'nombre',
        'direccion',
    ];
}
