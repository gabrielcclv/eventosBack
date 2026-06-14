@extends('layouts.app')

@section('title', 'Bienvenido a EventHub')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg py-16 mb-12">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-4">¡Bienvenido a EventHub!</h1>
            <p class="text-xl mb-8">Descubre, organiza y participa en los mejores eventos</p>
            @auth
                <a href="{{ route('events.index') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                    Explorar Eventos
                </a>
            @else
                <div class="space-x-4">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        Registrarse Gratis
                    </a>
                    <a href="{{ route('login') }}" class="inline-block border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-indigo-600 transition">
                        Iniciar Sesión
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-4xl mb-4">🎉</div>
            <h3 class="text-xl font-bold mb-2">Descubre Eventos</h3>
            <p class="text-gray-600">Encuentra eventos interesantes en tu ciudad y categoría favorita.</p>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-4xl mb-4">📝</div>
            <h3 class="text-xl font-bold mb-2">Crea Eventos</h3>
            <p class="text-gray-600">Organiza tus propios eventos y gestiona a los asistentes fácilmente.</p>
        </div>
        <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
            <div class="text-4xl mb-4">⭐</div>
            <h3 class="text-xl font-bold mb-2">Comparte tu Experiencia</h3>
            <p class="text-gray-600">Deja reseñas y ayuda a otros a encontrar los mejores eventos.</p>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gray-100 p-12 rounded-lg text-center">
        <h2 class="text-3xl font-bold mb-4">¿Listo para comenzar?</h2>
        @auth
            @if(auth()->user()->is_organizer)
                <a href="{{ route('events.create') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                    Crear Tu Primer Evento
                </a>
            @else
                <div class="space-x-4">
                    <a href="{{ route('events.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                        Explorar Eventos
                    </a>
                    <form action="{{ route('become-organizer') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition">
                            Ser Organizador
                        </button>
                    </form>
                </div>
            @endif
        @else
            <p class="text-gray-600 mb-4">Únete a miles de usuarios que disfrutan EventHub</p>
            <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                Registrarse Ahora
            </a>
        @endauth
    </div>
@endsection