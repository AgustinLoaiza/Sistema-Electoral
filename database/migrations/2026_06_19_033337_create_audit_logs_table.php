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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->enum('accion', [
                'login_exitoso',
                'login_fallido',
                'logout',
                'cambio_password',
                'crear_usuario',
                'actualizar_usuario',
                'inhabilitar_usuario',
                'crear_eleccion',
                'abrir_eleccion',
                'cerrar_eleccion',
                'publicar_resultados',
                'emitir_voto',
            ]);
            $table->string('tabla_afectada')->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
