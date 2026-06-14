@extends('layouts.app')

@section('title', 'Mis Entradas')

@section('content')
    <h1 class="text-4xl font-bold mb-8">Mis Entradas</h1>

    <!-- Próximos Eventos -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Próximos Eventos {{ $registrations->count() > 0 ? '(' . $registrations->count() . ')' : '' }}</h2>
        
        @if($registrations->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($registrations as $event)
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
                            <h3 class="text-lg font-bold mb-2">{{ $event->title }}</h3>
                            
                            <div class="space-y-2 mb-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span>📅</span>
                                    <span>{{ $event->date->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span>📍</span>
                                    <span>{{ $event->city }}</span>
                                </div>
                            </div>

                            <!-- Código de entrada -->
                            @php
                                $registration = $event->users()->wherePivot('user_id', auth()->id())->first();
                                $code = $registration->pivot->unique_code ?? null;
                            @endphp
                            
                            @if($code)
                                <div class="bg-indigo-50 border border-indigo-200 p-3 rounded mb-4 font-mono text-center text-sm">
                                    <p class="text-gray-600 text-xs mb-1">Tu código de entrada:</p>
                                    <p class="font-bold text-indigo-600 break-all">{{ $code }}</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('events.show', $event) }}" class="flex-1 bg-indigo-600 text-white py-2 rounded-lg font-bold text-center hover:bg-indigo-700 transition text-sm">
                                    Ver Detalles
                                </a>
                                <form action="{{ route('events.cancel-registration', $event) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" onclick="return confirm('¿Deseas desinscribirse?')" class="w-full bg-red-600 text-white py-2 rounded-lg font-bold hover:bg-red-700 transition text-sm">
                                        Desinscribirse
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white p-12 rounded-lg text-center">
                <div class="text-6xl mb-4">🎫</div>
                <h3 class="text-2xl font-bold mb-2">No tienes entradas próximas</h3>
                <p class="text-gray-600 mb-4">¡Explora eventos y regístrate en uno que te interese!</p>
                <a href="{{ route('events.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                    Explorar Eventos
                </a>
            </div>
        @endif
    </div>

    <!-- Eventos Pasados -->
    <div>
        <h2 class="text-2xl font-bold mb-6">Eventos Que Asististe {{ $attended->count() > 0 ? '(' . $attended->count() . ')' : '' }}</h2>
        
        @if($attended->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($attended as $event)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden border-2 border-green-500">
                        @if($event->image_url)
                            <div class="h-40 overflow-hidden bg-gray-200">
                                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
                                <span class="text-white text-5xl">✅</span>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="mb-2">
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">
                                    Asististe
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-bold mb-2">{{ $event->title }}</h3>
                            
                            <div class="space-y-2 mb-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span>📅</span>
                                    <span>{{ $event->date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span>📍</span>
                                    <span>{{ $event->city }}</span>
                                </div>
                            </div>

                            <a href="{{ route('events.show', $event) }}" class="block w-full bg-green-600 text-white py-2 rounded-lg font-bold text-center hover:bg-green-700 transition text-sm">
                                Ver Evento
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 p-8 rounded-lg text-center">
                <p class="text-gray-600">Aún no has asistido a ningún evento</p>
            </div>
        @endif
    </div>
@endsection