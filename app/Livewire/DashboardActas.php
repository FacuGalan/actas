<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operativo;
use App\Models\Inspector;
use Illuminate\Support\Facades\DB;

class DashboardActas extends Component
{
    public $operativosEnCurso = [];
    public $operativosPlanificados = [];

    // Modal de inicio de operativo
    public $mostrarModalInicio = false;
    public $operativoIniciar = null;
    public $inspectoresAsignados = [];
    public $acompanamiento_policial = '';

    protected $listeners = ['confirmarFinalizacion'];

    public function mount()
    {
        $inspectorId = auth('inspector')->id();

        // IDs de todos los operativos donde el inspector está en la tabla pivot
        $operativoIdsAsignados = DB::connection('munimer_mapacalor')
            ->table('operativo_inspector')
            ->where('inspector_id', $inspectorId)
            ->pluck('operativo_id');

        // === OPERATIVOS EN CURSO ===

        $enCursoReferente = Operativo::enCurso()
            ->where('inspector_referente_id', $inspectorId)
            ->get();

        $enCursoAsignado = collect();
        if ($operativoIdsAsignados->isNotEmpty()) {
            $idsReferente = $enCursoReferente->pluck('id');
            $enCursoAsignado = Operativo::enCurso()
                ->whereIn('id', $operativoIdsAsignados)
                ->whereNotIn('id', $idsReferente)
                ->get();
        }

        $this->operativosEnCurso = [];
        foreach ($enCursoReferente as $op) {
            $this->operativosEnCurso[] = [
                'id'                 => $op->id,
                'descripcion'        => $op->descripcion,
                'lugar'              => $op->lugar,
                'hora_apertura_real' => $op->hora_apertura_real,
                'es_referente'       => true,
            ];
        }
        foreach ($enCursoAsignado as $op) {
            $this->operativosEnCurso[] = [
                'id'                 => $op->id,
                'descripcion'        => $op->descripcion,
                'lugar'              => $op->lugar,
                'hora_apertura_real' => $op->hora_apertura_real,
                'es_referente'       => false,
            ];
        }

        // === OPERATIVOS PLANIFICADOS ===

        $planificadosReferente = Operativo::planificado()
            ->where('inspector_referente_id', $inspectorId)
            ->get();

        $planificadosAsignado = collect();
        if ($operativoIdsAsignados->isNotEmpty()) {
            $idsReferente = $planificadosReferente->pluck('id');
            $planificadosAsignado = Operativo::planificado()
                ->whereIn('id', $operativoIdsAsignados)
                ->whereNotIn('id', $idsReferente)
                ->get();
        }

        $this->operativosPlanificados = [];
        foreach ($planificadosReferente as $op) {
            $this->operativosPlanificados[] = [
                'id'          => $op->id,
                'descripcion' => $op->descripcion,
                'lugar'       => $op->lugar,
                'fecha'       => $op->fecha->format('d/m/Y'),
                'hora_desde'  => $op->hora_desde,
                'hora_hasta'  => $op->hora_hasta,
                'es_referente' => true,
            ];
        }
        foreach ($planificadosAsignado as $op) {
            $this->operativosPlanificados[] = [
                'id'          => $op->id,
                'descripcion' => $op->descripcion,
                'lugar'       => $op->lugar,
                'fecha'       => $op->fecha->format('d/m/Y'),
                'hora_desde'  => $op->hora_desde,
                'hora_hasta'  => $op->hora_hasta,
                'es_referente' => false,
            ];
        }
    }

    public function abrirModalInicio($operativoId)
    {
        $this->operativoIniciar = Operativo::find($operativoId);

        if (!$this->operativoIniciar || !$this->operativoIniciar->esInspectorReferente(auth('inspector')->id())) {
            session()->flash('error', 'No tenés permisos para iniciar este operativo.');
            return;
        }

        $asignaciones = DB::connection('munimer_mapacalor')
            ->table('operativo_inspector')
            ->where('operativo_id', $operativoId)
            ->get();

        $this->inspectoresAsignados = [];
        foreach ($asignaciones as $asignacion) {
            $inspector = Inspector::find($asignacion->inspector_id);
            if ($inspector) {
                $this->inspectoresAsignados[] = [
                    'id'          => $inspector->id,
                    'nombre'      => $inspector->nombre . ' ' . $inspector->apellido,
                    'dni'         => $inspector->dni,
                    'estado'      => 'presente',
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
            if ($estado === 'presente') {
                $this->inspectoresAsignados[$index]['observacion'] = '';
            }
        }
    }

    public function confirmarInicioOperativo()
    {
        foreach ($this->inspectoresAsignados as $inspector) {
            if ($inspector['estado'] === 'ausente' && empty($inspector['observacion'])) {
                session()->flash('error', 'Completá la observación para los inspectores ausentes.');
                return;
            }
        }

        foreach ($this->inspectoresAsignados as $inspector) {
            DB::connection('munimer_mapacalor')
                ->table('operativo_inspector')
                ->where('operativo_id', $this->operativoIniciar->id)
                ->where('inspector_id', $inspector['id'])
                ->update([
                    'estado'      => $inspector['estado'],
                    'observacion' => $inspector['observacion'] ?: null,
                ]);
        }

        $this->operativoIniciar->acompanamiento_policial = $this->acompanamiento_policial ?: null;
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
