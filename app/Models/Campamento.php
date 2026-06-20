<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campamento extends Model
{
    /** @use HasFactory<\Database\Factories\CampamentoFactory> */
    use HasFactory;

    protected $table = 'campamentos';

    protected $fillable = [
        'nombre',
        'numero',
        'ciudad',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function elecciones()
    {
        return $this->hasMany(Eleccion::class);
    }
}
