<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Paciente;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir las variables de alertas globales en la vista del layout
        View::composer('layouts.app', function ($view) {
            // 1. Conteo de pacientes con información incompleta
            $conteoIncompletos = \App\Models\Paciente::whereNull('dni')
                ->orWhereNull('fecha_nacimiento')
                ->orWhereNull('genero')
                ->orWhereNull('celular_personal')
                ->orWhereNull('distrito')
                ->count();

            // Buscamos citas de hoy que NO tengan una historia clínica asociada (atención pendiente)
            $hoy = \Carbon\Carbon::today()->toDateString();
            $conteoCitasHoy = \App\Models\Cita::where('fecha', $hoy)
                ->whereDoesntHave('historiaClinica') // Asegúrate de que tu modelo Cita tenga la relación 'historiaClinica'
                ->count();

            $view->with([
                'pacientesIncompletosCount' => $conteoIncompletos,
                'citasPendientesHoyCount'   => $conteoCitasHoy
            ]);
        });
    }
}