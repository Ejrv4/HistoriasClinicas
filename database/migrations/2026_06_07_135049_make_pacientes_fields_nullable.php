<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePacientesFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Convertimos todas las columnas en opcionales a nivel de base de datos
            $table->string('dni')->nullable()->change();
            $table->string('trabajo')->nullable()->change();
            $table->date('fecha_nacimiento')->nullable()->change();
            $table->string('genero')->nullable()->change();
            $table->string('celular_personal')->nullable()->change();
            $table->string('distrito')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('pais_nacimiento')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // En caso de revertir la migración, vuelven a ser estrictos
            $table->string('dni')->nullable(false)->change();
            $table->string('trabajo')->nullable(false)->change();
            $table->date('fecha_nacimiento')->nullable(false)->change();
            $table->string('genero')->nullable(false)->change();
            $table->string('celular_personal')->nullable(false)->change();
            $table->string('distrito')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('pais_nacimiento')->nullable(false)->change();
        });
    }
}