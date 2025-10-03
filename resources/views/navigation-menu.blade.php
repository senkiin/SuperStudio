{{-- resources/views/navigation-menu.blade.php --}}
<div x-data="{ open: false, scrolled: false }" @scroll.window.debounce.50ms="scrolled = (window.scrollY > 50)"
    class="fixed top-0 z-40 w-full transition-all duration-300 ease-in-out text-gray-100"
    :class="{
        'bg-black/20 backdrop-blur-sm': !scrolled,
        'bg-black/80 backdrop-blur-md': scrolled,
    }">

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
        <div class="@if (!auth()->check() || (auth()->check() && auth()->user()->role === 'admin' && !session('original_admin_id'))) max-w-7xl @endif mx-auto pt-7 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-2">
                <div class="flex">
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
                        class="hidden space-x-4 sm:-my-px lg:ms-10 lg:flex">

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
                    x-transition:enter-end="opacity-100 translate-y-0" class="hidden lg:flex lg:items-center lg:ms-6">
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
                            <nav class="-mx-3 flex flex-1 justify-end">
                                <a href="{{ route('login') }}" title="Login"
                                    class="rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-none focus-visible:ring-[#FF2D20] text-gray-200 hover:text-white">
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" title="Register"
                                        class="ms-3 rounded-md px-3 py-2 ring-1 ring-transparent transition focus:outline-none focus-visible:ring-[#FF2D20] text-gray-200 hover:text-white">
                                        Register
                                    </a>
                                @endif
                            </nav>
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




        {{-- Menú Responsive --}}
        <div :class="{ 'block': openNav, 'hidden': !openNav }" class="hidden lg:hidden bg-black text-white">
            <div class="divide-y divide-gray-700">
                {{-- Primera sección de enlaces --}}
                <div class="pt-2 pb-3">
                    <x-responsive-nav-link href="{{ route('home') }}" title="Ir a la pagina principal"
                        :active="request()->routeIs('home')" class="px-4 py-3">
                        {{ __('Inicio') }} </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('weddings') }}" title="Ver nuestras bodas"
                        :active="request()->routeIs('weddings')" class="px-4 py-3">
                        {{ __('Bodas') }} </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('comuniones') }}"
                        title="Ver nuestras comuniones" :active="request()->routeIs('comuniones')" class="px-4 py-3">
                        {{ __('Comuniones') }} </x-responsive-nav-link>
                    {{-- ENLACE CORREGIDO PARA FOTOCARNET (RESPONSIVE) --}}
                    <x-responsive-nav-link href="{{ route('fotocarnet.almeria') }}" title="Pedir Cita Fotocarnet"
                        :active="request()->routeIs('fotocarnet.almeria')" class="px-4 py-3"> {{ __('Fotocarnet') }} </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('studio.index') }}"
                        title="Ver nuestros sessiones de foto en nuestro Estudio" :active="request()->routeIs('studio.index')"
                        class="px-4 py-3">
                        {{ __('Studio') }} </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('videos') }}" title="Ver nuestros Videos"
                        :active="request()->routeIs('videos')" class="px-4 py-3">
                        {{ __('Video Reportajes') }} </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('blog.index') }}"
                        title="Ver nuestro Blog" :active="request()->routeIs('blog.*')">
                        {{ __('Blog') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('gallery') }}"
                        title="Ver Galería Pública" :active="request()->routeIs('gallery')">
                        {{ __('Galería') }}
                    </x-responsive-nav-link>
                </div> {{-- Menú "Gestión" --}}
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            {{-- No necesita el div extra si el trigger de x-dropdown ya maneja el estilo --}}
                            Gestion
                        </x-slot>
                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Navegación Admin') }}</div>
                            <x-dropdown-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Panel Admin') }} </x-dropdown-link>
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Gestión Contenido') }}</div>
                            <x-dropdown-link href="{{ route('albums') }}" title="Albums" :active="request()->routeIs('albums')">
                                {{ __('Álbumes') }} </x-dropdown-link>
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Gestión Usuarios') }}</div>
                            @if (Route::has('admin.users.index'))
                                <x-dropdown-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                                    {{ __('Lista de Usuarios') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link href="{{ route('admin.user.likes') }}" :active="request()->routeIs('admin.user.likes')">
                                {{ __('Likes Cliente') }} </x-dropdown-link>
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            <x-dropdown-link href="{{ route('videos') }}" title="Ir a nuestros videos" target="_blank">
                                {{ __('Videos') }} </x-dropdown-link>
                            <x-dropdown-link href="{{ route('home') }}" target="_blank">
                                {{ __('Ver Sitio') }} </x-dropdown-link>

                        </x-slot>
                    </x-dropdown>
                </div>
                {{-- "Servicios Bebés" con título de grupo --}}
                <div class="pt-2 pb-3">
                    <div class="px-4 py-2 text-xs text-gray-500 tracking-wide"> {{ __('Servicios Bebés') }} </div>
                    <x-responsive-nav-link href="{{ route('embarazo.index') }}"
                        title="Ver nuestras sessiones de Embarazadas" :active="request()->routeIs('embarazo.index')" class="px-4 py-3">
                        <div class="flex justify-between items-center">
                            {{ __('Fotografía Embarazo') }}
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('newborn.index') }}"
                        title="Ver nuestras sessiones de Recién Nacidos" :active="request()->routeIs('newborn.index')"
                        class="px-4 py-3">
                        <div class="flex justify-between items-center">
                            {{ __('Fotografía Recién Nacidos') }}
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </x-responsive-nav-link>
                </div>
            </div>

            {{-- Sección de usuario / login --}}
            <div class="pt-4 pb-1 border-t border-gray-700">
                @auth
                    <div class="px-4 flex items-center space-x-3">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-200">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="mt-3 divide-y divide-gray-700">
                        @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                            <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')"
                                class="px-4 py-3"> {{ __('Panel Admin') }} </x-responsive-nav-link>
                        @else
                            <x-responsive-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')"
                                class="px-4 py-3"> {{ __('Mis Favoritas') }} </x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="px-4 py-3">
                            {{ __('Profile') }} </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}" x-data class="px-4 py-3">
                            @csrf
                            <button type="submit"
                                class="w-full text-left text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                                {{ __('Log Out') }} </button>
                        </form>
                    </div>
                @else
                    <div class="mt-3 space-y-1 divide-y divide-gray-700">
                        <x-responsive-nav-link href="{{ route('login') }}"
                            title="Login" :active="request()->routeIs('login')" class="px-4 py-3">
                            {{ __('Log in') }} </x-responsive-nav-link>
                        @if (Route::has('register'))
                            <x-responsive-nav-link href="{{ route('register') }}" title="Register" :active="request()->routeIs('register')"
                                class="px-4 py-3">
                                {{ __('Register') }} </x-responsive-nav-link>
                        @endif

                    </div>
                @endauth
            </div>
        </div>
    </nav>
</div>
