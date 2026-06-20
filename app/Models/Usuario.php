<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory, Notifiable;

    public const ROL_SUPER_ADMIN = 'super_admin';
    public const ROL_ADMIN = 'admin';
    public const ROL_VOTANTE = 'votante';

    protected $table = 'usuarios';

    protected $fillable = [
        'campamento_id',
        'nombres',
        'apellidos',
        'nombre_usuario',
        'password',
        'rol',
        'activo',
        'debe_cambiar_password',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'debe_cambiar_password' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function campamento()
    {
        return $this->belongsTo(Campamento::class);
    }

    public function eleccionesCreadas()
    {
        return $this->hasMany(Eleccion::class, 'creado_por');
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class);
    }

    public function votantesEleccion()
    {
        return $this->hasMany(VotanteEleccion::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
