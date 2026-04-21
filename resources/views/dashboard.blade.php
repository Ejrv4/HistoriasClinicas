@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-uppercase m-0" style="letter-spacing: -1px; color: #1e293b;">Gestión de citas</h2>
            <p class="text-muted small fw-medium">{{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pacientes.create') }}" class="btn btn-success shadow-sm d-flex align-items-center fw-bold">
                <i class="bi bi-person-plus me-2"></i> Nuevo Paciente
            </a>
            <a href="{{ route('citas.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center fw-bold">
                <i class="bi bi-calendar-event me-2"></i> Agendar Cita
            </a>
        </div>
    </div>

    {{-- Filtro de Fecha y Leyenda --}}
    <div class="row mb-4">
        <div class="col-md-4 ms-auto text-end">
            <div class="card border-0 shadow-sm rounded-3 mb-2">
                <div class="card-body p-2">
                    <form action="{{ route('dashboard') }}" method="GET" id="form-fecha">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-primary"></i></span>
                            <input type="date" name="fecha" id="calendario-input" class="form-control border-0 fw-bold" 
                                   value="{{ $fechaSeleccionada }}" 
                                   onchange="document.getElementById('form-fecha').submit()">
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3 px-1">
                <small class="text-muted"><span class="dot-legend bg-pending"></span> Hay Pendientes</small>
                <small class="text-muted"><span class="dot-legend bg-attended"></span> Todo Atendido</small>
            </div>
        </div>
    </div>

    {{-- Tabla de Citas --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-dark">Lista de Atenciones</h5>
        </div>
        <div class="p-0">
            <table id="tabla-citas" class="table table-hover align-middle mb-0 w-100">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Hora</th>
                        <th>DNI</th>
                        <th>Paciente</th>
                        <th class="text-center">Edad</th>
                        <th>Motivo de Cita</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4 no-sort">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($citas as $cita)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}</td>
                            <td class="text-muted small">{{ $cita->paciente->dni }}</td>
                            <td><div class="fw-semibold">{{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }}</div></td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }}</td>
                            <td class="small text-muted">{{ Str::limit($cita->motivo, 40) }}</td>
                            <td class="text-center">
                                @if($cita->estado == 'Atendido')
                                    <span class="badge-status st-atendido">Atendido</span>
                                @elseif($cita->estado == 'Cancelado')
                                    <span class="badge-status st-cancelado">Cancelado</span>
                                @else
                                    <span class="badge-status st-pendiente">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    @if($cita->estado == 'Atendido')
                                        @if($cita->recetas && $cita->recetas->count() > 0)
                                            <a href="{{ route('receta.pdf', $cita->id) }}" target="_blank" class="btn btn-action btn-pdf" title="Imprimir Receta">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        @endif
                                        @if($cita->historiaClinica)
                                            <a href="{{ route('historias.edit', $cita->historiaClinica->id) }}" class="btn btn-action btn-edit" title="Editar Ficha">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                    @elseif($cita->estado == 'Cancelado')
                                        <span class="text-muted small fst-italic">Sin acciones</span>
                                    @else
                                        <a href="{{ route('historias.create', ['cita_id' => $cita->id]) }}" class="btn btn-dark btn-sm fw-bold px-3 rounded-3 shadow-sm">
                                            <i class="bi bi-person-check me-1"></i> Atender
                                        </a>
                                        <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-action btn-cancel" title="Cancelar Cita" onclick="confirmarCancelacion(this)">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Scripts --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    $(document).ready(function() {
        $('#tabla-citas').DataTable({
            "order": [[ 0, "asc" ]],
            "columnDefs": [{ "targets": 'no-sort', "orderable": false }],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Buscar paciente:",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            },
            "dom": '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>'
        });

        const resumenCitas = @json($resumenCitas ?? []);

        flatpickr("#calendario-input", {
            locale: "es",
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj;
                const offset = date.getTimezoneOffset();
                const adjustedDate = new Date(date.getTime() - (offset * 60 * 1000));
                const dateString = adjustedDate.toISOString().split('T')[0];

                if (resumenCitas[dateString]) {
                    if (resumenCitas[dateString] === 'pendiente') {
                        dayElem.classList.add('day-has-pending');
                    } else if (resumenCitas[dateString] === 'atendido') {
                        dayElem.classList.add('day-has-attended');
                    }
                }
            }
        });
    });

    function confirmarCancelacion(button) {
        if (confirm('¿Estás seguro de que deseas cancelar esta cita? Esta acción no se puede deshacer.')) {
            button.closest('form').submit();
        }
    }
</script>

<style>
    /* Alineación DataTables */
    .dataTables_info { font-size: 0.85rem; font-weight: 600; color: #64748b !important; }
    .pagination { margin-bottom: 0 !important; }
    .dataTables_filter input { border-radius: 20px; padding: 6px 16px; border: 1px solid #e2e8f0; outline: none; width: 250px !important; }

    /* Colores */
    :root {
        --attended-bg: #dcfce7; --attended-text: #166534; --attended-border: #86efac;
        --pending-bg: #f1f5f9; --pending-text: #475569; --pending-border: #cbd5e1;
        --cancelled-bg: #fee2e2; --cancelled-text: #991b1b; --cancelled-border: #fecaca;
    }

    .fw-black { font-weight: 900; }

    /* Badges de Estado */
    .badge-status { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; border: 1px solid transparent; }
    .st-atendido { background: var(--attended-bg); color: var(--attended-text); border-color: var(--attended-border); }
    .st-pendiente { background: var(--pending-bg); color: var(--pending-text); border-color: var(--pending-border); }
    .st-cancelado { background: var(--cancelled-bg); color: var(--cancelled-text); border-color: var(--cancelled-border); }

    /* Botones de Acción */
    .btn-action { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: white; transition: all 0.2s; }
    .btn-pdf { color: #dc3545; } .btn-pdf:hover { background: #dc3545; color: white; }
    .btn-edit { color: #0d6efd; } .btn-edit:hover { background: #0d6efd; color: white; }
    .btn-cancel { color: #dc3545; border-color: var(--cancelled-border); } .btn-cancel:hover { background: #dc3545; color: white; }

    /* Calendario (Color de Celda Completa) */
    .flatpickr-day.day-has-pending { 
        background: var(--pending-bg) !important; 
        color: var(--pending-text) !important; 
        border-color: var(--pending-border) !important; 
    }
    .flatpickr-day.day-has-attended { 
        background: var(--attended-bg) !important; 
        color: var(--attended-text) !important; 
        border-color: var(--attended-border) !important; 
    }
    .flatpickr-day.selected { background: #0d6efd !important; color: white !important; }

    /* Leyenda */
    .dot-legend { width: 10px; height: 10px; display: inline-block; border-radius: 50%; margin-right: 2px; }
    .bg-pending { background: var(--pending-border); } 
    .bg-attended { background: var(--attended-border); }
    .bg-cancelled { background: var(--cancelled-border); }
</style>
@endsection