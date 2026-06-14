@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Contenido Principal -->
        <div class="md:col-span-2">
            <!-- Imagen -->
            @if($event->image_url)
                <div class="mb-8 rounded-lg overflow-hidden h-96 bg-gray-200">
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="mb-8 rounded-lg overflow-hidden h-96 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                    <span class="text-white text-9xl">🎉</span>
                </div>
            @endif

            <!-- Detalles -->
            <div class="bg-white p-8 rounded-lg shadow-md mb-8">
                <div class="mb-4">
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-sm font-bold px-4 py-2 rounded-full">
                        {{ $event->category->name }}
                    </span>
                </div>

                <h1 class="text-4xl font-bold mb-4">{{ $event->title }}</h1>

                <div class="grid grid-cols-2 gap-4 mb-8 text-gray-700">
                    <div>
                        <p class="font-medium">📅 Fecha</p>
                        <p>{{ $event->date->format('d/m/Y') }}</p>
                        <p>{{ $event->date->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-medium">📍 Ciudad</p>
                        <p>{{ $event->city }}</p>
                    </div>
                    <div>
                        <p class="font-medium">👥 Capacidad</p>
                        <p>{{ $event->users()->count() }} / {{ $event->capacity }} asistentes</p>
                    </div>
                    <div>
                        <p class="font-medium">👤 Organizador</p>
                        <p>{{ $event->organizer->name }}</p>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Descripción</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $event->description }}</p>
                </div>

                <!-- Reseñas -->
                @if($event->reviews->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Reseñas ({{ $event->reviews->count() }})</h2>
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $avgRating)
                                        <span class="text-yellow-400 text-2xl">★</span>
                                    @else
                                        <span class="text-gray-300 text-2xl">★</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xl font-bold">{{ round($avgRating, 1) }}/5</span>
                        </div>

                        <div class="space-y-4">
                            @foreach($event->reviews as $review)
                                <div class="border-l-4 border-indigo-600 pl-4 py-2">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-bold">{{ $review->user->name }}</p>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Card de Inscripción -->
            <div class="bg-white p-8 rounded-lg shadow-md sticky top-24">
                <div class="mb-6">
                    @if($event->users()->count() >= $event->capacity)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <p class="font-bold">Evento Lleno</p>
                            <p class="text-sm">Ya no hay lugares disponibles</p>
                        </div>
                    @else
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            <p class="text-sm">{{ $event->capacity - $event->users()->count() }} lugares disponibles</p>
                        </div>
                    @endif
                </div>

                @auth
                    @if($isRegistered)
                        <form action="{{ route('events.cancel-registration', $event) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-bold hover:bg-red-700 transition">
                                Desinscribirse
                            </button>
                        </form>
                        
                        <a href="{{ route('tickets') }}" class="block w-full bg-gray-600 text-white py-2 rounded-lg font-bold text-center hover:bg-gray-700 transition">
                            Ver Mi Entrada
                        </a>

                        <!-- Dejar Reseña -->
                        @php
                            $userReview = $event->reviews()->where('user_id', auth()->id())->first();
                        @endphp
                        
                        @if(!$userReview)
                            <form action="{{ route('events.review', $event) }}" method="POST" class="mt-6 pt-6 border-t">
                                @csrf
                                <h3 class="font-bold mb-3">Dejar Reseña</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Calificación</label>
                                    <div class="flex gap-2" id="ratingStars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $i }}" class="hidden" required>
                                                <span class="text-3xl text-gray-300 hover:text-yellow-400 transition star" data-value="{{ $i }}">★</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Comentario (Opcional)</label>
                                    <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-600"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                                    Enviar Reseña
                                </button>
                            </form>
                        @else
                            <div class="mt-6 pt-6 border-t bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm font-medium">Tu reseña:</p>
                                <div class="flex my-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                                @if($userReview->comment)
                                    <p class="text-gray-700 text-sm">{{ $userReview->comment }}</p>
                                @endif
                            </div>
                        @endif
                    @else
                        <form action="{{ route('events.register', $event) }}" method="POST">
                            @csrf
                            <button 
                                type="submit"
                                @disabled($event->users()->count() >= $event->capacity)
                                class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold hover:bg-indigo-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
                            >
                                Inscribirse al Evento
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white py-2 rounded-lg font-bold text-center hover:bg-indigo-700 transition">
                        Inicia Sesión para Inscribirse
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.querySelector('input[name="rating"]');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                document.querySelector(`input[value="${value}"]`).checked = true;
                
                stars.forEach(s => {
                    if (s.dataset.value <= value) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.add('text-gray-300');
                        s.classList.remove('text-yellow-400');
                    }
                });
            });

            star.addEventListener('mouseover', function() {
                const value = this.dataset.value;
                stars.forEach(s => {
                    if (s.dataset.value <= value) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.add('text-gray-300');
                        s.classList.remove('text-yellow-400');
                    }
                });
            });
        });

        document.getElementById('ratingStars').addEventListener('mouseout', function() {
            const checked = document.querySelector('input[name="rating"]:checked');
            if (checked) {
                const value = checked.value;
                stars.forEach(s => {
                    if (s.dataset.value <= value) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.add('text-gray-300');
                        s.classList.remove('text-yellow-400');
                    }
                });
            } else {
                stars.forEach(s => {
                    s.classList.add('text-gray-300');
                    s.classList.remove('text-yellow-400');
                });
            }
        });
    </script>
@endsection