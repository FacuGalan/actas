<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Operativo extends Model
{
    protected $connection = 'munimer_mapacalor';
    protected $table = 'operativos';
    
    protected $fillable = [
        'descripcion',
        'lugar',
        'latitud',
        'longitud',
        'fecha',
        'hora_desde',
        'hora_hasta',
        'hora_apertura_real',
        'hora_cierre_real',
        'estado',
        'departamento_id',
        'inspector_referente_id',
        'observaciones',
        'acompanamiento_policial',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Scope para operativos EN CURSO
     */
    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    /**
     * Scope para operativos PLANIFICADOS
     */
    public function scopePlanificado($query)
    {
        return $query->where('estado', 'planificado');
    }

    /**
     * Verificar si el operativo está EN CURSO
     */
    public function estaEnCurso(): bool
    {
        return $this->estado === 'en_curso';
    }

    /**
     * Verificar si el operativo está PLANIFICADO
     */
    public function estaPlanificado(): bool
    {
        return $this->estado === 'planificado';
    }

    /**
     * Verificar si un inspector está asignado (sin joins problemáticos)
     */
    public function tieneInspector($inspectorId): bool
    {
        return \DB::connection('munimer_mapacalor')
            ->table('operativo_inspector')
            ->where('operativo_id', $this->id)
            ->where('inspector_id', $inspectorId)
            ->exists();
    }

    /**
     * Verificar si un inspector es el referente
     */
    public function esInspectorReferente($inspectorId): bool
    {
        return $this->inspector_referente_id == $inspectorId;
    }

    /**
     * Iniciar el operativo (cambiar a en_curso)
     */
    public function iniciar()
    {
        $this->estado = 'en_curso';
        $this->hora_apertura_real = now()->format('H:i:s');
        $this->save();
    }

    /**
     * Finalizar el operativo
     */
    public function finalizar()
    {
        $this->estado = 'finalizado';
        $this->hora_cierre_real = now()->format('H:i:s');
        $this->save();
    }
}