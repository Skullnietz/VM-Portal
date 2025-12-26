<?php

namespace App\Http\Middleware;

use Closure;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

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
        session_start();
        // Verifica si $_SESSION['usuario'] está definido
        if (!isset($_SESSION['usuario'])) {

            // TRY TO RESTORE SESSION FROM COOKIE
            $cookieName = 'vm_remember_token';
            if (Cookie::get($cookieName)) {
                try {
                    $tokenData = Crypt::decrypt(Cookie::get($cookieName));

                    if (isset($tokenData['id']) && isset($tokenData['type'])) {
                        $user = null;

                        if ($tokenData['type'] == 'cliente') {
                            $user = DB::table('Cat_Usuarios')
                                ->where('Id_Usuario', $tokenData['id'])
                                ->where('Txt_Estatus', 1)
                                ->first();
                        } elseif ($tokenData['type'] == 'administrador') {
                            $user = DB::table('Cat_Usuarios_Administradores')
                                ->where('Id_Usuario_Admon', $tokenData['id'])
                                ->where('Txt_Estatus', 1)
                                ->first();
                            if ($user)
                                $user->Img_URL = '/Images/Usuarios/urvina.png';
                        } elseif ($tokenData['type'] == 'operador') {
                            $user = DB::table('Cat_Operadores')
                                ->where('Id_Operador', $tokenData['id'])
                                ->where('Txt_Estatus', 1)
                                ->first();
                            if ($user)
                                $user->Img_URL = '/Images/Usuarios/urvina.png';
                        }

                        if ($user) {
                            $_SESSION['usuario'] = $user;
                            return $next($request);
                        }
                    }
                } catch (DecryptException $e) {
                    // Invalid token, ignore and redirect to login
                }
            }

            // Redirigir a una página de inicio de sesión o mostrar un mensaje de error
            return redirect('/login')->with('error', 'Debes estar autenticado para acceder a esta página.');
        }

        return $next($request);
    }
}
