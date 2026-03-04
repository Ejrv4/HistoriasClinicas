@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">Nuevo Medicamento</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('medicamentos.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre del Fármaco</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Paracetamol" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Presentación</label>
                            <input type="text" name="presentacion" class="form-control" placeholder="Ej: Tabletas 500mg" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="bi bi-plus-circle me-2"></i> REGISTRAR
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">Catálogo Registrado</h5>
                    <table id="tabla-medicamentos" class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Nombre del Medicamento</th>
                                <th>Presentación</th>
                                <th class="text-center" width="15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicamentos as $med)
                            <tr id="row-{{ $med->id }}">
                                <td id="nombre-{{ $med->id }}" 
                                    class="px-3 fw-bold text-primary" 
                                    contenteditable="false">{{ $med->nombre }}</td>
                                
                                <td id="pres-{{ $med->id }}" 
                                    class="px-3" 
                                    contenteditable="false">{{ $med->presentacion }}</td>
                                
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <button id="btn-edit-{{ $med->id }}" 
                                                onclick="toggleEditar({{ $med->id }})" 
                                                class="btn btn-sm btn-outline-primary" 
                                                title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <button onclick="confirmarEliminar({{ $med->id }}, '{{ $med->nombre }}')" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Eliminar">
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

<script>
// Variable para rastrear qué filas están en modo edición
let editando = {};

function toggleEditar(id) {
    const btn = document.getElementById(`btn-edit-${id}`);
    const cellNombre = document.getElementById(`nombre-${id}`);
    const cellPres = document.getElementById(`pres-${id}`);
    const row = document.getElementById(`row-${id}`);

    if (!editando[id]) {
        // --- ACTIVAR EDICIÓN ---
        editando[id] = true;
        
        // Habilitar edición
        cellNombre.contentEditable = "true";
        cellPres.contentEditable = "true";
        
        // Estilo visual
        row.classList.add('table-warning');
        cellNombre.focus();
        
        // Cambiar icono del botón a un "Check" de confirmación
        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
        btn.classList.replace('btn-outline-primary', 'btn-success');
        btn.title = "Confirmar cambios";

    } else {
        // --- GUARDAR CAMBIOS ---
        const nuevoNombre = cellNombre.innerText.trim();
        const nuevaPres = cellPres.innerText.trim();

        ejecutarActualizacion(id, nuevoNombre, nuevaPres, btn, cellNombre, cellPres, row);
    }
}

async function ejecutarActualizacion(id, nombre, presentacion, btn, c1, c2, row) {
    try {
        const response = await fetch(`/medicamentos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nombre, presentacion })
        });

        if (response.ok) {
            // Bloquear edición
            c1.contentEditable = "false";
            c2.contentEditable = "false";
            editando[id] = false;

            // Resetear interfaz
            row.classList.remove('table-warning');
            row.classList.add('table-success'); // Destello verde de éxito
            
            btn.innerHTML = '<i class="bi bi-pencil-square"></i>';
            btn.classList.replace('btn-success', 'btn-outline-primary');
            
            setTimeout(() => row.classList.remove('table-success'), 1500);
        }
    } catch (error) {
        alert("Error al actualizar");
    }
}

// --- ELIMINACIÓN CON CONFIRMACIÓN EXTRA ---
async function confirmarEliminar(id, nombre) {
    // Primera confirmación
    const primera = confirm(`¿Estás seguro de que deseas eliminar "${nombre}" del catálogo?`);
    
    if (primera) {
        // Segunda confirmación (Extra de seguridad)
        const segunda = confirm(`¡Atención! Esta acción no se puede deshacer. ¿Realmente eliminar ${nombre}?`);
        
        if (segunda) {
            enviarEliminacion(id);
        }
    }
}

async function enviarEliminacion(id) {
    try {
        const response = await fetch(`/medicamentos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            $(`#row-${id}`).fadeOut(500, function() { $(this).remove(); });
        }
    } catch (error) {
        alert("Error al eliminar");
    }
}
</script>
<style>
    /* Solo mostrar borde cuando la celda es editable */
    [contenteditable="true"] {
        outline: 2px solid #4e73df;
        background-color: white;
        border-radius: 4px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    .table-warning td {
        transition: background-color 0.3s ease;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.6rem;
    }
</style>

@endsection