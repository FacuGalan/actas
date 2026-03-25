<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operativo;
use App\Models\Inspector;
use Illuminate\Support\Facades\DB;

class DashboardActas extends Component
{
    public $operativoEnCurso = null;
    public $operativoPlanificado = null;
    public $esReferente = false;
    
    // Modal de inicio de operativo
    public $mostrarModalInicio = false;
    public $operativoIniciar = null;
    public $inspectoresAsignados = [];
    public $acompanamiento_policial = '';

    protected $listeners = ['confirmarFinalizacion'];

    public function mount()
    {
        $inspectorId = auth('inspector')->id();
        
        // Buscar operativo EN CURSO
        $operativoEnCurso = Operativo::enCurso()->first();
        
        if ($operativoEnCurso) {
            // Si el inspector está asignado O es el referente, puede verlo
            if ($operativoEnCurso->tieneInspector($inspectorId) || $operativoEnCurso->esInspectorReferente($inspectorId)) {
                $this->operativoEnCurso = $operativoEnCurso;
                
                // Marcar si es referente para mostrar botón de finalizar
                if ($operativoEnCurso->esInspectorReferente($inspectorId)) {
                    $this->esReferente = true;
                }
            }
        }
        
        // Buscar operativo PLANIFICADO donde el inspector es el referente
        $operativoPlanificado = Operativo::planificado()
            ->where('inspector_referente_id', $inspectorId)
            ->first();
        
        if ($operativoPlanificado) {
            $this->operativoPlanificado = $operativoPlanificado;
        }
    }

    public function abrirModalInicio($operativoId)
    {
        $this->operativoIniciar = Operativo::find($operativoId);
        
        if (!$this->operativoIniciar || !$this->operativoIniciar->esInspectorReferente(auth('inspector')->id())) {
            session()->flash('error', 'No tenés permisos para iniciar este operativo.');
            return;
        }
        
        // Cargar inspectores asignados
        $asignaciones = DB::connection('munimer_mapacalor')
            ->table('operativo_inspector')
            ->where('operativo_id', $operativoId)
            ->get();
        
        $this->inspectoresAsignados = [];
        
        foreach ($asignaciones as $asignacion) {
            $inspector = Inspector::find($asignacion->inspector_id);
            
            if ($inspector) {
                $this->inspectoresAsignados[] = [
                    'id' => $inspector->id,
                    'nombre' => $inspector->nombre . ' ' . $inspector->apellido,
                    'dni' => $inspector->dni,
                    'estado' => 'presente',
                    'observacion' => '',
                ];
            }
        }
        
        $this->mostrarModalInicio = true;
    }

    public function actualizarEstadoInspector($index, $estado)
    {
        if (isset($this->inspectoresAsignados[$index])) {
            $this->inspectoresAsignados[$index]['estado'] = $estado;
            
            // Limpiar observación si vuelve a presente
            if ($estado === 'presente') {
                $this->inspectoresAsignados[$index]['observacion'] = '';
            }
        }
    }

    public function confirmarInicioOperativo()
    {
        // Validar que los ausentes tengan observación
        foreach ($this->inspectoresAsignados as $inspector) {
            if ($inspector['estado'] === 'ausente' && empty($inspector['observacion'])) {
                session()->flash('error', 'Completá la observación para los inspectores ausentes.');
                return;
            }
        }
        
        // Actualizar estados en operativo_inspector
        foreach ($this->inspectoresAsignados as $inspector) {
            DB::connection('munimer_mapacalor')
                ->table('operativo_inspector')
                ->where('operativo_id', $this->operativoIniciar->id)
                ->where('inspector_id', $inspector['id'])
                ->update([
                    'estado' => $inspector['estado'],
                    'observacion' => $inspector['observacion'] ?: null,
                ]);
        }
        
        // Actualizar acompañamiento policial
        $this->operativoIniciar->acompanamiento_policial = $this->acompanamiento_policial ?: null;
        
        // Iniciar el operativo
        $this->operativoIniciar->iniciar();
        
        $this->mostrarModalInicio = false;
        session()->flash('message', 'Operativo iniciado exitosamente.');
        
        return redirect()->route('actas.dashboard');
    }

    public function cerrarModalInicio()
    {
        $this->mostrarModalInicio = false;
        $this->reset(['operativoIniciar', 'inspectoresAsignados', 'acompanamiento_policial']);
    }

    public function confirmarFinalizacion($operativoId)
    {
        $operativo = Operativo::find($operativoId);
        
        if ($operativo && $operativo->esInspectorReferente(auth('inspector')->id())) {
            $operativo->finalizar();
            session()->flash('message', 'Operativo finalizado exitosamente.');
            $this->dispatch('operativoFinalizado');
            return redirect()->route('actas.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.dashboard-actas');
    }
}