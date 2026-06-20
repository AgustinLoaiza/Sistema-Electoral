<?php

namespace Database\Seeders;

use App\Models\Campamento;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $campamento = Campamento::updateOrCreate(
            ['numero' => env('DEFAULT_CAMPAMENTO_NUMERO', '0001')],
            [
                'nombre' => env('DEFAULT_CAMPAMENTO_NOMBRE', 'Institucion DeMolay Bolivia'),
                'ciudad' => env('DEFAULT_CAMPAMENTO_CIUDAD', 'Bolivia'),
                'activo' => true,
            ]
        );

        $superAdmin = Usuario::firstOrNew([
            'nombre_usuario' => env('SUPER_ADMIN_USERNAME', 'superadmin'),
        ]);

        $superAdmin->fill([
            'campamento_id' => $campamento->id,
            'nombres' => env('SUPER_ADMIN_NOMBRES', 'Super'),
            'apellidos' => env('SUPER_ADMIN_APELLIDOS', 'Administrador'),
            'rol' => Usuario::ROL_SUPER_ADMIN,
            'activo' => true,
        ]);

        if (! $superAdmin->exists) {
            $superAdmin->password = env('SUPER_ADMIN_PASSWORD', 'Cambiar12345!');
            $superAdmin->debe_cambiar_password = true;
        }

        $superAdmin->save();
    }
}
