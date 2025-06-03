{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div class="bg-gray-100 dark:bg-gray-900"> {{-- Cambiado bg-gray-700 a un color de fondo más estándar para dashboards --}}

    {{-- Modal para el carrusel (existente) --}}
    @livewire('admin.manage-homepage-carousel')

    {{-- >>> Modal para Campañas de Email (el nuevo componente) <<< --}}
    {{-- Asegúrate que el alias es correcto según el nombre de tu clase PHP --}}
    {{-- Si la clase es AdminEmailCampaignCreator, el alias sería admin.admin-email-campaign-creator --}}
    {{-- Si la clase es EmailCampaignCreator (en App\Livewire\Admin), el alias es admin.email-campaign-creator --}}
    @livewire('admin.email-campaign-creator')


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Sección de Bienvenida y Resumen --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <svg class="h-12 w-12 text-indigo-600 dark:text-indigo-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900 dark:text-gray-100">
                                ¡Bienvenido al Panel de Administración!
                            </h1>
                            <p class="mt-1 text-gray-500 dark:text-gray-400 leading-relaxed">
                                Desde aquí podrás gestionar los diferentes aspectos de la plataforma.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Widgets de Estadísticas --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 p-6 lg:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Widget Usuarios --}}
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-200">
                            <div class="flex-shrink-0 bg-indigo-500 dark:bg-indigo-600 p-3 rounded-full">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-700 dark:text-gray-300">Clientes Registrados</h3>
                                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $userCount }}</p>
                            </div>
                        </div>

                        {{-- Widget Álbumes --}}
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-200">
                             <div class="flex-shrink-0 bg-green-500 dark:bg-green-600 p-3 rounded-full">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-700 dark:text-gray-300">Álbumes Creados</h3>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $albumCount }}</p>
                                <a href="{{ route('albums') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline mt-1 block">Ver Álbumes</a>
                            </div>
                        </div>

                        {{-- Widget Citas Pendientes --}}
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md flex items-center space-x-4 hover:shadow-lg transition-shadow duration-200">
                            <div class="flex-shrink-0 bg-yellow-500 dark:bg-yellow-600 p-3 rounded-full">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-700 dark:text-gray-300">Citas Pendientes</h3>
                                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $pendingAppointmentCount }}</p>
                                <a href="#appointment-manager-section" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline mt-1 block">Gestionar Citas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enlaces a Secciones de Admin / Accesos Rápidos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-6">Accesos Rápidos</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <button wire:click="$dispatch('openCarouselModal')" type="button" class="group flex flex-col items-center justify-center text-center p-6 bg-gray-50 dark:bg-gray-700/60 hover:bg-indigo-50 dark:hover:bg-indigo-700/30 rounded-lg text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow hover:shadow-md">
                            <svg class="h-10 w-10 mb-3 text-indigo-500 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Gestionar Carrusel
                        </button>
                        <a href="{{ route('admin.user.likes') }}" class="group flex flex-col items-center justify-center text-center p-6 bg-gray-50 dark:bg-gray-700/60 hover:bg-blue-50 dark:hover:bg-blue-700/30 rounded-lg text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 shadow hover:shadow-md">
                            <svg class="h-10 w-10 mb-3 text-blue-500 dark:text-blue-400 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            Ver Likes por Cliente
                        </a>
                        <a href="{{ route('albums') }}" class="group flex flex-col items-center justify-center text-center p-6 bg-gray-50 dark:bg-gray-700/60 hover:bg-green-50 dark:hover:bg-green-700/30 rounded-lg text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 shadow hover:shadow-md">
                             <svg class="h-10 w-10 mb-3 text-green-500 dark:text-green-400 group-hover:scale-110 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            Gestionar Álbumes
                        </a>
                        {{-- BOTÓN PARA ABRIR EL MODAL DE CAMPAÑAS DE EMAIL --}}
                        <button wire:click="$dispatch('openEmailCampaignModalEvent')" type="button" class="group flex flex-col items-center justify-center text-center p-6 bg-gray-50 dark:bg-gray-700/60 hover:bg-purple-50 dark:hover:bg-purple-700/30 rounded-lg text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow hover:shadow-md">
                            <svg class="h-10 w-10 mb-3 text-purple-500 dark:text-purple-400 group-hover:scale-110 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Campañas de Email
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sección del Gestor de Citas --}}
            <div id="appointment-manager-section" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                {{-- El alias debe ser admin.admin-appointment-manager si la clase es AdminAppointmentManager --}}
                {{-- o admin.appointment-manager si la clase es AppointmentManager --}}
                @livewire('admin.appointment-manager')
            </div>

        </div>
    </div>
</div>
