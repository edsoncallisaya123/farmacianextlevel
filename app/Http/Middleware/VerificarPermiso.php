<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarPermiso
{
    public function handle(Request $request, Closure $next, string $clave)
    {
        $user = $request->user();
        if (!$user || !$user->tienePermiso($clave)) {
            abort(403, 'No tiene permiso para acceder a esta sección.');
        }
        return $next($request);
    }
}
