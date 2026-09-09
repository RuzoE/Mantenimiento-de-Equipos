<?php

use App\Models\Equipo;
use App\Models\Responsable;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Equipo::class)->constrained()->cascadeOnDelete();
            $table->string('tipo')->index();
            $table->date('fecha')->index();
            $table->foreignIdFor(Responsable::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(User::class, 'registrado_por_id')->constrained('users')->restrictOnDelete();
            $table->string('estado_antes')->nullable();
            $table->string('estado_despues')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
