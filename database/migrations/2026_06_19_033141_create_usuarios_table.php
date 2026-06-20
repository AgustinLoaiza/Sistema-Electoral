<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campamento_id')->constrained('campamentos')->cascadeOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('nombre_usuario')->unique();
            $table->string('password');
            $table->enum('rol', ['super_admin', 'admin', 'votante'])->default('votante');
            $table->boolean('activo')->default(true);
            $table->boolean('debe_cambiar_password')->default(true);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
