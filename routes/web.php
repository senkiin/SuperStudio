<?php

use App\Http\Middleware\IsAdmin;
use App\Livewire\Admin\UserLikedPhotos;
use App\Livewire\Albums;
use App\Livewire\LikedPhotos;
use App\Livewire\Admin\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Dashboard as AdminDashboard; // Alias para claridad
use App\Livewire\Admin\ManageHomepageCarousel; // Importar el nuevo componente


Route::get('/', HomepageController::class)->name('home'); // Cambié el nombre a 'home'


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('albums', Albums::class)->name('albums');
    Route::get('/mis-favoritos', LikedPhotos::class)
    ->middleware('auth') // Asegura que el usuario esté logueado
    ->name('photos.liked'); // Nombre para generar URLs fácilmente
    Route::get('/user-likes', UserLikedPhotos::class)->name('user.likes');
    Route::get('/admin.dashboard', Dashboard::class)->name('admin.dashboard');


});

Route::get('/', HomepageController::class)->name('home'); // Ya estaba, mantenlo

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard normal de Jetstream (si aún lo usas para usuarios normales)
    Route::get('/dashboard', function () {
        // Redirigir admin al dashboard de admin, otros a la vista normal
         if(Auth::user()->role === 'admin'){
             return redirect()->route('admin.dashboard');
         }
        return view('dashboard'); // O redirige a 'albums' si prefieres
    })->name('dashboard');

    // Rutas existentes de la aplicación
    Route::get('albums', Albums::class)->name('albums');
    Route::get('/mis-favoritos', LikedPhotos::class)->name('photos.liked');

    // Mantén el panel de admin general si lo necesitas
    Route::get('/admin/dashboard', AdminDashboard::class)
         ->middleware(IsAdmin::class) // Doble seguridad nunca está de más
         ->name('admin.dashboard');

    // Ruta para ver likes por cliente (ya estaba)
    Route::get('/admin/user-likes', UserLikedPhotos::class)
         ->middleware(IsAdmin::class)
         ->name('admin.user.likes'); // Mantenido nombre simple por consistencia previa

    // *** NUEVA RUTA PARA GESTIONAR CARRUSEL ***
    Route::get('/admin/manage-homepage-carousel', ManageHomepageCarousel::class)
          ->middleware(IsAdmin::class)
          ->name('admin.homepage.carousel'); // Nuevo nombre de ruta
});
