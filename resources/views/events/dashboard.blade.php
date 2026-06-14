@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold">Bienvenido, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mt-2">
            @if(auth()->user()->is_organizer)
                Eres organizador • 
            @endif
            Última sesión: hace poco
        </p>
    </div>

    <!-- Estadísticas -->
    <div class="grid md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-4xl mb-2">🎉</div>
            <p class="text-gray-600 mb-1">Próximos Eventos</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $upcomingEvents }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="text-4xl mb-2">✅</div>
            <p class="text-gray-600 mb-1">Eventos Asistidos</p>
            <p class="text-3xl font-bold text-green-600">{{ $attendedEvents }}</p>
        </div>

        @if(auth()->user()->is_organizer)
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-4xl mb-2">📝</div>
                <p class="text-gray-600 mb-1">Mis Eventos</p>
                <p class="text-3xl font-bold text-purple-600">{{ $myEvents }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-4xl mb-2">👥</div>
                <p class="text-gray-600 mb-1">Inscripciones Totales</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalRegistrations }}</p>
            </div>
        @else
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-4xl mb-2">⭐</div>
                <p class="text-gray-600 mb-1">Estado</p>
                <p class="text-lg font-bold text-yellow-600">Usuario Regular</p>
            </div>
        @endif
    </div>

    <!-- Acciones Rápidas -->
    <div class="grid md:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('events.index') }}" class="bg-indigo-100 border-2 border-indigo-600 p-6 rounded-lg hover:shadow-lg transition text-center">
            <div class="text-4xl mb-2">🔍</div>
            <h3 class="font-bold text-lg mb-1">Explorar Eventos</h3>
            <p class="text-gray-600 text-sm">Descubre nuevos eventos</p>
        </a>

        @if(auth()->user()->is_organizer)
            <a href="{{ route('events.create') }}" class="bg-green-100 border-2 border-green-600 p-6 rounded-lg hover:shadow-lg transition text-center">
                <div class="text-4xl mb-2">➕</div>
                <h3 class="font-bold text-lg mb-1">Crear Evento</h3>
                <p class="text-gray-600 text-sm">Organiza un nuevo evento</p>
            </a>

            <a href="{{ route('my-events') }}" class="bg-purple-100 border-2 border-purple-600 p-6 rounded-lg hover:shadow-lg transition text-center">
                <div class="text-4xl mb-2">📋</div>
                <h3 class="font-bold text-lg mb-1">Mis Eventos</h3>
                <p class="text-gray-600 text-sm">Gestiona tus eventos</p>
            </a>
        @else
            <a href="{{ route('tickets') }}" class="bg-green-100 border-2 border-green-600 p-6 rounded-lg hover:shadow-lg transition text-center">
                <div class="text-4xl mb-2">🎫</div>
                <h3 class="font-bold text-lg mb-1">Mis Entradas</h3>
                <p class="text-gray-600 text-sm">Ver tus entradas</p>
            </a>

            <form action="{{ route('become-organizer') }}" method="POST" class="bg-yellow-100 border-2 border-yellow-600 p-6 rounded-lg hover:shadow-lg transition text-center">
                @csrf
                <button type="submit" class="w-full h-full flex flex-col items-center justify-center">
                    <div class="text-4xl mb-2">👤</div>
                    <h3 class="font-bold text-lg mb-1">Ser Organizador</h3>
                    <p class="text-gray-600 text-sm">Crea y gestiona eventos</p>
                </button>
            </form>
        @endif
    </div>

    <!-- Sugerencias -->
    <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg">
        <h3 class="text-lg font-bold mb-2">💡 Consejo</h3>
        @if(auth()->user()->is_organizer)
            <p class="text-gray-700">
                Haz que tus eventos sean atractivos con buenas descripciones, imágenes y ajusta la capacidad según tus necesidades. 
                Así atraerás más asistentes.
            </p>
        @else
            <p class="text-gray-700">
                ¿Quieres organizar eventos? Conviértete en organizador y comienza a crear experiencias memorable para otros.
            </p>
        @endif
    </div>
@endsection