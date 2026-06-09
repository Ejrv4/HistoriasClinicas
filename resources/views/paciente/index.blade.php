@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Bloque Superior de Título y Botones --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Directorio General de Historias Clínicas</h2>
            <p class="text-muted">Visualización y gestión de todas las historias clínicas registradas en el sistema.</p>
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

    {{-- LEYENDA INFORMATIVA DE ESTADOS CLÍNICOS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3 bg-white rounded">
            <div class="d-flex align-items-center flex-wrap gap-4 small">
                <span class="fw-bold text-secondary text-uppercase"><i class="bi bi-info-circle-fill me-1 text-primary"></i> Leyenda de Control:</span>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded me-2" style="width: 20px; height: 12px; background-color: #ffffff; border: 1px solid #dee2e6;"></span>
                    <span class="text-muted fw-medium">Fila Blanca: Expediente Completo</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded me-2" style="width: 20px; height: 12px; background-color: #e6f0fa; border: 1px solid #b3d1f0;"></span>
                    <span class="text-dark fw-bold" style="color: #2c5282 !important;">Fila Azul Claro: Registro Incompleto (Faltan Datos Esenciales)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-primary">Registro de Pacientes</h5>
        </div>
        <div class="p-4 pt-0">
            <div class="table-responsive">
                <table id="tabla-pacientes-general" class="table table-hover align-middle w-100">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="d-none">Estado Oculto</th> {{-- Columna invisible auxiliar para ordenar --}}
                            <th>DNI</th>
                            <th>Apellidos</th>
                            <th>Nombres</th>
                            <th class="text-center">Género</th>
                            <th class="text-center">Fecha Nac.</th>
                            <th class="text-center">Edad</th>
                            <th>Celular Personal</th>
                            <th>Distrito</th>
                            <th class="text-end no-sort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                            @php
                                $camposVacios = empty($paciente->dni) || 
                                                empty($paciente->fecha_nacimiento) || 
                                                empty($paciente->genero) || 
                                                empty($paciente->celular_personal) || 
                                                empty($paciente->distrito);

                                $isIncompleto = ($camposVacios && !$paciente->ignorar_alerta);
                            @endphp
                            
                            {{-- Inyección condicional de clase CSS si es incompleto --}}
                            <tr id="row-paciente-{{ $paciente->id }}" class="{{ $isIncompleto ? 'fila-incompleta' : '' }}">
                                
                                {{-- Celda de peso para que DataTables empuje arriba (0 para incompleto, 1 para completo) --}}
                                <td class="d-none">{{ $isIncompleto ? '0' : '1' }}</td>
                                
                                <td class="fw-bold">
                                    {{ $paciente->dni ?? 'N/R' }}
                                    @if($isIncompleto)
                                        <span class="badge bg-primary text-white ms-1" style="font-size: 0.65rem; padding: 2px 4px;">FALTAN DATOS</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $paciente->apellido }}</td>
                                <td>{{ $paciente->nombre }}</td>
                                <td class="text-center">
                                    @if($paciente->genero == 'Masculino')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">M</span>
                                    @elseif($paciente->genero == 'Femenino')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">F</span>
                                    @elseif($paciente->genero == 'Otros')
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">O</span>
                                    @else
                                        <span class="text-muted small italic">N/R</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    {{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') : 'N/R' }}
                                </td>
                                <td class="text-center">
                                    @if($paciente->fecha_nacimiento)
                                        {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años
                                    @else
                                        <span class="text-muted small italic">N/R</span>
                                    @endif
                                </td>
                                <td>
                                    @if($paciente->celular_personal)
                                        <i class="bi bi-phone text-muted me-1"></i> {{ $paciente->celular_personal }}
                                    @else
                                        <span class="text-muted small italic">N/R</span>
                                    @endif
                                </td>
                                <td>{{ $paciente->distrito ?? 'N/R' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary shadow-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('pacientes.datos', $paciente->id) }}">
                                                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Ver Antecedentes / Expediente
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('pacientes.edit', $paciente->id) }}">
                                                    <i class="bi bi-pencil-square me-2 text-dark"></i>Editar Datos del Paciente
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 text-danger" onclick="eliminarPacienteHistorial({{ $paciente->id }}, '{{ $paciente->apellido }}, {{ $paciente->nombre }}')">
                                                    <i class="bi bi-trash-fill me-2"></i>Eliminar Registro
                                                </button>
                                            </li>
                                            @if($isIncompleto && !$paciente->ignorar_alerta)
                                                <li>
                                                    <form action="{{ route('pacientes.ignorarAlertas', $paciente->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item py-2 text-warning">
                                                            <i class="bi bi-eye-slash me-2"></i> Ignorar alertas de datos
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let tablaGeneral;

    $(document).ready(function() {
        tablaGeneral = $('#tabla-pacientes-general').DataTable({
            // 🚨 ORDENAMIENTO EN CASCADA INTELIGENTE:
            // Prioridad 1: Columna 0 (Incompletos '0' primero, luego completos '1')
            // Prioridad 2: Columna 2 (Apellidos ordenados alfabéticamente de la A a la Z)
            "order": [[ 0, "asc" ], [ 2, "asc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Filtrar directorio:"
            },
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rtip',
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ]
        });
    });

    async function eliminarPacienteHistorial(id, nombreCompleto) {
        if (confirm(`⚠️ ¿Está completamente seguro de eliminar a "${nombreCompleto}"?\nEsta acción borrará permanentemente sus antecedentes, citas e historias clínicas registradas.`)) {
            try {
                const response = await fetch(`/pacientes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    const fila = document.getElementById(`row-paciente-${id}`);
                    if (fila) {
                        fila.style.transition = "all 0.4s ease";
                        fila.style.opacity = "0";
                        fila.style.transform = "scale(0.95)";
                        
                        setTimeout(() => {
                            tablaGeneral.row($(fila)).remove().draw(false);
                            alert("✅ Registro eliminado correctamente.");
                        }, 400);
                    }
                } else {
                    alert("Error: " + (result.message || "No se pudo completar la eliminación."));
                }
            } catch (error) {
                alert("Error crítico de conexión con el servidor.");
            }
        }
    }
</script>

<style>
    /* Estilización quirúrgica para filas con expedientes incompletos (Azul claro clínico) */
    .fila-incompleta td {
        background-color: #e6f0fa !important;
        color: #2b4c7e !important;
    }
    .fila-incompleta:hover td {
        background-color: #d9e8f5 !important;
    }
    .table-responsive {
        border-radius: 8px;
    }
</style>
@endsection