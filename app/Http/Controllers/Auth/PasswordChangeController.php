<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario = Auth::user();

        if (! Hash::check($request->password_actual, $usuario->password)) {
            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        $usuario->update([
            'password' => $request->password,
            'debe_cambiar_password' => false,
        ]);

        AuditLog::create([
            'usuario_id' => $usuario->id,
            'accion' => 'cambio_password',
            'tabla_afectada' => 'usuarios',
            'registro_id' => $usuario->id,
            'descripcion' => 'El usuario cambió su contraseña.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('dashboard');
    }
}
