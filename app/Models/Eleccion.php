<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleccion extends Model
{
    /** @use HasFactory<\Database\Factories\EleccionFactory> */
    use HasFactory;

    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_ABIERTA = 'abierta';
    public const ESTADO_CERRADA = 'cerrada';
    public const ESTADO_PUBLICADA = 'publicada';
    public const ESTADO_ANULADA = 'anulada';

    protected $table = 'elecciones';

    protected $fillable = [
        'campamento_id',
        'creado_por',
        'titulo',
        'descripcion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'resultados_publicados',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'resultados_publicados' => 'boolean',
        ];
    }

    public function campamento()
    {
        return $this->belongsTo(Campamento::class);
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function puestos()
    {
        return $this->belongsToMany(Puesto::class, 'eleccion_puestos')->withPivot(['orden', 'activo'])->withTimestamps();
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class);
    }

    public function votantesEleccion()
    {
        return $this->hasMany(VotanteEleccion::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
}
