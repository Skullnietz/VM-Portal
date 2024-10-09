<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        session_start();
        // Verifica si $_SESSION['usuario'] está definido
        if (!isset($_SESSION['usuario'])) {
            // Redirigir a una página de inicio de sesión o mostrar un mensaje de error
            return redirect('/login')->with('error', 'Debes estar autenticado para acceder a esta página.');
        }

        return $next($request);
    }
}
