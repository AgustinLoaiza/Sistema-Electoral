<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return view('dashboard.super-admin');
    }

    public function admin()
    {
        return view('dashboard.admin');
    }

    public function votante()
    {
        return view('dashboard.votante');
    }
}
