<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EventHub - Gestión de Eventos')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold text-indigo-600">
                        🎉 EventHub
                    </a>
                </div>
                
                <div class="hidden md:flex items-center gap-8">
                    @auth
                        <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Eventos</a>
                        
                        @if(auth()->user()->is_organizer)
                            <a href="{{ route('my-events') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Mis Eventos</a>
                            <a href="{{ route('events.create') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Crear Evento</a>
                        @else
                            <form action="{{ route('become-organizer') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-indigo-600 font-medium">Ser Organizador</button>
                            </form>
                        @endif
                        
                        <a href="{{ route('tickets') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Mis Entradas</a>
                    @else
                        <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Explorar</a>
                    @endauth
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        <div class="hidden md:flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                @if(auth()->user()->is_organizer)
                                    <p class="text-xs text-indigo-600">Organizador</p>
                                @endif
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-700 transition">Salir</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-4 gap-8 mb-4">
                <div>
                    <h3 class="text-white font-bold mb-3">EventHub</h3>
                    <p class="text-sm">La mejor plataforma para organizar y disfrutar eventos.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-3">Plataforma</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">Explorar Eventos</a></li>
                        <li><a href="#" class="hover:text-white">Crear Evento</a></li>
                        <li><a href="#" class="hover:text-white">Ser Organizador</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-3">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white">Términos de Servicio</a></li>
                        <li><a href="#" class="hover:text-white">Privacidad</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-3">Contacto</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Email: info@eventhub.com</li>
                        <li>Teléfono: +1 (555) 123-4567</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-4 text-center text-sm">
                <p>&copy; 2026 EventHub. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>