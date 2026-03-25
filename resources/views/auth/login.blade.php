<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Actas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 py-6">
        <div class="w-full max-w-md">
            {{-- Card Principal --}}
            <div class="bg-gradient-to-b from-slate-50 to-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] hover:shadow-[0_25px_70px_-15px_rgba(0,0,0,0.35)] transition-shadow duration-300 overflow-hidden">
                
                {{-- Header con Logo y Títulos --}}
                <div 
                    class="px-8 py-8 text-center
                        bg-gradient-to-br 
                        from-[#5FB7C8] 
                        via-[#74C4D4] 
                        to-[#5FB7C8]"
                    >   
                    {{-- Logo --}}
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg ring-4 ring-white/20">
                            <img 
                                src="{{ asset('images/icono-muni.png') }}" 
                                alt="Logo Municipalidad" 
                                class="w-16 h-16 object-contain"
                            >
                        </div>
                    </div>
                    
                    {{-- Títulos --}}
                    <h1 class="text-2xl font-bold text-white mb-1">
                        Secretaría de Seguridad
                    </h1>
                    <p class="text-slate-200 text-sm font-medium">
                        App de Inspectores
                    </p>
                </div>

                {{-- Formulario --}}
                <div class="px-8 py-8 bg-white">
                    {{-- Session Status --}}
                    @if (session('status'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-green-700 text-sm">{{ session('status') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Campo DNI --}}
                        <div>
                            <label for="dni" class="block text-sm font-semibold text-gray-700 mb-2">
                                DNI
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="dni" 
                                    type="number" 
                                    name="dni" 
                                    value="{{ old('dni') }}"
                                    inputmode="numeric" 
                                    pattern="[0-9]*"   
                                    required
                                    autofocus 
                                    autocomplete="username"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all @error('dni') border-red-500 @enderror"
                                    placeholder="Ingrese su DNI"
                                >
                            </div>
                            @error('dni')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Campo Contraseña --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                    placeholder="Ingrese su contraseña"
                                >
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Recordarme --}}
                        <div class="flex items-center justify-between pt-2">
                            <label for="remember_me" class="flex items-center cursor-pointer">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember"
                                    class="w-4 h-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500 cursor-pointer"
                                >
                                <span class="ml-2 text-sm text-gray-600 font-medium">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a 
                                    href="{{ route('password.request') }}" 
                                    class="text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botón Ingresar --}}
                        <button 
                            type="submit" 
                            class="w-full bg-gradient-to-br 
                                from-[#5FB7C8] 
                                via-[#74C4D4] 
                                to-[#5FB7C8]
                                hover:from-[#3FA6B8] 
                                hover:via-[#3FA6B8] 
                                hover:to-[#3FA6B8]
                                text-white font-semibold py-3 px-4 rounded-lg 
                                shadow-lg hover:shadow-xl 
                                transform hover:-translate-y-0.5 
                                transition-all duration-200 
                                flex items-center justify-center mt-6"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Ingresar al Sistema
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>