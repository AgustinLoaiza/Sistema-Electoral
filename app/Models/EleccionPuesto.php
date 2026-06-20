<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EleccionPuesto extends Model
{
    /** @use HasFactory<\Database\Factories\EleccionPuestoFactory> */
    use HasFactory;

    protected $table = 'eleccion_puestos';

    protected $fillable = [
        'eleccion_id',
        'puesto_id',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function eleccion()
    {
        return $this->belongsTo(Eleccion::class);
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class);
    }
}
