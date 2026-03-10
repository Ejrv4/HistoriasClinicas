<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('buscar');
        $fechaSeleccionada = $request->input('fecha', date('Y-m-d'));

        // CORRECCIÓN: Agregamos 'historiaClinica' al método with
        $query = Cita::with(['paciente', 'historiaClinica']) 
                    ->whereDate('fecha', $fechaSeleccionada);

        if ($busqueda) {
            $query->whereHas('paciente', function($q) use ($busqueda) {
                $q->where('dni', 'LIKE', "%$busqueda%")
                ->orWhere('nombre', 'LIKE', "%$busqueda%")
                ->orWhere('apellido', 'LIKE', "%$busqueda%");
            });
        }

        $citas = $query->orderBy('hora', 'asc')->get();
        $diasConCitas = Cita::select('fecha')->distinct()->pluck('fecha')->toArray();

        return view('dashboard', compact('citas', 'diasConCitas', 'fechaSeleccionada'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::all(); // Para seleccionar un paciente existente
        return view('cita.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        Cita::create($request->all());
        return redirect()->route('dashboard');
    }
}
