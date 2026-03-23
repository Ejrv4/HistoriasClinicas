@extends('layouts.app')

@section('content')
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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="mb-0 fw-bold text-primary">Registro de Pacientes</h5>
    </div>
    <div class="p-4 pt-0">
        <table id="tabla-pacientes-general" class="table table-hover align-middle w-100">
            <thead class="bg-light text-muted">
                <tr>
                    <th>DNI</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th class="text-center">Género</th>
                    <th class="text-center">Edad</th>
                    <th>Ocupación</th>
                    <th>Celular Personal</th>
                    <th>Distrito</th>
                    <th class="text-end no-sort">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pacientes as $paciente)
                <tr>
                    <td class="fw-bold">{{ $paciente->dni }}</td>
                    <td class="fw-semibold">{{ $paciente->apellido }}</td>
                    <td>{{ $paciente->nombre }}</td>
                    <td class="text-center">
                        @if($paciente->genero == 'Masculino')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">M</span>
                        @elseif($paciente->genero == 'Femenino')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">F</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">O</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años
                    </td>
                    {{-- DATO DE OCUPACIÓN --}}
                    <td>
                        <span class="text-dark small fw-medium">{{ $paciente->trabajo ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <i class="bi bi-phone text-muted me-1"></i> {{ $paciente->celular_personal }}
                    </td>
                    <td>{{ $paciente->distrito }}</td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary shadow-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-gear-fill"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('pacientes.datos', $paciente->id) }}">
                                        <i class="bi bi-person-lines-fill me-2 text-primary"></i>Ver Antecedentes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('pacientes.edit', $paciente->id) }}">
                                        <i class="bi bi-pencil-square me-2 text-dark"></i>Editar Datos del Paciente
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="#">
                                        <i class="bi bi-trash-fill me-2"></i>Eliminar Registro
                                    </a>
                                </li>
                            </ul>
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

<script>
    $(document).ready(function() {
        $('#tabla-pacientes-general').DataTable({
            "order": [[ 1, "asc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Filtrar directorio:"
            },
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rtip'
        });
    });
</script>
@endsection