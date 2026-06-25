@extends('layouts.app')

@section('title', 'Gestión CIE-10')

@section('content')
<div class="container-fluid p-0">
    
    {{-- ENCABEZADO VIBRANTE COMPACTO CON BOTONES VERTICALES APILADOS --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.85rem; letter-spacing: -1px; font-weight: 900; line-height: 1.2;">Catálogo CIE-10</h2>
                    <p class="m-0 small opacity-75 fw-medium">Clasificación Internacional de Enfermedades y Motivos de Consulta.</p>
                </div>
                {{-- BOTONES VERTICALES UNO ENCIMA DE OTRO --}}
                <div class="col-12 col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2 justify-content-md-end align-items-md-end">
                        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 190px;" data-bs-toggle="modal" data-bs-target="#modalCie10">
                            <i class="bi bi-plus-circle-fill"></i> Nuevo Diagnóstico
                        </button>
                        <button type="button" class="btn btn-success btn-sm text-white fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 190px; background-color: #10b981;" data-bs-toggle="modal" data-bs-target="#modalImportarExcel">
                            <i class="bi bi-file-earmark-excel-fill"></i> Importar Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS DE SISTEMA --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABLA CONTENEDORA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3" style="background: #ffffff; overflow: visible;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-folder2-open text-primary me-2"></i> Códigos de Diagnósticos Activos
            </h5>
        </div>
        
        <div class="p-0">
            <div class="table-responsive">
                <table id="tabla-cie10" class="table table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr class="table-light text-secondary uppercase font-monospace" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                            <th class="py-3 ps-4" width="15%">Código</th>
                            <th class="py-3">Descripción del Diagnóstico</th>
                            <th class="text-center pe-4 py-3 no-sort" width="120px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($diagnosticos as $cie)
                        <tr id="fila-{{ $cie->id }}" class="transition-row-normal">
                            <td class="font-monospace fw-bold ps-4 text-primary" style="font-size: 0.95rem;">{{ $cie->codigo }}</td>
                            <td>
                                <span class="text-view fw-medium text-dark" style="font-size: 0.92rem;">{{ $cie->descripcion }}</span>
                                <input type="text" class="form-control d-none text-edit rounded-3 py-1 font-semibold" value="{{ $cie->descripcion }}" style="font-size: 0.92rem;">
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1.5">
                                    <button class="btn btn-sm btn-outline-secondary btn-edit-toggle rounded-3" style="padding: 5px 9px;" title="Editar Descripción">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success d-none btn-save-inline rounded-3" style="padding: 5px 9px;" onclick="actualizarCie({{ $cie->id }})" title="Guardar Cambios">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-3" style="padding: 5px 9px;" onclick="eliminarCie({{ $cie->id }})" title="Eliminar Registro">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CONTENEDOR CONTROLES INFERIORES FIJOS (FIXED) --}}
        <div id="wrapper-controles-sticky" class="barra-paginacion-fixed d-flex flex-wrap justify-content-between align-items-center px-4 py-2.5 border-top border-light shadow-lg">
            <div id="bloque-info-left" class="d-flex align-items-center gap-3"></div>
            <div id="bloque-paginado-right" class="d-flex align-items-center gap-3"></div>
        </div>
    </div>
</div>

{{-- MODAL ORIGINAL: NUEVO DIAGNÓSTICO --}}
<div class="modal fade" id="modalCie10" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-plus me-2"></i> Registrar Código CIE-10</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cie10.store') }}" method="POST" class="m-0">
                @csrf
                <div class="modal-body p-4 bg-light-subtle">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Código CIE-10</label>
                        <input type="text" name="codigo" class="form-control rounded-3 font-monospace fw-bold" placeholder="Ej: E10.9" required style="font-size: 0.95rem; letter-spacing: 0.5px;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Descripción Médica Ofical</label>
                        <textarea name="descripcion" class="form-control rounded-3 text-uppercase" rows="3" placeholder="Escribe el nombre patológico completo..." required style="font-size: 0.9rem; resize: none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-4 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-bold">GUARDAR DIAGNÓSTICO</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DE IMPORTACIÓN EXCEL --}}
<div class="modal fade" id="modalImportarExcel" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i> Importar Estructura Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cie10.importar') }}" method="POST" enctype="multipart/form-data" class="m-0">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3 text-dark" style="background-color: #e0f2fe;">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i> La fila inicial del libro debe poseer el mapeado de columnas nativas:
                        <br><span class="font-monospace fw-bold text-secondary" style="font-size: 0.76rem;">codigo, diagnostico</span>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Seleccionar libro de datos (.xlsx, .xls)</label>
                        <input type="file" name="archivo_excel" class="form-control rounded-3" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-4 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 fw-bold">PROCESAR E IMPORTAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assets Externos Coexistentes --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let tablaGeneral;

