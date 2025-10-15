{{-- resources/views/navigation-menu.blade.php --}}
<div x-data="{ open: false, scrolled: false }" @scroll.window.debounce.50ms="scrolled = (window.scrollY > 50)"
    class="fixed top-0 z-40 w-full transition-all duration-300 ease-in-out text-gray-100">

    @if (session('original_admin_id'))
        {{-- ... Barra de impersonación ... --}}
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 relative" role="alert">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <p class="font-bold text-sm sm:text-base">
                        <svg class="inline-block w-5 h-5 mr-1 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        Estás viendo como: <span class="font-semibold">{{ Auth::user()->name }}</span>
                    </p>
                </div>
                <a href="{{ route('impersonate.leave') }}"
                    class="flex-shrink-0 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-yellow-800 bg-yellow-300 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="inline-block w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Volver a ser Admin
                </a>
            </div>
        </div>
    @endif

    <nav x-data="{ openNav: false }" class="w-full">
        <div class="w-full pt-7 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-2">
                <div class="flex items-center flex-shrink-0">
                    {{-- Logo --}}
                    <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 -translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0" class="shrink-0 flex items-center">
                        @php
                            $logoUrlScrolled = Storage::disk('logos')->temporaryUrl('logo2.png', now()->addMinutes(30));
                        @endphp
                        @auth
                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                <a href="{{ route('admin.dashboard') }}">
                                    <img src="{{ $logoUrlScrolled }} " class="mt-2 block h-36 w-auto lg:h-56"
                                        alt="Logo" title="Logo" />
                                </a>
                            @else
                                <a href="{{ route('home') }}">
                                    <img src="{{ $logoUrlScrolled }}" class="mt-2 block h-36 w-auto lg:h-56"
                                        alt="Logo" title="Logo" />
                                </a>
                            @endif
                        @else
                            <a href="{{ route('home') }}">
                                <img src="{{ $logoUrlScrolled }}" alt="Logo" class="mt-2 block h-36 w-auto lg:h-56" />
                            </a>
                        @endauth
                    </div>

                    {{-- Enlaces de Navegación para Escritorio --}}
                    <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 -translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="hidden space-x-6 sm:-my-px lg:ms-8 lg:flex">

                        @auth
                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                {{-- Admin Real --}}
                                <x-nav-link href="{{ route('home') }}" title="Ir a la pagina principal" :active="request()->routeIs('home')">
                                    {{ __('Inicio') }} </x-nav-link>
                                <x-nav-link href="{{ route('weddings') }}" title="Ver nuestras bodas" :active="request()->routeIs('weddings')">
                                    {{ __('Bodas') }}
                                </x-nav-link>

                                {{-- MENÚ DESPLEGABLE "BEBÉS" --}}
                                <div class="hidden sm:flex sm:items-center">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <div class="pl-2 normal-case"> {{-- Mantuve normal-case por si lo prefieres para este trigger específico --}}
                                                {{ __('Bebés') }}
                                            </div>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link href="{{ route('embarazo.index') }}"
                                                title="Ver nuestras sessiones de Embarazadas" :active="request()->routeIs('embarazo.index')">
                                                {{ __('Fotografía Embarazo') }}
                                            </x-dropdown-link>
                                            <x-dropdown-link href="{{ route('newborn.index') }}"
                                                title="Ver nuestras sessiones de Recién Nacidos" :active="request()->routeIs('newborn.index')">
                                                {{ __('Fotografía Recién Nacidos') }}
                                            </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                                <x-nav-link href="{{ route('comuniones') }}" title="Ver nuestras Comuniones"
                                    :active="request()->routeIs('comuniones')"> {{ __('Comuniones') }}
                                </x-nav-link>
                                {{-- ENLACE CORREGIDO PARA FOTOCARNET (ADMIN) --}}
                                <x-nav-link href="{{ route('fotocarnet.almeria') }}" title="Pedir Cita Fotocarnet"
                                    :active="request()->routeIs('fotocarnet.almeria')">
                                    {{ __('Fotocarnet') }} </x-nav-link>
                                <x-nav-link href="{{ route('studio.index') }}" :active="request()->routeIs('studio.index')"
                                    title="Ver nuestros sessiones de foto en nuestro Estudio">{{ __('Studio') }}</x-nav-link>
                                <x-nav-link href="{{ route('blog.index') }}" title="Ver nuestro Blog" :active="request()->routeIs('blog.*')">
                                    {{ __('Blog') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('gallery') }}" title="Ver Galería Pública" :active="request()->routeIs('gallery')">
                                    {{ __('Galería') }}
                                </x-nav-link>
                            @else
                                {{-- Usuario Normal o Admin Impersonando --}}
                                <x-nav-link href="{{ route('home') }}" title="Ir a la pagina principal"
                                    :active="request()->routeIs('home')">{{ __('Inicio') }}</x-nav-link>

                                <x-nav-link href="{{ route('weddings') }}" title="Ver nuestras bodas" :active="request()->routeIs('weddings')">
                                    {{ __('Bodas') }}
                                </x-nav-link>
                                {{-- MENÚ DESPLEGABLE "BEBÉS" --}}
                                <div class="hidden sm:flex sm:items-center">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <div class="pl-2"> {{-- Puedes quitar 'normal-case' si no lo quieres aquí --}}
                                                {{ __('Bebés') }}
                                            </div>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link href="{{ route('embarazo.index') }}" :active="request()->routeIs('embarazo.index')">
                                                {{ __('Fotografía Embarazo') }} </x-dropdown-link>
                                            <x-dropdown-link href="{{ route('newborn.index') }}" :active="request()->routeIs('newborn.index')">
                                                {{ __('Fotografía Recién Nacidos') }} </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                                <x-nav-link href="{{ route('comuniones') }}"
                                    title="Ver nuestras comuniones" :active="request()->routeIs('comuniones')">
                                    {{ __('Comuniones') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('fotocarnet.almeria') }}"
                                    title="Pedir Cita Fotocarnet" :active="request()->routeIs('fotocarnet.almeria')">
                                    {{ __('Fotocarnet') }} </x-nav-link>
                                <x-nav-link href="{{ route('studio.index') }}"
                                    title="Ver nuestros sessiones de foto en nuestro Estudio"
                                    :active="request()->routeIs('studio.index')">{{ __('Studio') }}</x-nav-link>
                                <x-nav-link href="{{ route('videos') }}" title="Ver nuestros Videos" :active="request()->routeIs('videos')">
                                    {{ __('Videos') }}</x-nav-link>
                                <x-nav-link href="{{ route('blog.index') }}" title="Ver nuestro Blog" :active="request()->routeIs('blog.*')">
                                    {{ __('Blog') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('albums') }}" title="Ver albumes" :active="request()->routeIs('albums')">
                                    {{ __('Álbumes') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('photos.liked') }}" title="Ver las fotos gustadas"
                                    :active="request()->routeIs('photos.liked')">
                                    {{ __('Favoritas') }} </x-nav-link>
                                <x-nav-link href="{{ route('gallery') }}" title="Ver Galería Pública" :active="request()->routeIs('gallery')">
                                    {{ __('Galería') }}
                                </x-nav-link>
                            @endif
                        @else

                            {{-- Invitados --}}
                            <x-nav-link href="{{ route('home') }}" title="Ir a la pagina principal" :active="request()->routeIs('home')">
                                {{ __('Inicio') }} </x-nav-link>
                            <x-nav-link href="{{ route('weddings') }}" title="Ver nuestras bodas" :active="request()->routeIs('weddings')">
                                {{ __('Bodas') }}
                            </x-nav-link>
                            {{-- MENÚ DESPLEGABLE "BEBÉS" --}}
                            <div class="hidden sm:flex sm:items-center">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <div class="pl-2">
                                            {{ __('Bebés') }}
                                        </div>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link href="{{ route('embarazo.index') }}"
                                            title="Ver nuestras sessiones de Embarazadas" :active="request()->routeIs('embarazo.index')">
                                            {{ __('Fotografía Embarazo') }} </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('newborn.index') }}"
                                            title="Ver nuestras sessiones de Recién Nacidos" :active="request()->routeIs('newborn.index')">
                                            {{ __('Fotografía Recién Nacidos') }} </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                            <x-nav-link href="{{ route('comuniones') }}" title="Ver nuestras comuniones"
                                :active="request()->routeIs('comuniones')"> {{ __('Comuniones') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('fotocarnet.almeria') }}" title="Pedir cita Fotocarnet"
                                :active="request()->routeIs('fotocarnet.almeria')">
                                {{ __('Fotocarnet') }} </x-nav-link>
                            <x-nav-link href="{{ route('studio.index') }}"
                                title="Ver nuestros sessiones de foto en nuestro Estudio"
                                :active="request()->routeIs('studio.index')">{{ __('Studio') }}</x-nav-link>
                            <x-nav-link href="{{ route('videos') }}"
                                title="Ver nuestros Videos" :active="request()->routeIs('videos')">
                                {{ __('Videos') }}</x-nav-link>
                            <x-nav-link href="{{ route('blog.index') }}"
                                title="Ver nuestro Blog" :active="request()->routeIs('blog.*')">
                                {{ __('Blog') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('gallery') }}" title="Ver Galería Pública" :active="request()->routeIs('gallery')">
                                {{ __('Galería') }}
                            </x-nav-link>

                        @endauth
                    </div>
                </div>

                {{-- Menú Usuario (Login/Register o Perfil) --}}

                <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 -translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0" class="hidden lg:flex lg:items-center lg:space-x-6 flex-shrink-0">
                    @auth
                        {{-- 2) Menú de “Gestión” (engranaje), justo debajo del anterior --}}
                        @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                            <div class="relative ms-3">
                                <x-dropdown align="right">
                                    <x-slot name="trigger">
                                        <button type="button" title="Gestión"
                                            class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                        </button>
                                    </x-slot>
                                    <x-slot name="content" contentClasses="py-1 bg-white">

                                        {{-- MENÚ PARA ADMINISTRADORES --}}
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Navegación Admin') }}
                                        </div>
                                        <x-dropdown-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                                            {{ __('Panel Admin') }}
                                        </x-dropdown-link>
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Gestión Contenido') }}
                                        </div>
                                        <x-dropdown-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                                            {{ __('Mis Favoritas') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('albums') }}" title="Ver albumes" :active="request()->routeIs('albums')">
                                            {{ __('Álbumes') }}
                                        </x-dropdown-link>
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Gestión Usuarios') }}
                                        </div>
                                        @if (Route::has('admin.users.index'))
                                            <x-dropdown-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                                                {{ __('Lista de Usuarios') }}
                                            </x-dropdown-link>
                                        @endif
                                        <x-dropdown-link href="{{ route('admin.user.likes') }}" :active="request()->routeIs('admin.user.likes')">
                                            {{ __('Likes Cliente') }}
                                        </x-dropdown-link>
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <x-dropdown-link href="{{ route('videos') }}" title="Ir a nuestros videos" target="_blank">
                                            {{ __('Videos') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('home') }}" target="_blank">
                                            {{ __('Ver Sitio') }}
                                        </x-dropdown-link>

                                        <x-responsive-nav-link href="{{ route('admin.blog.manager') }}"
                                            :active="request()->routeIs('blog.*')">
                                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                            {{ __('Editar Blog') }}
                                        </x-responsive-nav-link>


                                    </x-slot>

                                </x-dropdown>
                            </div>
                        @endif


                        {{-- 1) Menú de usuario (nombre / ADMIN) --}}
                        <div class="relative ms-3">
                            <x-dropdown align="right" width="48"
                                dropdownClasses="bg-white rounded-full shadow-lg ring-1 ring-black ring-opacity-5">
                                <x-slot name="trigger">
                                    <button type="button"
                                        class="inline-flex items-center space-x-1 px-3 py-1 text-xs font-semibold rounded-full text-black whitespace-nowrap
                               transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                                        <span>{{ strtoupper(Auth::user()->name) }}</span>
                                        @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                            <span class="text-xs font-medium text-indigo-500">(ADMIN)</span>
                                        @endif
                                    </button>
                                </x-slot>

                                <x-slot name="content" contentClasses="py-1 bg-white">
                                    <x-dropdown-link href="{{ route('profile.show') }}" title="Ver perfil">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}" title="Cerrar Sesión" x-data
                                        class="mt-1">
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}" title="Cerrar Sesión"
                                            @click.prevent="$root.submit()">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>

                                </x-slot>
                            </x-dropdown>


                        </div>
                    @else
                        @if (Route::has('login'))
                            <x-nav-link href="{{ route('login') }}" title="Iniciar Sesión" :active="request()->routeIs('login')">
                                {{ __('Login') }}
                            </x-nav-link>
                            @if (Route::has('register'))
                                <x-nav-link href="{{ route('register') }}" title="Registrarse" :active="request()->routeIs('register')">
                                    {{ __('Register') }}
                                </x-nav-link>
                            @endif
                        @endif
                    @endauth

                </div>

                {{-- Botón Hamburguesa para Móvil --}}
                <div class="-me-2 flex items-center lg:hidden">
                    <button @click="openNav = !openNav"
                        class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition duration-150 ease-in-out text-gray-300 hover:text-white"
                        :class="{
                            'bg-gray-700': !scrolled && !openNav,
                            'bg-black/20': scrolled && !openNav,
                            'bg-gray-900': openNav
                        }">
                        <span class="sr-only">Abrir menú principal</span>
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': openNav, 'inline-flex': !openNav }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !openNav, 'inline-flex': openNav }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>




        {{-- Menú Responsive Modernizado --}}
        <div :class="{ 'block': openNav, 'hidden': !openNav }"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="hidden lg:hidden bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white shadow-2xl">

            <div class="max-h-screen overflow-y-auto">
                {{-- Header del menú móvil --}}
                <div class="px-6 py-4 border-b border-gray-700/50 bg-gradient-to-r from-gray-800/50 to-transparent">
                    <h3 class="text-lg font-semibold text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        Menú
                    </h3>
                </div>

                {{-- Sección principal de navegación --}}
                <div class="px-2 py-4">
                    <div class="space-y-1">
                        <x-responsive-nav-link href="{{ route('home') }}" title="Ir a la pagina principal"
                            :active="request()->routeIs('home')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Inicio') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('weddings') }}" title="Ver nuestras bodas"
                            :active="request()->routeIs('weddings')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-pink-500/20 flex items-center justify-center group-hover:bg-pink-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Bodas') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        {{-- Servicios Bebés - Sección especial --}}
                        <div class="mt-6 mb-4">
                            <div class="px-4 py-2 text-xs font-semibold text-blue-400 uppercase tracking-wider bg-blue-500/10 rounded-lg mx-2">
                                {{ __('Servicios Bebés') }}
                            </div>
                        </div>

                        <x-responsive-nav-link href="{{ route('embarazo.index') }}"
                            title="Ver nuestras sessiones de Embarazadas" :active="request()->routeIs('embarazo.index')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9M19 9H14V4L19 9M7 18C7 16.34 8.34 15 10 15S13 16.34 13 18 11.66 21 10 21 7 19.66 7 18M10 20C9.45 20 9 19.55 9 19S9.45 18 10 18 11 18.45 11 19 10.55 20 10 20M15 13C14.45 13 14 12.55 14 12S14.45 11 15 11 16 11.45 16 12 15.55 13 15 13M19 13C18.45 13 18 12.55 18 12S18.45 11 19 11 20 11.45 20 12 19.55 13 19 13Z"/>
                                        <circle cx="12" cy="18" r="1" fill="#8B5CF6"/>
                                        <path d="M9 16H15V18H9V16Z" fill="#8B5CF6" opacity="0.7"/>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Fotografía Embarazo') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('newborn.index') }}"
                            title="Ver nuestras sessiones de Recién Nacidos" :active="request()->routeIs('newborn.index')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9M19 9H14V4L19 9M7 15C7 13.34 8.34 12 10 12S13 13.34 13 15 11.66 18 10 18 7 16.66 7 15M10 17C9.45 17 9 16.55 9 16S9.45 15 10 15 11 15.45 11 16 10.55 17 10 17M15 10C14.45 10 14 9.55 14 9S14.45 8 15 8 16 8.45 16 9 15.55 10 15 10M19 10C18.45 10 18 9.55 18 9S18.45 8 19 8 20 8.45 20 9 19.55 10 19 10Z"/>
                                        <circle cx="10" cy="16" r="2" fill="#10B981"/>
                                        <path d="M8 14H12V16H8V14Z" fill="#10B981" opacity="0.7"/>
                                        <circle cx="6" cy="8" r="1" fill="#10B981" opacity="0.6"/>
                                        <circle cx="18" cy="8" r="1" fill="#10B981" opacity="0.6"/>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Fotografía Recién Nacidos') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('comuniones') }}"
                            title="Ver nuestras comuniones" :active="request()->routeIs('comuniones')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-yellow-500/20 flex items-center justify-center group-hover:bg-yellow-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Comuniones') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('fotocarnet.almeria') }}" title="Pedir Cita Fotocarnet"
                            :active="request()->routeIs('fotocarnet.almeria')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center group-hover:bg-indigo-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Fotocarnet') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('studio.index') }}"
                            title="Ver nuestros sessiones de foto en nuestro Estudio" :active="request()->routeIs('studio.index')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center group-hover:bg-red-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Studio') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('videos') }}" title="Ver nuestros Videos"
                            :active="request()->routeIs('videos')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center group-hover:bg-orange-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Video Reportajes') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('blog.index') }}"
                            title="Ver nuestro Blog" :active="request()->routeIs('blog.*')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center group-hover:bg-teal-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Blog') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link href="{{ route('gallery') }}"
                            title="Ver Galería Pública" :active="request()->routeIs('gallery')"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/20 flex items-center justify-center group-hover:bg-cyan-500/30 transition-colors">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Galería') }}</span>
                        </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                    </x-responsive-nav-link>
                </div>
            </div>

                {{-- Sección de usuario / login modernizada --}}
                <div class="px-4 py-4 border-t border-gray-700/50 bg-gradient-to-r from-gray-800/30 to-transparent">
                @auth
                        <div class="flex items-center space-x-4 mb-4 p-4 bg-white/5 rounded-xl backdrop-blur-sm">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-12 w-12 rounded-full object-cover ring-2 ring-blue-400/50"
                                     src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center ring-2 ring-blue-400/50">
                                    <span class="text-white font-bold text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="text-base font-semibold text-gray-100">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                                @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 mt-1">
                                        ADMIN
                                    </span>
                        @endif
                            </div>
                    </div>

                        <div class="space-y-2">
                        @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                            <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')"
                                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-100 font-medium">{{ __('Panel Admin') }}</span>
                                </x-responsive-nav-link>
                        @else
                            <x-responsive-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')"
                                    class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                                    <div class="w-8 h-8 rounded-lg bg-pink-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-100 font-medium">{{ __('Mis Favoritas') }}</span>
                                </x-responsive-nav-link>
                        @endif

                            <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"
                                class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98]">
                                <div class="w-8 h-8 rounded-lg bg-gray-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-100 font-medium">{{ __('Profile') }}</span>
                            </x-responsive-nav-link>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-red-500/20 hover:scale-[1.02] active:scale-[0.98] text-left">
                                    <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                    </div>
                                    <span class="text-red-400 font-medium">{{ __('Log Out') }}</span>
                                </button>
                        </form>
                    </div>
                @else
                        <div class="space-y-3">
                        <x-responsive-nav-link href="{{ route('login') }}"
                                title="Login" :active="request()->routeIs('login')"
                                class="group flex items-center justify-between px-4 py-4 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98] bg-gradient-to-r from-blue-500/20 to-transparent border border-blue-500/30">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/30 flex items-center justify-center group-hover:bg-blue-500/40 transition-colors">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-100 font-semibold">{{ __('Log in') }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </x-responsive-nav-link>

                        @if (Route::has('register'))
                            <x-responsive-nav-link href="{{ route('register') }}" title="Register" :active="request()->routeIs('register')"
                                    class="group flex items-center justify-between px-4 py-4 rounded-xl transition-all duration-200 hover:bg-white/10 hover:scale-[1.02] active:scale-[0.98] bg-gradient-to-r from-green-500/20 to-transparent border border-green-500/30">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-green-500/30 flex items-center justify-center group-hover:bg-green-500/40 transition-colors">
                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-gray-100 font-semibold">{{ __('Register') }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </x-responsive-nav-link>
                        @endif
                    </div>
                @endauth
                </div>
            </div>
        </div>
    </nav>
</div>
