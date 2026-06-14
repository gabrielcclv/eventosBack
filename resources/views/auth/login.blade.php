@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h1>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('password') border-red-500 @enderror"
                    required
                >
                @error('password')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <button 
                type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition"
            >
                Iniciar Sesión
            </button>

            <div class="mt-4 text-center">
                <p class="text-gray-600">
                    ¿No tienes cuenta? 
                    <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">Registrarse</a>
                </p>
            </div>
        </form>
    </div>
@endsection