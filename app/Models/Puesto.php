<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    /** @use HasFactory<\Database\Factories\PuestoFactory> */
    use HasFactory;

    protected $table = 'puestos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function elecciones()
    {
        return $this->belongsToMany(Eleccion::class, 'eleccion_puestos')->withPivot(['orden', 'activo'])->withTimestamps();
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
}
