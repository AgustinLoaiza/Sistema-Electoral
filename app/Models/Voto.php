<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    /** @use HasFactory<\Database\Factories\VotoFactory> */
    use HasFactory;

    public const TIPO_CANDIDATO = 'candidato';
    public const TIPO_BLANCO = 'blanco';
    public const TIPO_NULO = 'nulo';

    protected $table = 'votos';

    protected $fillable = [
        'eleccion_id',
        'puesto_id',
        'candidato_id',
        'tipo_voto',
    ];

    public function eleccion()
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function candidato()
    {
        return $this->belongsTo(Candidato::class);
    }
}
