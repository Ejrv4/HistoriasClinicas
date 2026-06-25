@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Botón Regresar Limpio --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0 transition-row-normal d-inline-flex align-items-center" style="font-size: 0.88rem;">
            <i class="bi bi-arrow-left me-2 fs-5"></i>REGRESAR AL CALENDARIO
        </a>
    </div>

    {{-- ENCABEZADO VIBRANTE COMPACTO --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 bg-white-50 rounded-3 text-white" style="background: rgba(255,255,255,0.15);">
                    <i class="bi bi-calendar-plus-fill fs-3"></i>
                </div>
                <div>
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.6rem; letter-spacing: -0.5px; font-weight: 900;">Nueva Cita Médica</h2>
                    <p class="m-0 small opacity-75 fw-medium">Asignación de turnos, horarios y motivos de consulta diarios.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD PRINCIPAL DEL FORMULARIO --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-search-heart text-primary me-2"></i> 1. Selección de Paciente
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('citas.store') }}" method="POST" autocomplete="off" class="m-0">
                @csrf
                
                {{-- SECCIÓN DE BÚSQUEDA DINÁMICA --}}
                <div class="row g-3 mb-3.5">
                    <div class="col-12 col-md-6">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Buscar por Apellidos (mín. 2 letras)</label>
                        <div class="position-relative w-100">
                            <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="buscarApellido" class="form-control rounded-3 py-2" placeholder="Escriba apellido paterno o materno..." style="padding-left: 40px; border: 1px solid #cbd5e1; font-size: 0.95rem;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Buscar por DNI (mín. 3 números)</label>
                        <div class="position-relative w-100">
                            <i class="bi bi-card-text position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="buscarDNI" maxlength="8" class="form-control rounded-3 py-2 font-monospace fw-semibold" placeholder="Escriba número de documento..." style="padding-left: 40px; border: 1px solid #cbd5e1; font-size: 0.95rem;">
                        </div>
                    </div>
                </div>

                {{-- VISOR DE RESULTADOS INTERACTIVOS --}}
                <div class="mb-4">
                    <label class="form-label font-monospace uppercase fw-bold text-dark-subtle" style="font-size: 0.72rem; letter-spacing: 0.5px;">Resultados del Directorio</label>
                    <select name="paciente_id" id="selectPaciente" class="form-select rounded-3 border" required size="5" style="border-color: #cbd5e1 !important;">
                        <option value="" disabled selected class="text-muted italic select-placeholder-custom">Utiliza los filtros de arriba para desplegar pacientes coincidentes...</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" 
                                    data-apellido="{{ strtolower($p->apellido) }}" 
                                    data-dni="{{ $p->dni }}" 
                                    style="display: none;" class="font-semibold text-dark">
                                {{ strtoupper($p->apellido) }}, {{ $p->nombre }} — DNI: {{ $p->dni ?? 'N/R' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="statusBusqueda" class="form-text mt-2 font-monospace small fw-bold text-muted">
                        <i class="bi bi-info-circle"></i> Ingrese datos filiatorios para indexar el expediente.
                    </div>
                </div>

                {{-- SECCIÓN 2: PARAMETRIZACIÓN DE LA CITA --}}
                <h5 class="text-dark fw-bold border-bottom pb-2 mt-4.5 mb-3.5 d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="bi bi-clock-history text-primary me-2 fs-5"></i> 2. Horario y Motivo Médico
                </h5>

                <div class="row g-3 mb-4.5">
                    <div class="col-12 col-md-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Fecha de Cita</label>
                        <input type="date" name="fecha" class="form-control rounded-3 py-2 font-monospace fw-semibold" value="{{ date('Y-m-d') }}" required style="font-size: 0.92rem; height: 38px;">
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Hora Programada</label>
                        <input type="time" name="hora" id="inputHora" class="form-control rounded-3 py-2 font-monospace fw-semibold" 
                               step="60"
                               value="{{ request('hora_redondeada') ?? (request('quick_start') ? date('H:i') : '') }}" 
                               required style="font-size: 0.92rem; height: 38px;">
                    </div>
                    
                    <div class="col-12 col-md-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Motivo Clínico de Consulta</label>
                        <select name="motivo" id="motivoCita" class="form-select rounded-3 h-auto py-2 fw-semibold" required style="font-size: 0.92rem; height: 38px;">
                            <option value="Control" selected>Control Periódico</option>
                            <option value="Paciente nuevo">Paciente Nuevo / Primera Consulta</option>
                        </select>
                    </div>
                </div>

                {{-- ACCIONES --}}
                <div class="pt-4 border-top border-light d-flex gap-2.5 justify-content-end">
                    <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3 px-4 py-2 border small fw-semibold text-secondary" style="font-size: 0.88rem;">Cancelar</a>
                    
                    <button type="submit" class="btn btn-primary rounded-3 px-5 py-2 fw-bold border-0 shadow-sm" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); font-size: 0.88rem;">
                        <i class="bi bi-check-circle-fill me-1.5"></i> Agendar e Insertar Cita
                    </button>
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
    const inputHora = document.getElementById('inputHora');
    const options = Array.from(select.options).filter(opt => opt.value !== "");

    const urlParams = new URLSearchParams(window.location.search);
    const quickPacienteId = urlParams.get('paciente_id');
    const isQuickStart = urlParams.get('quick_start');

    // Saneamiento para asegurar formato HH:mm sin segundos ni AM/PM
    if (inputHora && inputHora.value) {
        let partes = inputHora.value.split(':');
        if (partes.length >= 2) {
            inputHora.value = `${partes[0].padStart(2, '0')}:${partes[1].padStart(2, '0')}`;
        }
    }

    if (quickPacienteId) {
        const optionToSelect = options.find(opt => opt.value == quickPacienteId);
        if (optionToSelect) {
            optionToSelect.style.display = "block"; 
            select.value = quickPacienteId; 
            status.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-person-check-fill"></i> Paciente recién registrado seleccionado automáticamente.</span>';
        }
    }

    if (isQuickStart && selectMotivo) {
        selectMotivo.value = "Paciente nuevo";
        selectMotivo.focus();
    }

    function removerTildes(texto) {
        if (!texto) return "";
        return texto
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function filtrar() {
        const valApellido = removerTildes(inputApellido.value);
        const valDNI = inputDNI.value.trim();
        let coincidentes = [];

        options.forEach(opt => {
            const apellidoPac = removerTildes(opt.dataset.apellido);
            const matchApellido = valApellido.length >= 2 && apellidoPac.includes(valApellido);
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
            status.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-shield-check"></i> Paciente verificado e indexado.</span>';
        } else if (coincidentes.length > 1) {
            select.value = "";
            status.innerHTML = `<span class="text-primary fw-bold"><i class="bi bi-info-circle-fill"></i> Se encontraron ${coincidentes.length} coincidencias en el directorio.</span>`;
        } else {
            if(valApellido.length < 2 && valDNI.length < 3) {
                status.innerHTML = '<span class="text-muted"><i class="bi bi-info-circle"></i> Ingrese datos filiatorios para indexar el expediente.</span>';
            } else {
                status.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> No hay coincidencias registradas.</span>';
            }
        }
    }

    inputApellido.addEventListener('input', () => { inputDNI.value = ""; filtrar(); });
    inputDNI.addEventListener('input', () => { inputApellido.value = ""; filtrar(); });
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: opacity 0.15s ease; }
    .transition-row-normal:hover { opacity: 0.85; }

    input.form-control, select.form-select {
        border: 1px solid #cbd5e1;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    input.form-control:focus, select.form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    #selectPaciente {
        overflow-y: auto;
        border-radius: 10px;
        min-height: 160px;
    }
    #selectPaciente option {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.92rem;
        transition: background-color 0.1s;
    }
    #selectPaciente option:checked {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        font-weight: 700;
    }
    .select-placeholder-custom {
        font-size: 0.88rem !important;
        padding: 14px !important;
        background-color: #f8fafc;
    }
</style>
@endsection