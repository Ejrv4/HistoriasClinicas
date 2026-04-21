<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('buscar');
        // Si no hay fecha en el request, usamos hoy (Hora de Lima)
        $fechaSeleccionada = $request->input('fecha', Carbon::now('America/Lima')->format('Y-m-d'));

        // 1. Query para la tabla (Incluimos 'recetas' para la lógica del botón de impresión)
        $query = Cita::with(['paciente', 'historiaClinica', 'recetas']) 
                    ->whereDate('fecha', $fechaSeleccionada);

        if ($busqueda) {
            $query->whereHas('paciente', function($q) use ($busqueda) {
                $q->where('dni', 'LIKE', "%$busqueda%")
                  ->orWhere('nombre', 'LIKE', "%$busqueda%")
                  ->orWhere('apellido', 'LIKE', "%$busqueda%");
            });
        }

        $citas = $query->orderBy('hora', 'asc')->get();

        // 2. LÓGICA DE COLORES PARA EL CALENDARIO (REGLA SOLICITADA)
        $todasLasCitas = Cita::select('fecha', 'estado')->get();

        $resumenCitas = $todasLasCitas->groupBy('fecha')->map(function ($grupo) {
            // REGLA: Si hay al menos una cita 'Pendiente', el día es Gris.
            // Si NO hay pendientes (es decir, todos son 'Atendido' o 'Cancelado'), el día es Verde.
            $hayPendientes = $grupo->contains('estado', 'Pendiente');

            return $hayPendientes ? 'pendiente' : 'atendido';
        });

        // 3. Extraemos las fechas para Flatpickr
        $diasConCitas = $resumenCitas->keys()->toArray();

        return view('dashboard', compact('citas', 'resumenCitas', 'diasConCitas', 'fechaSeleccionada'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::orderBy('apellido', 'asc')->get();
        return view('cita.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha'       => 'required|date',
            'hora'        => 'required',
            'motivo'      => 'nullable|string|max:500',
        ]);

        // Toda cita nueva nace como 'Pendiente'
        $validated['estado'] = 'Pendiente';

        Cita::create($validated);

        return redirect()->route('dashboard')->with('success', 'Cita agendada correctamente.');
    }

    public function cancelar($id)
    {
        $cita = Cita::findOrFail($id);
        
        // Solo permitimos cancelar si no ha sido atendida previamente
        if ($cita->estado === 'Pendiente') {
            $cita->update(['estado' => 'Cancelado']);
            return redirect()->back()->with('success', 'La cita ha sido cancelada correctamente.');
        }

        return redirect()->back()->with('error', 'No se puede cancelar una cita ya atendida.');
    }
}