<?php

namespace App\Http\Controllers;

use App\Models\Campamento;
use App\Models\Eleccion;
use App\Models\VotanteEleccion;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        return match ($usuario->rol) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('votante.dashboard'),
        };
    }

    public function superAdmin()
    {
        $campamentos = Campamento::query()
            ->withCount(['usuarios', 'elecciones'])
            ->orderBy('nombre', 'asc')
            ->get();

        $elecciones = Eleccion::query()
            ->with('campamento')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboards.super-admin', compact('campamentos', 'elecciones'));
    }

    public function admin()
    {
        $usuario = Auth::user();

        $campamentos = Campamento::query()
            ->where('id', $usuario->campamento_id)
            ->withCount(['usuarios', 'elecciones'])
            ->get();

        $elecciones = Eleccion::query()
            ->with('campamento')
            ->where('campamento_id', $usuario->campamento_id)
            ->latest()
            ->get();

        return view('dashboards.admin', compact('campamentos', 'elecciones'));
    }

    public function votante()
    {
        $usuario = Auth::user();

        $participaciones = VotanteEleccion::query()
            ->with('eleccion.campamento')
            ->where('usuario_id', $usuario->id)
            ->latest()
            ->get();

        return view('dashboards.votante', compact('participaciones'));
    }
}
