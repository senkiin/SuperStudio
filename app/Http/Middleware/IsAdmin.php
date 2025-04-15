<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verifica si el usuario está autenticado
        // 2. Verifica si la propiedad 'role' del usuario es 'admin' (¡Ajusta si tu rol se llama diferente!)
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Si cumple ambas, permite que la petición continúe
            return $next($request);
        }

        // Si no es admin o no está logueado, deniega el acceso (Error 403 Forbidden)
        abort(403, 'Acción no autorizada.');
    }
}
