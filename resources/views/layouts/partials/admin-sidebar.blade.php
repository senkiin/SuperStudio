{{-- resources/views/layouts/partials/admin-sidebar.blade.php --}}
<aside class="w-64 bg-white shadow-md min-h-screen p-4 sticky top-0 h-screen overflow-y-auto"> {{-- Ancho fijo, sombra, scroll si es necesario --}}
    <div class="shrink-0 flex items-center mb-6 px-2"> {{-- Logo opcional en sidebar --}}
        <a href="{{ route('admin.dashboard') }}">
            <x-application-mark class="block h-9 w-auto" />
        </a>
    </div>

    <nav class="mt-5 flex-1 space-y-1">
        {{-- Enlace Principal Admin --}}
        <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard*')">
            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            {{ __('Panel Admin') }}
        </x-responsive-nav-link>

        {{-- Sección Gestión Contenido --}}
        <div class="pt-4">
            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión Contenido') }}</div>
            <x-responsive-nav-link href="{{ route('admin.homepage.carousel') }}" :active="request()->routeIs('admin.homepage.carousel')">
                 <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm0 0l6 6 6-6"></path></svg>
                {{ __('Carrusel Inicio') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums*')"> {{-- Asume que admin gestiona desde la misma ruta que usuario --}}
                 <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ __('Álbumes') }}
            </x-responsive-nav-link>
             {{-- Añade más enlaces de contenido aquí --}}
        </div>

        {{-- Sección Gestión Usuarios --}}
        <div class="pt-4">
            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión Usuarios') }}</div>
            <x-responsive-nav-link href="{{ route('admin.user.likes') }}" :active="request()->routeIs('admin.user.likes')">
                 <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                {{ __('Likes Cliente') }}
            </x-responsive-nav-link>
            @if(Route::has('admin.users.index'))
                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                     <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif
             {{-- Añade aquí el enlace "Impersonar" si quieres que sea un elemento principal del menú lateral --}}
             @if(Route::has('admin.users.index'))
                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                     <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Impersonar') }}
                 </x-responsive-nav-link>
             @endif
        </div>

        {{-- Sección Otros --}}
        <div class="pt-4">
             <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Otros') }}</div>
             <x-responsive-nav-link href="{{ route('home') }}" :active="false" target="_blank"> {{-- target="_blank" para abrir en nueva pestaña --}}
                 <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                {{ __('Ver Sitio') }}
            </x-responsive-nav-link>
        </div>

         {{-- Opcional: Botón Logout directamente en el sidebar --}}
         <div class="pt-4 mt-auto"> {{-- mt-auto empuja al fondo si el sidebar es flex --}}
             <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                 <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                     <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                     {{ __('Log Out') }}
                 </x-responsive-nav-link>
             </form>
         </div>

    </nav>
</aside>
