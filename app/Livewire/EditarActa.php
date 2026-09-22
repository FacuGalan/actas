<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Acta;
use Livewire\WithFileUploads;
use App\Models\Motivo;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class EditarActa extends Component
{
    use WithFileUploads;
    
    public $actaId;
    public $acta;
    
    // Campos del formulario (igual que CrearActa)
    public $actanro;
    public $dto_id;
    public $fecha;
    public $hora;
    public $lugarinfra;
    public $decomiso = false;
    public $clausura = false;
    
    // Vehículo
    public $dominio;
    public $licencia;
    public $tipo_id;
    public $marca_id;
    public $modelo;
    public $motor;
    public $chasis;
    
    // Infractor
    public $dni;
    public $nombreinf;
    public $direcinf;
    public $secuestro = false;
    public $arenavese;
    public $brenavese;
    public $retiene_lic = false;
    public $grad_alcohol;
    public $observa;
    
    // Motivos
    public $motivosSeleccionados = [];
    public $mostrarModalMotivos = false;
    public $busquedaMotivo = '';
    public $motivosEncontrados = [];

    // Fotos existentes
    public $fotoExistente1;
    public $fotoExistente2;
    public $fotoExistente3;
    public $fotoExistente4;
    public $fotoExistente5;
    
    // Nuevas fotos
    public $foto1;
    public $foto2;
    public $foto3;
    public $foto4;
    public $foto5;

    // Tamaño máximo permitido para las fotos, en bytes (debe coincidir con la regla 'max' de la validación, en KB)
    private const FOTO_MAX_BYTES = 2048 * 1024;

    public function mount($acta)
    {
        $this->actaId = $acta;
        $this->acta = Acta::find($acta);
        
        // Verificar que el acta existe y pertenece al inspector
        if (!$this->acta || $this->acta->inspector_id != auth('inspector')->id()) {
            session()->flash('error', 'No tenés permisos para editar esta acta.');
            return redirect()->route('actas.listar');
        }
        
        // Cargar datos del acta
        $this->actanro = $this->acta->actanro;
        $this->dto_id = $this->acta->dto_id;
        $this->fecha = $this->acta->fecha->format('Y-m-d');
        $this->hora = $this->acta->hora;
        $this->lugarinfra = $this->acta->lugarinfra;
        $this->decomiso = $this->acta->decomiso;
        $this->clausura = $this->acta->clausura;
        
        $this->dominio = $this->acta->dominio;
        $this->licencia = $this->acta->licencia;
        $this->tipo_id = $this->acta->tipo_id;
        $this->marca_id = $this->acta->marca_id;
        $this->modelo = $this->acta->modelo;
        $this->motor = $this->acta->motor;
        $this->chasis = $this->acta->chasis;
        
        $this->dni = $this->acta->dni;
        $this->nombreinf = $this->acta->nombreinf;
        $this->direcinf = $this->acta->direcinf;
        $this->secuestro = $this->acta->secuestro;
        $this->arenavese = $this->acta->arenavese;
        $this->brenavese = $this->acta->brenavese;
        $this->retiene_lic = $this->acta->retiene_lic;
        $this->grad_alcohol = $this->acta->grad_alcohol;
        $this->observa = $this->acta->obs;
        
        // Cargar motivos existentes
        $motivos = DB::table('fa_acta_motivo')
            ->join('fa_motivo', 'fa_acta_motivo.motivo_id', '=', 'fa_motivo.id')
            ->where('fa_acta_motivo.acta_id', $this->actaId)
            ->select('fa_motivo.*')
            ->get();
        
        foreach ($motivos as $motivo) {
            $this->motivosSeleccionados[] = [
                'id' => $motivo->id,
                'descripcion' => $motivo->nombre,
                'tipo' => $motivo->tipo,
                'ley' => $motivo->ley,
                'articulo' => $motivo->articulo,
            ];
        }

        // Cargar nombres de fotos existentes
        $actaNroFormateado = str_pad($this->acta->actanro, 10, '0', STR_PAD_LEFT);
        
        $this->fotoExistente1 = 'fot-' . $actaNroFormateado . '-001.jpg';
        $this->fotoExistente2 = 'fot-' . $actaNroFormateado . '-002.jpg';
        $this->fotoExistente3 = 'fot-' . $actaNroFormateado . '-003.jpg';
        $this->fotoExistente4 = 'fot-' . $actaNroFormateado . '-004.jpg';
        $this->fotoExistente5 = 'fot-' . $actaNroFormateado . '-005.jpg';
    }

    public function updatedDni($value)
    {
        if (strlen($value) >= 7) {
            $persona = Persona::where('dni', $value)->first();
            
            if ($persona) {
                $this->nombreinf = $persona->nombre;
                $this->direcinf = $persona->direccion;
            }
        }
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

   public function eliminarFoto($numeroFoto)
    {
        $actaNroFormateado = str_pad($this->actanro, 10, '0', STR_PAD_LEFT);
        $numeroFotoFormateado = str_pad($numeroFoto, 3, '0', STR_PAD_LEFT);
        $nombreArchivo = 'fot-' . $actaNroFormateado . '-' . $numeroFotoFormateado . '.jpg';
        $rutaArchivo = public_path('fotos/' . $nombreArchivo);
        
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
            
            // Actualizar la propiedad correspondiente
            $this->{"fotoExistente{$numeroFoto}"} = null;
            
            // Parámetros con nombre: en Livewire 3 un array posicional llega al JS envuelto en una lista
            // y el listener no encuentra tipo/titulo/mensaje.
            $this->dispatch('mostrar-alerta',
                tipo: 'success',
                titulo: 'Foto eliminada correctamente',
                mensaje: '',
            );
        }
    }

    public function updatedFoto1() { $this->comprimirFotoSiEsNecesario($this->foto1); }
    public function updatedFoto2() { $this->comprimirFotoSiEsNecesario($this->foto2); }
    public function updatedFoto3() { $this->comprimirFotoSiEsNecesario($this->foto3); }
    public function updatedFoto4() { $this->comprimirFotoSiEsNecesario($this->foto4); }
    public function updatedFoto5() { $this->comprimirFotoSiEsNecesario($this->foto5); }

    /**
     * Las fotos de celular suelen pesar más que el límite de 2MB. En vez de rechazarlas,
     * las redimensionamos y recomprimimos apenas se suben para que entren en el límite,
     * así el inspector no tiene que editar la foto a mano antes de subirla.
     */
    private function comprimirFotoSiEsNecesario($foto): void
    {
        if (!$foto || !$foto->isValid() || $foto->getSize() <= self::FOTO_MAX_BYTES) {
            return;
        }

        $rutaTemporal = $foto->getRealPath();
        $imagen = @imagecreatefromstring(file_get_contents($rutaTemporal));

        if ($imagen === false) {
            return; // No es una imagen procesable; la validación posterior la va a rechazar con un mensaje claro.
        }

        $anchoMax = 1920;
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);

        if ($ancho > $anchoMax) {
            $nuevoAlto = (int) round($alto * ($anchoMax / $ancho));
            $redimensionada = imagecreatetruecolor($anchoMax, $nuevoAlto);
            imagecopyresampled($redimensionada, $imagen, 0, 0, 0, 0, $anchoMax, $nuevoAlto, $ancho, $alto);
            imagedestroy($imagen);
            $imagen = $redimensionada;
        }

        // Bajamos la calidad JPEG en pasos hasta que entre en el límite de tamaño.
        $calidad = 85;
        do {
            imagejpeg($imagen, $rutaTemporal, $calidad);
            clearstatcache(true, $rutaTemporal);
            $calidad -= 15;
        } while (filesize($rutaTemporal) > self::FOTO_MAX_BYTES && $calidad >= 20);

        imagedestroy($imagen);
    }

    private function guardarFoto($foto, $numeroFoto)
    {
        if (!$foto) {
            return null;
        }

        $actaNroFormateado = str_pad($this->actanro, 10, '0', STR_PAD_LEFT);
        $numeroFotoFormateado = str_pad($numeroFoto, 3, '0', STR_PAD_LEFT);
        $nombreArchivo = 'fot-' . $actaNroFormateado . '-' . $numeroFotoFormateado . '.jpg';
        $destinationPath = public_path('fotos/' . $nombreArchivo);

        $imagen = @imagecreatefromjpeg($foto->getRealPath());

        if ($imagen === false) {
            throw new \Exception("No se pudo procesar la foto {$numeroFoto}. Verificá que sea un archivo JPEG válido.");
        }

        $guardado = imagejpeg($imagen, $destinationPath, 85);
        imagedestroy($imagen);

        if (!$guardado) {
            throw new \Exception("No se pudo guardar la foto {$numeroFoto} en el servidor.");
        }

        return $nombreArchivo;
    }

    public function actualizarActa()
    {
        $this->validate([
            'actanro' => 'required|numeric',
            'dto_id' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'lugarinfra' => 'required',
            'foto1' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'foto2' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'foto3' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'foto4' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'foto5' => 'nullable|image|mimes:jpeg,jpg|max:2048',
        ], [
            'actanro.required'      => 'El número de acta es obligatorio',
            'actanro.numeric'       => 'El número de acta debe ser un número',
            'dto_id.required'       => 'El departamento es obligatorio',
            'fecha.required'        => 'La fecha es obligatoria',
            'hora.required'         => 'La hora es obligatoria',
            'lugarinfra.required'   => 'La dirección de la infracción es obligatoria',
            'foto1.image'           => 'La fotografía 1 debe ser una imagen válida',
            'foto1.mimes'           => 'La fotografía 1 debe ser un archivo JPG o JPEG',
            'foto1.max'             => 'La fotografía 1 no pudo comprimirse por debajo de 2MB. Probá con otra foto',
            'foto2.image'           => 'La fotografía 2 debe ser una imagen válida',
            'foto2.mimes'           => 'La fotografía 2 debe ser un archivo JPG o JPEG',
            'foto2.max'             => 'La fotografía 2 no pudo comprimirse por debajo de 2MB. Probá con otra foto',
            'foto3.image'           => 'La fotografía 3 debe ser una imagen válida',
            'foto3.mimes'           => 'La fotografía 3 debe ser un archivo JPG o JPEG',
            'foto3.max'             => 'La fotografía 3 no pudo comprimirse por debajo de 2MB. Probá con otra foto',
            'foto4.image'           => 'La fotografía 4 debe ser una imagen válida',
            'foto4.mimes'           => 'La fotografía 4 debe ser un archivo JPG o JPEG',
            'foto4.max'             => 'La fotografía 4 no pudo comprimirse por debajo de 2MB. Probá con otra foto',
            'foto5.image'           => 'La fotografía 5 debe ser una imagen válida',
            'foto5.mimes'           => 'La fotografía 5 debe ser un archivo JPG o JPEG',
            'foto5.max'             => 'La fotografía 5 no pudo comprimirse por debajo de 2MB. Probá con otra foto',
        ]);

        DB::beginTransaction();
        
        try {
            // Guardar nuevas fotos si se subieron
            if ($this->foto1) {
                $this->guardarFoto($this->foto1, 1);
            }
            if ($this->foto2) {
                $this->guardarFoto($this->foto2, 2);
            }
            if ($this->foto3) {
                $this->guardarFoto($this->foto3, 3);
            }
            if ($this->foto4) {
                $this->guardarFoto($this->foto4, 4);
            }
            if ($this->foto5) {
                $this->guardarFoto($this->foto5, 5);
            }

            // Actualizar el acta
            $this->acta->update([
                'actanro' => $this->actanro,
                'fecha' => $this->fecha,
                'hora' => $this->hora,
                'lugarinfra' => $this->lugarinfra,
                'clausura' => $this->clausura,
                'decomiso' => $this->decomiso,
                'secuestro' => $this->secuestro,
                'retiene_lic' => $this->retiene_lic,
                'dni' => $this->dni ?: null,
                'nombreinf' => $this->nombreinf ?: null,
                'direcinf' => $this->direcinf ?: null,
                'dominio' => $this->dominio ?: null,
                'licencia' => $this->licencia ?: null,
                'modelo' => $this->modelo ?: null,
                'chasis' => $this->chasis ?: null,
                'motor' => $this->motor ?: null,
                'marca_id' => $this->marca_id ?: 0,
                'tipo_id' => $this->tipo_id ?: 0,
                'obs' => $this->observa ?: null,
                'detallada' => count($this->motivosSeleccionados),
                'grad_alcohol' => $this->grad_alcohol ?: null,
                'arenavese' => $this->arenavese ?: null,
                'brenavese' => $this->brenavese ?: null,
                'modif_user' => auth('inspector')->user()->dni,
                'modif_fecha' => now()->format('Y-m-d'),
            ]);

            // Eliminar motivos anteriores
            DB::table('fa_acta_motivo')->where('acta_id', $this->actaId)->delete();

            // Guardar nuevos motivos
            foreach ($this->motivosSeleccionados as $motivo) {
                DB::table('fa_acta_motivo')->insert([
                    'acta_id' => $this->actaId,
                    'motivo_id' => $motivo['id'],
                    'crea_user' => auth('inspector')->user()->dni,
                    'crea_fecha' => now()->format('Y-m-d'),
                ]);
            }

            DB::commit();
            
            session()->flash('message', 'Acta actualizada exitosamente.');
            return redirect()->route('actas.listar');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el acta: ' . $e->getMessage());
            $this->dispatch('scroll-to-top');
        }
    }

    public function render()
    {
        $deptos = DB::table('fa_departamento')->select('id', 'nombre')->orderBy('nombre')->get();
        $tipos = DB::table('fa_tiporodado')->select('id', 'nombre')->orderBy('nombre')->get();
        $marcas = DB::table('fa_marca')->select('id', 'nombre')->orderBy('nombre')->get();

        return view('livewire.editar-acta', [
            'deptos' => $deptos,
            'tipos' => $tipos,
            'marcas' => $marcas,
        ])->layout('components.layouts.app');
    }
}