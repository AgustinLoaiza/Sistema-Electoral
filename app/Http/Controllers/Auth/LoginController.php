<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nombre_usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($request->input('nombre_usuario')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['nombre_usuario' => 'Demasiados intentos. Intenta nuevamente en unos segundos.']);
        }

        $usuario = Usuario::query()->where('nombre_usuario', $request->nombre_usuario)->first();

        if (! $usuario || ! $usuario->activo) {
            RateLimiter::hit($key, 60);

            AuditLog::create([
                'usuario_id' => $usuario?->id,
                'accion' => 'login_fallido',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario?->id,
                'descripcion' => 'Intento de acceso con usuario inexistente o inactivo.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'nombre_usuario' => 'Las credenciales no son correctas.',
            ]);
        }

        if (! Auth::attempt([
            'nombre_usuario' => $request->nombre_usuario,
            'password' => $request->password,
            'activo' => true,
        ], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            AuditLog::create([
                'usuario_id' => $usuario->id,
                'accion' => 'login_fallido',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'descripcion' => 'Intento de acceso con contraseña incorrecta.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'nombre_usuario' => 'Las credenciales no son correctas.',
            ]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();

        $usuario->update(['ultimo_acceso' => now()]);

        AuditLog::create([
            'usuario_id' => $usuario->id,
            'accion' => 'login_exitoso',
            'tabla_afectada' => 'usuarios',
            'registro_id' => $usuario->id,
            'descripcion' => 'Inicio de sesión exitoso.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($usuario->debe_cambiar_password) {
            return redirect()->route('password.change');
        }

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $usuario = Auth::user();

        if ($usuario) {
            AuditLog::create([
                'usuario_id' => $usuario->id,
                'accion' => 'logout',
                'tabla_afectada' => 'usuarios',
                'registro_id' => $usuario->id,
                'descripcion' => 'Cierre de sesión.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
