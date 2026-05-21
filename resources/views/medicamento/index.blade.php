@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Formulario de Registro con Selectores Estandarizados --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">Nuevo Medicamento</h5>
                </div>
                <div class="card-body p-4">
                    {{-- Añadimos un ID al formulario para gestionarlo con JS antes del submit --}}
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
                                <input type="text" name="presentacion" class="form-control" placeholder="Ej: Tabletas" required>
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
                                <select name="via_administracion" class="form-select">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Via Oral">Via Oral</option>
                                    <option value="Intramuscular">Intramuscular</option>
                                    <option value="Sublingual">Sublingual</option>
                                    <option value="Tópico">Tópico</option>
                                    <option value="Oftálmica">Oftálmica</option>
                                </select>
                            </div>
                        </div>

                        {{-- MODIFICACIÓN: Frecuencia idéntica a la receta --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Frecuencia por defecto</label>
                            <div class="input-group">
                                <input type="number" id="reg_f_n" class="form-control reg-calc-trigger" placeholder="Cada...">
                                <select id="reg_f_t" class="form-select reg-calc-trigger">
                                    <option value="Horas">Horas</option>
                                    <option value="Días">Días</option>
                                </select>
                            </div>
                            {{-- Input oculto que recibirá el string "X Tiempo" para la BD --}}
                            <input type="hidden" name="frecuencia" id="hidden_frecuencia">
                        </div>

                        {{-- MODIFICACIÓN: Duración idéntica a la receta --}}
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
                            {{-- Input oculto que recibirá el string "X Tiempo" para la BD --}}
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

        {{-- Tabla de Catálogo Expandida --}}
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

<script>
let editando = {};

// --- 1. CÁLCULO AUTOMÁTICO EN EL REGISTRO ---
function calcularTotalRegistro() {
    const dosis = parseFloat(document.getElementById('reg_dos').value) || 0;
    const f_num = parseFloat(document.getElementById('reg_f_n').value) || 0;
    const f_tipo = document.getElementById('reg_f_t').value;
    const d_num = parseFloat(document.getElementById('reg_d_n').value) || 0;
    const d_tipo = document.getElementById('reg_d_t').value;

    if (dosis > 0 && f_num > 0 && d_num > 0) {
        let tomasAlDia = (f_tipo === 'Horas') ? (24 / f_num) : (1 / f_num);
        let diasTotales = d_num;
        if (d_tipo === 'Semanas') diasTotales = d_num * 7;
        if (d_tipo === 'Meses') diasTotales = d_num * 30;
        document.getElementById('reg_total').value = Math.ceil(dosis * tomasAlDia * diasTotales);
    }
}

// Asignar los escuchas para el cálculo automático
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

    // Unimos los valores Ej: "8" + " " + "Horas" = "8 Horas"
    if (f_num) {
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
        // 1. CAPTURAMOS LOS VALORES INGRESADOS
        const dosisVal = document.getElementById(`dosis-${id}`).innerText.trim();
        const totalVal = document.getElementById(`cantidad_total-${id}`).innerText.trim();

        // 2. VALIDACIÓN: Si no son números o son menores a cero, frenamos el proceso
        if ((dosisVal && isNaN(dosisVal)) || (totalVal && isNaN(totalVal))) {
            alert("❌ Error: Los campos de Dosis y Cantidad Total solo permiten números.");
            return; // Detiene la ejecución, no envía nada al servidor y mantiene la edición abierta
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

async function confirmarEliminar(id, nombre) {
    if (confirm(`¿Eliminar "${nombre}"?`)) {
        try {
            const response = await fetch(`/medicamentos/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (response.ok) { $(`#row-${id}`).fadeOut(); }
        } catch (e) { alert("Error al eliminar"); }
    }
}
</script>

<style>
    [contenteditable="true"] { outline: 2px solid #4e73df; background: white; padding: 2px 5px; border-radius: 4px; }
    .table-warning td { background-color: #fff3cd !important; }
</style>
@endsection