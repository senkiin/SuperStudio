<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Asegúrate de que Auth esté importado
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    /**
     * Inicia la impersonación de un usuario.
     */
    public function take(Request $request, User $user): RedirectResponse
    {
        // *** Especifica el guard 'web' al obtener el admin ***
        $admin = Auth::guard('web')->user();

        // Verificaciones (asumiendo que $admin no es null)
        if (!$admin) {
            abort(401); // O redirigir al login si no hay admin logueado en 'web'
        }
        if ($admin->role !== 'admin') {
            abort(403, 'No tienes permiso para impersonar.');
        }
        // ... (otras verificaciones como antes) ...
        if (Session::has('original_admin_id')) {
            abort(403, 'Ya estás impersonando a un usuario. Sal primero.');
        }

        Session::put('original_admin_id', $admin->id);

        // *** Especifica el guard 'web' al hacer login ***
        Auth::guard('web')->loginUsingId($user->id);
        $request->session()->regenerate();

        return redirect()->route('albums')->with('status', 'Ahora estás viendo como ' . $user->name);
    }

    /**
     * Finaliza la sesión de impersonación y vuelve al admin original.
     */
    public function leave(Request $request): RedirectResponse
    {
        if (!Session::has('original_admin_id')) {
            abort(403, 'No estás impersonando a nadie.');
        }

        $originalAdminId = Session::get('original_admin_id');

        // *** Especifica el guard 'web' al hacer login ***
        // Esta es la línea que daba el error (línea 66 aprox. en tu stack trace)
        Auth::guard('web')->loginUsingId($originalAdminId);
        $request->session()->regenerate();

        Session::forget('original_admin_id');

        return redirect()->route('admin.dashboard')->with('status', 'Has vuelto a tu sesión de administrador.');
    }
}
