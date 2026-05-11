<div class="min-h-screen bg-gray-50">
    {{-- Encabezado compacto --}}
    <div 
        class="bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-3 py-3 sm:px-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold text-white">
                        {{ $esOperativo ? 'Acta Operativo' : 'Acta Simple' }}
                    </h1>
                    <p class="text-white/90 text-xs">
                        {{ auth('inspector')->user()->nombre }} 
                        {{ auth('inspector')->user()->apellido }}
                    </p>
                </div>

                <a 
                    href="{{ route('actas.dashboard') }}" 
                    class="text-white/90 hover:text-white text-xs font-medium transition-colors"
                >
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
        <form wire:submit.prevent="guardarActa">
            
            {{-- Card del Operativo (compacto) --}}
            @if($esOperativo && $operativo)
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded mb-3 text-xs">
                <p class="font-semibold text-emerald-900">Op. #{{ $operativo_nro }}: {{ $operativo->descripcion }}</p>
                <p class="text-emerald-700">{{ $operativo->lugar }}</p>
            </div>
            @endif

            {{-- Sección: Motivos (compacta) --}}
            <div class="bg-white rounded-lg shadow-sm border @error('motivos') border-red-500 @else border-gray-200 @enderror mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-motivos').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 @error('motivos') text-red-600 @else text-gray-600 @enderror" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="font-semibold @error('motivos') text-red-900 @else text-gray-900 @enderror text-sm">Motivos *</span>
                        @if(count($motivosSeleccionados) > 0)
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ count($motivosSeleccionados) }}
                            </span>
                        @endif
                        @error('motivos')
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        @enderror
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-motivos" class="@if(!$errors->has('motivos')) hidden @endif border-t">
                    @error('motivos')
                        <div class="px-3 pt-3">
                            <p class="text-red-600 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        </div>
                    @enderror
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
            <div class="bg-white rounded-lg shadow-sm border @if($errors->hasAny(['actanro', 'dto_id', 'fecha', 'hora', 'lugarinfra'])) border-red-500 @else border-gray-200 @endif mb-3">
                <button 
                    type="button"
                    onclick="document.getElementById('seccion-encabezado').classList.toggle('hidden')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 @if($errors->hasAny(['actanro', 'dto_id', 'fecha', 'hora', 'lugarinfra'])) text-red-600 @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-semibold @if($errors->hasAny(['actanro', 'dto_id', 'fecha', 'hora', 'lugarinfra'])) text-red-900 @else text-gray-900 @endif text-sm">Encabezado *</span>
                        @if($errors->hasAny(['actanro', 'dto_id', 'fecha', 'hora', 'lugarinfra']))
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
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
                            >
                            @error('actanro') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        @if($esOperativo)
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nro Op.</label>
                            <input 
                                type="text" 
                                value="{{ $operativo_nro }}"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg bg-emerald-50 text-emerald-900 font-semibold"
                                readonly
                            >
                        </div>
                        @endif

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Fecha *</label>
                            <input 
                                type="date" 
                                wire:model="fecha"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg @if($esOperativo) bg-gray-100 @endif"
                                required
                                @if($esOperativo) readonly @endif
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
                        <input type="hidden" wire:model="dto_id">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lugar de la infracción *</label>
                        <input 
                            type="text" 
                            wire:model="lugarinfra"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase @if($esOperativo) bg-gray-100 @endif"
                            @if($esOperativo) readonly @endif
                        >
                        @error('lugarinfra') 
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
                    onclick="toggleSeccion('seccion-vehiculo')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Datos del Vehículo</span>
                    </div>
                    <svg id="icono-seccion-vehiculo" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-vehiculo" class="hidden border-t p-3 space-y-3" wire:ignore.self>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Dominio</label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.600ms="dominio"
                            class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg uppercase"
                        >
                        @error('dominio') 
                            <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
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
                    onclick="toggleSeccion('seccion-infractor')"
                    class="w-full px-4 py-3 flex justify-between items-center hover:bg-gray-50"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-semibold text-gray-900 text-sm">Datos del Infractor</span>
                    </div>
                    <svg id="icono-seccion-infractor" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-infractor" class="border-t p-3 space-y-3 hidden" wire:ignore.self>
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
                            @error('dni') 
                                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> 
                            @enderror
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

                    {{-- Historial del infractor --}}
                    @if(count($historialInfractor) > 0)
                        <div class="mt-2">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-amber-700">
                                    Antecedentes: {{ count($historialInfractor) }} acta(s) registrada(s)
                                </span>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($historialInfractor as $item)
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-semibold text-amber-900">Acta #{{ $item['actanro'] }}</span>
                                            <span class="text-xs text-amber-700">
                                                {{ \Carbon\Carbon::parse($item['fecha'])->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        @if(count($item['motivos']) > 0)
                                            <div class="flex flex-col gap-1">
                                                @foreach($item['motivos'] as $motivo)
                                                    <span class="text-xs text-amber-800 leading-tight">• {{ $motivo }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-amber-600 italic">Sin motivos registrados</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

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
                            $fotosCount = count(array_filter([$foto1, $foto2, $foto3, $foto4, $foto5]));
                        @endphp
                        @if($fotosCount > 0)
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ $fotosCount }}
                            </span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="seccion-imagenes" class="border-t p-3 space-y-3 hidden" wire:ignore.self>
                    <p class="text-xs text-gray-600 mb-3">
                        Podés adjuntar hasta 5 fotografías del acta (formato JPG/JPEG, máx. 5MB cada una)
                    </p>

                    {{-- Foto 1 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 1</label>
                        <div class="relative">
                            <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                    <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                </div>
                                <input type="file" wire:model="foto1" accept="image/jpeg,image/jpg" class="hidden">
                            </label>
                            @if($foto1)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="flex-1">{{ $foto1->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="$set('foto1', null)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endif
                            <div wire:loading wire:target="foto1" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Subiendo imagen...
                            </div>
                            @error('foto1') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Foto 2 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 2</label>
                        <div class="relative">
                            <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                    <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                </div>
                                <input type="file" wire:model="foto2" accept="image/jpeg,image/jpg" class="hidden">
                            </label>
                            @if($foto2)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="flex-1">{{ $foto2->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="$set('foto2', null)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>
                            @endif
                            <div wire:loading wire:target="foto2" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                            @error('foto2') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Foto 3 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 3</label>
                        <div class="relative">
                            <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                    <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                </div>
                                <input type="file" wire:model="foto3" accept="image/jpeg,image/jpg" class="hidden">
                            </label>
                            @if($foto3)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="flex-1">{{ $foto3->getClientOriginalName() }}</span><button type="button" wire:click="$set('foto3', null)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
                            @endif
                            <div wire:loading wire:target="foto3" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                            @error('foto3') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Foto 4 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 4</label>
                        <div class="relative">
                            <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                    <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                </div>
                                <input type="file" wire:model="foto4" accept="image/jpeg,image/jpg" class="hidden">
                            </label>
                            @if($foto4)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="flex-1">{{ $foto4->getClientOriginalName() }}</span><button type="button" wire:click="$set('foto4', null)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
                            @endif
                            <div wire:loading wire:target="foto4" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                            @error('foto4') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Foto 5 --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Fotografía 5</label>
                        <div class="relative">
                            <label class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors duration-200 py-6">
                                <div class="flex flex-col items-center justify-center pt-2 pb-3">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-1 text-sm text-gray-600"><span class="font-semibold">Click para subir</span> o arrastrá la imagen</p>
                                    <p class="text-xs text-gray-500">JPG o JPEG (MAX. 5MB)</p>
                                </div>
                                <input type="file" wire:model="foto5" accept="image/jpeg,image/jpg" class="hidden">
                            </label>
                            @if($foto5)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="flex-1">{{ $foto5->getClientOriginalName() }}</span><button type="button" wire:click="$set('foto5', null)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>
                            @endif
                            <div wire:loading wire:target="foto5" class="mt-2 flex items-center gap-2 text-xs text-blue-600 bg-blue-50 p-2 rounded"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Subiendo imagen...</div>
                            @error('foto5') <div class="mt-2 flex items-center gap-2 text-xs text-red-600 bg-red-50 p-2 rounded"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de Acción (sticky bottom) --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-200 p-3 flex gap-2 shadow-lg">
                <a 
                    href="{{ route('actas.dashboard') }}"
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 text-center text-sm"
                >
                    Cancelar
                </a>

                <button 
                    type="submit"
                    class="flex-1 px-4 py-3 bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] hover:from-[#3FA6B8] hover:via-[#3FA6B8] hover:to-[#3FA6B8] text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 text-sm"
                >
                    Guardar Acta
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
                    
                    {{-- Buscador --}}
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
                
                {{-- Resultados con altura fija calculada --}}
                <div class="overflow-y-auto" style="height: calc(85vh - 120px);">
                    @if(strlen($busquedaMotivo) >= 3)
                        @if(count($motivosEncontrados) > 0)
                            {{-- Vista Mobile --}}
                            <div class="sm:hidden divide-y divide-gray-200">
                                @foreach($motivosEncontrados as $motivo)
                                    <button 
                                        type="button"
                                        wire:click="agregarMotivo({{ $motivo['id'] }})"
                                        class="w-full text-left p-3 hover:bg-gray-50 active:bg-gray-100"
                                    >
                                        <p class="text-sm font-medium text-gray-900 mb-1">{{ $motivo['nombre'] }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="text-xs px-2 py-0.5 rounded {{ $motivo['tipo'] == 'O' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $motivo['tipo'] == 'O' ? 'ORD' : 'LEY' }}
                                            </span>
                                            <span class="text-xs text-gray-600">{{ $motivo['ley'] }} - Art {{ $motivo['articulo'] }}</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            {{-- Vista Desktop --}}
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
                                                <td class="px-4 py-2">
                                                    <span class="text-xs px-2 py-0.5 rounded {{ $motivo['tipo'] == 'O' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                        {{ $motivo['tipo'] == 'O' ? 'ORD' : 'LEY' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-sm">{{ $motivo['ley'] }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $motivo['articulo'] }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <button 
                                                        type="button"
                                                        wire:click="agregarMotivo({{ $motivo['id'] }})"
                                                        class="text-xs px-3 py-1 bg-slate-600 text-white rounded-md hover:bg-slate-700"
                                                    >
                                                        Agregar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex items-center justify-center" style="height: calc(85vh - 120px);">
                                <div class="text-center px-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No se encontraron motivos</h3>
                                    <p class="mt-1 text-sm text-gray-500">Probá con otros términos</p>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-center" style="height: calc(85vh - 120px);">
                            <div class="text-center px-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Buscá un motivo</h3>
                                <p class="mt-1 text-sm text-gray-500">Escribí al menos 3 caracteres</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Scripts para manejo de secciones y scroll --}}
    <script>
        // Objeto para mantener el estado de las secciones
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

        // Mantener el estado de las secciones después de actualizaciones de Livewire
        document.addEventListener('livewire:updated', function() {
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

        document.addEventListener('livewire:init', () => {
            // Listener para abrir sección desde el componente
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

            // Listener para scroll automático
            Livewire.on('scroll-to-top', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</div>