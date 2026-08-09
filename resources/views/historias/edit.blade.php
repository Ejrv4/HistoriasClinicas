@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Botón Regresar Alineado y Limpio --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0 transition-row-normal d-inline-flex align-items-center" style="font-size: 0.88rem;">
            <i class="bi bi-arrow-left me-2 fs-5"></i>REGRESAR AL CALENDARIO
        </a>
    </div>
    
    {{-- Encabezado del Paciente con Diseño de Alta Gama --}}
    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden card-header-patient-info" style="border-left: 5px solid #4e73df !important;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                        <h3 class="fw-black text-dark-mode-title m-0 tracking-tight" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            {{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }} 
                        </h3>
                        <span class="text-secondary fw-semibold font-monospace" style="font-size: 1.1rem;">
                            @if($cita->paciente->fecha_nacimiento)
                                ({{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años{{ $cita->paciente->trabajo ? ', ' . $cita->paciente->trabajo : '' }})
                            @else
                                (Edad no registrada{{ $cita->paciente->trabajo ? ', ' . $cita->paciente->trabajo : '' }})
                            @endif
                        </span> 
                        @if($cita->paciente->genero)
                            <span class="badge border font-monospace fw-bold px-2 py-1 rounded-2 badge-gender-custom">
                                {{ strtoupper($cita->paciente->genero) }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 font-monospace">
                        <span class="badge bg-dark px-2.5 py-1.5 rounded-2">HC N° {{ str_pad($cita->paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="badge bg-primary px-2.5 py-1.5 rounded-2" style="background-color: #4e73df !important;">CITA N° {{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-muted ms-2 small fw-bold">DNI: <span class="text-dark-mode-title">{{ $cita->paciente->dni ?? 'N/R' }}</span></span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-block text-start bg-light-subtle px-3 py-2 rounded-3 border card-inner-box">
                        <small class="text-secondary d-block uppercase font-monospace fw-bold mb-0.5" style="font-size: 0.65rem; letter-spacing: 0.5px;">País de Origen</small>
                        <span class="fw-bold text-dark-mode-title" style="font-size: 0.95rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $cita->paciente->pais_nacimiento ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pestañas de Navegación Estilizadas --}}
    <ul class="nav nav-tabs border-0 mb-4 gap-2" id="hcTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold px-4 rounded-3 border-0 transition-row-normal" id="antecedentes-tab" data-bs-toggle="tab" data-bs-target="#pestana-antecedentes" type="button">
                <i class="bi bi-file-earmark-person me-2"></i>Antecedentes
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold px-4 rounded-3 border-0 transition-row-normal" id="consulta-tab" data-bs-toggle="tab" data-bs-target="#consulta" type="button">
                <i class="bi bi-file-earmark-medical me-2"></i>Historia Clínica
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold px-4 text-danger rounded-3 border-0 transition-row-normal" id="receta-tab" data-bs-toggle="tab" data-bs-target="#pestana-receta" type="button">
                <i class="bi bi-capsule me-2"></i>Receta Médica
            </button>
        </li>
    </ul>

    {{-- FORMULARIO PRINCIPAL EN MODO EDICIÓN (PUT) --}}
    <form action="{{ route('historias.update', $historia->id) }}" method="POST" id="formAtencionMedica" autocomplete="off">
        @csrf
        @method('PUT')
        <input type="hidden" name="cita_id" value="{{ $cita->id }}">
        <input type="hidden" name="paciente_id" id="paciente_id_global" value="{{ $paciente->id }}">

        <div class="tab-content" id="hcTabsContent">
            
            {{-- PESTAÑA ANTECEDENTES --}}
            <div class="tab-pane fade show active" id="pestana-antecedentes" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body-card">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold text-dark-mode-title mb-0 d-flex align-items-center"><i class="bi bi-file-earmark-medical-fill text-primary me-2 fs-4"></i>Actualizar Antecedentes</h5>
                        <div class="d-flex align-items-center gap-3">
                            <span id="save-status" class="small fw-semibold font-monospace"></span>
                            <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success fw-bold shadow-sm rounded-3 border-0 px-3 py-2" style="background-color: #10b981; font-size: 0.85rem;">
                                <i class="bi bi-save2-fill me-2"></i>ACTUALIZAR ANTECEDENTES
                            </button>
                        </div>
                    </div>
                    
                    <div id="formAntecedentesContenedor">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Médicos</label>
                                <textarea name="Medico" class="form-control rounded-3" rows="3">{{ $antecedentes->where('tipo', 'Médico')->first()->descripcion ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Quirúrgicos</label>
                                <textarea name="Quirúrgico" class="form-control rounded-3" rows="3">{{ $antecedentes->where('tipo', 'Quirúrgico')->first()->descripcion ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Alergias</label>
                                <textarea name="Alergia" class="form-control border-danger-subtle rounded-3" rows="3">{{ $antecedentes->where('tipo', 'Alergia')->first()->descripcion ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Medicación Habitual</label>
                                <textarea name="Medicación" class="form-control border-success-subtle rounded-3" rows="3">{{ $antecedentes->where('tipo', 'Medicación')->first()->descripcion ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑA HISTORIA CLÍNICA --}}
            <div class="tab-pane fade" id="consulta" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden border bg-body-card">
                            <div class="card-header border-bottom fw-bold font-monospace uppercase text-secondary py-2.5 px-3 bg-card-cap" style="font-size: 0.72rem; letter-spacing: 0.5px;">Referencia Histórica</div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush small font-monospace" id="lista-referencia">
                                    @forelse($antecedentes as $ant)
                                        <li class="list-group-item bg-transparent py-2.5 px-3 border-0 border-bottom border-light text-body-card">
                                            <strong class="text-primary d-block uppercase mb-0.5" style="font-size: 0.72rem;">{{ $ant->tipo }}</strong>
                                            <span class="text-secondary fw-medium">{{ $ant->descripcion }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-transparent text-muted fst-italic p-3 text-center small">Sin registros previos.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body-card">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Anamnesis</label>
                                <textarea name="anamnesis" class="form-control rounded-3" rows="4" required>{{ old('anamnesis', $historia->anamnesis) }}</textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Examen Físico</label>
                                <textarea name="examen_fisico" class="form-control rounded-3" rows="4">{{ old('examen_fisico', $historia->examen_fisico) }}</textarea>
                            </div>
                            
                            {{-- DIAGNÓSTICOS MÚLTIPLES EXISTENTES / NUEVOS --}}
                            <div id="diagnosticos-container">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Diagnósticos de la Atención</label>
                                
                                @forelse($historia->diagnosticos as $index => $diag)
                                    <div class="row g-2 mb-3 diagnostico-row align-items-end" id="diag-row-{{ $index }}">
                                        <div class="col-md-8">
                                            <x-custom-search-dropdown 
                                                name="diagnosticos[{{ $index }}][diagnostico]"
                                                id="diag_select_{{ $index }}"
                                                placeholder="Escriba descripción médica o código..."
                                                :options="$cie10Lista->map(fn($c) => ['id' => $c->descripcion, 'nombre' => $c->descripcion . ' — ' . $c->codigo])->toArray()"
                                                :selectedValue="$diag->diagnostico"
                                                :uppercase="true"
                                            />
                                        </div>
                                        <div class="col-md-3">
                                            <x-custom-search-dropdown 
                                                name="diagnosticos[{{ $index }}][cie_10]"
                                                id="cie_select_{{ $index }}"
                                                placeholder="Código..."
                                                :options="$cie10Lista->map(fn($c) => ['id' => $c->codigo, 'nombre' => $c->codigo . ' — ' . $c->descripcion])->toArray()"
                                                :selectedValue="$diag->cie_10"
                                                :uppercase="true"
                                            />
                                        </div>
                                        <div class="col-md-1 pb-1 text-center">
                                            @if($index > 0)
                                                <button type="button" onclick="eliminarFilaDiagnostico({{ $index }})" class="btn btn-outline-danger border-0 rounded-circle btn-remove-row">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="row g-2 mb-3 diagnostico-row align-items-end" id="diag-row-0">
                                        <div class="col-md-8">
                                            <x-custom-search-dropdown 
                                                name="diagnosticos[0][diagnostico]"
                                                id="diag_select_0"
                                                placeholder="Escriba descripción médica o código..."
                                                :options="$cie10Lista->map(fn($c) => ['id' => $c->descripcion, 'nombre' => $c->descripcion . ' — ' . $c->codigo])->toArray()"
                                                :uppercase="true"
                                            />
                                        </div>
                                        <div class="col-md-3">
                                            <x-custom-search-dropdown 
                                                name="diagnosticos[0][cie_10]"
                                                id="cie_select_0"
                                                placeholder="Código..."
                                                :options="$cie10Lista->map(fn($c) => ['id' => $c->codigo, 'nombre' => $c->codigo . ' — ' . $c->descripcion])->toArray()"
                                                :uppercase="true"
                                            />
                                        </div>
                                        <div class="col-md-1 pb-1"></div>
                                    </div>
                                @endforelse
                            </div>
                            
                            <div class="mb-4">
                                <button type="button" onclick="agregarFilaDiagnostico()" class="btn btn-sm btn-outline-primary fw-bold rounded-3 px-3 py-1.5" style="font-size: 0.8rem;">
                                    <i class="bi bi-plus-circle-fill me-1"></i> AÑADIR OTRO DIAGNÓSTICO
                                </button>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold text-secondary small uppercase font-monospace" style="font-size: 0.72rem; letter-spacing: 0.5px;">Plan / Tratamiento</label>
                                <textarea name="plan" class="form-control rounded-3 textarea-expandable" rows="4" placeholder="Indicaciones médicas generales...">{{ old('plan', $historia->plan) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑA RECETA --}}
            <div class="tab-pane fade" id="pestana-receta" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body-card">
                    <div class="p-3 rounded-4 border mb-4 card-inner-box">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <x-custom-search-dropdown 
                                    label="MEDICAMENTO"
                                    name="temp_rec_med"
                                    id="rec_med_select"
                                    placeholder="Escriba el nombre del fármaco..."
                                    :options="$medicamentosLista->unique('nombre')->map(fn($m) => ['id' => $m->nombre, 'nombre' => $m->nombre])->toArray()"
                                    :uppercase="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-custom-search-dropdown 
                                    label="CONCENTRACIÓN"
                                    name="temp_rec_conc"
                                    id="rec_conc_select"
                                    placeholder="Seleccione concentración..."
                                    :options="[]"
                                    :uppercase="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-custom-search-dropdown 
                                    label="PRESENTACIÓN"
                                    name="temp_rec_pres"
                                    id="rec_pres_select"
                                    placeholder="Seleccione presentación..."
                                    :options="[]"
                                    :uppercase="true"
                                />
                            </div>
                        </div>

                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small font-monospace fw-bold text-secondary" style="font-size: 0.7rem;">DOSIS (Cant.)</label>
                                <input type="number" id="rec_dos" class="form-control rounded-3 calc-trigger" step="0.1" style="height: 38px;">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small font-monospace fw-bold text-secondary" style="font-size: 0.7rem;">VÍA</label>
                                <select id="rec_via" class="form-select rounded-3" style="height: 38px;">
                                    <option value="VÍA ORAL">VÍA ORAL</option>
                                    <option value="VÍA ENDOVENOSA">VÍA ENDOVENOSA</option>
                                    <option value="VÍA INTRAMUSCULAR">VÍA INTRAMUSCULAR</option>
                                    <option value="VÍA TÓPICA">VÍA TÓPICA</option>
                                    <option value="VÍA ANAL">VÍA ANAL</option>
                                    <option value="VÍA SUBCUTÁNEA">VÍA SUBCUTÁNEA</option>
                                    <option value="VÍA RECTAL">VÍA RECTAL</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small font-monospace fw-bold text-secondary" style="font-size: 0.7rem;">FRECUENCIA</label>
                                <div class="input-group">
                                    <input type="number" id="f_n" class="form-control calc-trigger" placeholder="Cada..." style="height: 38px;">
                                    <select id="f_t" class="form-select calc-trigger" style="height: 38px;">
                                        <option value="Horas">Horas</option>
                                        <option value="Días">Días</option>
                                        <option value="Dosis Única">Dosis Única</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small font-monospace fw-bold text-secondary" style="font-size: 0.7rem;">DURACIÓN</label>
                                <div class="input-group">
                                    <input type="number" id="d_n" class="form-control calc-trigger" placeholder="Por..." style="height: 38px;">
                                    <select id="d_t" class="form-select calc-trigger" style="height: 38px;">
                                        <option value="Días">Días</option>
                                        <option value="Semanas">Semanas</option>
                                        <option value="Meses">Meses</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small font-monospace fw-bold text-primary" style="font-size: 0.7rem;">CANT. TOTAL</label>
                                <input type="number" id="rec_total" class="form-control fw-black text-center border-primary text-primary input-total-calc" value="0" style="height: 38px;">
                            </div>
                            
                            <div class="col-12 text-end mt-3">
                                <button type="button" onclick="addMedicamento()" class="btn btn-danger px-4 py-2 fw-bold shadow-sm rounded-3 border-0" style="background-color: #e11d48; font-size: 0.88rem;">
                                    <i class="bi bi-plus-lg me-2"></i>AÑADIR A LA RECETA
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table border align-middle mb-4 shadow-sm rounded-3 overflow-hidden">
                            <thead>
                                <tr class="table-dark small text-center uppercase font-monospace" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <th class="text-start py-2.5 ps-3">Medicamento / Concentración</th>
                                    <th class="py-2.5">Presentación</th>
                                    <th class="py-2.5">Dosis/Vía</th>
                                    <th class="py-2.5">Frecuencia</th>
                                    <th class="py-2.5">Duración</th>
                                    <th class="bg-primary text-white py-2.5">Cant. Total</th>
                                    <th width="5%" class="py-2.5 pe-3">X</th>
                                </tr>
                            </thead>
                            <tbody id="listaRecetaVisual"></tbody>
                        </table>
                    </div>

                    <div id="inputs-receta-ocultos"></div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg fw-bold rounded-3 border-0">
                            <i class="bi bi-save-fill me-1.5 small"></i> GUARDAR CAMBIOS Y FINALIZAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    {{-- ACORDEÓN DE HISTORIAL HISTÓRICO --}}
    <div class="mt-5 mb-5">
        <h5 class="fw-bold text-dark-mode-title mb-3 d-flex align-items-center"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Atenciones Previas</h5>
        <div class="accordion shadow-sm rounded-4 overflow-hidden" id="historialToggles">
            @forelse($historiasAnteriores as $hist)
                <div class="accordion-item border-0 mb-2 shadow-sm rounded overflow-hidden card-accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-body-card fw-bold py-3 text-dark-mode-title" type="button" data-bs-toggle="collapse" data-bs-target="#h{{ $hist->id }}">
                            <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                                <span class="fw-bold text-dark-mode-title"><i class="bi bi-calendar-check me-2 text-success"></i>Atención del {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y') }}</span>
                                <span class="badge border font-monospace text-primary bg-light-subtle px-3 py-1.5 rounded-2 badge-history-cie" style="font-size: 0.78rem;">
                                    CIE-10: {{ $hist->diagnosticos->first() ? $hist->diagnosticos->first()->cie_10 : 'N/A' }}
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="h{{ $hist->id }}" class="accordion-collapse collapse">
                        <div class="accordion-body bg-body-card border-top p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end-md">
                                    <h6 class="fw-bold font-monospace text-secondary small text-uppercase mb-2.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">Anamnesis / Examen Físico</h6>
                                    <div class="p-3 bg-light-subtle rounded-3 border-start border-4 border-info mb-3 card-inner-box">
                                        <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Anamnesis narrada</small>
                                        <p class="small text-dark-mode-title mb-0 fw-medium">{{ $hist->anamnesis }}</p>
                                    </div>
                                    <div class="p-3 bg-light-subtle rounded-3 border-start border-4 border-secondary card-inner-box">
                                        <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Examen exploratorio</small>
                                        <p class="small text-dark-mode-title mb-0 fw-medium">{{ $hist->examen_fisico }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bold font-monospace text-secondary small text-uppercase mb-2.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">Diagnóstico y Plan de Trabajo</h6>
                                    <div class="mb-3">
                                        @if($hist->diagnosticos->count() > 0)
                                            @foreach($hist->diagnosticos as $d)
                                                <p class="fw-bold text-primary mb-2" style="font-size: 0.95rem;">
                                                    <i class="bi bi-patch-check-fill me-1.5 text-success"></i> 
                                                    {{ $d->diagnostico }} <span class="text-secondary font-monospace opacity-75">({{ $d->cie_10 }})</span>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-muted small fst-italic">Sin diagnósticos especificados.</p>
                                        @endif
                                    </div>
                                    <div class="p-3 bg-light-subtle rounded-3 border text-dark-mode-title card-inner-box">
                                        <small class="d-block text-muted font-monospace uppercase fw-bold mb-1" style="font-size: 0.65rem;">Plan farmacológico / Terapéutico</small>
                                        <span class="small fw-medium">{{ $hist->plan }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted p-4 border rounded-4 bg-body-card text-center small fw-medium text-secondary">El paciente no registra consultas previas en el sistema.</div>
            @endforelse
        </div>
    </div>
    
    {{-- MODAL ADVERTENCIA DE SALIDA --}}
    <div class="modal fade" id="modalConfirmarSalida" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 bg-body-card">
                <div class="modal-header bg-warning-subtle border-0 py-3">
                    <h5 class="modal-title fw-bold text-warning-emphasis d-flex align-items-center" style="font-size: 1.1rem;">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i> Cambios sin guardar
                    </h5>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0 text-dark-mode-title fw-semibold" style="font-size: 0.92rem; line-height: 1.5;">
                        ⚠️ Detectamos que has modificado la atención actual pero no has guardado los cambios. ¿Estás seguro de que deseas salir? Perderás toda la información ingresada.
                    </p>
                </div>
                <div class="modal-footer border-top-0 pt-0 p-2.5 rounded-bottom-4 card-inner-box">
                    <button type="button" class="btn btn-light rounded-3 fw-bold text-secondary px-3 py-2 small" data-bs-dismiss="modal" style="font-size: 0.85rem;">Permanecer aquí</button>
                    <a href="#" id="btnConfirmarSalidaURL" class="btn btn-warning rounded-3 fw-bold px-4 py-2 shadow-sm text-dark" style="font-size: 0.85rem;">Salir sin guardar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<datalist id="lista_nombres_med">
    @foreach($medicamentosLista->unique('nombre') as $m)
        <option value="{{ $m->nombre }}">
    @endforeach
</datalist>

<script>
    let formChanged = false;
    let recIdx = 0;

    let diagCounter = {{ $historia->diagnosticos->count() > 0 ? $historia->diagnosticos->count() : 1 }};
    const baseCie = @json($cie10Lista);
    const baseMedicamentos = @json($medicamentosLista);

    const opcionesDiagnostico = baseCie.map(c => ({ id: c.descripcion, nombre: `${c.descripcion} — ${c.codigo}` }));
    const opcionesCie = baseCie.map(c => ({ id: c.codigo, nombre: `${c.codigo} — ${c.descripcion}` }));

    document.addEventListener('DOMContentLoaded', function() {
        const recetasExistentes = @json($cita->recetas);
        recetasExistentes.forEach(r => {
            injectReceta(r.medicamento, r.concentracion, r.presentacion, r.dosis, r.via_administracion, r.frecuencia, r.duracion, r.cantidad_total);
        });

        @foreach($historia->diagnosticos as $idx => $d)
            if(typeof window.initSingleCustomDropdown === 'function') {
                window.initSingleCustomDropdown('diag_select_{{ $idx }}');
                window.initSingleCustomDropdown('cie_select_{{ $idx }}');
            }
        @endforeach

        configurarNavegacionTecladoParaDropdowns();
    });

    /* =========================================================================
       ⌨️ MOTOR DE NAVEGACIÓN POR TECLADO (TAB, FLECHAS ARRIBA/ABAJO, ENTER)
       ========================================================================= */
    function configurarNavegacionTecladoParaDropdowns() {
        document.addEventListener('keydown', function(e) {
            const input = e.target;
            if (!input || !input.id || (!input.id.includes('_select_') && !input.id.includes('rec_'))) return;

            const box = input.closest('.custom-search-dropdown-box');
            if (!box) return;

            const list = box.querySelector('ul');
            if (!list || list.classList.contains('d-none')) return;

            const items = Array.from(list.querySelectorAll('.dropdown-item-custom')).filter(i => i.style.display !== 'none');
            if (items.length === 0) return;

            let currentIndex = items.findIndex(i => i.classList.contains('item-active-keyboard'));

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex >= 0) items[currentIndex].classList.remove('item-active-keyboard');
                currentIndex = (currentIndex + 1) % items.length;
                items[currentIndex].classList.add('item-active-keyboard');
                items[currentIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex >= 0) items[currentIndex].classList.remove('item-active-keyboard');
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                items[currentIndex].classList.add('item-active-keyboard');
                items[currentIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter' || e.key === 'Tab') {
                const itemParaSeleccionar = currentIndex >= 0 ? items[currentIndex] : items[0];
                if (itemParaSeleccionar) {
                    if (e.key === 'Enter') e.preventDefault();
                    itemParaSeleccionar.click();
                }
            } else if (e.key === 'Escape') {
                list.classList.add('d-none');
            }
        });
    }

    function generarHtmlOpciones(arrayOpts) {
        return arrayOpts.map(o => `
            <li data-value="${o.id}" data-text="${o.nombre}" class="dropdown-item-custom position-relative px-3 py-2 border-bottom text-dark-mode-title cursor-pointer text-truncate uppercase" style="font-size: 0.88rem; font-weight: 500;">
                <span class="visible-text-span">${o.nombre}</span>
                <div class="custom-hover-tooltip" style="position: absolute; inset: 0; padding: 0.5rem 1rem; background-color: #212529; color: #ffffff; font-size: 0.78rem; font-weight: 600; line-height: 1.3; display: flex; align-items: center; justify-content: start; pointer-events: none; visibility: hidden; opacity: 0; transition: opacity 0.1s ease; white-space: normal; z-index: 10;">${o.nombre}</div>
            </li>`).join('');
    }

    function agregarFilaDiagnostico() {
        const container = document.getElementById('diagnosticos-container');
        
        const html = `
        <div class="row g-2 mb-3 diagnostico-row align-items-end" id="diag-row-${diagCounter}">
            <div class="col-md-8">
                <div class="position-relative w-100 text-start custom-search-dropdown-box" id="diag_select_${diagCounter}" data-uppercase="true">
                    <div class="position-relative">
                        <input type="text" id="diag_select_${diagCounter}_input" placeholder="Escriba descripción médica o código..." autocomplete="off" class="form-control pe-5 text-uppercase" style="font-size: 0.9rem; font-weight: 500;" />
                        <input type="hidden" name="diagnosticos[${diagCounter}][diagnostico]" id="diag_select_${diagCounter}_value" class="real-hidden-value">
                        <div class="position-absolute end-0 top-50 translate-middle-y pe-3 pointer-events-none text-muted" style="line-height: 1;">
                            <i class="bi bi-chevron-down dropdown-arrow-icon" id="diag_select_${diagCounter}_arrow" style="transition: transform 0.2s; display: inline-block;"></i>
                        </div>
                    </div>
                    <ul id="diag_select_${diagCounter}_list" class="d-none position-absolute start-0 w-100 dropdown-menu-floating shadow-lg border rounded-3 p-0 m-0 overflow-auto bg-body-card" style="max-height: 240px; z-index: 1050; list-style: none;">${generarHtmlOpciones(opcionesDiagnostico)}</ul>
                </div>
            </div>
            <div class="col-md-3">
                <div class="position-relative w-100 text-start custom-search-dropdown-box" id="cie_select_${diagCounter}" data-uppercase="true">
                    <div class="position-relative">
                        <input type="text" id="cie_select_${diagCounter}_input" placeholder="Código..." autocomplete="off" class="form-control pe-5 text-uppercase" style="font-size: 0.9rem; font-weight: 500;" />
                        <input type="hidden" name="diagnosticos[${diagCounter}][cie_10]" id="cie_select_${diagCounter}_value" class="real-hidden-value">
                        <div class="position-absolute end-0 top-50 translate-middle-y pe-3 pointer-events-none text-muted" style="line-height: 1;">
                            <i class="bi bi-chevron-down dropdown-arrow-icon" id="cie_select_${diagCounter}_arrow" style="transition: transform 0.2s; display: inline-block;"></i>
                        </div>
                    </div>
                    <ul id="cie_select_${diagCounter}_list" class="d-none position-absolute start-0 w-100 dropdown-menu-floating shadow-lg border rounded-3 p-0 m-0 overflow-auto bg-body-card" style="max-height: 240px; z-index: 1050; list-style: none;">${generarHtmlOpciones(opcionesCie)}</ul>
                </div>
            </div>
            <div class="col-md-1 pb-1 text-center">
                <button type="button" onclick="eliminarFilaDiagnostico(${diagCounter})" class="btn btn-outline-danger border-0 rounded-circle btn-remove-row">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        
        if (typeof window.initSingleCustomDropdown === 'function') {
            window.initSingleCustomDropdown(`diag_select_${diagCounter}`);
            window.initSingleCustomDropdown(`cie_select_${diagCounter}`);
        }
        
        diagCounter++;
    }

    function eliminarFilaDiagnostico(id) {
        const row = document.getElementById(`diag-row-${id}`);
        if(row) row.remove();
    }

    document.getElementById('diagnosticos-container').addEventListener('change', function(e) {
        if (!e.target.classList.contains('real-hidden-value')) return;
        const row = e.target.closest('.diagnostico-row');
        if (!row) return;
        const inputDiagHidden = row.querySelector('[id^="diag_select_"][id$="_value"]');
        const inputCieHidden = row.querySelector('[id^="cie_select_"][id$="_value"]');
        if (!e.target.value) return;

        if (e.target.id.startsWith('cie_select_')) {
            const coincidencia = baseCie.find(c => c.codigo.toUpperCase() === e.target.value.toUpperCase());
            if (coincidencia && inputDiagHidden.value !== coincidencia.descripcion) {
                const inputDiagVisible = row.querySelector('[id^="diag_select_"][id$="_input"]');
                inputDiagHidden.value = coincidencia.descripcion;
                inputDiagVisible.value = coincidencia.descripcion;
                marcarItemActivo(inputDiagHidden.id.replace('_value', '_list'), coincidencia.descripcion);
            }
        } else if (e.target.id.startsWith('diag_select_')) {
            const coincidencia = baseCie.find(c => c.descripcion.toUpperCase() === e.target.value.toUpperCase());
            if (coincidencia && inputCieHidden.value !== coincidencia.codigo) {
                const inputCieVisible = row.querySelector('[id^="cie_select_"][id$="_input"]');
                inputCieHidden.value = coincidencia.codigo;
                inputCieVisible.value = coincidencia.codigo;
                marcarItemActivo(inputCieHidden.id.replace('_value', '_list'), coincidencia.codigo);
            }
        }
    });

    function marcarItemActivo(listId, valorBuscar) {
        const list = document.getElementById(listId);
        if (!list) return;
        const items = list.querySelectorAll('.dropdown-item-custom');
        items.forEach(item => {
            item.classList.remove('bg-primary', 'text-white', 'font-bold');
            if (item.getAttribute('data-value').toUpperCase() === valorBuscar.toUpperCase()) {
                item.classList.add('bg-primary', 'text-white', 'font-bold');
            }
        });
    }

    if (typeof window.initSingleCustomDropdown === 'function') {
        window.initSingleCustomDropdown('rec_med_select');
        window.initSingleCustomDropdown('rec_conc_select');
        window.initSingleCustomDropdown('rec_pres_select');
    }

    const inputMedHidden = document.getElementById('rec_med_select_value');
    const inputConcHidden = document.getElementById('rec_conc_select_value');
    const inputConcVisible = document.getElementById('rec_conc_select_input');
    const inputPresHidden = document.getElementById('rec_pres_select_value');
    const inputPresVisible = document.getElementById('rec_pres_select_input');
    const listConc = document.getElementById('rec_conc_select_list');
    const listPres = document.getElementById('rec_pres_select_list');

    function actualizarConcentraciones(medNombre) {
        const val = medNombre.trim().toLowerCase();
        const filtrados = baseMedicamentos.filter(m => m.nombre.toLowerCase() === val);
        listConc.innerHTML = ''; listPres.innerHTML = '';
        inputConcHidden.value = ''; inputConcVisible.value = '';
        inputPresHidden.value = ''; inputPresVisible.value = '';
        if (filtrados.length > 0) {
            const concentracionesUnicas = [...new Set(filtrados.map(m => m.concentracion))].filter(Boolean);
            listConc.innerHTML = generarHtmlOpciones(concentracionesUnicas.map(c => ({ id: c, nombre: c })));
            if (concentracionesUnicas.length === 1) {
                inputConcHidden.value = concentracionesUnicas[0];
                inputConcVisible.value = concentracionesUnicas[0];
                actualizarPresentaciones(medNombre, concentracionesUnicas[0]);
            }
        }
    }

    function actualizarPresentaciones(medNombre, concNombre) {
        const medVal = medNombre.trim().toLowerCase();
        const concVal = concNombre.trim().toLowerCase();
        const filtrados = baseMedicamentos.filter(m => m.nombre.toLowerCase() === medVal && m.concentracion.toLowerCase() === concVal);
        listPres.innerHTML = ''; inputPresHidden.value = ''; inputPresVisible.value = '';
        if (filtrados.length > 0) {
            const presentacionesUnicas = [...new Set(filtrados.map(m => m.presentacion))].filter(Boolean);
            listPres.innerHTML = generarHtmlOpciones(presentacionesUnicas.map(p => ({ id: p, nombre: p })));
            if (presentacionesUnicas.length === 1) {
                inputPresHidden.value = presentacionesUnicas[0];
                inputPresVisible.value = presentacionesUnicas[0];
                procesarMedicamentoExacto(medNombre, concNombre, presentacionesUnicas[0]);
            }
        }
    }

    function procesarMedicamentoExacto(medNombre, concNombre, presNombre) {
        const medVal = medNombre.trim().toLowerCase();
        const concVal = concNombre.trim().toLowerCase();
        const presVal = presNombre.trim().toLowerCase();
        const medExacto = baseMedicamentos.find(m => m.nombre.toLowerCase() === medVal && m.concentracion.toLowerCase() === concVal && m.presentacion.toLowerCase() === presVal);

        if (medExacto) {
            if (medExacto.dosis) document.getElementById('rec_dos').value = medExacto.dosis;
            if (medExacto.via_administracion) document.getElementById('rec_via').value = medExacto.via_administracion;
            if (medExacto.frecuencia) {
                if (medExacto.frecuencia === 'Dosis Única') {
                    document.getElementById('f_t').value = 'Dosis Única';
                } else {
                    const datosFrecuencia = separarNumeroYTexto(medExacto.frecuencia);
                    if (datosFrecuencia) {
                        document.getElementById('f_n').value = datosFrecuencia.numero;
                        document.getElementById('f_t').value = datosFrecuencia.text;
                    }
                }
            }
            if (medExacto.duracion) {
                const datosDuracion = separarNumeroYTexto(medExacto.duracion);
                if (datosDuracion) {
                    document.getElementById('d_n').value = datosDuracion.numero;
                    document.getElementById('d_t').value = datosDuracion.text;
                }
            } else if (medExacto.frecuencia === 'Dosis Única') {
                document.getElementById('d_n').value = '';
            }
            if (medExacto.cantidad_total) {
                document.getElementById('rec_total').value = medExacto.cantidad_total;
            }
            configurarVisibilidadCampos();
        }
    }

    inputMedHidden.addEventListener('change', function() { actualizarConcentraciones(this.value); });
    inputConcHidden.addEventListener('change', function() { actualizarPresentaciones(inputMedHidden.value, this.value); });
    inputPresHidden.addEventListener('change', function() { procesarMedicamentoExacto(inputMedHidden.value, inputConcHidden.value, this.value); });

    function separarNumeroYTexto(stringOriginal) {
        if (!stringOriginal) return null;
        const matches = stringOriginal.trim().match(/^(\d+(?:\.\d+)?)\s*(.*)$/);
        if (matches && matches.length === 3) {
            let unidadTexto = matches[2].trim().toLowerCase();
            if (unidadTexto === 'mes' || unidadTexto === 'meses') unidadTexto = 'Meses';
            else if (unidadTexto === 'día' || unidadTexto === 'dia' || unidadTexto === 'días' || unidadTexto === 'dias') unidadTexto = 'Días';
            else if (unidadTexto === 'semana' || unidadTexto === 'semanas') unidadTexto = 'Semanas';
            else if (unidadTexto === 'hora' || unidadTexto === 'horas') unidadTexto = 'Horas';
            else unidadTexto = unidadTexto.charAt(0).toUpperCase() + unidadTexto.slice(1);
            return { numero: matches[1], text: unidadTexto };
        }
        return null;
    }

    function injectReceta(med, conc, pres, dos, via, freq, dur, total) {
        const fila = `<tr id="fila_${recIdx}" class="align-middle text-center">
            <td class="text-start ps-3"><strong>${med}</strong><br><span class="badge bg-secondary">${conc}</span></td>
            <td><small>${pres}</small></td><td>${dos} - ${via}</td><td>${freq}</td><td>${dur}</td><td class="fw-bold text-primary">${total}</td>
            <td class="pe-3"><button type="button" onclick="removeMed(${recIdx})" class="btn btn-outline-danger btn-sm rounded-circle"><i class="bi bi-x-lg"></i></button></td></tr>`;
        document.getElementById('listaRecetaVisual').insertAdjacentHTML('beforeend', fila);

        const hiddens = `<div id="hidden_${recIdx}">
            <input type="hidden" name="recetas[${recIdx}][medicamento]" value="${med}">
            <input type="hidden" name="recetas[${recIdx}][concentracion]" value="${conc}">
            <input type="hidden" name="recetas[${recIdx}][presentacion]" value="${pres}">
            <input type="hidden" name="recetas[${recIdx}][dosis]" value="${dos}">
            <input type="hidden" name="recetas[${recIdx}][via_administracion]" value="${via}">
            <input type="hidden" name="recetas[${recIdx}][frecuencia]" value="${freq}">
            <input type="hidden" name="recetas[${recIdx}][duracion]" value="${dur}">
            <input type="hidden" name="recetas[${recIdx}][cantidad_total]" value="${total}"></div>`;
        document.getElementById('inputs-receta-ocultos').insertAdjacentHTML('beforeend', hiddens);
        recIdx++;
    }

    function addMedicamento() {
        const med = inputMedHidden.value;
        const conc = inputConcHidden.value;
        const pres = inputPresHidden.value;
        const dos = document.getElementById('rec_dos').value;
        const via = document.getElementById('rec_via').value;
        const f_tipo = document.getElementById('f_t').value;
        let freq = f_tipo === 'Dosis Única' ? 'Dosis Única' : document.getElementById('f_n').value + ' ' + f_tipo;
        let dur = f_tipo === 'Dosis Única' ? 'N/A' : document.getElementById('d_n').value + ' ' + document.getElementById('d_t').value;
        const total = document.getElementById('rec_total').value;

        if(!med || !dos || !via || !pres || total <= 0) {
            return alert("❌ Error: Faltan completar campos obligatorios del fármaco.");
        }
        injectReceta(med, conc, pres, dos, via, freq, dur, total);
        ['rec_dos','f_n','d_n'].forEach(id => document.getElementById(id).value = '');
        inputMedHidden.value = ''; document.getElementById('rec_med_select_input').value = '';
        inputConcHidden.value = ''; inputConcVisible.value = '';
        inputPresHidden.value = ''; inputPresVisible.value = '';
        listConc.innerHTML = ''; listPres.innerHTML = '';
        document.getElementById('rec_total').value = '0';
        configurarVisibilidadCampos();
    }

    function removeMed(id) {
        document.getElementById(`fila_${id}`).remove();
        document.getElementById(`hidden_${id}`).remove();
    }

    function configurarVisibilidadCampos() {
        const f_tipoSelect = document.getElementById('f_t');
        const f_numInput = document.getElementById('f_n');
        const d_numInput = document.getElementById('d_n');
        const d_tipoSelect = document.getElementById('d_t');
        if (f_tipoSelect.value === 'Dosis Única') {
            f_numInput.value = ""; f_numInput.disabled = true; f_numInput.placeholder = "N/A";
            d_numInput.value = ""; d_numInput.disabled = true; d_numInput.placeholder = "N/A";
            d_tipoSelect.disabled = true;
        } else {
            f_numInput.disabled = false; f_numInput.placeholder = "Cada...";
            d_numInput.disabled = false; d_numInput.placeholder = "Por...";
            d_tipoSelect.disabled = false;
        }
    }

    function calcularCantidadTotal() {
        const dosisInput = document.getElementById('rec_dos');
        const f_numInput = document.getElementById('f_n');
        const f_tipoSelect = document.getElementById('f_t');
        const d_numInput = document.getElementById('d_n');
        const d_tipoSelect = document.getElementById('d_t');
        const totalInput = document.getElementById('rec_total');
        const dosis = parseFloat(dosisInput.value) || 0;

        if (f_tipoSelect.value === 'Dosis Única') {
            totalInput.value = dosis > 0 ? Math.ceil(dosis) : 0;
        } else {
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

    document.querySelectorAll('.calc-trigger').forEach(el => {
        el.addEventListener('input', function() { configurarVisibilidadCampos(); calcularCantidadTotal(); });
        el.addEventListener('change', function() { configurarVisibilidadCampos(); calcularCantidadTotal(); });
    });

    async function guardarAntecedentesManual(event) {
        const statusLabel = document.getElementById('save-status');
        const btn = event.currentTarget;
        const pacienteId = document.getElementById('paciente_id_global').value;
        if (!pacienteId) return alert("Error: No se encontró el ID del paciente.");
        const textoMedico = document.querySelector('textarea[name="Medico"]').value.trim();
        const textoQuirurgico = document.querySelector('textarea[name="Quirúrgico"]').value.trim();
        const textoAlergia = document.querySelector('textarea[name="Alergia"]').value.trim();
        const textoMedicacion = document.querySelector('textarea[name="Medicación"]').value.trim();

        btn.disabled = true;
        statusLabel.className = 'text-muted';
        statusLabel.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i>Guardando...';
        try {
            const response = await fetch("{{ route('antecedentes.guardar_todo') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ paciente_id: pacienteId, Medico: textoMedico, Quirúrgico: textoQuirurgico, Alergia: textoAlergia, Medicación: textoMedicacion })
            });
            const result = await response.json();
            if (response.ok && result.status === 'success') {
                statusLabel.className = 'text-success fw-bold';
                statusLabel.innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardado correctamente';
                const listaReferencia = document.getElementById('lista-referencia');
                if (listaReferencia) {
                    let nuevoHtml = '';
                    if (textoMedico) nuevoHtml += `<li class="list-group-item bg-transparent py-2.5 px-3 border-bottom"><strong class="text-primary d-block small">MÉDICO</strong><span>${textoMedico}</span></li>`;
                    if (textoQuirurgico) nuevoHtml += `<li class="list-group-item bg-transparent py-2.5 px-3 border-bottom"><strong class="text-primary d-block small">QUIRÚRGICO</strong><span>${textoQuirurgico}</span></li>`;
                    if (textoAlergia) nuevoHtml += `<li class="list-group-item bg-transparent py-2.5 px-3 border-bottom"><strong class="text-danger d-block small">ALERGIA</strong><span>${textoAlergia}</span></li>`;
                    if (textoMedicacion) nuevoHtml += `<li class="list-group-item bg-transparent py-2.5 px-3 border-bottom"><strong class="text-success d-block small">MEDICACIÓN</strong><span>${textoMedicacion}</span></li>`;
                    listaReferencia.innerHTML = nuevoHtml || '<li class="list-group-item bg-transparent text-muted fst-italic">Sin registros previos.</li>';
                }
                formChanged = false;
                setTimeout(() => { statusLabel.innerHTML = ''; }, 3000);
            }
        } catch (error) {
            statusLabel.className = 'text-danger fw-bold';
            statusLabel.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>Error`;
        } finally { btn.disabled = false; }
    }

    const atencionForm = document.getElementById('formAtencionMedica');
    const btnConfirmarSalidaURL = document.getElementById('btnConfirmarSalidaURL');
    let modalSalidaInstance = null;

    /* =========================================================================
       🛡️ VALIDACIÓN PREVENTIVA DEL FORMULARIO DE ATENCIÓN Y CIE-10
       ========================================================================= */
    atencionForm.addEventListener('submit', function(e) {
        const filasDiag = document.querySelectorAll('.diagnostico-row');
        let errorDiag = false;

        filasDiag.forEach((row, idx) => {
            const hiddenDiag = row.querySelector('[id^="diag_select_"][id$="_value"]');
            const hiddenCie = row.querySelector('[id^="cie_select_"][id$="_value"]');
            const inputDiag = row.querySelector('[id^="diag_select_"][id$="_input"]');
            const inputCie = row.querySelector('[id^="cie_select_"][id$="_input"]');

            if (!hiddenDiag.value.trim() || !hiddenCie.value.trim()) {
                errorDiag = true;
                if (inputDiag) inputDiag.style.borderColor = '#ef4444';
                if (inputCie) inputCie.style.borderColor = '#ef4444';
            } else {
                if (inputDiag) inputDiag.style.borderColor = '';
                if (inputCie) inputCie.style.borderColor = '';
            }
        });

        if (errorDiag) {
            e.preventDefault();
            alert("⚠️ Atención: Debes seleccionar un Diagnóstico válido y su código CIE-10 correspondiente antes de finalizar la atención.");
            
            const consultaTabBtn = document.getElementById('consulta-tab');
            if (consultaTabBtn) {
                const tab = new bootstrap.Tab(consultaTabBtn);
                tab.show();
            }
            return false;
        }

        formChanged = false;
    });

    atencionForm.addEventListener('input', () => { formChanged = true; });
    atencionForm.addEventListener('change', () => { formChanged = true; });

    document.addEventListener('DOMContentLoaded', function() {
        const modalSalidaElement = document.getElementById('modalConfirmarSalida');
        if (modalSalidaElement && typeof bootstrap !== 'undefined') {
            modalSalidaInstance = new bootstrap.Modal(modalSalidaElement);
        }
        document.addEventListener('click', function (e) {
            const target = e.target.closest('a');
            if (target && target.href && formChanged) {
                const href = target.getAttribute('href');
                if (href === '#' || href.startsWith('#') || target.hasAttribute('data-bs-toggle') || target.hasAttribute('data-bs-dismiss')) return;
                e.preventDefault();
                if (btnConfirmarSalidaURL) btnConfirmarSalidaURL.setAttribute('href', target.href);
                if (modalSalidaInstance) modalSalidaInstance.show();
                else if (confirm("⚠️ Cambios sin guardar. ¿Deseas salir?")) window.location.href = target.href;
            }
        });
    });

    if (btnConfirmarSalidaURL) btnConfirmarSalidaURL.addEventListener('click', () => { formChanged = false; });
    window.addEventListener('beforeunload', function (e) { if (formChanged) { e.preventDefault(); e.returnValue = ''; } });
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: all 0.2s ease; }
    .transition-row-normal:hover { opacity: 0.85; }

    /* Estilos estructurales claro */
    .bg-body-card { background-color: #ffffff; }
    .text-dark-mode-title { color: #0f172a; }
    .card-inner-box { background-color: #f8fafc; border-color: #e2e8f0; }
    .card-header-patient-info { background: #ffffff; }
    .badge-gender-custom { background-color: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
    .input-total-calc { background-color: #f0f4ff; }

    /* Estilización moderna de pestañas de control */
    .nav-tabs .nav-link { 
        color: #64748b; 
        border: 1px solid #e2e8f0; 
        background-color: #ffffff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
        padding: 10px 24px; 
        font-size: 0.88rem;
    }
    .nav-tabs .nav-link:hover { background-color: #f1f5f9; color: #0f172a; }
    .nav-tabs .nav-link.active { 
        color: #ffffff !important; 
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
    }
    .nav-tabs .nav-link#receta-tab.active { 
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%) !important; 
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
    }
    
    textarea.form-control { resize: none; border-radius: 8px; border: 1px solid #cbd5e1; }
    textarea.textarea-expandable { 
        resize: vertical !important; 
        min-height: 100px; 
        max-height: 400px; 
    }
    textarea.form-control:focus { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }

    .btn-remove-row { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }

    /* Resaltado del teclado en listas desplegables */
    .dropdown-item-custom.item-active-keyboard {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
    }

    @media(min-width: 768px) {
        .border-end-md { border-right: 1px solid #e2e8f0 !important; }
    }

    /* ==========================================================================
       🌙 ADAPTACIONES ESPECÍFICAS MODO OSCURO (DARK MODE OVERRIDES)
       ========================================================================== */
    [data-bs-theme="dark"] .bg-body-card { background-color: #1e293b !important; }
    [data-bs-theme="dark"] .text-dark-mode-title { color: #ffffff !important; }
    [data-bs-theme="dark"] .card-header-patient-info { background: #1e293b !important; }
    [data-bs-theme="dark"] .card-inner-box { background-color: #111827 !important; border-color: #334155 !important; }
    [data-bs-theme="dark"] .card-card-cap { background-color: #0f172a !important; }
    [data-bs-theme="dark"] .badge-gender-custom { background-color: #0c4a6e !important; color: #38bdf8 !important; border-color: #0369a1 !important; }
    [data-bs-theme="dark"] .badge-history-cie { background-color: #0f172a !important; border-color: #334155 !important; }
    [data-bs-theme="dark"] .card-accordion-item { border-color: #334155 !important; }
    [data-bs-theme="dark"] .input-total-calc { background-color: #111827 !important; color: #60a5fa !important; border-color: #3b82f6 !important; }
    
    [data-bs-theme="dark"] .nav-tabs .nav-link:not(.active) {
        background-color: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }
    [data-bs-theme="dark"] .nav-tabs .nav-link:not(.active):hover {
        background-color: #334155;
        color: #ffffff;
    }
    
    [data-bs-theme="dark"] textarea.form-control,
    [data-bs-theme="dark"] input.form-control,
    [data-bs-theme="dark"] select.form-select {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #ffffff !important;
    }
</style>
@endsection