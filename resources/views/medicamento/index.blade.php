@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    
    {{-- ENCABEZADO VIBRANTE COMPACTO CON BOTONES VERTICALES --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.85rem; letter-spacing: -1px; font-weight: 900; line-height: 1.2;">Gestión de Medicamentos</h2>
                    <p class="m-0 small opacity-75 fw-medium">Catálogo general de fármacos y dosificaciones base parametrizadas para recetas rápidas.</p>
                </div>
                {{-- BOTONES VERTICALES UNO ENCIMA DE OTRO --}}
                <div class="col-12 col-md-4 text-md-end">
                    <div class="d-flex flex-column gap-2 justify-content-md-end align-items-md-end">
                        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 190px;" data-bs-toggle="modal" data-bs-target="#modalNuevoMedicamento">
                            <i class="bi bi-plus-circle-fill"></i> Nuevo Medicamento
                        </button>
                        <button type="button" class="btn btn-success btn-sm text-white fw-bold rounded-3 px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center justify-content-center gap-1.5 w-100 w-md-auto" style="font-size: 0.88rem; min-width: 190px; background-color: #10b981;" data-bs-toggle="modal" data-bs-target="#modalImportarExcel">
                            <i class="bi bi-file-earmark-excel-fill"></i> Importar Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS DE ACCIONES --}}
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

    {{-- TABLA DE CATÁLOGO GENERAL --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5" style="background: #ffffff; overflow: visible;">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-journal-medical text-primary me-2"></i> Catálogo de Fármacos Registrados
            </h5>
        </div>
        
        <div class="p-0">
            <div class="table-responsive">
                <table id="tabla-medicamentos" class="table table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr class="table-light text-secondary uppercase font-monospace" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                            <th class="py-3 ps-3">Nombre del Fármaco</th>
                            <th class="py-3">Concentración</th>
                            <th class="py-3">Presentación</th>
                            <th class="py-3">Dosis Base</th>
                            <th class="py-3">Vía Adm.</th>
                            <th class="py-3">Frecuencia</th>
                            <th class="py-3">Duración</th>
                            <th class="text-primary py-3">Cant. Total</th>
                            <th class="text-center pe-3 py-3 no-sort" width="100px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($medicamentos as $med)
                        <tr id="row-{{ $med->id }}" class="transition-row-normal">
                            <td id="nombre-{{ $med->id }}" class="fw-bold text-primary ps-3" style="font-size: 0.92rem;">{{ $med->nombre }}</td>
                            <td id="concentracion-{{ $med->id }}" class="fw-bold text-secondary font-monospace">{{ $med->concentracion }}</td>
                            <td id="presentacion-{{ $med->id }}"><span class="badge bg-light text-dark border px-2 py-1 rounded-2">{{ $med->presentacion }}</span></td>
                            <td id="dosis-{{ $med->id }}" class="font-monospace fw-semibold">{{ $med->dosis ?? '' }}</td>
                            <td id="via_administracion-{{ $med->id }}" class="small fw-semibold text-secondary">{{ $med->via_administracion ?? '' }}</td>
                            <td id="frecuencia-{{ $med->id }}" class="small">{{ $med->frecuencia ?? '' }}</td>
                            <td id="duracion-{{ $med->id }}" class="small">{{ $med->duracion ?? '' }}</td>
                            <td id="cantidad_total-{{ $med->id }}" class="fw-black text-dark font-monospace" style="font-size: 0.95rem;">{{ $med->cantidad_total ?? '' }}</td>
                            <td class="text-center pe-3">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <button id="btn-edit-{{ $med->id }}" onclick="toggleEditar({{ $med->id }})" class="btn btn-sm btn-outline-primary" style="padding: 5px 10px;" title="Editar en Línea">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button onclick="confirmarEliminar({{ $med->id }}, '{{ $med->nombre }}')" class="btn btn-sm btn-outline-danger" style="padding: 5px 10px;" title="Eliminar Fármaco">
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
        <div class="contenedor-layout-espaciador" style="height: 30px;"></div>
        <div id="wrapper-controles-sticky" class="barra-paginacion-fixed d-flex flex-wrap justify-content-between align-items-center px-4 py-2.5 border-top border-light shadow-lg">
            <div id="bloque-info-left" class="d-flex align-items-center gap-3"></div>
            <div id="bloque-paginado-right" class="d-flex align-items-center gap-3"></div>
        </div>
    </div>
</div>

{{-- MODAL ADICIONAL: REGISTRO DE NUEVO MEDICAMENTO --}}
<div class="modal fade" id="modalNuevoMedicamento" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-capsule-pill me-2"></i> Registrar Nuevo Fármaco</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('medicamentos.store') }}" method="POST" id="formNuevoMedicamento" class="m-0">
                @csrf
                <div class="modal-body p-4 bg-light-subtle">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Nombre del Fármaco</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Paracetamol" required style="font-size: 0.9rem;">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Concentración</label>
                            <input type="text" name="concentracion" class="form-control rounded-3" placeholder="Ej: 500mg" required style="font-size: 0.9rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Presentación</label>
                            <select name="presentacion" class="form-select rounded-3" required style="font-size: 0.9rem;">
                                <option value="">-- Seleccionar --</option>
                                <option value="TABLETA">TABLETA</option>
                                <option value="CÁPSULA">CÁPSULA</option>
                                <option value="AMPOLLA">AMPOLLA</option>
                                <option value="FRASCO">FRASCO</option>
                                <option value="CUCHARADA">CUCHARADA</option>
                                <option value="APLICACIÓN">APLICACIÓN</option>
                                <option value="SOBRE">SOBRE</option>
                                <option value="SUPOSITORIO">SUPOSITORIO</option>
                                <option value="ENEMA">ENEMA</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-primary fw-bold small text-uppercase border-bottom pb-1 mt-4 mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Valores Predeterminados para Receta</h6>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Dosis (Cant.)</label>
                            <input type="number" id="reg_dos" name="dosis" class="form-control rounded-3 reg-calc-trigger" step="0.1" placeholder="Ej: 1" style="font-size: 0.9rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Vía de Adm.</label>
                            <select name="via_administracion" class="form-select rounded-3" required style="font-size: 0.9rem;">
                                <option value="">-- Seleccionar --</option>
                                <option value="VÍA ORAL">VÍA ORAL</option>
                                <option value="VÍA ENDOVENOSA">VÍA ENDOVENOSA</option>
                                <option value="VÍA INTRAMUSCULAR">VÍA INTRAMUSCULAR</option>
                                <option value="VÍA TÓPICA">VÍA TÓPICA</option>
                                <option value="VÍA ANAL">VÍA ANAL</option>
                                <option value="VÍA SUBCUTÁNEA">VÍA SUBCUTÁNEA</option>
                                <option value="VÍA RECTAL">VÍA RECTAL</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Frecuencia Base</label>
                        <div class="input-group">
                            <input type="number" id="reg_f_n" class="form-control reg-calc-trigger" placeholder="Cada..." style="font-size: 0.9rem;">
                            <select id="reg_f_t" class="form-select reg-calc-trigger" style="font-size: 0.9rem;">
                                <option value="Horas">Horas</option>
                                <option value="Días">Días</option>
                                <option value="Dosis Única">Dosis Única</option>
                            </select>
                        </div>
                        <input type="hidden" name="frecuencia" id="hidden_frecuencia">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Duración Tratamiento</label>
                        <div class="input-group">
                            <input type="number" id="reg_d_n" class="form-control reg-calc-trigger" placeholder="Por..." style="font-size: 0.9rem;">
                            <select id="reg_d_t" class="form-select reg-calc-trigger" style="font-size: 0.9rem;">
                                <option value="Días">Días</option>
                                <option value="Semanas">Semanas</option>
                                <option value="Meses">Meses</option>
                            </select>
                        </div>
                        <input type="hidden" name="duracion" id="hidden_duracion">
                    </div>

                    <div class="mb-0 bg-primary-subtle p-3 rounded-3 border border-primary-subtle">
                        <label class="form-label fw-bold small text-primary text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cantidad Total Sugerida a Entregar</label>
                        <input type="number" id="reg_total" name="cantidad_total" class="form-control border-primary text-center fw-black text-primary bg-white fs-5 py-1.5" value="0" style="border-width: 2px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-4 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-bold">GUARDAR REGISTRO</button>
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
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i>Importar Catálogo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('medicamentos.importar') }}" method="POST" enctype="multipart/form-data" class="m-0">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3 text-dark" style="background-color: #e0f2fe;">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i> La primera fila debe incluir los títulos de columna exactos:
                        <br><span class="font-monospace fw-bold text-secondary" style="font-size: 0.76rem;">medicamento, concentracion, presentacion, dosis, via_administracion, frecuencia, duracion, cantidad_total</span>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.7rem;">Seleccione plantilla Excel (.xlsx, .xls)</label>
                        <input type="file" name="archivo_excel" class="form-control rounded-3" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light">
                    <button type="button" class="btn btn-light rounded-3 px-4 py-2 small fw-semibold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 fw-bold">PROCESAR ARCHIVO</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DataTables Component Assets --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let tablaGeneral;
    let editando = {};

    $(document).ready(function() {
        tablaGeneral = $('#tabla-medicamentos').DataTable({
            "order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 20, 50, 100], [10, 20, 50, 100]],
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
                "search": "Filtrar catálogo:",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ fármacos",
                "lengthMenu": "Mostrar: _MENU_"
            },
            // Estructura DOM limpia y simplificada para envolver los filtros superiores
            "dom": '<"d-flex justify-content-start align-items-center px-3 py-1.5 border-bottom border-light bg-light-subtle"f>rt<"tabla-controles-nativos-originales"ilp>',
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ],
            "drawCallback": function(settings) {
                // Sincronización analítica por reflejo de nodos hacia los contenedores fijos inferiores
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

        // Event Delegation para el mapeo interactivo de la barra fixed inferior
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

        // Estilización ultracompacta del buscador de medicamentos
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

    // --- 2. CÁLCULO AUTOMÁTICO DE DOSIS ÚNICA ---
    function calcularTotalRegistro() {
        const dosisInput = document.getElementById('reg_dos');
        const f_numInput = document.getElementById('reg_f_n');
        const f_tipoSelect = document.getElementById('reg_f_t');
        const d_numInput = document.getElementById('reg_d_n');
        const d_tipoSelect = document.getElementById('reg_d_t');
        const totalInput = document.getElementById('reg_total');
        const dosis = parseFloat(dosisInput.value) || 0;

        if (f_tipoSelect.value === 'Dosis Única') {
            f_numInput.value = ""; f_numInput.disabled = true; f_numInput.placeholder = "N/A";
            totalInput.value = dosis > 0 ? Math.ceil(dosis) : 0;
        } else {
            f_numInput.disabled = false; f_numInput.placeholder = "Cada...";
            const f_num = parseFloat(f_numInput.value) || 0;
            const f_tipo = f_tipoSelect.value;
            const d_num = parseFloat(d_numInput.value) || 0;
            const d_tipo = d_tipoSelect.value;

            if (dosis > 0 && f_num > 0 && d_num > 0) {
                let tomasAlDia = (f_tipo === 'Horas') ? (24 / f_num) : (1 / f_num);
                let diasTotales = d_num;
                if (d_tipo === 'Semanas') diasTotales = d_num * 7;
                if (d_tipo === 'Meses') diasTotales = d_num * 30;
                totalInput.value = Math.ceil(dosis * tomasAlDia * diasTotales);
            }
        }
    }

    document.querySelectorAll('.reg-calc-trigger').forEach(el => {
        el.addEventListener('input', calcularTotalRegistro);
        el.addEventListener('change', calcularTotalRegistro);
    });

    document.getElementById('formNuevoMedicamento').addEventListener('submit', function(e) {
        const f_num = document.getElementById('reg_f_n').value;
        const f_tipo = document.getElementById('reg_f_t').value;
        const d_num = document.getElementById('reg_d_n').value;
        const d_tipo = document.getElementById('reg_d_t').value;

        document.getElementById('hidden_frecuencia').value = (f_tipo === 'Dosis Única') ? 'Dosis Única' : (f_num ? f_num + ' ' + f_tipo : '');
        if (d_num) document.getElementById('hidden_duracion').value = d_num + ' ' + d_tipo;
    });

    // --- 3. INLINE EDITING RESISTENTE ---
    function toggleEditar(id) {
        const btn = document.getElementById(`btn-edit-${id}`);
        const keys = ['nombre', 'concentracion', 'presentacion', 'dosis', 'via_administracion', 'frecuencia', 'duracion', 'cantidad_total'];
        const fields = keys.map(f => document.getElementById(`${f}-${id}`));
        const row = document.getElementById(`row-${id}`);

        if (!editando[id]) {
            editando[id] = true;
            fields.forEach(f => { if(f) f.contentEditable = "true"; });
            row.classList.add('table-warning');
            if(fields[0]) fields[0].focus();
            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.className = 'btn btn-sm btn-success';
        } else {
            const dosisVal = document.getElementById(`dosis-${id}`).innerText.trim();
            const totalVal = document.getElementById(`cantidad_total-${id}`).innerText.trim();

            if ((dosisVal && isNaN(dosisVal)) || (totalVal && isNaN(totalVal))) {
                alert("❌ Error: Los campos numéricos no permiten caracteres."); return;
            }

            const data = {};
            keys.forEach((key, idx) => { if(fields[key] || fields[idx]) data[key] = fields[idx].innerText.trim(); });
            ejecutarActualizacion(id, data, btn, fields, row);
        }
    }

    async function ejecutarActualizacion(id, data, btn, fields, row) {
        try {
            const response = await fetch(`/medicamentos/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(data)
            });
            if (response.ok) {
                fields.forEach(f => { if(f) f.contentEditable = "false"; });
                editando[id] = false;
                row.classList.replace('table-warning', 'table-success');
                btn.innerHTML = '<i class="bi bi-pencil-square"></i>';
                btn.className = 'btn btn-sm btn-outline-primary';
                setTimeout(() => row.classList.remove('table-success'), 1200);
            }
        } catch (e) { alert("Error de servidor."); }
    }

    // --- 4. ELIMINACIÓN ASÍNCRONA DE REGISTROS ---
    async function confirmarEliminar(id, nombre) {
        if (confirm(`¿Eliminar permanentemente "${nombre}" del catálogo?`)) {
            try {
                const response = await fetch(`/medicamentos/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (response.ok) {
                    const fila = document.getElementById(`row-${id}`);
                    if (fila) {
                        fila.style.transition = "all 0.3s ease"; fila.style.opacity = "0";
                        setTimeout(() => { tablaGeneral.row($(fila)).remove().draw(false); }, 300);
                    }
                }
            } catch (e) { alert("Error crítico."); }
        }
    }
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    [contenteditable="true"] { outline: 2px solid #4e73df; background: white; padding: 2px 4px; border-radius: 4px; }
    .table-warning td { background-color: #fff3cd !important; color: #664d03 !important; }
    .transition-row-normal { transition: background-color 0.15s ease; }
    
    .tabla-controles-nativos-originales { display: none !important; }
    
    /* ── SOBREESCRITURA ABSOLUTA PARA EL ALINEAMIENTO DEL BUSCADOR ── */
    .dataTables_filter {
        text-align: left !important;
        float: left !important;
        margin: 0 !important;
        margin-left: 19px !important;
    }

    /* BARRA FLOTANTE FIJA SUPERPUESTA EN LA BASE DE LA VENTANA */
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