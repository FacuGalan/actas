<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acta extends Model
{
    protected $table = 'fa_acta';
    
    public $timestamps = false;

    // Valor de operativo_id para las actas simples (fuera de operativo) creadas desde este sistema.
    // Permite distinguirlas de las actas cargadas por otros medios, que tienen operativo_id NULL.
    public const OPERATIVO_ACTA_SIMPLE = -1;

    protected $fillable = [
        'actanro',
        'dto_id',
        'inspector_id',
        'operativo_id',
        'fecha',
        'hora',
        'dni',
        'nombreinf',
        'direcinf',
        'dniden',
        'nombreden',
        'direcden',
        'lugarinfra',
        'obs',
        'dominio',
        'licencia',
        'secuestro',
        'arenavese',
        'brenavese',
        'decomiso',
        'retiene_lic',
        'clausura',
        'marca_id',
        'tipo_id',
        'modelo',
        'chasis',
        'motor',
        'motivobaja_id',
        'estado',
        'preacta_id',
        'marca',
        'crea_user',
        'crea_fecha',
        'modif_user',
        'modif_fecha',
        'audi',
        'detallada',
        'grad_alcohol'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Relación con el inspector
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }

    /**
     * Si el acta pertenece a un operativo real (las simples tienen NULL o -1)
     */
    public function esDeOperativo(): bool
    {
        return $this->operativo_id > 0;
    }

    /**
     * Scope para actas pendientes
     * Estado < 3 (estados 1 y 2 son editables)
     * Del último mes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', '<', 3)
                     ->where('fecha', '>=', now()->subMonth());
    }
}