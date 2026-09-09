<?php

use App\Models\Mantenimiento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mantenimiento::class)->constrained()->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime');
            $table->unsignedInteger('tamano');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_evidencias');
    }
};
