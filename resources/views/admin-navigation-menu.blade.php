{{-- resources/views/admin-navigation-menu.blade.php --}}

{{-- Banner Impersonación (Importante tenerlo aquí si este menú se muestra siempre que eres admin) --}}
@impersonating($guard = null)
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 relative" role="alert">
        <p class="font-bold">Estás viendo como: {{ Auth::user()->name }}</p>
        <p class="text-sm">Haz clic en el botón para volver a tu cuenta de administrador.</p>
        <a href="{{ route('impersonate.leave') }}"
            @dd(app('impersonate'));
           class="absolute top-0 bottom-0 right-0 px-4 py-3 bg-yellow-200 hover:bg-yellow-300 text-yellow-800 font-semibold">
            Volver a ser Admin
        </a>
    </div>
@endImpersonating

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}"> {{-- Logo apunta al dashboard admin --}}
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- Enlace Principal Admin --}}
                    <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Panel Admin') }}
                    </x-nav-link>

                    {{-- Menú Desplegable de Gestión Admin --}}
                    <div class="hidden sm:flex sm:items-center sm:ms-3">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        Gestión
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Contenido') }}</div>
                                <x-dropdown-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-dropdown-link>
                                <x-dropdown-link href="{{ route('albums') }}"> {{ __('Álbumes') }} </x-dropdown-link>
                                <div class="border-t border-gray-200"></div>
                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Usuarios') }}</div>
                                <x-dropdown-link href="{{ route('admin.user.likes') }}"> {{ __('Likes Cliente') }} </x-dropdown-link>
                                @if(Route::has('admin.users.index'))
                                    <x-dropdown-link href="{{ route('admin.users.index') }}"> {{ __('Usuarios') }} </x-dropdown-link>
                                @endif
                                {{-- Otros enlaces admin --}}
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- Enlace Impersonar/Ver Sitio --}}
                     @if(Route::has('admin.users.index') && !$impersonating) {{-- Mostrar solo si no está impersonando --}}
                        <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')">
                             {{ __('Impersonar Usuario') }}
                         </x-nav-link>
                     @elseif(!$impersonating) {{-- Mostrar "Ver Sitio" si no hay impersonación y no se está impersonando --}}
                        <x-nav-link href="{{ route('home') }}" :active="false" target="_blank">
                            {{ __('Ver Sitio') }}
                        </x-nav-link>
                     @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                 <div class="ms-3 relative">
                     <x-dropdown align="right" width="48">
                         <x-slot name="trigger">
                              <span class="inline-flex rounded-md">
                                 <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                     {{ Auth::user()->name }} <span class="text-xs text-indigo-500 ms-1">(Admin)</span>
                                     <svg class="ms-2 -me-0.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                 </button>
                             </span>
                         </x-slot>
                         <x-slot name="content">
                             <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                             <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                             <div class="border-t border-gray-200"></div>
                             <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                                 <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                             </form>
                         </x-slot>
                     </x-dropdown>
                 </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Enlaces principales para admin en móvil --}}
            <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')"> {{ __('Panel Admin') }} </x-responsive-nav-link>
             @if(Route::has('admin.users.index') && !$impersonating)
                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.index')"> {{ __('Impersonar Usuario') }} </x-responsive-nav-link>
             @elseif(!$impersonating)
                 <x-responsive-nav-link href="{{ route('home') }}" :active="false"> {{ __('Ver Sitio') }} </x-responsive-nav-link>
             @endif
        </div>

        {{-- Separador y enlaces de gestión en móvil --}}
         <div class="pt-4 pb-1 border-t border-gray-200">
             <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Gestión') }}</div>
             <x-responsive-nav-link href="{{ route('admin.homepage.carousel') }}"> {{ __('Carrusel Inicio') }} </x-responsive-nav-link>
             <x-responsive-nav-link href="{{ route('albums') }}"> {{ __('Álbumes') }} </x-responsive-nav-link>
             <x-responsive-nav-link href="{{ route('admin.user.likes') }}"> {{ __('Likes Cliente') }} </x-responsive-nav-link>
             @if(Route::has('admin.users.index'))
                 <x-responsive-nav-link href="{{ route('admin.users.index') }}"> {{ __('Usuarios') }} </x-responsive-nav-link>
             @endif
             {{-- Otros enlaces admin... --}}
         </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3"> <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" /> </div>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')"> {{ __('Profile') }} </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}" x-data> @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();"> {{ __('Log Out') }} </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
