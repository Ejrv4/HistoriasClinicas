<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\AntecedenteController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\Cie10Controller;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [CitaController::class, 'index'])->name('dashboard');

Route::get('/citas/crear', [CitaController::class, 'create'])->name('citas.create');
Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
Route::post('/citas/{id}/cancelar', [App\Http\Controllers\CitaController::class, 'cancelar'])->name('citas.cancelar');

Route::get('/historias/create', [HistoriaClinicaController::class, 'create'])->name('historias.create');
Route::post('/historias', [HistoriaClinicaController::class, 'store'])->name('historias.store');
Route::post('/historias/autoguardar', [App\Http\Controllers\HistoriaClinicaController::class, 'autoguardar'])->name('historias.autoguardar');
Route::get('historias/{id}/edit', [HistoriaClinicaController::class, 'edit'])->name('historias.edit');
Route::put('historias/{id}', [HistoriaClinicaController::class, 'update'])->name('historias.update');

Route::post('/antecedentes', [AntecedenteController::class, 'store'])->name('antecedentes.store');
Route::post('/antecedentes/guardar-todo', [App\Http\Controllers\AntecedenteController::class, 'guardarTodo'])->name('antecedentes.guardar_todo');

Route::resource('pacientes', PacienteController::class);
Route::get('/pacientes/{id}/datos', [App\Http\Controllers\PacienteController::class, 'verDatos'])->name('pacientes.datos');

Route::get('/medicamentos', [App\Http\Controllers\MedicamentoController::class, 'index'])->name('medicamentos.index');
Route::post('/medicamentos', [App\Http\Controllers\MedicamentoController::class, 'store'])->name('medicamentos.store');
Route::put('/medicamentos/{id}', [App\Http\Controllers\MedicamentoController::class, 'updateInline'])->name('medicamentos.updateInline');
Route::delete('/medicamentos/{id}', [App\Http\Controllers\MedicamentoController::class, 'destroy'])->name('medicamentos.destroy');

Route::get('/receta/pdf/{cita_id}', [RecetaController::class, 'generarPDF'])->name('receta.pdf');

Route::get('/cie10', [Cie10Controller::class, 'index'])->name('cie10.index');
Route::post('/cie10', [Cie10Controller::class, 'store'])->name('cie10.store');
Route::put('/cie10/{id}/inline', [Cie10Controller::class, 'updateInline'])->name('cie10.updateInline');
Route::delete('/cie10/{id}', [Cie10Controller::class, 'destroy'])->name('cie10.destroy');

