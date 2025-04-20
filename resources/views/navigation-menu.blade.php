{{-- resources/views/navigation-menu.blade.php (CORREGIDO) --}}
<div> {{-- DIV ENVOLVENTE --}}

    {{-- Banner de Impersonación (Usa la comprobación de sesión) --}}
    @if (session('original_admin_id')) {{-- Verifica si la clave existe en la sesión --}}
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 relative" role="alert">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <p class="font-bold text-sm sm:text-base">
                        <svg class="inline-block w-5 h-5 mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Estás viendo como: <span class="font-semibold">{{ Auth::user()->name }}</span>
                    </p>
                </div>
                <a href="{{ route('impersonate.leave') }}" {{-- Usa la ruta leave de tu controlador --}}
                   class="flex-shrink-0 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-yellow-800 bg-yellow-300 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="inline-block w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Volver a ser Admin
                </a>
            </div>
        </div>
    @endif

    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        {{-- Decide a dónde apunta el logo según si eres admin REAL o no --}}
                        @auth
                            {{-- *** CORRECCIÓN AQUÍ *** --}}
                            @if(Auth::user()->role === 'admin' && !session('original_admin_id'))
                               <a href="{{ route('admin.dashboard') }}"> <x-application-mark class="block h-9 w-auto" /> </a>
                            @else
                               <a href="{{ route('home') }}"> <x-application-mark class="block h-9 w-auto" /> </a>
                            @endif
                        @else
                             <a href="{{ route('home') }}"> <x-application-mark class="block h-9 w-auto" /> </a>
                        @endauth
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        @auth
                            {{-- ENLACES PARA ADMIN REAL (NO IMPERSONANDO) --}}
                            {{-- *** CORRECCIÓN AQUÍ *** --}}
                            @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                                <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                                    {{ __('Panel Admin') }}
                                </x-nav-link>

                                {{-- Menú Desplegable Admin --}}
                                <div class="hidden sm:flex sm:items-center sm:ms-3">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <span class="inline-flex rounded-md"> <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"> Gestión <svg class="ms-2 -me-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg> </button> </span>
                                        </x-slot>
                                        <x-slot name="content">
                                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Contenido') }}</div>
                                            {{-- <x-dropdown-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-dropdown-link> --}}
                                            <x-dropdown-link href="{{ route('albums') }}"> {{ __('Álbumes') }} </x-dropdown-link> {{-- Asume que admin gestiona aquí --}}
                                            <div class="border-t border-gray-200"></div>
                                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Usuarios') }}</div>
                                            <x-dropdown-link href="{{ route('admin.user.likes') }}"> {{ __('Likes Cliente') }} </x-dropdown-link>
                                            @if(Route::has('admin.users.index'))
                                                <x-dropdown-link href="{{ route('admin.users.index') }}"> {{ __('Usuarios') }} </x-dropdown-link>
                                            @endif
                                        </x-slot>
                                    </x-dropdown>
                                </div>

                                 {{-- Enlace para Impersonar (Solo Admin Real) --}}
                                 @if(Route::has('admin.users.index'))
                                    <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                                         {{ __('Impersonar') }}
                                     </x-nav-link>
                                 @endif

                                 {{-- Enlace Ver Sitio (Solo Admin Real) --}}
                                  <x-nav-link href="{{ route('home') }}" target="_blank">
                                      {{ __('Ver Sitio') }}
                                  </x-nav-link>


                            {{-- ENLACES PARA USUARIO NORMAL (o Admin impersonando) --}}
                            @else
                                <x-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')">
                                    {{ __('Albumes') }}
                                </x-nav-link>
                                <x-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')">
                                    {{ __('Mis Favoritas') }}
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
                        @endauth
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                     @auth
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="48">
                                 <x-slot name="trigger">
                                     {{-- Código del trigger (botón con nombre y foto) --}}
                                      <span class="inline-flex rounded-md">
                                          <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                              {{ Auth::user()->name }}
                                              {{-- Muestra (Admin) solo si es el admin real --}}
                                              {{-- *** CORRECCIÓN AQUÍ *** --}}
                                              @if(Auth::user()->role === 'admin' && !session('original_admin_id'))
                                                  <span class="text-xs text-indigo-500 ms-1">(Admin)</span>
                                              @endif
                                               <svg class="ms-2 -me-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                          </button>
                                      </span>
                                 </x-slot>
                                 <x-slot name="content">
                                     {{-- Código del content (Profile, Logout, etc) --}}
                                      <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                                      <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                                      {{-- ... otros enlaces de perfil ... --}}
                                      <div class="border-t border-gray-200"></div>
                                      <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                                          <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                                      </form>
                                 </x-slot>
                            </x-dropdown>
                        </div>
                     @else
                        {{-- Botones Login/Register para invitados --}}
                         @if (Route::has('login'))
                             <nav class="-mx-3 flex flex-1 justify-end">
                                 <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"> Log in </a>
                                 @if (Route::has('register'))
                                     <a href="{{ route('register') }}" class="ml-3 rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"> Register </a>
                                 @endif
                             </nav>
                         @endif
                     @endauth
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                     <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                         <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"> <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /> <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /> </svg>
                     </button>
                 </div>

            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
             {{-- Lógica Condicional similar para menú responsive --}}
            <div class="pt-2 pb-3 space-y-1">
                 @auth
                     {{-- *** CORRECCIÓN AQUÍ *** --}}
                     @if (Auth::user()->role === 'admin' && !session('original_admin_id'))
                         <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')"> {{ __('Panel Admin') }} </x-responsive-nav-link>
                         {{-- Aquí puedes listar los enlaces de admin o añadir un submenú --}}
                         <div class="pt-2 pb-1 border-t border-gray-200">
                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión Admin') }}</div>
                            {{-- <x-responsive-nav-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-responsive-nav-link> --}}
                            <x-responsive-nav-link href="{{ route('albums') }}"> {{ __('Álbumes') }} </x-responsive-nav-link>
                             <x-responsive-nav-link href="{{ route('admin.user.likes') }}"> {{ __('Likes Cliente') }} </x-responsive-nav-link>
                             @if(Route::has('admin.users.index'))
                                <x-responsive-nav-link href="{{ route('admin.users.index') }}"> {{ __('Usuarios') }} </x-responsive-nav-link>
                             @endif
                        </div>
                         @if(Route::has('admin.users.index'))
                            <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')"> {{ __('Impersonar') }} </x-responsive-nav-link>
                         @endif
                         <x-responsive-nav-link href="{{ route('home') }}" target="_blank"> {{ __('Ver Sitio') }} </x-responsive-nav-link>

                     @else
                         <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')"> {{ __('Albumes') }} </x-responsive-nav-link>
                         <x-responsive-nav-link href="{{ route('photos.liked') }}" :active="request()->routeIs('photos.liked')"> {{ __('Mis Favoritas') }} </x-responsive-nav-link>
                         {{-- Otros enlaces de usuario móvil --}}
                     @endif
                 @else
                     <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')"> {{ __('Inicio') }} </x-responsive-nav-link>
                     <x-responsive-nav-link href="{{ route('albums') }}" :active="request()->routeIs('albums')"> {{ __('Galería') }} </x-responsive-nav-link>
                 @endauth
            </div>

            @auth
                <div class="pt-4 pb-1 border-t border-gray-200">
                    {{-- Código de opciones de usuario responsive (Perfil, Logout) --}}
                     <div class="flex items-center px-4">
                         @if (Laravel\Jetstream\Jetstream::managesProfilePhotos()) <div class="shrink-0 me-3"> <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" /> </div> @endif
                         <div> <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div> <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div> </div>
                     </div>
                     <div class="mt-3 space-y-1">
                         <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"> {{ __('Profile') }} </x-responsive-nav-link>
                         <form method="POST" action="{{ route('logout') }}" x-data> @csrf <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();"> {{ __('Log Out') }} </x-responsive-nav-link> </form>
                     </div>
                </div>
            @else
                 {{-- Opciones responsive para invitados (Login/Register) --}}
                 <div class="pt-4 pb-1 border-t border-gray-200">
                     <div class="mt-3 space-y-1">
                         <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')"> {{ __('Log in') }} </x-responsive-nav-link>
                         @if (Route::has('register'))
                             <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')"> {{ __('Register') }} </x-responsive-nav-link>
                         @endif
                     </div>
                 </div>
            @endauth
        </div>
    </nav>
</div>
