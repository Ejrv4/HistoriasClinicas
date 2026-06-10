@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Botón Regresar Alineado --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0">
            <i class="bi bi-arrow-left me-2"></i>REGRESAR
        </a>
    </div>
    
    {{-- ENCABEZADO --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-vcard me-2"></i>
                        {{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }}
                        <span class="text-dark">
                            {{-- PROTECCIÓN: Solo calcula la edad si la fecha de nacimiento existe --}}
                            @if($cita->paciente->fecha_nacimiento)
                                ({{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años{{ $cita->paciente->trabajo ? ', ' . $cita->paciente->trabajo : '' }})
                            @else
                                (Edad no registrada{{ $cita->paciente->trabajo ? ', ' . $cita->paciente->trabajo : '' }})
                            @endif
                        </span>
                        {{-- PROTECCIÓN: Solo muestra la medalla de género si este fue seleccionado --}}
                        @if($cita->paciente->genero)
                            <span class="badge bg-info-subtle text-info border border-info-subtle ms-2" style="font-size: 0.8rem;">
                                {{ $cita->paciente->genero }}
                            </span>
                        @endif
                    </h4>
                    <span class="badge bg-dark">HC N° {{ str_pad($cita->paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge bg-primary ms-1">CITA N° {{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</span>
                    {{-- PROTECCIÓN: Muestra "N/R" (No registrado) si no tiene DNI --}}
                    <span class="text-muted ms-3 small">DNI: {{ $cita->paciente->dni ?? 'N/R' }}</span>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-block text-start">
                        <small class="text-muted d-block small-caps">País</small>
                        <span class="fw-bold">{{ $cita->paciente->pais_nacimiento ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs border-0 mb-3" id="hcTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold px-4" id="antecedentes-tab" data-bs-toggle="tab" data-bs-target="#pestana-antecedentes" type="button">Antecedentes</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold px-4" id="consulta-tab" data-bs-toggle="tab" data-bs-target="#consulta" type="button">Historia Clínica</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold px-4 text-danger" id="receta-tab" data-bs-toggle="tab" data-bs-target="#pestana-receta" type="button">
                <i class="bi bi-capsule me-2"></i>Receta Médica
            </button>
        </li>
    </ul>

    {{-- FORMULARIO PRINCIPAL ACTUALIZADO CON COBERTURA ANTI-HISTORIAL --}}
    <form action="{{ route('historias.update', $historia->id) }}" method="POST" id="formAtencionMedica" autocomplete="off">
        @csrf
        @method('PUT')
        <input type="hidden" name="cita_id" value="{{ $cita->id }}">
        <input type="hidden" name="paciente_id" id="paciente_id_global" value="{{ $paciente->id }}">

        <div class="tab-content" id="hcTabsContent">
            
            {{-- PESTAÑA ANTECEDENTES --}}
            <div class="tab-pane fade show active" id="pestana-antecedentes" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-file-earmark-medical me-2"></i>Antecedentes del Paciente</h5>
                        <div class="d-flex align-items-center gap-3">
                            <span id="save-status" class="small fw-semibold"></span>
                            <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>ACTUALIZAR ANTECEDENTES
                            </button>
                        </div>
                    </div>
                    
                    <div id="formAntecedentesContenedor">
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
                    </div>
                </div>
            </div>

            {{-- PESTAÑA HISTORIA CLÍNICA --}}
            <div class="tab-pane fade" id="consulta" role="tabpanel">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-light mb-3">
                            <div class="card-header bg-white fw-bold small text-muted text-uppercase">Referencia Histórica</div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush small" id="lista-referencia">
                                    @forelse($antecedentes as $ant)
                                        <li class="list-group-item bg-transparent py-2 border-bottom">
                                            <strong class="text-primary d-block small">{{ strtoupper($ant->tipo) }}</strong>
                                            <span>{{ $ant->descripcion }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-transparent text-muted fst-italic">Sin registros.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card border-0 shadow-sm p-4">
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Anamnesis</label>
                            <textarea name="anamnesis" class="form-control mb-3" rows="4" required>{{ old('anamnesis', $historia->anamnesis) }}</textarea>
                            
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Examen Físico</label>
                            <textarea name="examen_fisico" class="form-control mb-3" rows="4">{{ old('examen_fisico', $historia->examen_fisico) }}</textarea>
                            
                            {{-- SECCIÓN DE DIAGNÓSTICOS MÚLTIPLES CARGADOS CON COMPONENTES --}}
                            <div id="diagnosticos-container">
                                <label class="fw-bold text-secondary small mb-2 text-uppercase d-block">Diagnósticos de la Atención</label>
                                
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
                                        <div class="col-md-1 pb-1">
                                            @if($index > 0)
                                                <button type="button" onclick="eliminarFilaDiagnostico({{ $index }})" class="btn btn-outline-danger border-0">
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
                                <button type="button" onclick="agregarFilaDiagnostico()" class="btn btn-sm btn-primary fw-bold shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> AÑADIR OTRO DIAGNÓSTICO
                                </button>
                            </div>

                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Plan / Tratamiento</label>
                            <textarea name="plan" class="form-control mb-4" rows="4">{{ old('plan', $historia->plan) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑA RECETA --}}
            <div class="tab-pane fade" id="pestana-receta" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="bg-light p-3 rounded border mb-4">
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
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">DOSIS (Cant.)</label>
                                <input type="number" id="rec_dos" class="form-control calc-trigger" step="0.1">
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">VÍA</label>
                                <select id="rec_via" class="form-select">
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
                                <label class="small fw-bold text-muted">FRECUENCIA</label>
                                <div class="input-group">
                                    <input type="number" id="f_n" class="form-control calc-trigger" placeholder="Cada...">
                                    <select id="f_t" class="form-select calc-trigger">
                                        <option value="Horas">Horas</option>
                                        <option value="Días">Días</option>
                                        <option value="Dosis Única">Dosis Única</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">DURACIÓN</label>
                                <div class="input-group">
                                    <input type="number" id="d_n" class="form-control calc-trigger" placeholder="Por...">
                                    <select id="d_t" class="form-select calc-trigger">
                                        <option value="Días">Días</option>
                                        <option value="Semanas">Semanas</option>
                                        <option value="Meses">Meses</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-primary">CANT. TOTAL</label>
                                <input type="number" id="rec_total" class="form-control fw-bold border-primary text-center" value="0">
                            </div>
                           
                            <div class="col-12 text-end mt-3">
                                <button type="button" onclick="addMedicamento()" class="btn btn-danger px-5 fw-bold shadow-sm">
                                    <i class="bi bi-plus-lg me-2"></i>AÑADIR A LA RECETA
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table border align-middle mb-4 shadow-sm">
                        <thead class="table-dark small text-center">
                            <tr>
                                <th class="text-start">Medicamento / Conc.</th>
                                <th>Presentación</th>
                                <th>Dosis/Vía</th>
                                <th>Frecuencia</th>
                                <th>Duración</th>
                                <th class="bg-primary text-white">Cant. Total</th>
                                <th width="5%">X</th>
                            </tr>
                        </thead>
                        <tbody id="listaRecetaVisual"></tbody>
                    </table>

                    <div id="inputs-receta-ocultos"></div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow-lg fw-bold">
                            <i class="bi bi-save me-2"></i> GUARDAR CAMBIOS Y FINALIZAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
   
    {{-- ACORDEÓN DE HISTORIAL HISTÓRICO --}}
    <div class="mt-5 mb-5">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Atenciones Previas</h5>
        <div class="accordion shadow-sm" id="historialToggles">
            @forelse($historiasAnteriores as $hist)
                <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#h{{ $hist->id }}">
                            <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                                <span><i class="bi bi-calendar-check me-2 text-success"></i>Atención del {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y') }}</span>
                                
                                {{-- CORRECCIÓN: Extrae el primer código CIE-10 de la relación de diagnósticos múltiples --}}
                                <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">
                                    CIE-10: {{ $hist->diagnosticos->first() ? $hist->diagnosticos->first()->cie_10 : 'N/A' }}
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="h{{ $hist->id }}" class="accordion-collapse collapse">
                        <div class="accordion-body bg-white border-top">
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
                                    
                                    {{-- CORRECCIÓN: Itera y muestra de forma estética todos los diagnósticos registrados en esta atención --}}
                                    <div class="mb-2">
                                        @if($hist->diagnosticos->count() > 0)
                                            @foreach($hist->diagnosticos as $d)
                                                <p class="fw-bold text-primary mb-1">
                                                    <i class="bi bi-patch-check-fill me-1 text-success"></i> 
                                                    {{ $d->diagnostico }} <span class="text-secondary font-monospace">({{ $d->cie_10 }})</span>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-muted small fst-italic">Sin diagnósticos especificados.</p>
                                        @endif
                                    </div>
                                    
                                    <div class="p-2 bg-light rounded border small text-dark">
                                        <strong>Plan de Tratamiento:</strong><br>
                                        {{ $hist->plan }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted p-3 border rounded bg-light text-center small">El paciente no registra consultas previas en el sistema.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- DATALISTS COMBINADOS EVITANDO CONGELAMIENTO POR CÓDIGOS DUPLICADOS --}}
<datalist id="lista_nombres_med">
    @foreach($medicamentosLista->unique('nombre') as $m)
        <option value="{{ $m->nombre }}">
    @endforeach
</datalist>

<script>
    let formChanged = false;
    let recIdx = 0;

    // --- 1. GESTIÓN DE DIAGNÓSTICOS MÚLTIPLES CON COMPONENTES ESTÉTICOS ---
    // DIFERENCIA CLAVE: Cuenta los registros guardados previos para no solapar IDs
    let diagCounter = {{ $historia->diagnosticos->count() > 0 ? $historia->diagnosticos->count() : 1 }};
    const baseCie = @json($cie10Lista);
    const baseMedicamentos = @json($medicamentosLista);

    // Mapeo estructurado de las opciones para alimentar las filas creadas dinámicamente por JS
    const opcionesDiagnostico = baseCie.map(c => ({ id: c.descripcion, nombre: `${c.descripcion} — ${c.codigo}` }));
    const opcionesCie = baseCie.map(c => ({ id: c.codigo, nombre: `${c.codigo} — ${c.descripcion}` }));

    // CARGA INICIAL: Recetas existentes + inicialización de dropdowns previos
    document.addEventListener('DOMContentLoaded', function() {
        const recetasExistentes = @json($cita->recetas);
        recetasExistentes.forEach(r => {
            injectReceta(r.medicamento, r.concentracion, r.presentacion, r.dosis, r.via_administracion, r.frecuencia, r.duracion, r.cantidad_total);
        });

        // Inicializar los que ya venían impresos desde la base de datos
        @foreach($historia->diagnosticos as $idx => $d)
            if(typeof window.initSingleCustomDropdown === 'function') {
                window.initSingleCustomDropdown('diag_select_{{ $idx }}');
                window.initSingleCustomDropdown('cie_select_{{ $idx }}');
            }
        @endforeach
    });

    function generarHtmlOpciones(arrayOpts) {
        return arrayOpts.map(o => `
            <li data-value="${o.id}" data-text="${o.nombre}" class="dropdown-item-custom position-relative px-3 py-2 border-bottom text-dark cursor-pointer text-truncate uppercase" style="font-size: 0.88rem; font-weight: 500; border-color: #f1f3f5 !important;">
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
                    <ul id="diag_select_${diagCounter}_list" class="d-none position-absolute start-0 w-100 dropdown-menu-floating shadow-lg border rounded-3 p-0 m-0 overflow-auto" style="max-height: 240px; z-index: 1050; background: #ffffff; list-style: none;">${generarHtmlOpciones(opcionesDiagnostico)}</ul>
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
                    <ul id="cie_select_${diagCounter}_list" class="d-none position-absolute start-0 w-100 dropdown-menu-floating shadow-lg border rounded-3 p-0 m-0 overflow-auto" style="max-height: 240px; z-index: 1050; background: #ffffff; list-style: none;">${generarHtmlOpciones(opcionesCie)}</ul>
                </div>
            </div>
            <div class="col-md-1 pb-1">
                <button type="button" onclick="eliminarFilaDiagnostico(${diagCounter})" class="btn btn-outline-danger border-0">
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

    // --- CORRECCIÓN DE SINCRONIZACIÓN EN CASMÉDICA SIN RECURSIVIDAD EN DIAGNÓSTICOS ---
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

    // --- 2. GESTIÓN DE RECETAS CON NUEVO DISEÑO (FILTRADO Y CASCADA AISLADA) ---
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

        const medExacto = baseMedicamentos.find(m => 
            m.nombre.toLowerCase() === medVal && m.concentracion.toLowerCase() === concVal && m.presentacion.toLowerCase() === presVal
        );

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
            if (medExacto.cantidad_total) document.getElementById('rec_total').value = medExacto.cantidad_total;
            calcularCantidadTotal();
        }
    }

    inputMedHidden.addEventListener('change', function() {
        actualizarConcentraciones(this.value);
    });

    inputConcHidden.addEventListener('change', function() {
        actualizarPresentaciones(inputMedHidden.value, this.value);
    });

    inputPresHidden.addEventListener('change', function() {
        procesarMedicamentoExacto(inputMedHidden.value, inputConcHidden.value, this.value);
    });

    // 🔄 PLURALIZACIÓN INTELIGENTE: Sincroniza cadenas singulares con los selectores del DOM
    function separarNumeroYTexto(stringOriginal) {
        if (!stringOriginal) return null;
        const matches = stringOriginal.trim().match(/^(\d+(?:\.\d+)?)\s*(.*)$/);
        if (matches && matches.length === 3) {
            let unidadTexto = matches[2].trim().toLowerCase();
            
            if (unidadTexto === 'mes' || unidadTexto === 'meses') {
                unidadTexto = 'Meses';
            } else if (unidadTexto === 'día' || unidadTexto === 'dia' || unidadTexto === 'días' || unidadTexto === 'dias') {
                unidadTexto = 'Días';
            } else if (unidadTexto === 'semana' || unidadTexto === 'semanas') {
                unidadTexto = 'Semanas';
            } else if (unidadTexto === 'hora' || unidadTexto === 'horas') {
                unidadTexto = 'Horas';
            } else {
                unidadTexto = unidadTexto.charAt(0).toUpperCase() + unidadTexto.slice(1);
            }

            return { 
                numero: matches[1], 
                text: unidadTexto 
            };
        }
        return null;
    }

    function injectReceta(med, conc, pres, dos, via, freq, dur, total) {
        const fila = `<tr id="fila_${recIdx}" class="align-middle text-center">
            <td class="text-start"><strong>${med}</strong><br><span class="badge bg-secondary">${conc}</span></td>
            <td><small>${pres}</small></td><td>${dos} - ${via}</td><td>${freq}</td><td>${dur}</td><td class="fw-bold text-primary">${total}</td>
            <td><button type="button" onclick="removeMed(${recIdx})" class="btn btn-outline-danger btn-sm rounded-circle"><i class="bi bi-x-lg"></i></button></td></tr>`;
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
        calcularCantidadTotal();
    }

    function removeMed(id) {
        document.getElementById(`fila_${id}`).remove();
        document.getElementById(`hidden_${id}`).remove();
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
            f_numInput.value = ""; f_numInput.disabled = true; f_numInput.placeholder = "N/A";
            d_numInput.value = ""; d_numInput.disabled = true; d_numInput.placeholder = "N/A";
            d_tipoSelect.disabled = true;
            totalInput.value = dosis > 0 ? Math.ceil(dosis) : 0;
        } else {
            f_numInput.disabled = false; f_numInput.placeholder = "Cada...";
            d_numInput.disabled = false; d_numInput.placeholder = "Por...";
            d_tipoSelect.disabled = false;

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
        el.addEventListener('input', calcularCantidadTotal);
        el.addEventListener('change', calcularCantidadTotal);
    });

    // --- 3. GUARDAR / ACTUALIZAR ANTECEDENTES MANUAL CON RESET DE ADVERTENCIA ---
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
                    if (textoMedico) nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-bottom"><strong class="text-primary d-block small">MÉDICO</strong><span>${textoMedico}</span></li>`;
                    if (textoQuirurgico) nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-bottom"><strong class="text-primary d-block small">QUIRÚRGICO</strong><span>${textoQuirurgico}</span></li>`;
                    if (textoAlergia) nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-bottom"><strong class="text-danger d-block small">ALERGIA</strong><span>${textoAlergia}</span></li>`;
                    if (textoMedicacion) nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-bottom"><strong class="text-success d-block small">MEDICACIÓN</strong><span>${textoMedicacion}</span></li>`;
                    listaReferencia.innerHTML = nuevoHtml || '<li class="list-group-item bg-transparent text-muted fst-italic">Sin registros previos.</li>';
                }
                formChanged = false;
                setTimeout(() => { statusLabel.innerHTML = ''; }, 3000);
            }
        } catch (error) {
            statusLabel.className = 'text-danger fw-bold';
            statusLabel.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>Error al guardar`;
        } finally { btn.disabled = false; }
    }

    const atencionForm = document.getElementById('formAtencionMedica');
    atencionForm.addEventListener('input', () => { formChanged = true; });
    document.addEventListener('click', function (e) {
        const target = e.target.closest('a');
        if (formChanged && target && target.href && !target.hasAttribute('data-bs-toggle')) {
            if (!confirm("⚠️ No se han guardado los cambios de la atención actual. ¿Desea salir sin guardar?")) e.preventDefault();
        }
    });
    window.addEventListener('beforeunload', function (e) { if (formChanged) { e.preventDefault(); e.returnValue = ''; } });
    atencionForm.addEventListener('submit', () => { formChanged = false; });
</script>

<style>
    .nav-tabs .nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; transition: all 0.3s ease; padding: 12px 25px; border-radius: 8px 8px 0 0; }
    .nav-tabs .nav-link.active { color: #ffffff !important; background-color: #2c3e50 !important; border-bottom: 3px solid #1a252f; }
    .nav-tabs .nav-link#receta-tab.active { background-color: #c0392b !important; }
    textarea.form-control { resize: none; border-radius: 8px; }
    .btn-outline-danger.rounded-circle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0; }
</style>
@endsection