@extends('layouts.app')

@section('title', 'Check-in: ' . $event->title)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 hover:text-indigo-700">← Volver al evento</a>
            <h1 class="text-4xl font-bold mt-2">Check-in: {{ $event->title }}</h1>
            <p class="text-gray-600">{{ $event->date->format('d/m/Y H:i') }} • {{ $event->city }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Formulario de Check-in -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md sticky top-24">
                    <h2 class="text-xl font-bold mb-4">Validar Código</h2>
                    
                    <form action="{{ route('events.check-in-store', $event) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="code" class="block text-gray-700 font-medium mb-2">Código de Entrada</label>
                            <input 
                                type="text" 
                                name="code" 
                                id="code"
                                placeholder="Ej: TKT-XXXXXXXXXX"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 font-mono @error('code') border-red-500 @enderror"
                                autofocus
                                required
                            >
                            @error('code')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <button 
                            type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700 transition"
                        >
                            Validar
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm text-gray-600 font-medium mb-2">Estadísticas</p>
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="text-gray-600">Total inscrito:</span>
                                <span class="font-bold">{{ $registrations->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Check-in realizado:</span>
                                <span class="font-bold text-green-600">{{ $registrations->where('pivot.checked_in', true)->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Pendiente:</span>
                                <span class="font-bold text-yellow-600">{{ $registrations->where('pivot.checked_in', false)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Asistentes -->
            <div class="md:col-span-2">
                <h2 class="text-2xl font-bold mb-6">Asistentes Registrados</h2>
                
                @if($registrations->count() > 0)
                    <div class="space-y-3">
                        @foreach($registrations as $registration)
                            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 {{ $registration->pivot->checked_in ? 'border-green-500' : 'border-yellow-500' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg">{{ $registration->name }}</h3>
                                        <p class="text-gray-600 text-sm">{{ $registration->email }}</p>
                                        <p class="text-gray-500 text-xs font-mono mt-2">{{ $registration->pivot->unique_code }}</p>
                                    </div>
                                    <div class="text-right">
                                        @if($registration->pivot->checked_in)
                                            <span class="inline-block bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">
                                                ✓ Check-in
                                            </span>
                                        @else
                                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">
                                                ⏳ Pendiente
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-12 rounded-lg text-center">
                        <p class="text-gray-600">No hay asistentes registrados aún</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection