<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Acta;
use App\Models\Operativo;
use App\Models\Motivo;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class CrearActa extends Component
{
    use WithFileUploads;

    // Indica si es un acta de operativo
    public $esOperativo = false;
    public $operativoId = null;
    public $operativo = null;
    public $operativo_nro = null;

    // Campos principales del acta
    public $actanro;
    public $dto_id;
    public $fecha;
    public $hora;
    public $lugarinfra;
    
    // Checkboxes del encabezado
    public $decomiso = false;
    public $clausura = false;
    
    // Datos del vehículo
    public $dominio;
    public $licencia;
    public $tipo_id;
    public $marca_id;
    public $modelo;
    public $motor;
    public $chasis;
    
    // Datos del infractor
    public $dni;
    public $nombreinf;
    public $direcinf;
    public $historialInfractor = [];
    public $mostrarTodoHistorial = false;
    
    // Secuestro y retención
    public $secuestro = false;
    public $arenavese;
    public $brenavese;
    public $retiene_lic = false;
    public $grad_alcohol;
    
    // Observaciones
    public $observa;
    
    // Fotos
    public $foto1;
    public $foto2;
    public $foto3;
    public $foto4;
    public $foto5;
    
    // Motivos seleccionados (temporal)
    public $motivosSeleccionados = [];
    public $mostrarModalMotivos = false;
    public $busquedaMotivo = '';
    public $motivosEncontrados = [];

    public function mount($operativo_id = null)
    {
        // Si viene operativo_id, es acta de operativo
        if ($operativo_id) {
            $this->esOperativo = true;
            $this->operativoId = $operativo_id;
            $this->operativo_nro = $operativo_id;
            
            // Cargar el operativo
            $this->operativo = Operativo::find($operativo_id);
            
            // Verificar que el operativo existe y está activo
            if (!$this->operativo || !$this->operativo->estaEnCurso()) {
                session()->flash('error', 'El operativo no está disponible.');
                return redirect()->route('actas.dashboard');
            }
            
            // Verificar que el inspector está asignado o es el referente
            $inspectorId = auth('inspector')->id();
            if (!$this->operativo->tieneInspector($inspectorId) && !$this->operativo->esInspectorReferente($inspectorId)) {
                session()->flash('error', 'No estás asignado a este operativo.');
                return redirect()->route('actas.dashboard');
            }
            
            // Pre-llenar campos del operativo
            $this->fecha = $this->operativo->fecha->format('Y-m-d');
            $this->hora = now()->format('H:i');
            $this->lugarinfra = $this->operativo->lugar;
        } else {
            // Acta simple - campos con fecha/hora actual
            $this->fecha = now()->format('Y-m-d');
            $this->hora = now()->format('H:i');
        }
        
        // Obtener departamento del inspector si no es admin
        $inspector = auth('inspector')->user();
        $this->dto_id = $inspector->dto_id ?? null;
    }

    public function abrirModalMotivos()
    {
        $this->mostrarModalMotivos = true;
        $this->busquedaMotivo = '';
        $this->motivosEncontrados = [];
    }

    public function cerrarModalMotivos()
    {
        $this->mostrarModalMotivos = false;
        $this->busquedaMotivo = '';
        $this->motivosEncontrados = [];
    }

    public function updatedBusquedaMotivo($value)
    {
        if (strlen($value) >= 3) {
            $inspector = auth('inspector')->user();
            $dtoDelInspector = $inspector->dto_id ?? null;
            
            $query = Motivo::buscar($value)
                ->where('nombre', '<>', ' ');
            
            // Filtrar por departamento del inspector
            if ($dtoDelInspector) {
                $query->where('dto_id', $dtoDelInspector);
            }
            
            $this->motivosEncontrados = $query
                ->orderBy('nombre', 'asc')
                ->limit(20)
                ->get()
                ->toArray();
        } else {
            $this->motivosEncontrados = [];
        }
    }

    public function updatedDni($value)
    {
        // Limpiar siempre antes de buscar
        $this->nombreinf = '';
        $this->direcinf = '';
        $this->mostrarTodoHistorial = false;

        if (strlen($value) >= 7) {
            $persona = Persona::where('dni', $value)->first();
            
            if ($persona) {
                $this->nombreinf = $persona->nombre;
                $this->direcinf = $persona->direccion;
            }

            // Historial de actas
            $actas = DB::table('fa_acta')
                ->select('fa_acta.id', 'fa_acta.actanro', 'fa_acta.fecha')
                ->where('fa_acta.dni', $value)
                ->orderBy('fa_acta.fecha', 'desc')
                ->get();

            $this->historialInfractor = $actas->map(function ($acta) {
                $motivos = DB::table('fa_acta_motivo')
                    ->join('fa_motivo', 'fa_acta_motivo.motivo_id', '=', 'fa_motivo.id')
                    ->where('fa_acta_motivo.acta_id', $acta->id)
                    ->pluck('fa_motivo.nombre')
                    ->toArray();

                return [
                    'actanro' => $acta->actanro,
                    'fecha'   => $acta->fecha,
                    'motivos' => $motivos,
                ];
            })->toArray();
        } else {
            $this->historialInfractor = [];
        }
    }

    public function updatedDominio($value)
    {
        $value = strtoupper(trim($value));
        $this->dominio = $value;

        if (strlen($value) >= 6) {
            $vehiculo = DB::table('fa_acta')
                ->select('tipo_id', 'marca_id', 'modelo', 'chasis', 'motor')
                ->whereRaw('UPPER(TRIM(dominio)) = ?', [$value])
                ->orderBy('id', 'desc')
                ->first();

            if ($vehiculo) {
                if ($vehiculo->tipo_id)  $this->tipo_id  = $vehiculo->tipo_id;
                if ($vehiculo->marca_id) $this->marca_id = $vehiculo->marca_id;
                if ($vehiculo->modelo)   $this->modelo   = strtoupper($vehiculo->modelo);
                if ($vehiculo->chasis)   $this->chasis   = strtoupper($vehiculo->chasis);
                if ($vehiculo->motor)    $this->motor    = strtoupper($vehiculo->motor);
            }
        }
    }

    public function verTodoHistorial()
    {
        $this->mostrarTodoHistorial = true;
    }

    public function agregarMotivo($motivoId)
    {
        $motivo = Motivo::find($motivoId);
        
        if ($motivo && !in_array($motivoId, array_column($this->motivosSeleccionados, 'id'))) {
            $this->motivosSeleccionados[] = [
                'id' => $motivo->id,
                'descripcion' => $motivo->nombre,
                'tipo' => $motivo->tipo,
                'ley' => $motivo->ley,
                'articulo' => $motivo->articulo,
            ];
        }
        
        $this->cerrarModalMotivos();
    }

    public function eliminarMotivo($index)
    {
        unset($this->motivosSeleccionados[$index]);
        $this->motivosSeleccionados = array_values($this->motivosSeleccionados);
    }

    public function guardarActa()
    {
       $this->validate([
        // Encabezado
        'actanro'     => 'required|integer|min:1|max:9999999999',
        'dto_id'      => 'required',
        'fecha'       => 'required|date',
        'hora'        => 'required',
        'lugarinfra'  => 'required|max:100',

        // Vehículo
        'dominio'  => 'nullable|max:10|alpha_num',
        'licencia' => 'nullable|max:20',
        'modelo'   => 'nullable|max:50',
        'chasis'   => 'nullable|max:50',
        'motor'    => 'nullable|max:50',

        // Infractor
        'dni'        => 'nullable|digits_between:1,8',
        'nombreinf'  => 'nullable|max:100',
        'direcinf'   => 'nullable|max:100',
        'grad_alcohol' => 'nullable|numeric|min:0|max:9.99',

        // Observaciones
        'observa'   => 'nullable|max:255',
        'arenavese' => 'nullable|max:50',
        'brenavese' => 'nullable|max:50',

        // Fotos
        'foto1' => 'nullable|image|mimes:jpeg,jpg|max:5120',
        'foto2' => 'nullable|image|mimes:jpeg,jpg|max:5120',
        'foto3' => 'nullable|image|mimes:jpeg,jpg|max:5120',
        'foto4' => 'nullable|image|mimes:jpeg,jpg|max:5120',
        'foto5' => 'nullable|image|mimes:jpeg,jpg|max:5120',
    ], [
        'actanro.required'      => 'El número de acta es obligatorio',
        'actanro.integer'       => 'El número de acta debe ser un número entero',
        'actanro.max'           => 'El número de acta es demasiado largo',
        'dto_id.required'       => 'El departamento es obligatorio',
        'fecha.required'        => 'La fecha es obligatoria',
        'hora.required'         => 'La hora es obligatoria',
        'lugarinfra.required'   => 'La dirección de la infracción es obligatoria',
        'lugarinfra.max'        => 'El lugar no puede superar los 100 caracteres',
        'dominio.max'           => 'El dominio no puede tener más de 10 caracteres',
        'dominio.alpha_num'     => 'El dominio solo puede contener letras y números',
        'licencia.max'          => 'La licencia no puede superar los 20 caracteres',
        'modelo.max'            => 'El modelo no puede superar los 50 caracteres',
        'chasis.max'            => 'El chasis no puede superar los 50 caracteres',
        'motor.max'             => 'El motor no puede superar los 50 caracteres',
        'dni.digits_between'    => 'El DNI debe tener entre 1 y 8 dígitos',
        'nombreinf.max'         => 'El nombre no puede superar los 100 caracteres',
        'direcinf.max'          => 'El domicilio no puede superar los 100 caracteres',
        'grad_alcohol.numeric'  => 'La graduación alcohólica debe ser un número',
        'grad_alcohol.min'      => 'La graduación alcohólica no puede ser negativa',
        'grad_alcohol.max'      => 'La graduación alcohólica no puede superar 9.99',
        'observa.max'           => 'Las observaciones no pueden superar los 255 caracteres',
    ]);

        // Si hay errores de validación, abrir la sección de encabezado
        if ($this->getErrorBag()->has('actanro') || $this->getErrorBag()->has('dto_id') || 
            $this->getErrorBag()->has('fecha') || $this->getErrorBag()->has('hora') || 
            $this->getErrorBag()->has('lugarinfra')) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-encabezado');
            $this->dispatch('scroll-to-top');
            return;
        }

        // Si hay errores en las fotos, abrir la sección de imágenes
        if ($this->getErrorBag()->hasAny(['actanro', 'dto_id', 'fecha', 'hora', 'lugarinfra'])) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-encabezado');
            $this->dispatch('scroll-to-top');
            return;
        }

        if ($this->getErrorBag()->hasAny(['dominio', 'licencia', 'modelo', 'chasis', 'motor'])) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-vehiculo');
            $this->dispatch('scroll-to-top');
            return;
        }

        if ($this->getErrorBag()->hasAny(['dni', 'nombreinf', 'direcinf', 'grad_alcohol', 'observa'])) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-infractor');
            $this->dispatch('scroll-to-top');
            return;
        }

        if ($this->getErrorBag()->hasAny(['foto1', 'foto2', 'foto3', 'foto4', 'foto5'])) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-imagenes');
            $this->dispatch('scroll-to-top');
            return;
        }

        // Validar que haya al menos un motivo seleccionado
        if (count($this->motivosSeleccionados) === 0) {
            $this->addError('motivos', 'Debe seleccionar al menos un motivo de infracción');
            $this->dispatch('abrir-seccion', seccion: 'seccion-motivos');
            $this->dispatch('scroll-to-top');
            return;
        }

        // Validar que no exista el número de acta para este departamento
        $actaExiste = Acta::where('actanro', $this->actanro)
            ->where('dto_id', $this->dto_id)
            ->exists();
            
        if ($actaExiste) {
            $this->dispatch('abrir-seccion', seccion: 'seccion-encabezado');
            $this->addError('actanro', 'El número de acta ya existe para este departamento.');
            $this->dispatch('scroll-to-top');
            return;
        }

        DB::beginTransaction();
        
        try {
            // Procesar imágenes antes de crear el acta
            // Nomenclatura: fot-0000000123-001.jpg (número de acta con 10 dígitos, número de foto con 3 dígitos)
            $foto1Name = null;
            $foto2Name = null;
            $foto3Name = null;
            $foto4Name = null;
            $foto5Name = null;

            // Número de acta formateado a 10 dígitos con ceros a la izquierda
            $actaNroFormateado = str_pad($this->actanro, 10, '0', STR_PAD_LEFT);

            /*if ($this->foto1) {
                $foto1Name = 'fot-' . $actaNroFormateado . '-001.jpg';
                $this->foto1->move(public_path('fotos'), $foto1Name);
            }*/

            if ($this->foto1) {
                $foto1Name = 'fot-' . $actaNroFormateado . '-001.jpg';

                $this->foto1->storeAs('', $foto1Name, 'fotos');
            }


            if ($this->foto2) {
                $foto2Name = 'fot-' . $actaNroFormateado . '-002.jpg';
                $this->foto2->storeAs('', $foto2Name, 'fotos');
            }

            if ($this->foto3) {
                $foto3Name = 'fot-' . $actaNroFormateado . '-003.jpg';
                $this->foto3->storeAs('', $foto3Name, 'fotos');
            }

            if ($this->foto4) {
                $foto4Name = 'fot-' . $actaNroFormateado . '-004.jpg';
                $this->foto4->storeAs('', $foto4Name, 'fotos');
            }

            if ($this->foto5) {
                $foto5Name = 'fot-' . $actaNroFormateado . '-005.jpg';
                $this->foto5->storeAs('', $foto5Name, 'fotos');
            }

            // Crear el acta
            $acta = Acta::create([
                'actanro' => $this->actanro,
                'dto_id' => $this->dto_id,
                'inspector_id' => auth('inspector')->id(),
                'operativo_id' => $this->esOperativo ? $this->operativoId : null,
                'fecha' => $this->fecha,
                'hora' => $this->hora,
                'lugarinfra' => $this->lugarinfra ?: null,
                'clausura' => $this->clausura,
                'decomiso' => $this->decomiso,
                'secuestro' => $this->secuestro,
                'retiene_lic' => $this->retiene_lic,
                'dni' => $this->dni,
                'nombreinf' => $this->nombreinf,
                'direcinf' => $this->direcinf,
                'dominio' => $this->dominio ?: null,
                'licencia' => $this->licencia ?: null,
                'modelo' => $this->modelo ?: null,
                'chasis' => $this->chasis ?: null,
                'motor' => $this->motor ?: null,
                'marca_id' => $this->marca_id ?: 0,
                'tipo_id' => $this->tipo_id ?: 0,
                'estado' => 1,
                'obs' => $this->observa ?: null,
                'detallada' => count($this->motivosSeleccionados),
                'grad_alcohol' => $this->grad_alcohol ?: 0,
                'arenavese' => $this->arenavese ?: null,
                'brenavese' => $this->brenavese ?: null,
                'crea_user' => auth('inspector')->user()->dni,
                'crea_fecha' => now()->format('Y-m-d'),
                'modif_user' => auth('inspector')->user()->dni,
                'modif_fecha' => now()->format('Y-m-d'),
            ]);

            // Guardar motivos
            foreach ($this->motivosSeleccionados as $motivo) {
                DB::table('fa_acta_motivo')->insert([
                    'acta_id' => $acta->id,
                    'motivo_id' => $motivo['id'],
                    'crea_user' => auth('inspector')->user()->dni,
                    'crea_fecha' => now()->format('Y-m-d'),
                ]);
            }

            DB::commit();
            
            session()->flash('message', 'Acta creada exitosamente.');
            return redirect()->route('actas.dashboard');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si falla, intentar eliminar las imágenes que se guardaron
            if (isset($foto1Name) && $foto1Name && file_exists(public_path('fotos/' . $foto1Name))) {
                unlink(public_path('fotos/' . $foto1Name));
            }
            if (isset($foto2Name) && $foto2Name && file_exists(public_path('fotos/' . $foto2Name))) {
                unlink(public_path('fotos/' . $foto2Name));
            }
            if (isset($foto3Name) && $foto3Name && file_exists(public_path('fotos/' . $foto3Name))) {
                unlink(public_path('fotos/' . $foto3Name));
            }
            if (isset($foto4Name) && $foto4Name && file_exists(public_path('fotos/' . $foto4Name))) {
                unlink(public_path('fotos/' . $foto4Name));
            }
            if (isset($foto5Name) && $foto5Name && file_exists(public_path('fotos/' . $foto5Name))) {
                unlink(public_path('fotos/' . $foto5Name));
            }
            
            session()->flash('error', 'Error al crear el acta: ' . $e->getMessage());
            $this->dispatch('scroll-to-top');
        }
    }

    public function render()
    {
        // Obtener departamentos
        $deptos = DB::table('fa_departamento')
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
            
        // Obtener tipos de rodado
        $tipos = DB::table('fa_tiporodado')
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
            
        // Obtener marcas
        $marcas = DB::table('fa_marca')
            ->select('id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('livewire.crear-acta', [
            'deptos' => $deptos,
            'tipos' => $tipos,
            'marcas' => $marcas,
        ])->layout('components.layouts.app');
    }
}