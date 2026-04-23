@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- ENCABEZADO DE EXPEDIENTE --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Directorio
            </a>
            <h2 class="fw-bold text-dark mb-0">Expediente de {{ $paciente->apellido }}, {{ $paciente->nombre }}</h2>
            <span class="badge bg-dark">HC N° {{ str_pad($paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
            <span id="save-status" class="ms-3 text-muted small"></span>
        </div>
        <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success fw-bold shadow-sm px-4">
            <i class="bi bi-save me-2"></i>GUARDAR CAMBIOS EN ANTECEDENTES
        </button>
    </div>

    <div class="row">
        {{-- SECCIÓN 1: ANTECEDENTES (FORMULARIO) --}}
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Antecedentes Generales</h5>
                <form id="formAntecedentes">
                    @csrf
                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Médicos</label>
                            <textarea name="Medico" class="form-control" rows="3">{{ $antecedentes->where('tipo', 'Médico')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Quirúrgicos</label>
                            <textarea name="Quirúrgico" class="form-control" rows="3">{{ $antecedentes->where('tipo', 'Quirúrgico')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-danger small mb-2 text-uppercase">Alergias</label>
                            <textarea name="Alergia" class="form-control border-danger-subtle" rows="3">{{ $antecedentes->where('tipo', 'Alergia')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-success small mb-2 text-uppercase">Medicación Habitual</label>
                            <textarea name="Medicación" class="form-control border-success-subtle" rows="3">{{ $antecedentes->where('tipo', 'Medicación')->first()->descripcion ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- SECCIÓN 2: HISTORIAL DE ATENCIONES --}}
        <div class="col-md-12">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-clock-history me-2 text-primary"></i>Historial de Atenciones Previas
            </h5>
            <div class="accordion shadow-sm" id="historialToggles">
                @forelse($historial as $hist)
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#h{{ $hist->id }}">
                                <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                                    <span>
                                        <i class="bi bi-calendar-check me-2 text-success"></i>
                                        Atención del {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y') }}
                                    </span>
                                    <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">
                                        CIE-10: {{ $hist->cie_10 ?? 'N/A' }}
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="h{{ $hist->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body bg-white border-top">
                                
                                {{-- FILA SUPERIOR: INFORMACIÓN MÉDICA --}}
                                <div class="row mb-4">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Anamnesis / Examen</h6>
                                        <div class="p-2 bg-light rounded border-start border-4 border-info mb-2">
                                            <p class="small text-dark mb-0">{{ $hist->anamnesis }}</p>
                                        </div>
                                        <div class="p-2 bg-light rounded border-start border-4 border-secondary">
                                            <p class="small text-dark mb-0">{{ $hist->examen_fisico }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ps-md-4">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-2">Diagnóstico y Plan</h6>
                                        <p class="fw-bold text-primary mb-1">{{ $hist->diagnostico }}</p>
                                        <div class="p-3 bg-light rounded border small text-dark">
                                            <strong>Plan de Tratamiento:</strong><br>
                                            {{ $hist->plan }}
                                        </div>
                                    </div>
                                </div>

                                {{-- FILA INFERIOR: RECETA COMPLETA (ANCHO TOTAL) --}}
                                @if($hist->cita && $hist->cita->recetas->count() > 0)
                                    <div class="border-top pt-4">
                                        <h6 class="fw-bold text-danger small text-uppercase mb-3">
                                            <i class="bi bi-prescription2 me-1"></i>Tratamiento Farmacológico Detallado
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle bg-light shadow-sm mb-0" style="font-size: 0.85rem;">
                                                <thead class="table-secondary text-muted small text-center">
                                                    <tr>
                                                        <th class="text-start">Medicamento / Concentración</th>
                                                        <th>Presentación</th>
                                                        <th>Dosis / Vía</th>
                                                        <th>Frecuencia</th>
                                                        <th>Duración</th>
                                                        <th class="bg-primary text-white">Cant. Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($hist->cita->recetas as $rec)
                                                        <tr class="text-center">
                                                            <td class="text-start px-3">
                                                                <strong class="text-dark">{{ $rec->medicamento }}</strong><br>
                                                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.7rem;">
                                                                    {{ $rec->concentracion }}
                                                                </span>
                                                            </td>
                                                            <td><small class="text-muted">{{ $rec->presentacion }}</small></td>
                                                            <td>
                                                                <span class="fw-bold">{{ $rec->dosis }}</span><br>
                                                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $rec->via_administracion }}</small>
                                                            </td>
                                                            <td>Cada {{ $rec->frecuencia }}</td>
                                                            <td>Por {{ $rec->duracion }}</td>
                                                            <td class="fw-bold text-primary bg-primary-subtle" style="font-size: 1rem;">
                                                                {{ $rec->cantidad_total }} <small>und.</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="{{ route('receta.pdf', $hist->cita_id) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Reimprimir Receta
                                            </a>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border text-center py-5 text-muted shadow-sm">
                        <i class="bi bi-folder-x fs-1 d-block mb-3 opacity-50"></i>
                        No hay atenciones registradas para este paciente.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    async function guardarAntecedentesManual(event) {
        const form = document.getElementById('formAntecedentes');
        const statusLabel = document.getElementById('save-status');
        const btn = event.currentTarget;

        btn.disabled = true;
        statusLabel.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Guardando...';

        try {
            const response = await fetch("{{ route('antecedentes.guardar_todo') }}", {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                statusLabel.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle"></i> ¡Datos actualizados!</span>';
                setTimeout(() => { 
                    btn.disabled = false;
                    statusLabel.innerHTML = '';
                }, 3000);
            }
        } catch (error) {
            btn.disabled = false;
            statusLabel.innerHTML = '<span class="text-danger">Error al guardar</span>';
            alert("Hubo un error de conexión.");
        }
    }
</script>

<style>
    textarea.form-control { resize: none; border-radius: 8px; border: 1px solid #dee2e6; transition: border-color 0.2s; }
    textarea.form-control:focus { border-color: #4e73df; }
    .accordion-button:not(.collapsed) { background-color: #f8f9fc; color: #4e73df; box-shadow: none; }
    .accordion-item { border-radius: 12px !important; overflow: hidden; border: 1px solid #e3e6f0 !important; }
    .table-responsive { border-radius: 8px; overflow: hidden; }
    .spin { animation: rotation 2s infinite linear; display: inline-block; }
    @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }
</style>
@endsection