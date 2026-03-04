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
            Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained()->onDelete('cascade');
            $table->string('medicamento');
            $table->string('presentacion');
            $table->float('dosis');
            $table->string('via_administracion');
            $table->string('frecuencia'); 
            $table->string('duracion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
