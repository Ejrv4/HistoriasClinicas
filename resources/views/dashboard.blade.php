@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Gestión de citas</h2>
        <p class="text-muted">{{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pacientes.create') }}" class="btn btn-success shadow-sm d-flex align-items-center">
            <i class="bi bi-person-plus me-2"></i> Nuevo Paciente
        </a>
        <a href="{{ route('citas.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <i class="bi bi-calendar-event me-2"></i> Agendar Cita
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 ms-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('dashboard') }}" method="GET" id="form-fecha">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="fecha" id="calendario-input" class="form-control" 
                               value="{{ $fechaSeleccionada }}" 
                               onchange="document.getElementById('form-fecha').submit()">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="mb-0 fw-bold">Lista de Atenciones</h5>
    </div>
    <div class="p-3">
        <table id="tabla-citas" class="table table-hover align-middle mb-0 w-100">
            <thead class="bg-light text-muted">
                <tr>
                    <th>Hora</th>
                    <th>DNI</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th class="text-center">Edad</th>
                    <th>Motivo de Cita</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end no-sort">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                    <tr>
                        <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</td>
                        <td class="text-muted">{{ $cita->paciente->dni }}</td>
                        <td class="fw-semibold">{{ $cita->paciente->apellido }}</td>
                        <td>{{ $cita->paciente->nombre }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }}</td>
                        <td>{{ $cita->motivo }}</td>
                        <td class="text-center">
                            @if($cita->estado == 'Atendido')
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3">Atendido</span>
                            @else
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary px-3">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
    <div class="d-flex justify-content-end gap-2">
        @if($cita->estado == 'Atendido')
            <a href="{{ route('receta.pdf', $cita->id) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-printer"></i>
            </a>

            {{-- CORRECCIÓN: Usar el nombre completo definido en el modelo --}}
            @if($cita->historiaClinica)
                <a href="{{ route('historias.edit', $cita->historiaClinica->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Editar ficha
                </a>
            @else
                <a href="{{ route('historias.create', ['cita_id' => $cita->id]) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-plus-circle me-1"></i> Completar ficha
                </a>
            @endif
        @else
            <a href="{{ route('historias.create', ['cita_id' => $cita->id]) }}" class="btn btn-sm btn-dark">
                <i class="bi bi-file-earmark-medical me-1"></i> Abrir ficha
            </a>
        @endif
    </div>
</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function() {
        $('#tabla-citas').DataTable({
            "order": [[ 0, "asc" ]],
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Buscar en esta fecha:"
            },
            "dom": '<"d-flex justify-content-between mb-3"f>rtip'
        });

        const fechasConCitas = @json($diasConCitas);
        flatpickr("#calendario-input", {
            locale: "es",
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj;
                const offset = date.getTimezoneOffset();
                const adjustedDate = new Date(date.getTime() - (offset * 60 * 1000));
                const dateString = adjustedDate.toISOString().split('T')[0];
                if (fechasConCitas.includes(dateString)) {
                    dayElem.innerHTML += "<span class='calendar-dot'></span>";
                }
            }
        });
    });
</script>

<style>
    .bg-success-subtle { background-color: #e8f5e9 !important; }
    .bg-secondary-subtle { background-color: #f5f5f5 !important; }
    .calendar-dot { height: 4px; width: 4px; background-color: #28a745; border-radius: 50%; position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); }
    .dataTables_filter input { border-radius: 20px; padding: 5px 15px; border: 1px solid #ddd; outline: none; }
</style>

@if(session('imprimir_receta'))
<script>
    setTimeout(function() {
        const url = "{{ session('imprimir_receta') }}";
        const win = window.open(url, '_blank');
        if (win) {
            win.focus();
        } else {
            alert('Atención guardada. El navegador bloqueó la apertura automática de la receta. Por favor, permite los pop-ups o usa el botón de la tabla.');
        }
    }, 800);
</script>
@endif
@endsection