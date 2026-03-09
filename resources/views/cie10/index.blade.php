@extends('layouts.app')

@section('title', 'Gestión CIE-10')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Catálogo CIE-10</h2>
        <p class="text-muted small">Clasificación Internacional de Enfermedades</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCie10">
        <i class="bi bi-plus-circle me-2"></i> Nuevo Diagnóstico
    </button>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <table id="tabla-cie10" class="table table-hover align-middle mb-0 w-100">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th width="10%">Código</th>
                            <th>Descripción del Diagnóstico</th>
                            <th width="15%" class="text-end no-sort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diagnosticos as $cie)
                        <tr id="fila-{{ $cie->id }}">
                            <td class="fw-bold text-primary">{{ $cie->codigo }}</td>
                            <td>
                                <span class="text-view">{{ $cie->descripcion }}</span>
                                <input type="text" class="form-control d-none text-edit" value="{{ $cie->descripcion }}">
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-secondary btn-edit-toggle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success d-none btn-save-inline" onclick="actualizarCie({{ $cie->id }})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarCie({{ $cie->id }})">
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

<div class="modal fade" id="modalCie10" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Registrar CIE-10</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cie10.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Código CIE-10</label>
                        <input type="text" name="codigo" class="form-control form-control-lg" placeholder="Ej: E10.9" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted text-uppercase">Descripción Médica</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Nombre completo del diagnóstico..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">GUARDAR DIAGNÓSTICO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabla-cie10').DataTable({
        "order": [[ 0, "asc" ]],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        "dom": '<"d-flex justify-content-between mb-3"f>rtip'
    });

    // Toggle para edición en línea (Similar a medicamentos)
    $('.btn-edit-toggle').on('click', function() {
        const row = $(this).closest('tr');
        row.find('.text-view, .text-edit, .btn-save-inline, .btn-edit-toggle').toggleClass('d-none');
    });
});

async function actualizarCie(id) {
    const row = $(`#fila-${id}`);
    const nuevaDesc = row.find('.text-edit').val();

    try {
        const response = await fetch(`/cie10/${id}/inline`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ descripcion: nuevaDesc })
        });

        if (response.ok) {
            row.find('.text-view').text(nuevaDesc);
            row.find('.text-view, .text-edit, .btn-save-inline, .btn-edit-toggle').toggleClass('d-none');
        }
    } catch (e) { alert('Error al actualizar'); }
}

async function eliminarCie(id) {
    if(!confirm('¿Seguro que desea eliminar este diagnóstico del catálogo?')) return;

    try {
        const response = await fetch(`/cie10/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (response.ok) { $(`#fila-${id}`).fadeOut(); }
    } catch (e) { alert('Error al eliminar'); }
}
</script>
@endsection