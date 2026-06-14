@extends('layouts.app')

@section('title', 'Mis Eventos')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold">Mis Eventos</h1>
        <a href="{{ route('events.create') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
            + Crear Evento
        </a>
    </div>

    @if($events->count() > 0)
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    @if($event->image_url)
                        <div class="h-40 overflow-hidden bg-gray-200">
                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-40 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                            <span class="text-white text-5xl">🎉</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full">
                                {{ $event->category->name }}
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold mb-2">{{ $event->title }}</h3>
                        
                        <div class="space-y-2 mb-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <span>📅</span>
                                <span>{{ $event->date->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span>📍</span>
                                <span>{{ $event->city }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span>👥</span>
                                <span>{{ $event->users()->count() }} / {{ $event->capacity }} inscritos</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('events.show', $event) }}" class="bg-indigo-600 text-white py-2 rounded-lg font-bold text-center hover:bg-indigo-700 transition text-sm">
                                Ver
                            </a>
                            <a href="{{ route('events.edit', $event) }}" class="bg-blue-600 text-white py-2 rounded-lg font-bold text-center hover:bg-blue-700 transition text-sm">
                                Editar
                            </a>
                            <a href="{{ route('events.check-in', $event) }}" class="bg-green-600 text-white py-2 rounded-lg font-bold text-center hover:bg-green-700 transition text-sm">
                                Check-in
                            </a>
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    onclick="return confirm('¿Estás seguro?')"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg font-bold hover:bg-red-700 transition text-sm"
                                >
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $events->links() }}
        </div>
    @else
        <div class="bg-white p-12 rounded-lg text-center">
            <div class="text-6xl mb-4">📝</div>
            <h2 class="text-2xl font-bold mb-2">No tienes eventos creados</h2>
            <p class="text-gray-600 mb-4">¡Crea tu primer evento y comienza a compartir tus ideas!</p>
            <a href="{{ route('events.create') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                Crear Evento
            </a>
        </div>
    @endif
@endsection