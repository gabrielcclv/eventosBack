@extends('layouts.app')

@section('title', 'Editar Evento')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">Editar Evento</h1>

        <form action="{{ route('events.update', $event) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-gray-700 font-medium mb-2">Título del Evento *</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title"
                    placeholder="Ej: Festival de Música 2026"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('title') border-red-500 @enderror"
                    value="{{ old('title', $event->title) }}"
                    required
                >
                @error('title')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-gray-700 font-medium mb-2">Descripción *</label>
                <textarea 
                    name="description" 
                    id="description"
                    placeholder="Describe tu evento en detalle..."
                    rows="5"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('description') border-red-500 @enderror"
                    required
                >{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-gray-700 font-medium mb-2">Fecha y Hora *</label>
                    <input 
                        type="datetime-local" 
                        name="date" 
                        id="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('date') border-red-500 @enderror"
                        value="{{ old('date', $event->date->format('Y-m-d\TH:i')) }}"
                        required
                    >
                    @error('date')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-gray-700 font-medium mb-2">Ciudad *</label>
                    <input 
                        type="text" 
                        name="city" 
                        id="city"
                        placeholder="Ej: Madrid"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('city') border-red-500 @enderror"
                        value="{{ old('city', $event->city) }}"
                        required
                    >
                    @error('city')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="capacity" class="block text-gray-700 font-medium mb-2">Capacidad Máxima *</label>
                    <input 
                        type="number" 
                        name="capacity" 
                        id="capacity"
                        placeholder="Número máximo de asistentes"
                        min="1"
                        max="100000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('capacity') border-red-500 @enderror"
                        value="{{ old('capacity', $event->capacity) }}"
                        required
                    >
                    @error('capacity')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-gray-700 font-medium mb-2">Categoría *</label>
                    <select 
                        name="category_id" 
                        id="category_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('category_id') border-red-500 @enderror"
                        required
                    >
                        <option value="">Selecciona una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image_url" class="block text-gray-700 font-medium mb-2">URL de Imagen (Opcional)</label>
                <input 
                    type="url" 
                    name="image_url" 
                    id="image_url"
                    placeholder="https://ejemplo.com/imagen.jpg"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600 @error('image_url') border-red-500 @enderror"
                    value="{{ old('image_url', $event->image_url) }}"
                >
                @error('image_url')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
                <p class="text-gray-500 text-sm mt-1">Proporciona la URL completa de la imagen que deseas usar</p>
            </div>

            <div class="flex gap-4">
                <button 
                    type="submit"
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition"
                >
                    Guardar Cambios
                </button>
                <a 
                    href="{{ route('events.show', $event) }}"
                    class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-bold text-center hover:bg-gray-700 transition"
                >
                    Cancelar
                </a>
            </div>

            <!-- Opción para cancelar evento -->
            <form action="{{ route('events.destroy', $event) }}" method="POST" class="pt-4 border-t">
                @csrf
                @method('DELETE')
                <button 
                    type="submit"
                    onclick="return confirm('¿Estás seguro de que deseas cancelar este evento? Se notificará a todos los participantes.')"
                    class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition"
                >
                    Cancelar Evento
                </button>
            </form>
        </form>
    </div>
@endsection