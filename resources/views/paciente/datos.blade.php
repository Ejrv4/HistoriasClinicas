@extends('layouts.app')

@section('content')
<div class="container-fluid">
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

        <div class="col-md-12">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Atenciones Previas</h5>
            <div class="accordion shadow-sm" id="historialToggles">
                @forelse($historial as $hist)
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#h{{ $hist->id }}">
                                <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                                    <span><i class="bi bi-calendar-check me-2 text-success"></i>Atención del {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y') }}</span>
                                    <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">CIE-10: {{ $hist->cie_10 ?? 'N/A' }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="h{{ $hist->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body bg-white border-top">
                                <div class="row">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-muted small text-uppercase">Anamnesis / Examen</h6>
                                        <p class="small text-dark mb-1"><strong>Anamnesis:</strong> {{ $hist->anamnesis }}</p>
                                        <p class="small text-dark"><strong>Físico:</strong> {{ $hist->examen_fisico }}</p>
                                    </div>
                                    <div class="col-md-6 ps-md-4">
                                        <h6 class="fw-bold text-muted small text-uppercase">Diagnóstico y Plan</h6>
                                        <p class="fw-bold text-primary mb-1">{{ $hist->diagnostico }}</p>
                                        <p class="small text-muted">{{ $hist->plan }}</p>

                                        @if($hist->cita && $hist->cita->recetas->count() > 0)
                                            <div class="table-responsive mt-3">
                                                <table class="table table-sm table-bordered small">
                                                    <thead class="table-light"><tr><th>Med.</th><th>Dosis</th><th>Frec.</th></tr></thead>
                                                    <tbody>
                                                        @foreach($hist->cita->recetas as $rec)
                                                            <tr>
                                                                <td>{{ $rec->medicamento }}</td>
                                                                <td>{{ $rec->dosis }}</td>
                                                                <td>{{ $rec->frecuencia }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border text-center py-4 text-muted small">No hay atenciones registradas para este paciente.</div>
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
        statusLabel.innerHTML = 'Guardando...';

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
                statusLabel.innerHTML = '<span class="text-success fw-bold">¡Datos actualizados correctamente!</span>';
                setTimeout(() => { 
                    btn.disabled = false;
                    statusLabel.innerHTML = '';
                }, 2000);
            }
        } catch (error) {
            btn.disabled = false;
            statusLabel.innerHTML = '<span class="text-danger">Error al guardar</span>';
            alert("Hubo un error.");
        }
    }
</script>

<style>
    textarea.form-control { resize: none; border-radius: 8px; border: 1px solid #dee2e6; }
    .accordion-button:not(.collapsed) { background-color: #f8f9fc; color: #4e73df; }
    .accordion-item { border-radius: 12px !important; overflow: hidden; }
</style>
@endsection