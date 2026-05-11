<div class="min-h-screen bg-gray-50">
    {{-- Encabezado --}}
    <div class="bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Mis Actas</h1>
                    <p class="text-white/90 text-sm mt-1">
                        {{ auth('inspector')->user()->nombre }} {{ auth('inspector')->user()->apellido }}
                    </p>
                </div>
                <a href="{{ route('actas.dashboard') }}" class="text-white/90 hover:text-white text-sm font-medium transition-colors">
                    Volver
                </a>
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

        {{-- Tabla de Actas --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Actas Realizadas</h2>
                        <p class="text-sm text-gray-600 mt-0.5">Total: {{ $actas->total() }} actas</p>
                    </div>
                </div>
            </div>

            @if($actas->count() > 0)
                {{-- Vista Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acta Nº</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operativo</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infractor</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($actas as $acta)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $acta->actanro }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($acta->operativo_id)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                Op. #{{ $acta->operativo_id }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Simple</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $acta->fecha->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $acta->hora }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-900">{{ $acta->nombreinf ?: '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">{{ $acta->dni ?: '-' }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">{{ $acta->lugarinfra }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                wire:click="editarActa({{ $acta->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-slate-600 text-white text-xs font-medium rounded-md hover:bg-slate-700 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Editar
                                            </button>
                                            <button 
                                                onclick="confirmarEliminacion({{ $acta->id }}, '{{ $acta->actanro }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Vista Mobile --}}
                <div class="md:hidden divide-y divide-gray-200">
                    @foreach($actas as $acta)
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-900">Acta {{ $acta->actanro }}</span>
                                    @if($acta->operativo_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Op. #{{ $acta->operativo_id }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Simple
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 whitespace-nowrap ml-2">{{ $acta->fecha->format('d/m/Y') }}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 mb-3 text-sm">
                                <div>
                                    <span class="text-gray-500 text-xs">Hora:</span>
                                    <span class="text-gray-900 ml-1">{{ $acta->hora }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">DNI:</span>
                                    <span class="text-gray-900 ml-1">{{ $acta->dni ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">Infractor:</span>
                                    <span class="text-gray-900 ml-1">{{ $acta->nombreinf ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs">Lugar:</span>
                                    <span class="text-gray-900 ml-1 break-words">{{ $acta->lugarinfra }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                                <button
                                    wire:click="editarActa({{ $acta->id }})"
                                    class="flex items-center justify-center gap-1 px-3 py-2 bg-[#6FAFBF] text-white text-sm font-medium rounded-lg hover:bg-[#5A9EAE] transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>

                                <button
                                    onclick="confirmarEliminacion({{ $acta->id }}, '{{ $acta->actanro }}')"
                                    class="flex items-center justify-center gap-1 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $actas->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay actas</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva acta.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmarEliminacion(actaId, actaNro) {
            Swal.fire({
                title: '¿Eliminar acta?',
                html: `¿Está seguro que desea eliminar la <strong>Acta ${actaNro}</strong>?<br><span class="text-sm text-gray-500">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg font-semibold px-5 py-2.5',
                    cancelButton: 'rounded-lg font-semibold px-5 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('confirmarEliminacion', actaId);
                }
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('actaEliminada', () => {
                Swal.fire({
                    title: '¡Eliminada!',
                    text: 'El acta ha sido eliminada exitosamente.',
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

            Livewire.on('acta-no-modificable', () => {
                Swal.fire({
                    title: 'Acta no disponible',
                    text: 'Esta acta ya no puede ser modificada ni eliminada.',
                    icon: 'warning',
                    confirmButtonColor: '#475569',
                    confirmButtonText: 'Aceptar',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg font-semibold px-5 py-2.5'
                    }
                });
            });
        });
    </script>
</div>