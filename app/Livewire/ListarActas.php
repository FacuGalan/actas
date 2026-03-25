<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Acta;

class ListarActas extends Component
{
    use WithPagination;

    protected $listeners = ['confirmarEliminacion'];

    public function confirmarEliminacion($actaId)
    {
        $acta = Acta::find($actaId);
        
        if ($acta && $acta->inspector_id == auth('inspector')->id()) {
            $acta->delete();
            session()->flash('message', 'Acta eliminada exitosamente.');
            $this->dispatch('actaEliminada');
        }
    }

    public function render()
    {
        $actas = Acta::where('inspector_id', auth('inspector')->id())
            ->where('estado', '<', 3)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(10);

        return view('livewire.listar-actas', [
            'actas' => $actas,
        ])->layout('components.layouts.app');
    }
}