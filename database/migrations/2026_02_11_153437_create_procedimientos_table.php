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
        Schema::create('procedimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->string('tipo'); // Ej: Sutura, Drenaje
            $table->text('tecnica');
            $table->string('anestesia')->nullable();
            $table->text('materiales')->nullable();
            $table->text('complicaciones')->nullable();
            $table->text('indicaciones_postop')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedimientos');
    }
};
