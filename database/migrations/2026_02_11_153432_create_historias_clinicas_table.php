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

        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('cita_id')->constrained()->onDelete('cascade');
            $table->text('anamnesis')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->text('diagnostico')->nullable();
            $table->string('cie_10')->nullable();
            $table->text('plan')->nullable();
            $table->timestamps();
        });

        Schema::create('antecedentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['Médico', 'Quirúrgico', 'Alergia', 'Medicación', 'Otros']);
            $table->text('descripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_clinicas');
    }
};
