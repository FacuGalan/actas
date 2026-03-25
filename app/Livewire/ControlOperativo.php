<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operativo;
use App\Models\Persona;
use App\Models\RegistroControl;
use Illuminate\Support\Facades\DB;

class ControlOperativo extends Component
{
    public $operativoId;
    public $operativo;
    
    // Campos del formulario de registro
    public $dni;
    public $nombreinf;
    public $dominio;
    public $observaciones;
    public $mostrarFormulario = false;

    public function mount($operativo_id)
    {
        $this->operativoId = $operativo_id;
        $this->operativo = Operativo::find($operativo_id);
        
        // Verificar que el operativo existe y está activo
        if (!$this->operativo || !$this->operativo->estaEnCurso()) {
            session()->flash('error', 'El operativo no está disponible.');
            return redirect()->route('actas.dashboard');
        }
        
        // Verificar que el inspector está asignado
        $asignado = DB::connection('munimer_mapacalor')
            ->table('operativo_inspector')
            ->where('operativo_id', $operativo_id)
            ->where('inspector_id', auth('inspector')->id())
            ->exists();
        
        if (!$asignado) {
            session()->flash('error', 'No estás asignado a este operativo.');
            return redirect()->route('actas.dashboard');
        }
    }

    public function updatedDni($value)
    {
        if (strlen($value) >= 7) {
            $persona = Persona::where('dni', $value)->first();
            
            if ($persona) {
                $this->nombreinf = $persona->nombre;
            }
        }
    }

    public function registrarControl()
    {
        $this->validate([
            'dni' => 'nullable|numeric',
            'nombreinf' => 'nullable|string|max:255',
            'dominio' => 'nullable|string|max:20',
        ]);

        RegistroControl::create([
            'operativo_id' => $this->operativoId,
            'inspector_id' => auth('inspector')->id(),
            'fecha' => now()->format('Y-m-d'),
            'hora' => now()->format('H:i:s'),
            'dni' => $this->dni ?: null,
            'nombreinf' => $this->nombreinf ?: null,
            'dominio' => $this->dominio ?: null,
            'observaciones' => $this->observaciones ?: null,
            'crea_user' => auth('inspector')->user()->dni,
            'crea_fecha' => now()->format('Y-m-d'),
        ]);

        // Limpiar formulario
        $this->reset(['dni', 'nombreinf', 'dominio', 'observaciones']);
        // Ocultar formulario
        $this->mostrarFormulario = false;
        
        session()->flash('message', 'Control registrado exitosamente.');
        
        // Scroll to top
        $this->dispatch('scroll-to-top');
    }

    public function toggleFormulario()
    {
        $this->mostrarFormulario = !$this->mostrarFormulario;
    }

    public function render()
    {
        return view('livewire.control-operativo')->layout('components.layouts.app');
    }

}