<?php

use App\Models\Mantenimiento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mantenimiento::class)->constrained()->cascadeOnDelete();
            $table->string('descripcion');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_actividades');
    }
};
