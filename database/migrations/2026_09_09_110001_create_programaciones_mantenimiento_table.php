<?php

use App\Enums\EstadoProgramacion;
use App\Models\Equipo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programaciones_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Equipo::class)->constrained()->cascadeOnDelete();
            $table->string('tipo');
            $table->string('frecuencia');
            $table->date('fecha_ultimo_mantenimiento')->nullable();
            $table->date('proxima_fecha')->index();
            $table->string('estado')->default(EstadoProgramacion::Programado->value)->index();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programaciones_mantenimiento');
    }
};
