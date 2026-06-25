@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    
    {{-- ENCABEZADO VIBRANTE COMPACTO CON BOTONES APILADOS --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.85rem; letter-spacing: -1px; font-weight: 900; line-height: 1.2;">Directorio General</h2>
                    <p class="m-0 small opacity-75 fw-medium">Visualización y auditoría de todas las historias clínicas registradas.</p>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2 justify-content-md-end align-items-md-end">
                        <a href="{{ route('pacientes.create') }}" class="btn btn-light btn-sm text-primary fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 180px;">
                            <i class="bi bi-person-plus-fill"></i> Nuevo Paciente
                        </a>
                        <a href="{{ route('citas.create') }}" class="btn btn-warning btn-sm text-dark fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 180px;">
                            <i class="bi bi-calendar-event-fill"></i> Agendar Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LEYENDA INFORMATIVA DE CONTROL DE AUDITORÍA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff; border-left: 4px solid #cbd5e1;">
        <div class="card-body py-2.5 px-3">
            <div class="d-flex align-items-center flex-wrap gap-3 small">
                <span class="fw-bold text-secondary text-uppercase font-monospace" style="font-size: 0.72rem;"><i class="bi bi-funnel-fill text-primary"></i> Filtro de Auditoría:</span>
                <div class="d-flex align-items-center bg-light px-2 py-1 rounded-2 border">
                    <span class="indicator-box-sample bg-white border me-2"></span>
                    <span class="text-muted fw-bold font-monospace" style="font-size: 0.75rem;">Expediente Completo</span>
                </div>
                <div class="d-flex align-items-center style-incomplete-container px-2 py-1 rounded-2 border" style="background-color: #e0f2fe; border-color: #bae6fd !important;">
                    <span class="indicator-box-sample me-2" style="background-color: #0284c7;"></span>
                    <span class="fw-bold font-monospace" style="color: #0369a1; font-size: 0.75rem;">Registro Incompleto (Requiere Actualización)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENEDOR PRINCIPAL DEL REGISTRO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff; overflow: visible;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-people-fill text-primary me-2"></i> Base de Datos de Pacientes
            </h5>
        </div>
        
        <div class="p-0">
            <div class="table-responsive">
                <table id="tabla-pacientes-general" class="table table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr class="table-light text-secondary uppercase font-monospace" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                            <th class="d-none">Estado Oculto</th>
                            <th class="py-3 ps-3">DNI</th>
                            <th class="py-3">Apellidos</th>
                            <th class="py-3">Nombres</th>
                            <th class="text-center py-3">Género</th>
                            <th class="text-center py-3">Fecha Nac.</th>
                            <th class="text-center py-3">Edad</th>
                            <th class="py-3">Celular Personal</th>
                            <th class="py-3">Distrito</th>
                            <th class="text-end pe-4 py-3 no-sort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($pacientes as $paciente)
                            @php
                                $camposVacios = empty($paciente->dni) || 
                                                empty($paciente->fecha_nacimiento) || 
                                                empty($paciente->genero) || 
                                                empty($paciente->celular_personal) || 
                                                empty($paciente->distrito);

                                $isIncompleto = ($camposVacios && !$paciente->ignorar_alerta);
                            @endphp
                            
                            <tr id="row-paciente-{{ $paciente->id }}" class="{{ $isIncompleto ? 'fila-incompleta-clinical' : 'transition-row-normal' }}">
                                <td class="d-none">{{ $isIncompleto ? '0' : '1' }}</td>
                                <td class="font-monospace fw-bold ps-3" style="font-size: 0.9rem;">
                                    {{ $paciente->dni ?? 'N/R' }}
                                    @if($isIncompleto)
                                        <span class="d-block mt-0.5"><span class="badge bg-danger text-white uppercase font-monospace fw-bold" style="font-size: 0.6rem; padding: 2.5px 5px; letter-spacing: 0.2px;">Incompleto</span></span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $paciente->apellido }}</td>
                                <td class="fw-medium text-secondary" style="font-size: 0.92rem;">{{ $paciente->nombre }}</td>
                                <td class="text-center">
                                    @if($paciente->genero == 'Masculino')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-2 font-monospace fw-bold">M</span>
                                    @elseif($paciente->genero == 'Femenino')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-2 font-monospace fw-bold">F</span>
                                    @elseif($paciente->genero == 'Otros')
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-2 font-monospace fw-bold">O</span>
                                    @else
                                        <span class="text-muted small fst-italic">N/R</span>
                                    @endif
                                </td>
                                <td class="text-center font-monospace text-secondary small fw-medium">
                                    {{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') : 'N/R' }}
                                </td>
                                <td class="text-center fw-bold text-secondary font-monospace" style="font-size: 0.88rem;">
                                    {{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age . ' años' : 'N/R' }}
                                </td>
                                <td class="font-monospace text-dark fw-medium">
                                    @if($paciente->celular_personal)
                                        <i class="bi bi-phone-fill text-muted small me-0.5"></i> {{ $paciente->celular_personal }}
                                    @else
                                        <span class="text-muted small fst-italic">N/R</span>
                                    @endif
                                </td>
                                <td class="fw-semibold text-secondary" style="font-size: 0.88rem;">{{ $paciente->distrito ?? 'N/R' }}</td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-action-trigger rounded-3 border bg-white text-secondary shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 m-0 py-2" style="min-width: 210px; z-index: 1040;">
                                            <li>
                                                <a class="dropdown-item py-2 fw-medium" href="{{ route('pacientes.datos', $paciente->id) }}" style="font-size: 0.85rem;">
                                                    <i class="bi bi-folder-symlink-fill me-2 text-primary"></i>Ver Expediente Médico
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 fw-medium" href="{{ route('pacientes.edit', $paciente->id) }}" style="font-size: 0.85rem;">
                                                    <i class="bi bi-pencil-square me-2 text-dark"></i>Editar Datos Base
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider border-light"></li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 text-danger fw-bold" onclick="eliminarPacienteHistorial({{ $paciente->id }}, '{{ $paciente->apellido }}, {{ $paciente->nombre }}')" style="font-size: 0.85rem;">
                                                    <i class="bi bi-trash3-fill me-2"></i>Eliminar Registro
                                                </button>
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

        {{-- CONTENEDOR CONTROLES INFERIORES: Paginación y Selector juntos en bloque STICKY --}}
        <div id="wrapper-controles-sticky" class="barra-paginacion-sticky d-flex flex-wrap justify-content-between align-items-center px-4 py-2.5 border-top border-light shadow-lg">
            <div id="bloque-info-left" class="d-flex align-items-center gap-3"></div>
            <div id="bloque-paginado-right" class="d-flex align-items-center gap-3"></div>
        </div>
    </div>
</div>

{{-- Scripts y Estilos del Directorio --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let tablaGeneral;

    $(document).ready(function() {
        tablaGeneral = $('#tabla-pacientes-general').DataTable({
            "order": [[ 0, "asc" ], [ 2, "asc" ]],
            "lengthMenu": [[10, 20, 50, 100], [10, 20, 50, 100]],
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Filtrar directorio:",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ pacientes",
                "lengthMenu": "Mostrar: _MENU_"
            },
            // El dom original se mantiene vivo y seguro en la fila inferior original (no d-none)
            "dom": '<"d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-light bg-light-subtle"f>rt<"tabla-controles-nativos-originales"ilp>',
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ],
            // 🔄 SISTEMA DE REFLEJO EN TIEMPO REAL: Se ejecuta ante CUALQUIER cambio (filtros, orden, clics, etc.)
            "drawCallback": function(settings) {
                // Sincronizamos el texto informativo de la izquierda
                const infoTexto = $('.tabla-controles-nativos-originales .dataTables_info').html();
                $('#bloque-info-left').html(`<div class="dataTables_info">${infoTexto}</div>`);
                
                // Clonamos la estructura exacta del selector de páginas y del paginador
                const lengthHTML = $('.tabla-controles-nativos-originales .dataTables_length').html();
                const paginateHTML = $('.tabla-controles-nativos-originales .dataTables_paginate').html();
                
                $('#bloque-paginado-right').html(`
                    <div class="dataTables_length_clone">${lengthHTML}</div>
                    <div class="dataTables_paginate_clone paging_simple_numbers">${paginateHTML}</div>
                `);

                // Estilizamos el nuevo selector clonado
                $('.dataTables_length_clone select').addClass('form-select rounded-3 font-monospace py-1').css({
                    'width': '80px', 'height': '34px', 'font-weight': '700', 'display': 'inline-block'
                });

                // Sincronizar el valor seleccionado del dropdown clonado con el original
                const valorActualOriginal = $('.tabla-controles-nativos-originales .dataTables_length select').val();
                $('.dataTables_length_clone select').val(valorActualOriginal);
            }
        });

        // ── PUENTE INTERACTIVO DE EVENTOS (EVENT DELEGATION) ──
        
        // 1. Sincronizar clicks de los números de página flotantes con los botones nativos reales
        $('#bloque-paginado-right').on('click', '.page-link', function(e) {
            e.preventDefault();
            // Identificar a qué página se quiere ir
            const esItemAnterior = $(this).parent().hasClass('previous');
            const esItemSiguiente = $(this).parent().hasClass('next');
            const numeroPaginaText = $(this).text().trim();

            if (esItemAnterior) {
                $('.tabla-controles-nativos-originales .products-action .previous .page-link', $('.tabla-controles-nativos-originales .page-item.previous .page-link').click());
            } else if (esItemSiguiente) {
                $('.tabla-controles-nativos-originales .page-item.next .page-link').click();
            } else {
                // Buscar el botón original que tenga el mismo número y disparar el click real
                $('.tabla-controles-nativos-originales .page-link').each(function() {
                    if ($(this).text().trim() === numeroPaginaText) {
                        $(this).click();
                        return false; // romper bucle
                    }
                });
            }
        });

        // 2. Sincronizar cambios en el selector flotante de cantidad (10, 20, 50, 100)
        $('#bloque-paginado-right').on('change', '.dataTables_length_clone select', function() {
            const nuevoValor = $(this).val();
            // Le pasamos el valor al selector real oculto y disparamos su evento de cambio nativo
            $('.tabla-controles-nativos-originales .dataTables_length select').val(nuevoValor).change();
        });

        // Estilización de padding reducido en buscador superior
        $('.dataTables_filter input').addClass('form-control rounded-3').css({
            'padding-left': '36px',
            'border': '1px solid #cbd5e1',
            'font-weight': '500',
            'width': '280px',
            'height': '36px',
            'background-image': 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'%2394a3b8\' class=\'bi bi-search\'%3E%3Cpath d=\'M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z\'/%3E%3C/svg%3E")',
            'background-repeat': 'no-repeat',
            'background-position': '12px center'
        });
        $('.dataTables_filter label').contents().filter(function() { return this.nodeType === 3; }).remove();
    });

    async function eliminarPacienteHistorial(id, nombreCompleto) {
        if (confirm(`⚠️ ¿Está completamente seguro de eliminar a "${nombreCompleto}"?\nEsta acción borrará permanentemente sus registros.`)) {
            try {
                const response = await fetch(`/pacientes/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (response.ok && result.status === 'success') {
                    const fila = document.getElementById(`row-paciente-${id}`);
                    if (fila) {
                        fila.style.transition = "all 0.3s ease";
                        fila.style.opacity = "0";
                        setTimeout(() => { tablaGeneral.row($(fila)).remove().draw(false); }, 300);
                    }
                }
            } catch (error) { alert("Error crítico."); }
        }
    }
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .indicator-box-sample { width: 14px; height: 14px; display: inline-block; border-radius: 4px; flex-shrink: 0; }
    .transition-row-normal { transition: background-color 0.15s ease; }

    .fila-incompleta-clinical td {
        background-color: #f0f9ff !important;
        color: #0369a1 !important;
        border-bottom-color: #bae6fd !important;
    }
    .fila-incompleta-clinical:hover td { background-color: #e0f2fe !important; }

    .btn-action-trigger { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0; transition: all 0.15s; }
    .btn-action-trigger:hover { background-color: #f1f5f9 !important; border-color: #cbd5e1 !important; }

    /* =========================================================================
       ── BARRA FLOTANTE INTEGRAL VERDADERA (STICKY FIX EN MAIN SCOPE) ──
       ========================================================================= */
    .barra-paginacion-sticky {
        position: sticky;
        bottom: 0;
        z-index: 1025;
        background-color: #ffffff !important;
        box-shadow: 0 -8px 24px -4px rgba(15, 23, 42, 0.12) !important;
    }

    /* Paginador estético */
    .dataTables_info { font-size: 0.82rem; font-weight: 700; color: #475569 !important; margin: 0 !important; }
    .pagination { margin: 0 !important; gap: 3px; }
    .page-item .page-link { border-radius: 6px !important; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border: 1px solid #e2e8f0; }
    .page-item.active .page-link { background-color: #4e73df !important; border-color: #4e73df !important; color: white !important; }
    .tabla-controles-nativos-originales {
        display: none !important;
    }
</style>
@endsection