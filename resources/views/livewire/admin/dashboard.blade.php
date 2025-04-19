{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div> {{-- Usando el mismo layout base por ahora --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }} {{-- Título diferente --}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                {{-- Contenido específico del Admin --}}
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-medium text-gray-900">
                        ¡Bienvenido al Panel de Administración!
                    </h1>

                    <p class="mt-4 text-gray-500 leading-relaxed">
                        Desde aquí podrás gestionar los diferentes aspectos de la plataforma.
                    </p>

                    {{-- Ejemplo de Widgets/Stats --}}
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Widget Usuarios --}}
                        <div class="bg-gray-100 p-6 rounded-lg shadow">
                            <h3 class="font-semibold text-lg text-gray-700">Clientes Registrados</h3>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $userCount }}</p>
                            {{-- <a href="#" class="text-sm text-indigo-500 hover:underline mt-1 block">Gestionar Usuarios</a> --}}
                        </div>
                        {{-- Widget Álbumes --}}
                        <div class="bg-gray-100 p-6 rounded-lg shadow">
                            <h3 class="font-semibold text-lg text-gray-700">Álbumes Creados</h3>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $albumCount }}</p>
                             <a href="{{ route('albums') }}" class="text-sm text-indigo-500 hover:underline mt-1 block">Ver Álbumes</a>
                        </div>
                        {{-- Widget Pedidos (Ejemplo) --}}
                        <div class="bg-gray-100 p-6 rounded-lg shadow">
                             <h3 class="font-semibold text-lg text-gray-700">Pedidos Pendientes</h3>
                             <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $orderCount }}</p>
                             {{-- <a href="#" class="text-sm text-indigo-500 hover:underline mt-1 block">Gestionar Pedidos</a> --}}
                         </div>
                    </div>

                    {{-- Enlaces a Secciones de Admin --}}
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Accesos Rápidos</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('admin.homepage.carousel') }}" class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg text-green-700 font-medium transition-colors">
                                Gestionar Carrusel Inicio
                            </a>
                            <a href="{{ route('admin.user.likes') }}" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition-colors">
                                Ver Likes por Cliente
                            </a>
                            <a href="{{ route('albums') }}" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition-colors">
                                Gestionar Álbumes
                            </a>
                            {{-- Añade más enlaces a medida que crees las secciones --}}
                             {{-- <a href="#" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition-colors">Gestionar Usuarios</a> --}}
                             {{-- <a href="#" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 font-medium transition-colors">Gestionar Pedidos</a> --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
