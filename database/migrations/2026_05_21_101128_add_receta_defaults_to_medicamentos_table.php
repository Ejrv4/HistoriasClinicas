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
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->decimal('dosis', 8, 1)->nullable()->after('presentacion');
            $table->string('via_administracion')->nullable()->after('dosis');
            $table->string('frecuencia')->nullable()->after('via_administracion'); // Ej: "8 Horas" o "1 Días"
            $table->string('duracion')->nullable()->after('frecuencia');   // Ej: "7 Días"
            $table->integer('cantidad_total')->nullable()->after('duracion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropColumn(['dosis', 'via_administracion', 'frecuencia', 'duracion', 'cantidad_total']);
        });
    }
};
