<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotanteEleccion extends Model
{
    /** @use HasFactory<\Database\Factories\VotanteEleccionFactory> */
    use HasFactory;

    protected $table = 'votantes_elecciones';

    protected $fillable = [
        'eleccion_id',
        'usuario_id',
        'habilitado',
        'voto_emitido',
        'fecha_voto',
    ];

    protected function casts(): array
    {
        return [
            'habilitado' => 'boolean',
            'voto_emitido' => 'boolean',
            'fecha_voto' => 'datetime',
        ];
    }

    public function eleccion()
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
