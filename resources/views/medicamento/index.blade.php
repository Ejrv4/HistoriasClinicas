@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Formulario de Registro --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">Nuevo Medicamento</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('medicamentos.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">NOMBRE DEL FÁRMACO</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Paracetamol" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">CONCENTRACIÓN</label>
                            <input type="text" name="concentracion" class="form-control" placeholder="Ej: 500mg" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">PRESENTACIÓN</label>
                            <input type="text" name="presentacion" class="form-control" placeholder="Ej: Tabletas" required>
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
                    <table id="tabla-medicamentos" class="table table-hover align-middle w-100">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Nombre</th>
                                <th>Concentración</th>
                                <th>Presentación</th>
                                <th class="text-center" width="100px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicamentos as $med)
                            <tr id="row-{{ $med->id }}">
                                <td id="nombre-{{ $med->id }}" class="px-3 fw-bold text-primary">{{ $med->nombre }}</td>
                                
                                {{-- Quitamos el badge interno durante la edicion para que no de error --}}
                                <td id="conc-{{ $med->id }}" class="px-3 text-secondary">{{ $med->concentracion }}</td>

                                <td id="pres-{{ $med->id }}" class="px-3">{{ $med->presentacion }}</td>
                                
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

<script>
let editando = {};

function toggleEditar(id) {
    const btn = document.getElementById(`btn-edit-${id}`);
    const fields = ['nombre', 'conc', 'pres'].map(f => document.getElementById(`${f}-${id}`));
    const row = document.getElementById(`row-${id}`);

    if (!editando[id]) {
        editando[id] = true;
        fields.forEach(f => f.contentEditable = "true");
        row.classList.add('table-warning');
        fields[0].focus();
        
        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
        btn.className = 'btn btn-sm btn-success';
    } else {
        const data = {
            nombre: fields[0].innerText.trim(),
            concentracion: fields[1].innerText.trim(),
            presentacion: fields[2].innerText.trim()
        };
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
            fields.forEach(f => f.contentEditable = "false");
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