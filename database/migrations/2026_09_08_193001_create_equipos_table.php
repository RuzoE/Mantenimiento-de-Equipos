<?php

use App\Enums\EstadoEquipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('codigo_interno')->unique();
            $table->foreignId('tipo_equipo_id')->constrained('tipos_equipos')->restrictOnDelete();
            $table->foreignId('marca_id')->constrained('marcas')->restrictOnDelete();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable()->unique();

            // Ubicación y responsable
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->restrictOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('responsables')->nullOnDelete();

            // Estado
            $table->string('estado')->default(EstadoEquipo::Operativo->value)->index();

            // Fechas
            $table->date('fecha_adquisicion')->nullable();
            $table->date('fecha_garantia')->nullable();

            // Especificaciones
            $table->string('procesador')->nullable();
            $table->string('memoria_ram')->nullable();
            $table->string('almacenamiento')->nullable();
            $table->string('sistema_operativo')->nullable();

            $table->text('observaciones')->nullable();

            $table->boolean('activo')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
