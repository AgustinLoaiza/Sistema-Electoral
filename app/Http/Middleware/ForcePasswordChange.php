<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();
        if ($usuario && $usuario->debe_cambiar_password) {
            if (!$request->routeIs('password.change', 'password.update', 'logout')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
