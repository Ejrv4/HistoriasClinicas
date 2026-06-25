@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    
    {{-- BOTÓN REGRESAR --}}
    <div class="mb-3">
        <a href="{{ route('pacientes.index') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0 transition-row-normal d-inline-flex align-items-center" style="font-size: 0.88rem;">
            <i class="bi bi-arrow-left me-2 fs-5"></i>VOLVER AL DIRECTORIO GENERAL
        </a>
    </div>

    {{-- ENCABEZADO CLÍNICO INTEGRADO DEL PACIENTE --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: #ffffff; border-left: 5px solid #4e73df !important;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7 col-xl-8">
                    <h3 class="fw-black text-dark m-0 tracking-tight mb-2" style="font-size: 1.7rem; letter-spacing: -0.5px;">
                        {{ $paciente->apellido }}, {{ $paciente->nombre }}
                    </h3>
                    <div class="d-flex flex-wrap align-items-center gap-2 font-monospace">
                        <span class="badge bg-dark px-2.5 py-1.5 rounded-2">HC N° {{ str_pad($paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-secondary small fw-bold ms-1">DNI: <span class="text-dark">{{ $paciente->dni ?? 'N/R' }}</span></span>
                        <span id="save-status" class="ms-2 font-monospace small fw-bold"></span>
                    </div>
                </div>
                <div class="col-12 col-md-5 col-xl-4 text-md-end">
                    <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success rounded-4 fw-bold px-4 py-2.5 border-0 shadow-sm w-100 w-md-auto transition-row-normal" style="background-color: #10b981; font-size: 0.88rem;">
                        <i class="bi bi-save2-fill me-2 fs-6"></i>GUARDAR ANTECEDENTES
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- SECCIÓN 1: ANTECEDENTES DEL PACIENTE --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-4" style="background: #ffffff;">
                <h5 class="text-dark fw-bold border-bottom pb-2.5 mb-4 d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="bi bi-file-earmark-medical-fill text-primary me-2 fs-4"></i> Antecedentes Médicos Base
                </h5>
                <form id="formAntecedentes" class="m-0">
                    @csrf
                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
                    <div class="row g-3.5">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Médicos</label>
                            <textarea name="Medico" class="form-control rounded-3 py-2" rows="3" placeholder="Sin antecedentes registrados...">{{ $antecedentes->where('tipo', 'Médico')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Quirúrgicos</label>
                            <textarea name="Quirúrgico" class="form-control rounded-3 py-2" rows="3" placeholder="Sin antecedentes registrados...">{{ $antecedentes->where('tipo', 'Quirúrgico')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold text-danger small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Alergias conocidas</label>
                            <textarea name="Alergia" class="form-control border-danger-subtle rounded-3 py-2" rows="3" placeholder="Ninguna alergia registrada...">{{ $antecedentes->where('tipo', 'Alergia')->first()->descripcion ?? '' }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold text-success small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Medicación Habitual</label>
                            <textarea name="Medicación" class="form-control border-success-subtle rounded-3 py-2" rows="3" placeholder="Sin tratamiento farmacológico continuo...">{{ $antecedentes->where('tipo', 'Medicación')->first()->descripcion ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- SECCIÓN 2: HISTORIAL DE CONSULTAS Y ATENCIONES --}}
        <div class="col-12 mb-5">
            <h5 class="fw-bold text-dark mb-3.5 d-flex align-items-center">
                <i class="bi bi-clock-history me-2 text-primary fs-4"></i> Cronología de Consultas y Atenciones Previas
            </h5>
            <div class="accordion shadow-sm rounded-4 overflow-hidden" id="historialToggles" style="border: 1px solid #e2e8f0;">
                @forelse($historial as $hist)
                    <div class="accordion-item border-0 mb-0 border-bottom border-light">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-white fw-bold py-3 text-dark transition-row-normal" type="button" data-bs-toggle="collapse" data-bs-target="#h{{ $hist->id }}">
                                <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                                    <span class="fw-bold text-dark">
                                        <i class="bi bi-calendar-check-fill me-2 text-success"></i>
                                        Consulta del {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y') }}
                                    </span>
                                    <span class="badge border font-monospace text-primary bg-light-subtle px-3 py-1.5 rounded-2" style="font-size: 0.78rem; letter-spacing: 0.3px;">
                                        CIE-10: {{ $hist->diagnosticos->first() ? $hist->diagnosticos->first()->cie_10 : 'N/A' }}
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="h{{ $hist->id }}" class="accordion-collapse collapse" data-bs-parent="#historialToggles">
                            <div class="accordion-body bg-white p-4 border-top border-light">
                                
                                {{-- DETALLES CLÍNICOS ESTRUCTURADOS --}}
                                <div class="row g-4 mb-4">
                                    <div class="col-12 col-md-6 border-end-md">
                                        <h6 class="fw-bold font-monospace text-secondary small text-uppercase mb-2.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">Evaluación Física y Sintomatología</h6>
                                        <div class="p-3 bg-light rounded-3 border-start border-4 border-info mb-3">
                                            <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Anamnesis del paciente</small>
                                            <p class="small text-dark mb-0 fw-medium" style="line-height: 1.4;">{{ $hist->anamnesis }}</p>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border-start border-4 border-secondary">
                                            <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Examen exploratorio</small>
                                            <p class="small text-dark mb-0 fw-medium" style="line-height: 1.4;">{{ $hist->examen_fisico }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 ps-md-4">
                                        <h6 class="fw-bold font-monospace text-secondary small text-uppercase mb-2.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">Juicio Diagnóstico y Plan</h6>
                                        <div class="mb-3.5">
                                            @if($hist->diagnosticos->count() > 0)
                                                @foreach($hist->diagnosticos as $d)
                                                    <p class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">
                                                        <i class="bi bi-patch-check-fill me-1.5 text-success"></i> 
                                                        {{ $d->diagnostico }} <span class="text-secondary font-monospace opacity-75">({{ $d->cie_10 }})</span>
                                                    </p>
                                                @endforeach
                                            @else
                                                <p class="text-muted small fst-italic">Sin diagnósticos especificados en la atención.</p>
                                            @endif
                                        </div>
                                        
                                        <div class="p-3 bg-light rounded-3 border text-dark">
                                            <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Plan farmacológico / Indicaciones terapéuticas</small>
                                            <span class="small fw-medium" style="line-height: 1.4;">{{ $hist->plan }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- COMPONENTE DE RECETA ASOCIADO --}}
                                @if($hist->cita && $hist->cita->recetas->count() > 0)
                                    <div class="border-top border-light pt-3.5">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2.5">
                                            <h6 class="fw-bold text-danger small text-uppercase m-0 font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                <i class="bi bi-capsule-fill me-1"></i> Tratamiento Farmacológico Detallado
                                            </h6>
                                            <a href="{{ route('receta.pdf', $hist->cita_id) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm rounded-3 fw-bold px-3 py-1.5" style="font-size: 0.8rem;">
                                                <i class="bi bi-printer-fill me-1.5"></i> Reimprimir Receta PDF
                                            </a>
                                        </div>
                                        <div class="table-responsive rounded-3 border">
                                            <table class="table table-hover align-middle mb-0 w-100 text-center" style="font-size: 0.88rem;">
                                                <thead>
                                                    <tr class="table-light text-secondary uppercase font-monospace" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <th class="text-start py-2.5 ps-3">Medicamento / Concentración</th>
                                                        <th class="py-2.5">Presentación</th>
                                                        <th class="py-2.5">Dosis / Vía</th>
                                                        <th class="py-2.5">Frecuencia</th>
                                                        <th class="py-2.5">Duración</th>
                                                        <th class="bg-primary-subtle text-primary py-2.5 fw-black" width="15%">Cant. Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="border-top-0">
                                                    @foreach($hist->cita->recetas as $rec)
                                                        <tr>
                                                            <td class="text-start py-2.5 ps-3">
                                                                <div class="fw-bold text-dark mb-0.5">{{ $rec->medicamento }}</div>
                                                                <span class="badge bg-secondary-subtle text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.7rem;">{{ $rec->concentracion }}</span>
                                                            </td>
                                                            <td class="text-muted small fw-medium">{{ $rec->presentacion }}</td>
                                                            <td class="fw-semibold">
                                                                <div>{{ $rec->dosis }}</div>
                                                                <small class="text-muted text-uppercase font-monospace" style="font-size: 0.68rem;">{{ $rec->via_administracion }}</small>
                                                            </td>
                                                            <td class="text-secondary fw-medium">Cada {{ $rec->frecuencia }}</td>
                                                            <td class="text-secondary fw-medium">Por {{ $rec->duracion }}</td>
                                                            <td class="fw-black text-primary font-monospace bg-primary-subtle fs-6">{{ $rec->cantidad_total }} <small class="text-secondary">und.</small></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-5 bg-white rounded-4 border border-light">
                        <i class="bi bi-folder-x fs-1 d-block mb-2 text-muted opacity-50"></i>
                        <h6 class="fw-bold text-dark m-0">No se registran consultas previas en el expediente de este paciente.</h6>
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
        statusLabel.className = 'ms-2 font-monospace small text-muted';
        statusLabel.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Sincronizando...';

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
                statusLabel.className = 'ms-2 font-monospace small text-success fw-bold';
                statusLabel.innerHTML = '<i class="bi bi-check-circle-fill"></i> ¡Expediente base actualizado!';
                setTimeout(() => { 
                    btn.disabled = false;
                    statusLabel.innerHTML = '';
                }, 3000);
            } else {
                throw new Error();
            }
        } catch (error) {
            btn.disabled = false;
            statusLabel.className = 'ms-2 font-monospace small text-danger fw-bold';
            statusLabel.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Error al guardar';
        }
    }
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: all 0.2s ease; }
    
    textarea.form-control { resize: none; border-radius: 8px; border: 1px solid #cbd5e1; transition: border-color 0.15s, box-shadow 0.15s; }
    textarea.form-control:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
    
    .accordion-button:not(.collapsed) { background-color: #f8fafc; color: #4e73df; box-shadow: none; }
    .accordion-item:last-of-type { border-bottom: 0 !important; }

    .spin { animation: rotation 1.5s infinite linear; display: inline-block; }
    @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }

    @media(min-width: 768px) {
        .border-end-md { border-right: 1px solid #e2e8f0 !important; }
    }
</style>
@endsection