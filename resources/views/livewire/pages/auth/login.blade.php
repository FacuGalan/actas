<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Actas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-700 via-green-600 to-green-700 min-h-screen" style="background: linear-gradient(to bottom right, #5a9933, #77BF43, #5a9933);">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Card Principal --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                
                {{-- Header con Logo y Títulos --}}
                <div class="px-8 py-10 text-center" style="background-color: #77BF43;">
                    {{-- Logo --}}
                    <div class="flex justify-center mb-4">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                            {{-- Aquí irá el logo de la municipalidad --}}
                            <img 
                                src="{{ asset('images/logo-municipalidad.png') }}" 
                                alt="Logo Municipalidad" 
                                class="w-20 h-20 object-contain"
                            >
                        </div>
                    </div>
                    
                    {{-- Títulos --}}
                    <h1 class="text-2xl font-bold text-white mb-1">
                        Secretaría de Seguridad
                    </h1>
                    <p class="text-white text-opacity-90 text-sm font-medium">
                        App de Inspectores
                    </p>
                </div>

                {{-- Formulario --}}
                <div class="px-8 py-8">
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
                                    type="text" 
                                    name="dni" 
                                    value="{{ old('dni') }}"
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-all @error('dni') border-red-500 @enderror"
                                    style="focus:ring-color: #77BF43;"
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
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
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
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center cursor-pointer">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember"
                                    class="w-4 h-4 border-gray-300 rounded cursor-pointer"
                                    style="color: #77BF43;"
                                >
                                <span class="ml-2 text-sm text-gray-600 font-medium">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a 
                                    href="{{ route('password.request') }}" 
                                    class="text-sm font-medium transition-colors"
                                    style="color: #77BF43;"
                                    onmouseover="this.style.color='#5a9933'" 
                                    onmouseout="this.style.color='#77BF43'"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botón Ingresar --}}
                        <button 
                            type="submit" 
                            class="w-full text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center"
                            style="background-color: #77BF43;"
                            onmouseover="this.style.backgroundColor='#5a9933'" 
                            onmouseout="this.style.backgroundColor='#77BF43'"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Ingresar al Sistema
                        </button>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} Municipalidad - Todos los derechos reservados
                    </p>
                </div>
            </div>

            {{-- Texto adicional debajo --}}
            <div class="text-center mt-6">
                <p class="text-white text-sm">
                    Sistema de gestión de actas e infracciones
                </p>
            </div>
        </div>
    </div>
</body>
</html>