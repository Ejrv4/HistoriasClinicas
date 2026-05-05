@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-calendar-plus me-2"></i>Nueva Cita Médica</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('citas.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">1. Buscar por Apellidos (mín. 2 letras)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarApellido" class="form-control form-control-lg" placeholder="Escriba apellido...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">2. Buscar por DNI (mín. 3 números)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="bi bi-card-text"></i></span>
                            <input type="text" id="buscarDNI" class="form-control form-control-lg" placeholder="Escriba DNI...">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Paciente Seleccionado</label>
                    <select name="paciente_id" id="selectPaciente" class="form-select border-primary" required size="5">
                        <option value="" disabled selected>Resultados de búsqueda...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" 
                                    data-apellido="{{ strtolower($p->apellido) }}" 
                                    data-dni="{{ $p->dni }}" 
                                    style="display: none;">
                                {{ strtoupper($p->apellido) }}, {{ $p->nombre }} (DNI: {{ $p->dni }})
                            </option>
                        @endforeach
                    </select>
                    <div id="statusBusqueda" class="form-text mt-2 fw-medium text-muted">
                        Ingrese datos para filtrar.
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Fecha de Cita</label>
                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Hora</label>
                        <input type="time" name="hora" class="form-control" 
                            value="{{ request('hora_redondeada') ?? (request('quick_start') ? date('H:i') : '') }}" 
                            required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Motivo de Cita</label>
                        {{-- CAMBIO: De Input a Select con opción por defecto 'Control' --}}
                        <select name="motivo" id="motivoCita" class="form-select" required>
                            <option value="Control" selected>Control</option>
                            <option value="Paciente nuevo">Paciente nuevo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Guardar Cita
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputApellido = document.getElementById('buscarApellido');
    const inputDNI = document.getElementById('buscarDNI');
    const select = document.getElementById('selectPaciente');
    const status = document.getElementById('statusBusqueda');
    const selectMotivo = document.getElementById('motivoCita');
    const options = Array.from(select.options).filter(opt => opt.value !== "");

    const urlParams = new URLSearchParams(window.location.search);
    const quickPacienteId = urlParams.get('paciente_id');
    const isQuickStart = urlParams.get('quick_start');

    if (quickPacienteId) {
        const optionToSelect = options.find(opt => opt.value == quickPacienteId);
        if (optionToSelect) {
            optionToSelect.style.display = "block"; 
            select.value = quickPacienteId; 
            status.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-person-check"></i> Paciente recién registrado seleccionado automáticamente.</span>';
        }
    }

    if (isQuickStart && selectMotivo) {
        selectMotivo.value = "Paciente nuevo";
        selectMotivo.focus();
    }
    // ---------------------------------------

    function filtrar() {
        const valApellido = inputApellido.value.toLowerCase().trim();
        const valDNI = inputDNI.value.trim();
        let coincidentes = [];

        options.forEach(opt => {
            const matchApellido = valApellido.length >= 2 && opt.dataset.apellido.includes(valApellido);
            const matchDNI = valDNI.length >= 3 && opt.dataset.dni.includes(valDNI);

            if (matchApellido || matchDNI) {
                opt.style.display = "block";
                coincidentes.push(opt);
            } else {
                if (opt.value == quickPacienteId && valApellido === "" && valDNI === "") {
                    opt.style.display = "block";
                } else {
                    opt.style.display = "none";
                }
            }
        });

        if (coincidentes.length === 1) {
            select.value = coincidentes[0].value;
            status.innerHTML = '<span class="text-success"><i class="bi bi-check-all"></i> Paciente encontrado.</span>';
        } else if (coincidentes.length > 1) {
            select.value = "";
            status.innerHTML = `<span class="text-primary"><i class="bi bi-info-circle"></i> ${coincidentes.length} coincidencias.</span>`;
        }
    }

    inputApellido.addEventListener('input', () => { inputDNI.value = ""; filtrar(); });
    inputDNI.addEventListener('input', () => { inputApellido.value = ""; filtrar(); });
});
</script>

<style>
    #selectPaciente {
        overflow-y: auto;
        border-radius: 8px;
    }
    #selectPaciente option {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    #selectPaciente option:checked {
        background-color: #e7f1ff !important;
        color: #0d6efd;
        font-weight: bold;
    }
</style>
@endsection