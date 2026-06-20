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
        Schema::create('elecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campamento_id')->constrained('campamentos')->cascadeOnDelete();
            $table->foreignId('creado_por')->constrained('usuarios')->restrictOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['borrador', 'abierta', 'cerrada', 'publicada', 'anulada'])->default('borrador');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('resultados_publicados')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elecciones');
    }
};
