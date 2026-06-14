@extends('layouts.app')

@section('title', 'Todos los Eventos')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-4">Explorar Eventos</h1>
        
        <!-- Filtros -->
        <form action="{{ route('events.index') }}" method="GET" class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-gray-700 font-medium mb-2">Buscar</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        placeholder="Nombre o descripción"
                        value="{{ request('search') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600"
                    >
                </div>
                
                <div>
                    <label for="city" class="block text-gray-700 font-medium mb-2">Ciudad</label>
                    <select name="city" id="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600">
                        <option value="">Todas las ciudades</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="category_id" class="block text-gray-700 font-medium mb-2">Categoría</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de Eventos -->
    @if($events->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    @if($event->image_url)
                        <div class="h-48 overflow-hidden bg-gray-200">
                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                            <span class="text-white text-6xl">🎉</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full">
                                {{ $event->category->name }}
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold mb-2 line-clamp-2">{{ $event->title }}</h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $event->description }}</p>
                        
                        <div class="space-y-2 mb-4 text-sm text-gray-500">
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
                                <span>{{ $event->users()->count() }} / {{ $event->capacity }}</span>
                            </div>
                        </div>
                        
                        @if($event->reviews->count() > 0)
                            <div class="flex items-center gap-1 mb-4">
                                @php
                                    $avgRating = $event->reviews()->avg('rating');
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avgRating)
                                        <span class="text-yellow-400">★</span>
                                    @else
                                        <span class="text-gray-300">★</span>
                                    @endif
                                @endfor
                                <span class="text-sm text-gray-600 ml-2">({{ $event->reviews->count() }})</span>
                            </div>
                        @endif
                        
                        <a href="{{ route('events.show', $event) }}" class="block w-full bg-indigo-600 text-white py-2 rounded-lg font-bold text-center hover:bg-indigo-700 transition">
                            Ver Detalles
                        </a>
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
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-2xl font-bold mb-2">No hay eventos disponibles</h2>
            <p class="text-gray-600 mb-4">Intenta ajustar tus filtros de búsqueda</p>
            <a href="{{ route('events.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                Ver Todos los Eventos
            </a>
        </div>
    @endif
@endsection