<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('diagnosticos_atencion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->string('cie_10');
            $table->text('diagnostico');
            $table->timestamps();
        });
        
        Schema::table('historias_clinicas', function (Blueprint $table) {
            $table->dropColumn(['diagnostico', 'cie_10']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticos_atencion');
    }
};
