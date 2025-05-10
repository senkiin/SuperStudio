{{-- resources/views/navigation-menu.blade.php (CORREGIDO Y REESTRUCTURADO) --}}
<div x-data="{ open: false, scrolled: false }" @scroll.window.debounce.50ms="scrolled = (window.scrollY > 20)"
    class="sticky top-0 z-50 w-full transition-all duration-300 ease-in-out text-gray-100"
    :class="{
        'bg-grey-500 shadow-lg': !scrolled,
        'bg-transparent border-transparent': scrolled
    }">

    @if (session('original_admin_id'))
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

    <nav x-data="{ open: false }" class="w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 -translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0" class="shrink-0 flex items-center">
                        @php
                            $logoUrlDefault = Storage::url('media/logos/logo1.png');
                            $logoUrlScrolled = Storage::url('media/logos/logo2.png');
                        @endphp
                        @auth
                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                <a href="{{ route('admin.dashboard') }}">
                                    <img :src="scrolled ? '{{ $logoUrlScrolled }}' : '{{ $logoUrlDefault }}'" alt="Logo"
                                        style="display: block; height: 14rem; width: auto;" />
                                </a>
                            @else
                                <a href="{{ route('home') }}">
                                    <img :src="scrolled ? '{{ $logoUrlScrolled }}' : '{{ $logoUrlDefault }}'" alt="Logo"
                                        style="display: block; height: 14rem; width: auto;" />
                                </a>
                            @endif
                        @else
                            <a href="{{ route('home') }}">
                                <img :src="scrolled ? '{{ $logoUrlScrolled }}' : '{{ $logoUrlDefault }}'" alt="Logo"
                                    style="display: block; height: 14rem; width: auto;" />
                            </a>
                        @endauth
                    </div>

                    <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 -translate-y-10"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="hidden space-x-8 sm:-my-px lg:ms-10 lg:flex">
                        @auth
                            {{-- ENLACES PARA ADMIN REAL (NO IMPERSONANDO) --}}
                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                {{-- Enlaces adicionales para Admin Real (fuera del desplegable Gestión) --}}
                                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                                    {{ __('Inicio') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                                    {{ __('Bodas') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                                    {{ __('Comuniones') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                    {{ __('Galería') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                                    {{ __('Mis Favoritas') }}
                                </x-nav-link>
                                {{-- Menú Desplegable Admin --}}
                                <div class="hidden sm:flex sm:items-center sm:ms-3">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    Gestión
                                                    <svg class="ms-2 -me-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </x-slot>
                                        <x-slot name="content">
                                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Navegación Admin') }}
                                            </div>
                                            <x-dropdown-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                                                {{ __('Panel Admin') }}
                                            </x-dropdown-link>

                                            <div class="border-t border-gray-200"></div>
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Gestión Contenido') }}</div>
                                            {{-- <x-dropdown-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-dropdown-link> --}}
                                            <x-dropdown-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                                {{ __('Álbumes') }} </x-dropdown-link>

                                            <div class="border-t border-gray-200"></div>
                                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión Usuarios') }}
                                            </div>
                                            @if (Route::has('admin.users.index'))
                                                <x-dropdown-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                                                    {{ __('Lista de Usuarios') }} {{-- (Para Impersonar) --}}
                                                </x-dropdown-link>
                                            @endif
                                            <x-dropdown-link href="{{ route('admin.user.likes') }}" :active="request()->routeIs('admin.user.likes')">
                                                {{ __('Likes Cliente') }}
                                            </x-dropdown-link>

                                            <div class="border-t border-gray-200"></div>
                                            <x-dropdown-link href="{{ route('home') }}" target="_blank">
                                                {{ __('Ver Sitio') }}
                                            </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                                {{-- ENLACES PARA USUARIO NORMAL (o Admin impersonando) --}}
                            @else
                                <x-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                    {{ __('Albumes') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                                    {{ __('Mis Favoritas') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                                    {{ __('Bodas') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                                    {{ __('Comuniones') }}
                                </x-nav-link>
                                {{-- Otros enlaces de usuario aquí --}}
                            @endif
                        @else
                            {{-- ENLACES PARA INVITADOS --}}
                            <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                                {{ __('Inicio') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                {{ __('Galería') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                                {{ __('Bodas') }}
                            </x-nav-link>
                            <x-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                                {{ __('Comuniones') }}
                            </x-nav-link>
                        @endauth
                    </div>
                </div>

                <div x-data="{ showMenu: false }" x-init="setTimeout(() => showMenu = true, 400)" x-show="showMenu"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 -translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0" class="hidden lg:flex lg:items-center lg:ms-6">
                    @auth
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}
                                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                                <span class="text-xs text-indigo-500 ms-1">(Admin)</span>
                                            @endif
                                            <svg class="ms-2 -me-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Account') }}
                                    </div>
                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-200"></div>
                                    <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        @if (Route::has('login'))
                            <nav class="-mx-3 flex flex-1 justify-end">
                                <x-nav-link href="{{ route('login') }}"
                                    class="rounded-md px-3 py-2 text-[#BF00FF] ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]">
                                    Log in
                                </x-nav-link>
                                @if (Route::has('register'))
                                    <x-nav-link href="{{ route('register') }}"
                                        class="ml-3 rounded-md px-3 py-2 text-[#BF00FF] ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]">
                                        Register
                                    </x-nav-link>
                                @endif
                            </nav>
                        @endif
                    @endauth
                </div>

                <div class="-me-2 flex items-center lg:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition duration-150 ease-in-out"
                        :class="{
                            'text-gray-900 hover:bg-black/5 focus:bg-black/10': !scrolled,
                            'text-gray-100 hover:bg-white/10 focus:bg-white/20': scrolled
                        }">
                        <span class="sr-only">Abrir menú principal</span>
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menú Responsive --}}
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden">
            <div class="pt-2 pb-3 space-y-1">
                @auth
                    @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                        {{-- Admin Real en Móvil --}}
                        <div class="px-4 py-2 text-sm text-gray-500">{{ __('Menú Principal Admin') }}</div>
                        <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Panel Admin') }}
                        </x-responsive-nav-link>

                        <div class="pt-2 pb-1 border-t border-gray-200">
                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión Admin') }}</div>
                            {{-- <x-responsive-nav-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-responsive-nav-link> --}}
                            <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                {{ __('Álbumes') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('admin.user.likes') }}" :active="request()->routeIs('admin.user.likes')">
                                {{ __('Likes Cliente') }}
                            </x-responsive-nav-link>
                            @if (Route::has('admin.users.index'))
                                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                                    {{ __('Lista de Usuarios') }} {{-- (Para Impersonar) --}}
                                </x-responsive-nav-link>
                            @endif
                            <x-responsive-nav-link href="{{ route('home') }}" target="_blank">
                                {{ __('Ver Sitio') }}
                            </x-responsive-nav-link>
                        </div>

                        {{-- Enlaces adicionales Admin --}}
                        <div class="pt-2 pb-1 border-t border-gray-200">
                            <x-responsive-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                                {{ __('Mis Favoritas') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                                {{ __('Bodas') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                                {{ __('Comuniones') }}
                            </x-responsive-nav-link>
                        </div>
                    @else
                        {{-- Usuario Normal o Admin Impersonando en Móvil --}}
                        <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                            {{ __('Albumes') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                            {{ __('Mis Favoritas') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                            {{ __('Bodas') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                            {{ __('Comuniones') }}
                        </x-responsive-nav-link>
                        {{-- Otros enlaces de usuario móvil --}}
                    @endif
                @else
                    {{-- Invitado en Móvil --}}
                    <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Inicio') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                        {{ __('Galería') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('weddings') }}" :active="request()->routeIs('weddings')">
                        {{ __('Bodas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('comuniones') }}" :active="request()->routeIs('comuniones')">
                        {{ __('Comuniones') }}
                    </x-responsive-nav-link>
                @endauth
            </div>

            @auth
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <div class="shrink-0 me-3">
                                <img class="h-10 w-10 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </div>
                        @endif
                        <div>
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                            <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            @else
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                            {{ __('Log in') }}
                        </x-responsive-nav-link>
                        @if (Route::has('register'))
                            <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                                {{ __('Register') }}
                            </x-responsive-nav-link>
                        @endif
                    </div>
                </div>
            @endauth
        </div>
    </nav>
</div>
