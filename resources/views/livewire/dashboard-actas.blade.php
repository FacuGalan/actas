<div class="min-h-screen bg-gray-50">
    {{-- Encabezado Principal --}}
    <div 
        class="bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        Sistema de Actas
                    </h1>

                    <p class="text-white/90 text-sm mt-1">
                        {{ auth('inspector')->user()->nombre }} 
                        {{ auth('inspector')->user()->apellido }} 
                        - DNI: {{ auth('inspector')->user()->dni }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="text-white/90 hover:text-white text-sm font-medium transition-colors">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        
        {{-- Mensaje de éxito --}}
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Botones de Acción - Ahora todos en columna --}}
        <div class="space-y-4 mb-6">
            
            {{-- Botón: Crear Acta Simple --}}
            <a 
                href="{{ route('actas.crear-simple') }}"
                class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden border border-gray-200 block"
            >
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 bg-slate-100 p-3 rounded-lg group-hover:bg-slate-200 transition-colors">
                            <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-slate-700 transition-colors">Crear Acta Simple</h3>
                            <p class="text-sm text-gray-600 mt-0.5">Registrar acta en cualquier momento</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-slate-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </a>

           {{-- Botón Operativo EN CURSO (para inspectores asignados y referente) --}}
            @if($operativoEnCurso)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-emerald-50 px-5 py-3 border-b border-emerald-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-sm font-semibold text-emerald-900">OPERATIVO EN CURSO</span>
                        </div>
                        @if($esReferente)
                            <span class="text-xs bg-[#74C4D4] text-white font-bold tracking-wide px-2 py-1 rounded shadow-sm">
                                REFERENTE
                            </span>
                        @endif
                    </div>
                    
                    <a href="{{ route('actas.crear-operativo', $operativoEnCurso->id) }}" class="block p-5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 bg-emerald-100 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $operativoEnCurso->descripcion }}</h3>
                                    <p class="text-sm text-gray-600">{{ $operativoEnCurso->lugar }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Iniciado: {{ $operativoEnCurso->hora_apertura_real }}
                                    </p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                    
                    {{-- Botón finalizar (solo para inspector referente) --}}
                    @if($esReferente)
                    <div class="px-5 pb-4 border-t border-gray-200 pt-4">
                        <button 
                            onclick="confirmarFinalizacionOperativo({{ $operativoEnCurso->id }}, '{{ $operativoEnCurso->descripcion }}')"
                            class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Finalizar Operativo
                        </button>
                    </div>
                    @endif
                </div>
            @endif

            {{-- Operativo PLANIFICADO (solo para inspector referente) --}}
            @if($operativoPlanificado)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-blue-50 px-5 py-3 border-b border-blue-200">
                        <span class="text-sm font-semibold text-blue-900">OPERATIVO PLANIFICADO</span>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-900">{{ $operativoPlanificado->descripcion }}</h3>
                                <p class="text-sm text-gray-600">{{ $operativoPlanificado->lugar }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Fecha: {{ $operativoPlanificado->fecha->format('d/m/Y') }} | 
                                    Horario: {{ $operativoPlanificado->hora_desde }} - {{ $operativoPlanificado->hora_hasta }}
                                </p>
                            </div>
                        </div>
                        
                        <button 
                            wire:click="abrirModalInicio({{ $operativoPlanificado->id }})"
                            class="w-full px-4 py-3 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors shadow-md"
                        >
                            Iniciar Operativo
                        </button>
                    </div>
                </div>
            @endif

            {{-- Botón Ver Mis Actas --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <a href="{{ route('actas.listar') }}" class="block p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 bg-slate-100 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Ver Mis Actas</h3>
                                <p class="text-sm text-gray-600 mt-0.5">Actas pendientes y realizadas</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Modal: Confirmar Inicio de Operativo --}}
    @if($mostrarModalInicio)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" wire:click="cerrarModalInicio">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden" wire:click.stop>
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-emerald-900">Iniciar Operativo</h3>
                        <button wire:click="cerrarModalInicio" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @if($operativoIniciar)
                        <p class="text-sm text-emerald-700 mt-1">{{ $operativoIniciar->descripcion }}</p>
                    @endif
                </div>
                
                {{-- Contenido --}}
                <div class="overflow-y-auto p-6" style="max-height: calc(90vh - 180px)">
                    
                    {{-- Mensaje de error --}}
                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded">
                            <p class="text-red-700 text-sm">{{ session('error') }}</p>
                        </div>
                    @endif

                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Inspectores Asignados</h4>
                    
                    {{-- Lista de Inspectores --}}
                    <div class="space-y-3 mb-6">
                        @foreach($inspectoresAsignados as $index => $inspector)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $inspector['estado'] === 'ausente' ? 'bg-red-50 border-red-200' : 'bg-white' }}">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $inspector['nombre'] }}</p>
                                        <p class="text-xs text-gray-600">DNI: {{ $inspector['dni'] }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button 
                                            wire:click="actualizarEstadoInspector({{ $index }}, 'presente')"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $inspector['estado'] === 'presente' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        >
                                            Presente
                                        </button>
                                        <button 
                                            wire:click="actualizarEstadoInspector({{ $index }}, 'ausente')"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $inspector['estado'] === 'ausente' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                        >
                                            Ausente
                                        </button>
                                    </div>
                                </div>
                                
                                @if($inspector['estado'] === 'ausente')
                                    <div class="mt-2">
                                        <input 
                                            type="text" 
                                            wire:model="inspectoresAsignados.{{ $index }}.observacion"
                                            class="w-full px-3 py-2 text-sm border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                            placeholder="Motivo de la ausencia (requerido)"
                                        >
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Acompañamiento Policial --}}
                    <div class="border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Acompañamiento Policial</label>
                        <textarea 
                            wire:model="acompanamiento_policial"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm uppercase"
                            placeholder="Detalle del acompañamiento policial presente..."
                        ></textarea>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                    <button 
                        wire:click="cerrarModalInicio"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button 
                        wire:click="confirmarInicioOperativo"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors"
                    >
                        Confirmar e Iniciar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script de confirmación para finalizar operativo --}}
    <script>
        function confirmarFinalizacionOperativo(operativoId, descripcion) {
            Swal.fire({
                title: '¿Finalizar operativo?',
                html: `¿Está seguro que desea finalizar el operativo <strong>"${descripcion}"</strong>?<br><br><span class="text-sm text-gray-500">Una vez finalizado, no se podrán crear más actas en este operativo.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg font-semibold px-5 py-2.5',
                    cancelButton: 'rounded-lg font-semibold px-5 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('confirmarFinalizacion', operativoId);
                }
            });
        }

        // Escuchar evento de finalización exitosa
        document.addEventListener('livewire:init', () => {
            Livewire.on('operativoFinalizado', () => {
                Swal.fire({
                    title: '¡Finalizado!',
                    text: 'El operativo ha sido finalizado exitosamente.',
                    icon: 'success',
                    confirmButtonColor: '#475569',
                    confirmButtonText: 'Aceptar',
                    timer: 2000,
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg font-semibold px-5 py-2.5'
                    }
                });
            });
        });
    </script>
</div>