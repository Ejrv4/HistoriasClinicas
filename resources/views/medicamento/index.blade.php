@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Bloque Superior de Título y Botón de Importación --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Gestión de Medicamentos</h3>
            <p class="text-muted small mb-0">Catálogo general de fármacos y dosis por defecto para recetas</p>
        </div>
        <div>
            <button type="button" class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportarExcel">
                <i class="bi bi-file-earmark-excel me-2"></i> IMPORTAR EXCEL
            </button>
        </div>
    </div>

    {{-- ALERTAS DE ÉXITO O ERROR EN IMPORTACIÓN --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Formulario de Registro --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">Nuevo Medicamento</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('medicamentos.store') }}" method="POST" id="formNuevoMedicamento">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nombre del Fármaco</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Paracetamol" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Concentración</label>
                                <input type="text" name="concentracion" class="form-control" placeholder="Ej: 500mg" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Presentación</label>
                                <select name="presentacion" class="form-select" required>
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

                        <h6 class="text-primary fw-bold small text-uppercase border-bottom pb-1 mt-2 mb-3">Valores por Defecto (Receta)</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Dosis (Cant.)</label>
                                <input type="number" id="reg_dos" name="dosis" class="form-control reg-calc-trigger" step="0.1" placeholder="Ej: 1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Vía</label>
                                {{-- VALIDACIÓN: required añadido --}}
                                <select name="via_administracion" class="form-select" required>
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
                            <label class="form-label fw-bold small text-muted">Frecuencia por defecto</label>
                            <div class="input-group">
                                <input type="number" id="reg_f_n" class="form-control reg-calc-trigger" placeholder="Cada...">
                                <select id="reg_f_t" class="form-select reg-calc-trigger">
                                    <option value="Horas">Horas</option>
                                    <option value="Días">Días</option>
                                    <option value="Dosis Única">Dosis Única</option>
                                </select>
                            </div>
                            <input type="hidden" name="frecuencia" id="hidden_frecuencia">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Duración por defecto</label>
                            <div class="input-group">
                                <input type="number" id="reg_d_n" class="form-control reg-calc-trigger" placeholder="Por...">
                                <select id="reg_d_t" class="form-select reg-calc-trigger">
                                    <option value="Días">Días</option>
                                    <option value="Semanas">Semanas</option>
                                    <option value="Meses">Meses</option>
                                </select>
                            </div>
                            <input type="hidden" name="duracion" id="hidden_duracion">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-primary">Cantidad Total a Entregar</label>
                            <input type="number" id="reg_total" name="cantidad_total" class="form-control border-primary text-center fw-bold" value="0">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="bi bi-plus-circle me-2"></i> REGISTRAR
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabla de Catálogo --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">Catálogo Registrado</h5>
                    <div class="table-responsive">
                        <table id="tabla-medicamentos" class="table table-hover align-middle w-100 table-sm" style="font-size: 0.9rem;">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Conc.</th>
                                    <th>Pres.</th>
                                    <th>Dos.</th>
                                    <th>Vía</th>
                                    <th>Frecuencia</th>
                                    <th>Duración</th>
                                    <th class="text-primary">Cant.</th>
                                    <th class="text-center no-sort" width="90px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicamentos as $med)
                                <tr id="row-{{ $med->id }}">
                                    <td id="nombre-{{ $med->id }}" class="fw-bold text-primary">{{ $med->nombre }}</td>
                                    <td id="concentracion-{{ $med->id }}" class="text-secondary">{{ $med->concentracion }}</td>
                                    <td id="presentacion-{{ $med->id }}">{{ $med->presentacion }}</td>
                                    <td id="dosis-{{ $med->id }}">{{ $med->dosis ?? '' }}</td>
                                    <td id="via_administracion-{{ $med->id }}">{{ $med->via_administracion ?? '' }}</td>
                                    <td id="frecuencia-{{ $med->id }}">{{ $med->frecuencia ?? '' }}</td>
                                    <td id="duracion-{{ $med->id }}">{{ $med->duracion ?? '' }}</td>
                                    <td id="cantidad_total-{{ $med->id }}" class="fw-bold text-dark">{{ $med->cantidad_total ?? '' }}</td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <button id="btn-edit-{{ $med->id }}" onclick="toggleEditar({{ $med->id }})" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button onclick="confirmarEliminar({{ $med->id }}, '{{ $med->nombre }}')" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
    </div>
</div>

{{-- MODAL DE IMPORTACIÓN --}}
<div class="modal fade" id="modalImportarExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i>Importar Medicamentos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('medicamentos.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 small mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i> La primera fila debe ser la cabecera con los títulos exactos correspondientes: 
                        <br><strong class="text-uppercase">medicamento, concentracion, presentacion, dosis, via_administracion, frecuencia, duracion, cantidad_total</strong>.
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase">Seleccione Archivo de Excel (.xlsx, .xls)</label>
                        <input type="file" name="archivo_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">PROCESAR E IMPORTAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let editando = {};

// --- 1. CÁLCULO AUTOMÁTICO Y BLOQUEO POR DOSIS ÚNICA ---
function calcularTotalRegistro() {
    const dosisInput = document.getElementById('reg_dos');
    const f_numInput = document.getElementById('reg_f_n');
    const f_tipoSelect = document.getElementById('reg_f_t');
    const d_numInput = document.getElementById('reg_d_n');
    const d_tipoSelect = document.getElementById('reg_d_t');
    const totalInput = document.getElementById('reg_total');

    const dosis = parseFloat(dosisInput.value) || 0;

    if (f_tipoSelect.value === 'Dosis Única') {
        f_numInput.value = "";
        f_numInput.disabled = true;
        f_numInput.placeholder = "N/A";

        if (dosis > 0) {
            totalInput.value = Math.ceil(dosis);
        } else {
            totalInput.value = 0;
        }
    } else {
        f_numInput.disabled = false;
        f_numInput.placeholder = "Cada...";

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

// --- 2. CONCATENACIÓN ANTES DE ENVIAR EL FORMULARIO ---
document.getElementById('formNuevoMedicamento').addEventListener('submit', function(e) {
    const f_num = document.getElementById('reg_f_n').value;
    const f_tipo = document.getElementById('reg_f_t').value;
    const d_num = document.getElementById('reg_d_n').value;
    const d_tipo = document.getElementById('reg_d_t').value;

    if (f_tipo === 'Dosis Única') {
        document.getElementById('hidden_frecuencia').value = 'Dosis Única';
    } else if (f_num) {
        document.getElementById('hidden_frecuencia').value = f_num + ' ' + f_tipo;
    }

    if (d_num) {
        document.getElementById('hidden_duracion').value = d_num + ' ' + d_tipo;
    }
});

// --- 3. EDICIÓN RÁPIDA EN LÍNEA (INLINE EDIT) ---
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
            alert("❌ Error: Los campos de Dosis y Cantidad Total solo permiten números.");
            return;
        }

        const data = {};
        keys.forEach((key, index) => {
            if(fields[index]) data[key] = fields[index].innerText.trim();
        });
        
        ejecutarActualizacion(id, data, btn, fields, row);
    }
}

async function ejecutarActualizacion(id, data, btn, fields, row) {
    try {
        const response = await fetch(`/medicamentos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            fields.forEach(f => { if(f) f.contentEditable = "false"; });
            editando[id] = false;
            row.classList.replace('table-warning', 'table-success');
            btn.innerHTML = '<i class="bi bi-pencil-square"></i>';
            btn.className = 'btn btn-sm btn-outline-primary';
            setTimeout(() => row.classList.remove('table-success'), 1500);
        } else {
            const err = await response.json();
            alert("Error: " + (err.message || "No se pudo actualizar"));
        }
    } catch (error) {
        alert("Error de conexión al servidor");
    }
}

// --- 4. ELIMINACIÓN ---
async function confirmarEliminar(id, nombre) {
    if (confirm(`¿Eliminar "${nombre}"?`)) {
        try {
            const response = await fetch(`/medicamentos/${id}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) { 
                const fila = document.getElementById(`row-${id}`);
                if (fila) {
                    fila.style.transition = "all 0.4s ease";
                    fila.style.opacity = "0";
                    fila.style.transform = "scale(0.95)";
                    setTimeout(() => { fila.remove(); }, 400);
                }
            } else {
                alert("No se pudo eliminar el medicamento del catálogo.");
            }
        } catch (e) { 
            alert("Error de conexión al servidor"); 
        }
    }
}
</script>

<style>
    [contenteditable="true"] { outline: 2px solid #4e73df; background: white; padding: 2px 5px; border-radius: 4px; }
    .table-warning td { background-color: #fff3cd !important; }
</style>
@endsection