<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroControl extends Model
{
    protected $table = 'fa_registro_control';
    
    protected $fillable = [
        'operativo_id',
        'inspector_id',
        'fecha',
        'hora',
        'dni',
        'nombreinf',
        'dominio',
        'observaciones',
        'crea_user',
        'crea_fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function operativo()
    {
        return $this->belongsTo(Operativo::class, 'operativo_id');
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }
}
