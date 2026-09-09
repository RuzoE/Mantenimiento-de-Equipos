<?php

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Equipo::class)->constrained()->cascadeOnDelete();
            $table->foreignId('ubicacion_origen_id')->constrained('ubicaciones')->restrictOnDelete();
            $table->foreignId('ubicacion_destino_id')->constrained('ubicaciones')->restrictOnDelete();
            $table->date('fecha')->index();
            $table->string('motivo');
            $table->text('observaciones')->nullable();
            $table->foreignIdFor(User::class, 'registrado_por_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
