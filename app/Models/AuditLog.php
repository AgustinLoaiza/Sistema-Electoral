<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'usuario_id',
        'accion',
        'tabla_afectada',
        'registro_id',
        'descripcion',
        'ip_address',
        'user_agent',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