$(document).ready(function() {
    // --- MOTOR DATATABLES CON EMBEDDED CONTROLS ESPEJADOS ---
    tablaGeneral = $('#tabla-cie10').DataTable({
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[10, 20, 50, 100], [10, 20, 50, 100]],
        "pageLength": 10,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
            "search": "Filtrar catálogo:",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ diagnósticos",
            "lengthMenu": "Mostrar: _MENU_"
        },
        // 'f' a la izquierda con padding simétrico, el resto se procesa en el drawer original oculto
        "dom": '<"d-flex justify-content-start align-items-center px-3 py-1.5 border-bottom border-light bg-light-subtle"f>rt<"tabla-controles-nativos-originales"ilp>',
        "columnDefs": [
            { "orderable": false, "targets": "no-sort" }
        ],
        "drawCallback": function(settings) {
            // Sincronización cíclica por reflejo estricto de nodos
            const infoTexto = $('.tabla-controles-nativos-originales .dataTables_info').html();
            $('#bloque-info-left').html(`<div class="dataTables_info">${infoTexto}</div>`);
            
            const lengthHTML = $('.tabla-controles-nativos-originales .dataTables_length').html();
            const paginateHTML = $('.tabla-controles-nativos-originales .dataTables_paginate').html();
            
            $('#bloque-paginado-right').html(`
                <div class="dataTables_length_clone">${lengthHTML}</div>
                <div class="dataTables_paginate_clone paging_simple_numbers">${paginateHTML}</div>
            `);

            $('.dataTables_length_clone select').addClass('form-select rounded-3 font-monospace py-1').css({
                'width': '80px', 'height': '34px', 'font-weight': '700', 'display': 'inline-block'
            });

            const valorActualOriginal = $('.tabla-controles-nativos-originales .dataTables_length select').val();
            $('.dataTables_length_clone select').val(valorActualOriginal);
        }
    });

    // Event Delegation para control de clics e inputs en barra fixed
    $('#bloque-paginado-right').on('click', '.page-link', function(e) {
        e.preventDefault();
        const esItemAnterior = $(this).parent().hasClass('previous');
        const esItemSiguiente = $(this).parent().hasClass('next');
        const numeroPaginaText = $(this).text().trim();

        if (esItemAnterior) {
            $('.tabla-controles-nativos-originales .page-item.previous .page-link').click();
        } else if (esItemSiguiente) {
            $('.tabla-controles-nativos-originales .page-item.next .page-link').click();
        } else {
            $('.tabla-controles-nativos-originales .page-link').each(function() {
                if ($(this).text().trim() === numeroPaginaText) {
                    $(this).click();
                    return false;
                }
            });
        }
    });

    $('#bloque-paginado-right').on('change', '.dataTables_length_clone select', function() {
        const nuevoValor = $(this).val();
        $('.tabla-controles-nativos-originales .dataTables_length select').val(nuevoValor).change();
    });

    // Conmutador del inline edit nativo
    $('.btn-edit-toggle').on('click', function() {
        const row = $(this).closest('tr');
        row.find('.text-view, .text-edit, .btn-save-inline, .btn-edit-toggle').toggleClass('d-none');
    });

    // Estilización del buscador a la izquierda con padding interno
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

async function actualizarCie(id) {
    const row = $(`#fila-${id}`);
    const nuevaDesc = row.find('.text-edit').val().trim();

    if(!nuevaDesc) return alert('La descripción no puede guardarse vacía.');

    try {
        const response = await fetch(`/cie10/${id}/inline`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ descripcion: nuevaDesc })
        });

        if (response.ok) {
            row.find('.text-view').text(nuevaDesc);
            row.find('.text-view, .text-edit, .btn-save-inline, .btn-edit-toggle').toggleClass('d-none');
            row.addClass('table-success');
            setTimeout(() => row.removeClass('table-success'), 1200);
        }
    } catch (e) { alert('Error al actualizar'); }
}

async function eliminarCie(id) {
    if(!confirm('¿Seguro que desea eliminar este diagnóstico del catálogo?')) return;

    try {
        const response = await fetch(`/cie10/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (response.ok) { 
            $(`#fila-${id}`).fadeOut(300, function() {
                tablaGeneral.row($(this)).remove().draw(false);
            }); 
        }
    } catch (e) { alert('Error al eliminar'); }
}
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: background-color 0.15s ease; }
    
    .tabla-controles-nativos-originales { display: none !important; }

    /* ── SOBREESCRITURA CON MARGEN IZQUIERDO DE CORTESÍA ── */
    .dataTables_filter {
        text-align: left !important;
        float: left !important;
        margin: 0 !important;
        margin-left: 12px !important;
    }

    /* BARRA FLOTANTE FIJA SUPERPUESTA EN LA BASE DE LA VENTANA DE TRABAJO */
    .barra-paginacion-fixed {
        position: fixed;
        bottom: 0;
        right: 0;
        width: calc(100% - 290px); 
        z-index: 1030;
        background-color: #ffffff !important;
        box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.15) !important;
        transition: width 0.2s ease;
    }

    @media (max-width: 991.98px) {
        .barra-paginacion-fixed {
            width: 100% !important;
        }
    }

    .dataTables_info { font-size: 0.82rem; font-weight: 700; color: #475569 !important; margin: 0 !important; }
    .pagination { margin: 0 !important; gap: 3px; }
    .page-item .page-link { border-radius: 6px !important; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border: 1px solid #e2e8f0; }
    .page-item.active .page-link { background-color: #4e73df !important; border-color: #4e73df !important; color: white !important; }
</style>
@endsection