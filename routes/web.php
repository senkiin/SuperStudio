<?php

use App\Http\Middleware\IsAdmin;
use App\Livewire\Admin\UserLikedPhotos;
use App\Livewire\Albums;
use App\Livewire\LikedPhotos;
use App\Livewire\Admin\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

Route::middleware(['auth', IsAdmin::class]) // Usa tu middleware de admin
    ->prefix('admin') // Prefijo para URL (ej. /admin/user-likes)
    ->name('admin.') // Prefijo para nombres de ruta (ej. admin.user.likes)
    ->group(function () {

        // Ruta para ver fotos favoritas por cliente
        Route::get('/user-likes', UserLikedPhotos::class)->name('user.likes');
});
