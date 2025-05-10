<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ImpersonationController;
// Livewire Components
use App\Livewire\Albums;
use App\Livewire\LikedPhotos;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ManageHomepageCarousel;
use App\Livewire\Admin\UserLikedPhotos;
use App\Livewire\Admin\ManageUsers;
use App\Http\Middleware\IsAdmin;
use App\Livewire\AlbumDetailPage;
use App\Livewire\WeddingsPage;

// --- Ruta Pública Principal ---
Route::get('/', HomepageController::class)->name('home');
Route::get('/bodas', WeddingsPage::class)->name('weddings');
Route::get('/album/{album}', AlbumDetailPage::class)->name('album.show');
Route::get('/comuniones', function () {
    return view('comuniones', [
        'pageTitle' => 'Comuniones | Fotovalera',
        'metaDescription' => 'Reportajes fotográficos y de vídeo para primeras comuniones. Capturamos momentos únicos y especiales.'
        // Puedes añadir más datos para SEO o para la vista aquí
    ]);
})->name('comuniones');
// --- Rutas Autenticadas ---
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Redirección del Dashboard (/dashboard es la ruta por defecto de Jetstream/Fortify)
    Route::get('/dashboard', function () {
        if (Auth::user()?->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Admin va a su dashboard
        }
        return redirect()->route('albums'); // Usuarios normales van a los álbumes
    })->name('dashboard');

    // Rutas de Cliente Normal
    Route::get('/albums', Albums::class)->name('albums');
    Route::get('/mis-favoritos', LikedPhotos::class)->name('photos.liked');
    // Añade aquí otras rutas específicas de cliente

    // --- Rutas de Administración ---
    Route::prefix('admin')          // URL: /admin/...
         ->middleware(IsAdmin::class) // Middleware para proteger
         ->name('admin.')           // Nombre: admin....
         ->group(function () {

        // Rutas de gestión
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
       // Route::get('/manage-homepage-carousel', ManageHomepageCarousel::class)->name('homepage.carousel');
        Route::get('/user-likes', UserLikedPhotos::class)->name('user.likes');
        Route::get('/users', ManageUsers::class)->name('users.index');
        // Añade aquí otras rutas de admin que necesites

        // --- Ruta para INICIAR Impersonación ---
        // Usa Route Model Binding para obtener el usuario directamente
        Route::get('/users/{user}/impersonate', [ImpersonationController::class, 'take'])
             ->name('impersonate.take')
             ->whereNumber('user'); // Asegura que {user} sea un número (ID)
    }); // --- Fin Grupo Admin ---
    // --- Ruta para DEJAR Impersonación ---
    // Esta ruta debe ser accesible por alguien que *está* logueado,
    // pero la lógica del controlador ya verifica si está impersonando.
    Route::get('/impersonate/leave', [ImpersonationController::class, 'leave'])
             ->name('impersonate.leave');
}); // --- Fin Grupo Autenticado ---

// Las rutas de Login, Register, Password Reset, etc., son manejadas por Fortify/Jetstream
// No necesitas definirlas aquí normalmente.
