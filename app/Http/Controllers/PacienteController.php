<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class PacienteController extends Controller
{
    // Listar todos los pacientes
    public function index()
    {
        $pacientes = Paciente::all();
        $diasConCitas = Cita::select('fecha')->distinct()->pluck('fecha')->toArray();
        return view('paciente.index', compact('pacientes', 'diasConCitas'));
    }

    // Mostrar formulario de registro
    public function create()
    {
        return view('paciente.create');
    }

    // Guardar el nuevo paciente en la BD
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|unique:pacientes,dni',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'trabajo' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required',
            'celular_personal' => 'required',
            'correo' => 'nullable|email',
            'distrito' => 'required',
            'direccion' => 'nullable',
            'pais_nacimiento' => 'required'
        ],[
            'dni.required' => 'El campo DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya se encuentra registrado en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'correo.email' => 'Debes ingresar un formato de correo válido.',
        ]);

        $paciente = Paciente::create($validated);

        // LÓGICA DEL BOTÓN CREAR CITA AHORA
        if ($request->has('crear_cita_ahora')) {
            $ahora = Carbon::now('America/Lima');
            $minutos = $ahora->minute;

            if ($minutos > 0 && $minutos <= 30) {
                $ahora->minute(30)->second(0);
            } elseif ($minutos > 30) {
                $ahora->addHour()->minute(0)->second(0);
            }
        
            $horaCita = $ahora->format('H:i');

            return redirect()->route('citas.create', [
                'paciente_id' => $paciente->id, 
                'quick_start' => true,
                'hora_redondeada' => $horaCita
            ])->with('success', 'Paciente registrado. Complete los datos de la cita.');
        }

        return redirect()->route('dashboard')->with('success', 'Paciente registrado correctamente.');
    }

    public function buscar(Request $request)
    {
        $busqueda = $request->get('term');
        $pacientes = Paciente::where('dni', 'LIKE', "%$busqueda%")
                            ->orWhere('nombre', 'LIKE', "%$busqueda%")
                            ->orWhere('apellido', 'LIKE', "%$busqueda%")
                            ->get();
        
        return response()->json($pacientes);
    }
    
    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('paciente.edit', compact('paciente'));
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);

        $validated = $request->validate([
            'dni' => 'required|unique:pacientes,dni,' . $id,
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'trabajo' => 'nullable|string|max:255',
            'celular_personal' => 'required',
            'distrito' => 'required',
            'direccion' => 'required',
            'correo' => 'nullable|email',
            'genero' => 'nullable',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.index')->with('success', 'Datos del paciente actualizados.');
    }

    public function verDatos($id)
    {
        $paciente = Paciente::findOrFail($id);
        $antecedentes = \App\Models\Antecedente::where('paciente_id', $id)->get();
        
        $historial = \App\Models\HistoriaClinica::where('paciente_id', $id)
                        ->with(['cita.recetas', 'diagnosticos']) // Incluimos diagnósticos múltiples
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('paciente.datos', compact('paciente', 'antecedentes', 'historial'));
    }

    public function destroy($id)
    {
        try {
            $paciente = \App\Models\Paciente::findOrFail($id);

            // 1. Conseguimos los IDs de las historias clínicas del paciente para no dejar diagnósticos huérfanos
            $historiasIds = \App\Models\HistoriaClinica::where('paciente_id', $id)->pluck('id');
            
            // 2. Purgamos en cascada de forma manual y ordenada para evitar errores de llaves foráneas
            \App\Models\DiagnosticoAtencion::whereIn('historia_clinica_id', $historiasIds)->delete();
            \App\Models\HistoriaClinica::where('paciente_id', $id)->delete();
            \App\Models\Antecedente::where('paciente_id', $id)->delete();
            
            // Buscamos las citas para borrar las recetas médicas vinculadas a ellas primero
            $citasIds = \App\Models\Cita::where('paciente_id', $id)->pluck('id');
            \Illuminate\Support\Facades\DB::table('recetas')->whereIn('cita_id', $citasIds)->delete();
            \App\Models\Cita::where('paciente_id', $id)->delete();

            // 3. Finalmente eliminamos al paciente
            $paciente->delete();

            // Respondemos en JSON para que el Javascript de la vista remueva la fila con animación
            return response()->json([
                'status' => 'success',
                'message' => 'Paciente y todo su historial clínico eliminados permanentemente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo eliminar el registro: ' . $e->getMessage()
            ], 500);
        }
    }
}