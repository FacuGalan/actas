<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    protected $table = 'fa_motivo';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'tipo',
        'ley',
        'articulo',
        'inciso',
        'punto',
        'dto_id',
    ];

    /**
     * Scope para buscar motivos (mínimo 3 caracteres)
     */
    public function scopeBuscar($query, $busqueda)
    {
        if (strlen($busqueda) < 3) {
            return $query->whereRaw('1 = 0'); // No devolver nada si es menos de 3 caracteres
        }
        
        return $query->where(function($q) use ($busqueda) {
            $q->where('nombre', 'like', '%' . $busqueda . '%')
              ->orWhere('ley', 'like', '%' . $busqueda . '%')
              ->orWhere('articulo', 'like', '%' . $busqueda . '%');
        });
    }
}