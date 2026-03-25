<div class="min-h-screen bg-gray-50">
    {{-- Encabezado compacto --}}
    <div class="bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-3 py-3 sm:px-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold text-white">Editar Acta #{{ $actanro }}</h1>
                    <p class="text-white/90 text-xs">
                        {{ auth('inspector')->user()->nombre }} {{ auth('inspector')->user()->apellido }}
                    </p>
                </div>
                <a href="{{ route('actas.listar') }}" class="text-white/90 hover:text-white text-xs font-medium">
                    Volver
                </a>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-3 py-3 sm:px-6 lg:px-8">
        
        {{-- Mensajes --}}
        @if (session()->has('error'))
            <div class="mb-3 bg-red-50 border-l-4 border-red-500 p-3 rounded text-sm">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Formulario --}}
        <form wire:submit.prevent="actualizarActa">
            
            {{-- Info del operativo si existe --}}
            @if($acta->operativo_id)
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded mb-3 text-xs">
                <p class="font-semibold text-emerald-900">Acta de Operativo #{{ $acta->operativo_id }}</p>
            </div>
            @endif

            {{-- Sección: Motivos (compacta) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-motivos').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Motivos</span>
                        @if(count($motivosSeleccionados) > 0)
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ count($motivosSeleccionados) }}
                            </span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-motivos" class="hidden border-t">
                    <div class="p-3">
                        <button 
                            type="button" 
                            wire:click="abrirModalMotivos"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar Motivo
                        </button>

                        @if(count($motivosSeleccionados) > 0)
                            <div class="space-y-2 mt-3">
                                @foreach($motivosSeleccionados as $index => $motivo)
                                    <div class="flex items-start justify-between bg-gray-50 p-2 rounded gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900">{{ $motivo['descripcion'] }}</p>
                                            <p class="text-xs text-gray-600">
                                                {{ $motivo['tipo'] == 'O' ? 'ORD' : 'LEY' }} {{ $motivo['ley'] }} Art {{ $motivo['articulo'] }}
                                            </p>
                                        </div>
                                        <button 
                                            type="button"
                                            wire:click="eliminarMotivo({{ $index }})"
                                            class="flex-shrink-0 text-red-600 hover:text-red-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sección: Encabezado (colapsable) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-encabezado').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Encabezado *</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-encabezado" class="border-t p-3 space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nro Acta *</label>
                            <input 
                                type="number" 
                                wire:model="actanro"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500"
                                required
                            >
                            @error('actanro') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fecha *</label>
                            <input 
                                type="date" 
                                wire:model="fecha"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Hora *</label>
                            <input 
                                type="time" 
                                wire:model="hora"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Departamento *</label>
                        <input 
                            type="text" 
                            value="{{ collect($deptos)->firstWhere('id', $dto_id)->nombre ?? 'Sin departamento' }}"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lugar de la infracción *</label>
                        <input 
                            type="text" 
                            wire:model="lugarinfra"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Intersección</label>
                        <input 
                            type="text" 
                            wire:model="interseccion"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            placeholder="Calle / esquina o altura"
                        >
                        @error('interseccion') 
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sección: Medida Preventiva (colapsable) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-medida').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Medida Preventiva</span>
                        @php
                            $medidasActivas = collect([$decomiso, $clausura, $secuestro, $retiene_lic])->filter()->count();
                        @endphp
                        @if($medidasActivas > 0)
                            <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ $medidasActivas }}
                            </span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-medida" class="hidden border-t p-3">
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center p-3 bg-gray-50 rounded-lg cursor-pointer border border-gray-200 hover:bg-amber-50 hover:border-amber-300 transition-colors">
                            <input type="checkbox" wire:model="decomiso" class="rounded border-gray-300 text-amber-600 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-gray-900">DECOMISO</span>
                        </label>
                        <label class="flex items-center justify-center p-3 bg-gray-50 rounded-lg cursor-pointer border border-gray-200 hover:bg-amber-50 hover:border-amber-300 transition-colors">
                            <input type="checkbox" wire:model="clausura" class="rounded border-gray-300 text-amber-600 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-gray-900">CLAUSURA</span>
                        </label>
                        <label class="flex items-center justify-center p-3 bg-gray-50 rounded-lg cursor-pointer border border-gray-200 hover:bg-amber-50 hover:border-amber-300 transition-colors">
                            <input type="checkbox" wire:model="secuestro" class="rounded border-gray-300 text-amber-600 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-gray-900">SECUESTRO</span>
                        </label>
                        <label class="flex items-center justify-center p-3 bg-gray-50 rounded-lg cursor-pointer border border-gray-200 hover:bg-amber-50 hover:border-amber-300 transition-colors">
                            <input type="checkbox" wire:model="retiene_lic" class="rounded border-gray-300 text-amber-600 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-gray-900">RET. LICENCIA</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Sección: Datos del Vehículo (colapsable) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-vehiculo').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Datos del Vehículo</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-vehiculo" class="hidden border-t p-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Dominio</label>
                        <input 
                            type="text" 
                            wire:model="dominio"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                            <select 
                                wire:model="tipo_id"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                            >
                                <option value="">-</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Marca</label>
                            <select 
                                wire:model="marca_id"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                            >
                                <option value="">-</option>
                                @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Modelo</label>
                            <input 
                                type="text" 
                                wire:model="modelo"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Motor</label>
                            <input 
                                type="text" 
                                wire:model="motor"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Chasis</label>
                            <input 
                                type="text" 
                                wire:model="chasis"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            >
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección: Datos del Infractor (colapsable) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-infractor').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Datos del Infractor</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-infractor" class="hidden border-t p-3 space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">DNI</label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="dni"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                                >
                                <div wire:loading wire:target="dni" class="absolute right-2 top-2">
                                    <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nombre</label>
                            <input 
                                type="text" 
                                wire:model="nombreinf"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Domicilio</label>
                        <input 
                            type="text" 
                            wire:model="direcinf"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Graduación Alcohólica</label>
                        <input 
                            type="number" 
                            step="0.01"
                            wire:model="grad_alcohol"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea 
                            wire:model="observa"
                            rows="2"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                            placeholder="Observaciones..."
                        ></textarea>
                    </div>
                </div>
            </div>

            {{-- Sección: Imágenes Adjuntas (colapsable) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-imagenes').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Imágenes Adjuntas</span>
                        @php
                            $fotosExistentes = 0;
                            for($i = 1; $i <= 5; $i++) {
                                $fotoVar = "fotoExistente{$i}";
                                if($$fotoVar && file_exists(public_path('fotos/' . $$fotoVar))) {
                                    $fotosExistentes++;
                                }
                            }
                        @endphp
                        @if($fotosExistentes > 0)
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ $fotosExistentes }}
                            </span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-imagenes" class="border-t p-3 space-y-3 hidden">
                    <p class="text-xs text-gray-600 mb-3">
                        Podés adjuntar hasta 5 fotografías del acta (formato JPG/JPEG)
                    </p>

                    {{-- Foto 1 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 1</label>
                        @if($fotoExistente1 && file_exists(public_path('fotos/' . $fotoExistente1)))
                            <div class="relative group">
                                <img src="{{ asset('fotos/' . $fotoExistente1) }}?t={{ time() }}" alt="Foto 1" class="w-full max-w-xs rounded-lg border-2 border-gray-300 shadow-sm">
                                <button type="button" onclick="confirmarEliminarFoto(1)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                        <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="foto1" accept="image/jpeg,image/jpg" class="hidden">
                                </label>
                                @if($foto1) <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Nueva imagen lista para guardar</div> @endif
                                <div wire:loading wire:target="foto1" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                                @error('foto1') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Foto 2 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 2</label>
                        @if($fotoExistente2 && file_exists(public_path('fotos/' . $fotoExistente2)))
                            <div class="relative group">
                                <img src="{{ asset('fotos/' . $fotoExistente2) }}?t={{ time() }}" alt="Foto 2" class="w-full max-w-xs rounded-lg border-2 border-gray-300 shadow-sm">
                                <button type="button" onclick="confirmarEliminarFoto(2)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                        <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="foto2" accept="image/jpeg,image/jpg" class="hidden">
                                </label>
                                @if($foto2) <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Nueva imagen lista para guardar</div> @endif
                                <div wire:loading wire:target="foto2" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                                @error('foto2') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Foto 3 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 3</label>
                        @if($fotoExistente3 && file_exists(public_path('fotos/' . $fotoExistente3)))
                            <div class="relative group">
                                <img src="{{ asset('fotos/' . $fotoExistente3) }}?t={{ time() }}" alt="Foto 3" class="w-full max-w-xs rounded-lg border-2 border-gray-300 shadow-sm">
                                <button type="button" onclick="confirmarEliminarFoto(3)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                        <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="foto3" accept="image/jpeg,image/jpg" class="hidden">
                                </label>
                                @if($foto3) <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Nueva imagen lista para guardar</div> @endif
                                <div wire:loading wire:target="foto3" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                                @error('foto3') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Foto 4 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 4</label>
                        @if($fotoExistente4 && file_exists(public_path('fotos/' . $fotoExistente4)))
                            <div class="relative group">
                                <img src="{{ asset('fotos/' . $fotoExistente4) }}?t={{ time() }}" alt="Foto 4" class="w-full max-w-xs rounded-lg border-2 border-gray-300 shadow-sm">
                                <button type="button" onclick="confirmarEliminarFoto(4)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                        <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="foto4" accept="image/jpeg,image/jpg" class="hidden">
                                </label>
                                @if($foto4) <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Nueva imagen lista para guardar</div> @endif
                                <div wire:loading wire:target="foto4" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                                @error('foto4') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Foto 5 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 5</label>
                        @if($fotoExistente5 && file_exists(public_path('fotos/' . $fotoExistente5)))
                            <div class="relative group">
                                <img src="{{ asset('fotos/' . $fotoExistente5) }}?t={{ time() }}" alt="Foto 5" class="w-full max-w-xs rounded-lg border-2 border-gray-300 shadow-sm">
                                <button type="button" onclick="confirmarEliminarFoto(5)" class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Eliminar
                                </button>
                            </div>
                        @else
                            <div class="relative">
                                <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                    <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                        <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                        <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" wire:model="foto5" accept="image/jpeg,image/jpg" class="hidden">
                                </label>
                                @if($foto5) <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>Nueva imagen lista para guardar</div> @endif
                                <div wire:loading wire:target="foto5" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                                @error('foto5') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Botones de Acción (sticky bottom) --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-200 p-3 flex gap-2 shadow-lg">
                <a 
                    href="{{ route('actas.listar') }}"
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 text-center text-sm"
                >
                    Cancelar
                </a>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-3 bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] hover:from-[#3FA6B8] hover:via-[#3FA6B8] hover:to-[#3FA6B8] text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 text-sm"
                >
                    Actualizar Acta
                </button>
            </div>
        </form>
    </div>

    {{-- Modal de motivos (altura fija desde abajo) --}}
    @if($mostrarModalMotivos)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" wire:click="cerrarModalMotivos">
            <div class="bg-white rounded-t-2xl sm:rounded-lg w-full sm:max-w-4xl" style="height: 85vh;" wire:click.stop>
                
                {{-- Header fijo --}}
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-base font-semibold text-gray-900">Buscar Motivo</h3>
                        <button wire:click="cerrarModalMotivos" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="busquedaMotivo"
                            class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500"
                            placeholder="Mínimo 3 caracteres..."
                            autofocus
                        >
                    </div>
                    
                    @if(strlen($busquedaMotivo) > 0 && strlen($busquedaMotivo) < 3)
                        <p class="text-xs text-gray-500 mt-1">Escribí al menos 3 caracteres</p>
                    @endif
                </div>
                
                <div class="overflow-y-auto" style="height: calc(85vh - 120px);">
                    @if(strlen($busquedaMotivo) >= 3)
                        @if(count($motivosEncontrados) > 0)
                            <div class="sm:hidden divide-y divide-gray-200">
                                @foreach($motivosEncontrados as $motivo)
                                    <button type="button" wire:click="agregarMotivo({{ $motivo['id'] }})" class="w-full text-left p-3 hover:bg-gray-50 active:bg-gray-100">
                                        <p class="text-sm font-medium text-gray-900 mb-1">{{ $motivo['nombre'] }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="text-xs px-2 py-0.5 rounded {{ $motivo['tipo'] == 'O' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">{{ $motivo['tipo'] == 'O' ? 'ORD' : 'LEY' }}</span>
                                            <span class="text-xs text-gray-600">{{ $motivo['ley'] }} - Art {{ $motivo['articulo'] }}</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                            <div class="hidden sm:block">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ley</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Art</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($motivosEncontrados as $motivo)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm">{{ $motivo['nombre'] }}</td>
                                                <td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded {{ $motivo['tipo'] == 'O' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">{{ $motivo['tipo'] == 'O' ? 'ORD' : 'LEY' }}</span></td>
                                                <td class="px-4 py-2 text-sm">{{ $motivo['ley'] }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $motivo['articulo'] }}</td>
                                                <td class="px-4 py-2 text-right"><button type="button" wire:click="agregarMotivo({{ $motivo['id'] }})" class="text-xs px-3 py-1 bg-slate-600 text-white rounded-md hover:bg-slate-700">Agregar</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex items-center justify-center" style="height: calc(85vh - 120px);">
                                <div class="text-center px-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron motivos</h3>
                                    <p class="mt-1 text-sm text-gray-500">Probá con otros términos</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-center" style="height: calc(85vh - 120px);">
                            <div class="text-center px-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Buscá un motivo</h3>
                                <p class="mt-1 text-sm text-gray-500">Escribí al menos 3 caracteres</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        const seccionesAbiertas = {};

        function toggleSeccion(seccionId) {
            const seccion = document.getElementById(seccionId);
            const icono = document.getElementById('icono-' + seccionId);
            if (seccion.classList.contains('hidden')) {
                seccion.classList.remove('hidden');
                if (icono) icono.classList.add('rotate-180');
                seccionesAbiertas[seccionId] = true;
            } else {
                seccion.classList.add('hidden');
                if (icono) icono.classList.remove('rotate-180');
                seccionesAbiertas[seccionId] = false;
            }
        }

        document.addEventListener('livewire:update', function() {
            Object.keys(seccionesAbiertas).forEach(seccionId => {
                const seccion = document.getElementById(seccionId);
                const icono = document.getElementById('icono-' + seccionId);
                if (seccion && icono) {
                    if (seccionesAbiertas[seccionId]) {
                        seccion.classList.remove('hidden');
                        icono.classList.add('rotate-180');
                    } else {
                        seccion.classList.add('hidden');
                        icono.classList.remove('rotate-180');
                    }
                }
            });
        });

        function confirmarEliminarFoto(numeroFoto) {
            Swal.fire({
                title: '¿Eliminar fotografía?',
                text: `Esta acción eliminará permanentemente la fotografía ${numeroFoto}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('eliminarFoto', numeroFoto);
                }
            });
        }
        
        document.addEventListener('livewire:init', () => {
            Livewire.on('mostrar-alerta', (event) => {
                Swal.fire({
                    icon: event.tipo,
                    title: event.titulo,
                    text: event.mensaje,
                    confirmButtonColor: '#5FB7C8',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
            
            Livewire.on('abrir-seccion', (event) => {
                setTimeout(() => {
                    const seccionId = event.seccion;
                    const seccion = document.getElementById(seccionId);
                    const icono = document.getElementById('icono-' + seccionId);
                    if (seccion) {
                        seccion.classList.remove('hidden');
                        if (icono) icono.classList.add('rotate-180');
                        seccionesAbiertas[seccionId] = true;
                    }
                }, 50);
            });

            Livewire.on('scroll-to-top', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</div>