<div class="min-h-screen bg-gray-50">
    {{-- Encabezado --}}
    <div class="bg-gradient-to-br from-[#5FB7C8] via-[#74C4D4] to-[#5FB7C8] shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Control de Operativo</h1>
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
    <div class="max-w-4xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
        
        {{-- Mensaje de éxito --}}
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-3 rounded">
                <p class="text-green-700 text-sm">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Info del Operativo --}}
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded mb-4">
            <p class="text-sm font-semibold text-emerald-900">
                Op. #{{ $operativo->id }}: {{ $operativo->descripcion }}
            </p>
            <p class="text-xs text-emerald-700 mt-0.5">
                {{ $operativo->lugar }} | Hasta: {{ $operativo->hora_hasta }}
            </p>
        </div>

        {{-- Card con opciones --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">¿Cómo procedés?</h2>
            </div>

            <div class="p-4">
                {{-- Botones principales --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    {{-- TODO EN REGLA --}}
                    <button
                        wire:click="toggleFormulario"
                        type="button"
                        class="bg-emerald-50 border-2 border-emerald-500 rounded-lg p-4 hover:bg-emerald-100 transition-colors"
                    >
                        <div class="flex flex-col items-center justify-center gap-2 h-full">
                            <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-emerald-900 text-sm">Todo en Regla</p>
                                <p class="text-xs text-emerald-700">Registrar</p>
                            </div>
                        </div>
                    </button>

                    {{-- LEVANTAR ACTA --}}
                    <a href="{{ route('actas.crear-operativo-acta', $operativoId) }}" class="bg-red-50 border-2 border-red-500 rounded-lg p-4 hover:bg-red-100 transition-colors block">
                        <div class="flex flex-col items-center justify-center gap-2 h-full">
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-red-900 text-sm">Labrar Acta</p>
                                <p class="text-xs text-red-700">Infracción</p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Formulario de registro (oculto por defecto) --}}
                <div class="@if(!$mostrarFormulario) hidden @endif border-t pt-4">
                    <p class="text-xs font-semibold text-gray-700 mb-3 text-center">
                        Datos del Control (Opcional)
                    </p>
                    
                    <form wire:submit.prevent="registrarControl" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">DNI</label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        wire:model.live.debounce.500ms="dni"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                        placeholder="DNI"
                                    >
                                    <div wire:loading wire:target="dni" class="absolute right-2 top-2">
                                        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Dominio</label>
                                <input 
                                    type="text" 
                                    wire:model="dominio"
                                    class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent uppercase"
                                    placeholder="ABC123"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nombre</label>
                            <input 
                                type="text" 
                                wire:model="nombreinf"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent uppercase"
                                placeholder="Nombre"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea 
                                wire:model="observaciones"
                                rows="2"
                                class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent uppercase"
                                placeholder="Obs..."
                            ></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button 
                                type="submit"
                                class="flex-1 px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors"
                            >
                                Registrar
                            </button>
                            <button 
                                type="button"
                                wire:click="toggleFormulario"
                                class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('scroll-to-top', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    @endscript

</div>