@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    
    {{-- ENCABEZADO VIBRANTE COMPACTO --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.85rem; letter-spacing: -1px; font-weight: 900; line-height: 1.2;">Calendario de Citas</h2>
                    <p class="m-0 small opacity-75 fw-medium">Monitorea y atiende las citas programadas en el sistema.</p>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <div class="d-flex flex-row flex-md-column gap-2 justify-content-md-end">
                        <a href="{{ route('pacientes.create') }}" class="btn btn-light btn-sm text-primary fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.88rem;">
                            <i class="bi bi-person-plus-fill"></i> Nuevo Paciente
                        </a>
                        <a href="{{ route('citas.create') }}" class="btn btn-warning btn-sm text-dark fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.88rem;">
                            <i class="bi bi-calendar-event-fill"></i> Agendar Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN DE FILTRADO INTERACTIVO --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-body-card">
                <div class="card-body p-2.5 d-flex flex-column justify-content-center">
                    <label class="form-label font-monospace uppercase fw-bold px-1 text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; line-height: 1;">Fecha de Trabajo</label>
                    <form action="{{ route('dashboard') }}" method="GET" id="form-fecha" class="m-0">
                        <div class="position-relative w-100">
                            <input type="date" name="fecha" id="calendario-input" class="form-control fw-bold flatpickr-custom-input py-1.5" value="{{ $fechaSeleccionada }}" onchange="document.getElementById('form-fecha').submit()">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-body-card">
                <div class="card-body p-2.5 d-flex flex-column justify-content-center">
                    <label class="form-label font-monospace uppercase fw-bold px-1 text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px; line-height: 1;">Buscador General y Leyendas</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative flex-grow-1 w-100">
                            <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="buscador-tarjetas" class="form-control rounded-3 py-1.5 input-medical-search" placeholder="Buscar paciente, DNI o motivo..." style="padding-left: 38px; font-size: 0.92rem;">
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0">
                            <span class="badge border d-flex align-items-center gap-1.5 px-2.5 py-2 rounded-3 leyendas-custom-badge" style="font-size: 0.75rem;">
                                <span class="dot-sample bg-pending"></span>Pendientes
                            </span>
                            <span class="badge border d-flex align-items-center gap-1.5 px-2.5 py-2 rounded-3 leyendas-custom-badge" style="font-size: 0.75rem;">
                                <span class="dot-sample bg-attended"></span>Atendidos
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARRA DE CONTEO RÁPIDO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 bg-body-card">
        <div class="card-body py-2.5 px-3 d-flex justify-content-between align-items-center">
            <span class="text-secondary small fw-bold font-monospace text-uppercase"><i class="bi bi-funnel-fill text-primary"></i> Filtrado en tiempo real</span>
            <div class="text-secondary small fw-bold font-monospace">
                Citas en pantalla: <span class="badge bg-dark text-white font-monospace px-2.5 py-1 ms-1 rounded-2 fs-7" id="conteo-dinamico">{{ $citas->count() }}</span>
            </div>
        </div>
    </div>

    {{-- PARRILLA DINÁMICA --}}
    <div class="row g-3" id="contenedor-tarjetas-citas">
        @forelse($citas as $cita)
            <div class="col-12 col-md-6 col-xl-4 tarjeta-cita-item" data-search="{{ strtolower($cita->paciente->apellido . ' ' . $cita->paciente->nombre . ' ' . $cita->paciente->dni . ' ' . $cita->motivo) }}">
                
                @php
                    $colorAccent = '#4e73df'; $bgBox = '#f0f4ff'; $textColor = '#224abe';
                    if($cita->estado == 'Atendido') {
                        $colorAccent = '#10b981'; $bgBox = '#e6fcf5'; $textColor = '#0ca678';
                    } elseif($cita->estado == 'Cancelado') {
                        $colorAccent = '#fa5252'; $bgBox = '#fff5f5'; $textColor = '#f03e3e';
                    }
                @endphp

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-interactive-medical bg-body-card" style="border-top: 4px solid {{ $colorAccent }} !important;">
                    <div class="card-body p-3.5 d-flex flex-column h-100">
                        
                        {{-- BLOQUE SUPERIOR --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge fw-bold font-monospace px-2.5 py-1.5 rounded-2 d-inline-flex align-items-center badge-status-medical" style="background: {{ $bgBox }}; color: {{ $textColor }}; border: 1px solid rgba(0,0,0,0.02); font-size: 0.8rem;">
                                    <i class="bi bi-clock-fill me-1.5"></i> {{ \Carbon\Carbon::parse($cita->hora)->format('h:i A') }}
                                </span>
                            </div>
                            <span class="badge fw-bold text-uppercase px-2 py-1 rounded-2 badge-status-medical" style="background: {{ $bgBox }}; color: {{ $textColor }}; font-size: 0.68rem; letter-spacing: 0.3px;">
                                {{ $cita->estado }}
                            </span>
                        </div>

                        {{-- INFORMACIÓN DEL PACIENTE --}}
                        <div class="mb-2">
                            <h6 class="fw-bold text-body-card-title m-0 mb-1 text-truncate" style="font-size: 1.05rem;">
                                {{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }}
                            </h6>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <span class="font-monospace px-1.5 py-0.5 rounded bg-light border text-secondary card-inner-dni" style="font-size: 0.7rem;">DNI: {{ $cita->paciente->dni ?? 'N/R' }}</span>
                                <span class="fw-bold text-dark-subtle text-muted-dark" style="font-size: 0.78rem;"><i class="bi bi-person-fill text-secondary"></i> {{ $cita->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age . ' años' : 'N/R' }}</span>
                            </div>
                        </div>

                        {{-- MOTIVO MÉDICO --}}
                        <div class="p-2.5 rounded-3 border mb-3 flex-grow-1 card-inner-motivo-box" style="min-height: 58px;">
                            <p class="text-secondary small m-0 fw-medium line-clamp-custom text-muted-dark" title="{{ $cita->motivo }}">
                                {{ $cita->motivo ?? 'Sin motivo específico.' }}
                            </p>
                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="pt-2.5 border-top border-light d-flex justify-content-end gap-1.5 mt-auto align-items-center border-translucent-custom">
                            @if($cita->estado == 'Atendido')
                                @if($cita->recetas && $cita->recetas->count() > 0)
                                    <a href="{{ route('receta.pdf', $cita->id) }}" target="_blank" class="btn btn-md-pill bg-danger-subtle text-danger border-danger-subtle btn-pill-dark-danger" title="Imprimir Receta">
                                        <i class="bi bi-printer-fill"></i> PDF
                                    </a>
                                @endif
                                @if($cita->historiaClinica)
                                    <a href="{{ route('historias.edit', $cita->historiaClinica->id) }}" class="btn btn-md-pill bg-primary-subtle text-primary border-primary-subtle btn-pill-dark-primary" title="Editar Ficha">
                                        <i class="bi bi-pencil-square"></i> Ficha
                                    </a>
                                @endif
                            @elseif($cita->estado == 'Cancelado')
                                <span class="text-muted font-monospace small fst-italic px-1">Anulada</span>
                            @else
                                <a href="{{ route('historias.create', ['cita_id' => $cita->id]) }}" class="btn btn-execute-atencion btn-sm fw-bold px-3 py-2 rounded-3 text-white shadow-sm border-0 flex-grow-1 justify-content-center gap-1">
                                    <i class="bi bi-heart-pulse-fill"></i> ATENDER
                                </a>
                                <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="button" class="btn btn-md-pill bg-light text-danger border-light-subtle btn-cancel-dark-override" style="padding: 6px 10px;" title="Cancelar Cita" onclick="confirmarCancelacion(this)">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12" id="tarjeta-vacia-aviso">
                <div class="text-center p-5 rounded-4 shadow-sm border bg-body-card">
                    <h5 class="fw-bold text-muted">No se registran citas en este día</h5>
                    <p class="text-secondary small mb-0 text-muted-dark">Selecciona otra fecha utilizando el buscador de calendario.</p>
                </div>
            </div>
        @endforelse
        
        <div class="col-12 d-none" id="buscador-vacio-aviso">
            <div class="text-center p-4 rounded-4 shadow-sm text-muted bg-body-card border">
                <i class="bi bi-exclamation-circle-fill fs-3 text-danger mb-1"></i>
                <h6 class="fw-bold text-body-card-title m-0">Sin coincidencia para los filtros ingresados.</h6>
            </div>
        </div>
    </div>
</div>

{{-- Estilos e Inyecciones de Calendario Sólido --}}
<style>
    .line-clamp-custom { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.82rem; line-height: 1.4; }
    .card-interactive-medical { transition: transform 0.2s, box-shadow 0.2s; border: 1px solid var(--bs-border-color-translucent); }
    .card-interactive-medical:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0,0,0,0.08) !important; }

    .btn-md-pill { padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; border: 1px solid; text-decoration: none; transition: all 0.15s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-md-pill:hover { filter: brightness(0.95); transform: translateY(-0.5px); }
    
    .btn-execute-atencion { background: #4e73df; display: inline-flex; align-items: center; font-size: 0.78rem; letter-spacing: 0.5px; transition: background 0.2s; }
    .btn-execute-atencion:hover { background: #10b981; }

    .dot-sample { width: 8px; height: 8px; display: inline-block; border-radius: 50%; }
    .dot-sample.bg-pending { background-color: #4e73df; }
    .dot-sample.bg-attended { background-color: #10b981; }

    /* Estilos base claros */
    .bg-body-card { background-color: #ffffff; }
    .text-body-card-title { color: #0f172a; }
    .card-inner-motivo-box { background: #fdfdfd; border-color: #cbd5e1; }
    .input-medical-search { border: 1px solid #cbd5e1; background-color: #ffffff; color: #0f172a; }

    /* Flatpickr Clases Estáticas */
    .flatpickr-day.day-solid-pending { background-color: rgba(78, 115, 223, 0.18) !important; color: #4e73df !important; font-weight: 700 !important; border-radius: 6px !important; border: 1px solid rgba(78, 115, 223, 0.4) !important; }
    .flatpickr-day.day-solid-attended { background-color: rgba(16, 185, 129, 0.18) !important; color: #10b981 !important; font-weight: 700 !important; border-radius: 6px !important; border: 1px solid rgba(16, 185, 129, 0.4) !important; }
    .flatpickr-day.selected { background-color: #4e73df !important; color: #ffffff !important; border-color: #4e73df !important; }

    .flatpickr-custom-input, .flatpickr-input[type="text"] {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        font-size: 0.95rem !important;
        width: 100% !important;
    }
    .flatpickr-custom-input:focus, .flatpickr-input[type="text"]:focus {
        border-color: #86b7fe !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }

    /* ==========================================================================
       🌙 ADAPTACIONES ESPECÍFICAS MODO OSCURO (DARK MODE OVERRIDES)
       ========================================================================== */
    [data-bs-theme="dark"] .bg-body-card { background-color: #1e293b !important; }
    [data-bs-theme="dark"] .text-body-card-title { color: #ffffff !important; }
    [data-bs-theme="dark"] .text-muted-dark { color: #94a3b8 !important; }
    
    [data-bs-theme="dark"] .card-inner-motivo-box { 
        background: #111827 !important; 
        border-color: #334155 !important; 
    }
    [data-bs-theme="dark"] .input-medical-search { 
        border-color: #334155 !important; 
        background-color: #0f172a !important; 
        color: #ffffff !important; 
    }
    [data-bs-theme="dark"] .card-inner-dni {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }
    [data-bs-theme="dark"] .card-leyenda-badge {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .border-translucent-custom {
        border-top-color: #334155 !important;
    }

    /* Modificación de Badges de Estado en Modo Oscuro */
    [data-bs-theme="dark"] .badge-status-medical {
        background-color: #111827 !important;
        border-color: #334155 !important;
    }
    
    /* Botones píldora atenuados en Modo Oscuro */
    [data-bs-theme="dark"] .btn-pill-dark-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; border-color: rgba(239, 68, 68, 0.3) !important; }
    [data-bs-theme="dark"] .btn-pill-dark-primary { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; border-color: rgba(59, 130, 246, 0.3) !important; }
    [data-bs-theme="dark"] .btn-cancel-dark-override { background-color: #334155 !important; color: #f87171 !important; border-color: #475569 !important; }

    /* Inputs de Calendario Adaptados */
    [data-bs-theme="dark"] .flatpickr-custom-input, 
    [data-bs-theme="dark"] .flatpickr-input[type="text"] {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .flatpickr-calendar {
        background-color: #1e293b !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5) !important;
        border: 1px solid #334155 !important;
    }
    [data-bs-theme="dark"] .flatpickr-months .flatpickr-month,
    [data-bs-theme="dark"] .flatpickr-weekday {
        background: #1e293b !important;
        color: #ffffff !important;
        fill: #ffffff !important;
    }
    [data-bs-theme="dark"] .flatpickr-day { color: #cbd5e1 !important; }
    [data-bs-theme="dark"] .flatpickr-day.day-solid-pending { background-color: rgba(78, 115, 223, 0.3) !important; color: #60a5fa !important; }
    [data-bs-theme="dark"] .flatpickr-day.day-solid-attended { background-color: rgba(16, 185, 129, 0.3) !important; color: #34d399 !important; }

    .leyendas-custom-badge {
        background-color: #f8f9fa !important; /* Gris muy claro modo claro */
        border-color: #dee2e6 !important;
        color: #495057 !important;
    }

    [data-bs-theme="dark"] .leyendas-custom-badge {
        background-color: #334155 !important; /* Gris oscuro modo dark */
        border-color: #475569 !important;
        color: #ffffff !important;
    }

    /* Asegurar puntos de color estables */
    .dot-sample { width: 8px; height: 8px; display: inline-block; border-radius: 50%; }
    .bg-pending { background-color: #4e73df; }
    .bg-attended { background-color: #10b981; }

    /* ── CORRECCIÓN ADICIONAL PARA EL ENCABEZADO VIBRANTE ── */
    /* Esto asegura que el encabezado NO tenga estilo inline de fondo que interfiera */
    .card-header-dashboard {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
    }
    [data-bs-theme="dark"] .card-header-dashboard {
        background: #1e3a8a !important;
    }
</style>

{{-- Inyección de JavaScript --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscador = document.getElementById('buscador-tarjetas');
        const tarjetas = document.querySelectorAll('.tarjeta-cita-item');
        const avisoVacio = document.getElementById('buscador-vacio-aviso');
        const conteoDinamic = document.getElementById('conteo-dinamico');

        if (buscador) {
            buscador.addEventListener('input', function(e) {
                const termino = e.target.value.toLowerCase().trim();
                let encontrados = 0;

                tarjetas.forEach(tarjeta => {
                    if (tarjeta.getAttribute('data-search').includes(termino)) {
                        tarjeta.classList.remove('d-none');
                        encontrados++;
                    } else {
                        tarjeta.classList.add('d-none');
                    }
                });

                if(conteoDinamic) conteoDinamic.innerText = encontrados;
                avisoVacio.classList.toggle('d-none', encontrados > 0 || tarjetas.length === 0);
            });
        }

        const resumenCitas = @json($resumenCitas ?? []);

        flatpickr("#calendario-input", {
            locale: "es",
            altInput: true,
            altFormat: "j \\d\\e F, Y",
            dateFormat: "Y-m-d",
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                const date = dayElem.dateObj;
                const offset = date.getTimezoneOffset();
                const adjustedDate = new Date(date.getTime() - (offset * 60 * 1000));
                const dateString = adjustedDate.toISOString().split('T')[0];

                if (resumenCitas[dateString]) {
                    if (resumenCitas[dateString] === 'pendiente') {
                        dayElem.classList.add('day-solid-pending');
                    } else if (resumenCitas[dateString] === 'atendido') {
                        dayElem.classList.add('day-solid-attended');
                    }
                }
            }
        });
    });

    function confirmarCancelacion(button) {
        if (confirm('¿Estás seguro de cancelar esta cita médica?')) {
            button.closest('form').submit();
        }
    }
</script>
@endsection