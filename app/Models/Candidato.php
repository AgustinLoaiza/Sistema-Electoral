<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    /** @use HasFactory<\Database\Factories\CandidatoFactory> */
    use HasFactory;

    public const ESTADO_POSTULADO = 'postulado';
    public const ESTADO_APROBADO = 'aprobado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_RETIRADO = 'retirado';

    protected $table = 'candidatos';

    protected $fillable = [
        'eleccion_id',
        'puesto_id',
        'usuario_id',
        'estado',
    ];

    public function eleccion()
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
}
