@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Botón Regresar Alineado --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0">
            <i class="bi bi-arrow-left me-2"></i>REGRESAR
        </a>
    </div>
    {{-- Encabezado del Paciente --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-vcard me-2"></i>
                        {{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }} 
                        <span class="text-dark">
                            ({{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años{{ $cita->paciente->trabajo ? ', ' . $cita->paciente->trabajo : '' }})
                        </span> 
                        <span class="badge bg-info-subtle text-info border border-info-subtle ms-2" style="font-size: 0.8rem;">
                            {{ $cita->paciente->genero }}
                        </span>
                    </h4>
                    <span class="badge bg-dark">HC N° {{ str_pad($cita->paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge bg-primary ms-1">CITA N° {{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-muted ms-3 small">DNI: {{ $cita->paciente->dni }}</span>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-block text-start">
                        <small class="text-muted d-block small-caps">País</small>
                        <span class="fw-bold">{{ $cita->paciente->pais_nacimiento }}</span>
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

    <form action="{{ route('historias.store') }}" method="POST" id="formAtencionMedica">
        @csrf
        <input type="hidden" name="cita_id" value="{{ $cita->id }}">
        <input type="hidden" name="paciente_id" id="paciente_id_global" value="{{ $cita->paciente_id }}">

        <div class="tab-content" id="hcTabsContent">
            
            {{-- PESTAÑA ANTECEDENTES --}}
            <div class="tab-pane fade show active" id="pestana-antecedentes" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-file-earmark-medical me-2"></i>Antecedentes del Paciente</h5>
                        <div class="d-flex align-items-center gap-3">
                            <span id="save-status" class="small fw-semibold"></span>
                            <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>GUARDAR ANTECEDENTES
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
                                        <li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                                            <strong class="text-primary d-block small">{{ strtoupper($ant->tipo) }}</strong>
                                            <span>{{ $ant->descripcion }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item bg-transparent text-muted fst-italic">Sin registros previos.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card border-0 shadow-sm p-4">
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Anamnesis</label>
                            <textarea name="anamnesis" class="form-control mb-3" rows="4"></textarea>
                            
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Examen Físico</label>
                            <textarea name="examen_fisico" class="form-control mb-3" rows="4"></textarea>
                            
                            {{-- SECCIÓN DE DIAGNÓSTICOS MÚLTIPLES --}}
                            <div id="diagnosticos-container">
                                <div class="row g-2 mb-2 diagnostico-row" id="diag-row-0">
                                    <div class="col-md-8">
                                        <label class="fw-bold text-secondary small mb-2 text-uppercase">Diagnóstico de Atención</label>
                                        <input type="text" name="diagnosticos[0][diagnostico]" class="form-control diag-input" list="lista_descripciones_cies" autocomplete="off">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-secondary small mb-2 text-uppercase">CIE-10</label>
                                        <input type="text" name="diagnosticos[0][cie_10]" class="form-control cie-input" list="lista_cies" autocomplete="off">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end pb-1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" onclick="agregarFilaDiagnostico()" class="btn btn-sm btn-outline-primary fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> AÑADIR OTRO DIAGNÓSTICO
                                </button>
                            </div>

                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Plan / Tratamiento</label>
                            <textarea name="plan" class="form-control mb-4" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PESTAÑA RECETA MÉDICA --}}
            <div class="tab-pane fade" id="pestana-receta" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="bg-light p-3 rounded border mb-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">MEDICAMENTO</label>
                                <input type="text" id="rec_med" class="form-control" list="lista_nombres_med" autocomplete="off">
                                <datalist id="lista_nombres_med">
                                    @foreach($medicamentosLista->unique('nombre') as $m)
                                        <option value="{{ $m->nombre }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">CONCENTRACIÓN</label>
                                <input type="text" id="rec_conc" class="form-control" list="lista_conc_med" autocomplete="off">
                                <datalist id="lista_conc_med"></datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">PRESENTACIÓN</label>
                                {{-- MANTENIDO: Input nativo original --}}
                                <input type="text" id="rec_pres" class="form-control" list="lista_pres_med" autocomplete="off">
                                <datalist id="lista_pres_med"></datalist>
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
                                    {{-- MODIFICACIÓN: Se añade la opción Dosis Única --}}
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
                                <th class="text-start">Medicamento / Concentración</th>
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
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg fw-bold">
                            <i class="bi bi-check-all me-2"></i> FINALIZAR ATENCIÓN MÉDICA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-5 mb-5">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Atenciones Previas</h5>
        <div class="accordion shadow-sm" id="historialToggles">
            @forelse($historiasAnteriores as $hist)
                <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
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
                            <div class="row mb-4">
                                <div class="col-md-6 border-end">
                                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Anamnesis / Examen</h6>
                                    <p class="small text-dark mb-3">{{ $hist->anamnesis }}</p>
                                    <p class="small text-muted italic">{{ $hist->examen_fisico }}</p>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <h6 class="fw-bold text-muted small text-uppercase mb-2">Diagnóstico y Plan</h6>
                                    <p class="fw-bold text-primary mb-1">{{ $hist->diagnostico }}</p>
                                    <div class="p-2 bg-light rounded border small text-dark">{{ $hist->plan }}</div>
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

{{-- DATALISTS MANTENIENDO VALORES ÚNICOS --}}
<datalist id="lista_cies">
    @foreach($cie10Lista as $cie)
        <option value="{{ $cie->codigo }} — {{ $cie->descripcion }}"></option>
    @endforeach
</datalist>

<datalist id="lista_descripciones_cies">
    @foreach($cie10Lista as $cie)
        <option value="{{ $cie->descripcion }} — {{ $cie->codigo }}"></option>
    @endforeach
</datalist>

<script>
    let formChanged = false;

    // --- 1. GESTIÓN DE DIAGNÓSTICOS MÚLTIPLES ---
    let diagCounter = 1;
    const baseCie = @json($cie10Lista);

    function agregarFilaDiagnostico() {
        const container = document.getElementById('diagnosticos-container');
        const html = `
            <div class="row g-2 mb-2 diagnostico-row" id="diag-row-${diagCounter}">
                <div class="col-md-8">
                    <input type="text" name="diagnosticos[${diagCounter}][diagnostico]" class="form-control diag-input" list="lista_descripciones_cies" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <input type="text" name="diagnosticos[${diagCounter}][cie_10]" class="form-control cie-input" list="lista_cies" autocomplete="off">
                </div>
                <div class="col-md-1 d-flex align-items-end pb-1">
                    <button type="button" onclick="eliminarFilaDiagnostico(${diagCounter})" class="btn btn-outline-danger border-0">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        diagCounter++;
    }

    function eliminarFilaDiagnostico(id) {
        document.getElementById(`diag-row-${id}`).remove();
    }

    document.getElementById('diagnosticos-container').addEventListener('input', function(e) {
        const row = e.target.closest('.diagnostico-row');
        if (!row) return;

        const inputDiag = row.querySelector('.diag-input');
        const inputCie = row.querySelector('.cie-input');
        const val = e.target.value;

        if (e.target.classList.contains('cie-input')) {
            if (val.includes(' — ')) {
                const partes = val.split(' — ');
                inputCie.value = partes[0].trim();
                inputDiag.value = partes[1].trim();
            } else {
                const coincidencia = baseCie.find(c => c.codigo.trim().toUpperCase() === val.trim().toUpperCase());
                if (coincidencia) inputDiag.value = coincidencia.descripcion;
            }
        }

        if (e.target.classList.contains('diag-input')) {
            if (val.includes(' — ')) {
                const partes = val.split(' — ');
                inputDiag.value = partes[0].trim();
                inputCie.value = partes[1].trim();
            } else {
                const coincidencia = baseCie.find(c => c.descripcion.trim().toUpperCase() === val.trim().toUpperCase());
                if (coincidencia) inputCie.value = coincidencia.codigo;
            }
        }
    });

    // --- 2. LÓGICA DE MEDICAMENTOS (FILTRADO CASCADA ORIGINAL) ---
    const baseMedicamentos = @json($medicamentosLista);
    const inputMed = document.getElementById('rec_med');
    const inputConc = document.getElementById('rec_conc');
    const inputPres = document.getElementById('rec_pres');
    const datalistConc = document.getElementById('lista_conc_med');
    const datalistPres = document.getElementById('lista_pres_med');

    // Cambios en cascada: Nombre -> Concentraciones
    inputMed.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        const filtrados = baseMedicamentos.filter(m => m.nombre.toLowerCase() === val);
        datalistConc.innerHTML = ''; datalistPres.innerHTML = '';
        inputConc.value = ''; inputPres.value = '';
        
        if (filtrados.length > 0) {
            const concentracionesUnicas = [...new Set(filtrados.map(m => m.concentracion))];
            concentracionesUnicas.forEach(c => datalistConc.innerHTML += `<option value="${c}">`);
            if(concentracionesUnicas.length === 1) {
                inputConc.value = concentracionesUnicas[0];
                inputConc.dispatchEvent(new Event('input')); 
            }
        }
    });

    // Cambios en cascada: Concentración -> Presentaciones
    inputConc.addEventListener('input', function() {
        const medVal = inputMed.value.trim().toLowerCase();
        const concVal = this.value.trim().toLowerCase();
        const filtrados = baseMedicamentos.filter(m => 
            m.nombre.toLowerCase() === medVal && m.concentracion.toLowerCase() === concVal
        );
        datalistPres.innerHTML = ''; inputPres.value = '';
        
        if (filtrados.length > 0) {
            const presentacionesUnicas = [...new Set(filtrados.map(m => m.presentacion))];
            presentacionesUnicas.forEach(p => datalistPres.innerHTML += `<option value="${p}">`);
            if(presentacionesUnicas.length === 1) {
                inputPres.value = presentacionesUnicas[0];
                inputPres.dispatchEvent(new Event('input'));
            }
        }
    });

    // Captura la presentación y desestructura Frecuencia y Duración
    inputPres.addEventListener('input', function() {
        const medVal = inputMed.value.trim().toLowerCase();
        const concVal = inputConc.value.trim().toLowerCase();
        const presVal = this.value.trim().toLowerCase();

        const medExacto = baseMedicamentos.find(m => 
            m.nombre.toLowerCase() === medVal && 
            m.concentracion.toLowerCase() === concVal &&
            m.presentacion.toLowerCase() === presVal
        );

        if (medExacto) {
            if (medExacto.dosis) document.getElementById('rec_dos').value = medExacto.dosis;
            if (medExacto.via_administracion) document.getElementById('rec_via').value = medExacto.via_administracion;
            
            // Separar y comparar FRECUENCIA
            if (medExacto.frecuencia) {
                if (medExacto.frecuencia === 'Dosis Única') {
                    document.getElementById('f_t').value = 'Dosis Única';
                } else {
                    const datosFrecuencia = separarNumeroYTexto(medExacto.frecuencia);
                    if (datosFrecuencia) {
                        document.getElementById('f_n').value = datosFrecuencia.numero;
                        const tiempoFormateado = datosFrecuencia.texto.charAt(0).toUpperCase() + datosFrecuencia.texto.slice(1);
                        document.getElementById('f_t').value = tiempoFormateado;
                    }
                }
            }
            
            // Separar y comparar DURACIÓN
            if (medExacto.duracion) {
                const datosDuracion = separarNumeroYTexto(medExacto.duracion);
                if (datosDuracion) {
                    document.getElementById('d_n').value = datosDuracion.numero;
                    const tiempoFormateado = datosDuracion.texto.charAt(0).toUpperCase() + datosDuracion.texto.slice(1);
                    document.getElementById('d_t').value = tiempoFormateado;
                }
            } else if (medExacto.frecuencia === 'Dosis Única') {
                document.getElementById('d_n').value = '';
            }

            if (medExacto.cantidad_total) {
                document.getElementById('rec_total').value = medExacto.cantidad_total;
            }
            
            // Forzamos el recálculo y la activación de bloqueos tras la inyección de datos
            calcularCantidadTotal();
        }
    });

    function separarNumeroYTexto(stringOriginal) {
        if (!stringOriginal) return null;
        const matches = stringOriginal.trim().match(/^(\d+(?:\.\d+)?)\s*(.*)$/);
        if (matches && matches.length === 3) {
            return {
                numero: matches[1],
                texto: matches[2].toLowerCase()
            };
        }
        return null;
    }

    // --- 3. CÁLCULO DE DOSIS Y CONTROL DE INHABILITACIÓN POR DOSIS ÚNICA ---
    function calcularCantidadTotal() {
        const dosisInput = document.getElementById('rec_dos');
        const f_numInput = document.getElementById('f_n');
        const f_tipoSelect = document.getElementById('f_t');
        const d_numInput = document.getElementById('d_n');
        const d_tipoSelect = document.getElementById('d_t');
        const totalInput = document.getElementById('rec_total');

        const dosis = parseFloat(dosisInput.value) || 0;

        // MODIFICACIÓN: Si es Dosis Única, bloqueamos y limpiamos Frecuencia (numérica) y Duración completa
        if (f_tipoSelect.value === 'Dosis Única') {
            f_numInput.value = "";
            f_numInput.disabled = true;
            f_numInput.placeholder = "N/A";
            
            d_numInput.value = "";
            d_numInput.disabled = true;
            d_numInput.placeholder = "N/A";
            d_tipoSelect.disabled = true;

            if (dosis > 0) {
                totalInput.value = Math.ceil(dosis);
            } else {
                totalInput.value = 0;
            }
        } else {
            // Desbloqueo clásico si cambian a Horas o Días
            f_numInput.disabled = false;
            f_numInput.placeholder = "Cada...";
            d_numInput.disabled = false;
            d_numInput.placeholder = "Por...";
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

    // --- 4. MANEJO DE RECETAS ---
    let recIdx = 0;
    function addMedicamento() {
        const med = document.getElementById('rec_med').value;
        const conc = document.getElementById('rec_conc').value;
        const pres = document.getElementById('rec_pres').value;
        const dos = document.getElementById('rec_dos').value;
        const via = document.getElementById('rec_via').value;
        const f_tipo = document.getElementById('f_t').value;
        
        // MODIFICACIÓN: Si es Dosis Única armamos los strings planos correspondientes para la receta
        let freq = f_tipo === 'Dosis Única' ? 'Dosis Única' : document.getElementById('f_n').value + ' ' + f_tipo;
        let dur = f_tipo === 'Dosis Única' ? 'N/A' : document.getElementById('d_n').value + ' ' + document.getElementById('d_t').value;
        const total = document.getElementById('rec_total').value;

        if(!med || !dos || !via || !pres || total <= 0) {
            return alert("Datos incompletos o Cantidad Total inválida.");
        }

        const fila = `<tr id="fila_${recIdx}" class="align-middle text-center">
            <td class="text-start"><strong>${med}</strong><br><span class="badge bg-secondary">${conc}</span></td>
            <td><small>${pres}</small></td>
            <td>${dos} - ${via}</td>
            <td>${freq}</td><td>${dur}</td>
            <td class="fw-bold text-primary">${total}</td>
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
        
        // Limpieza y restauración clásica
        ['rec_med','rec_conc','rec_pres','rec_dos','f_n','d_n'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('rec_total').value = '0';
        
        // Ejecutamos cálculo para remover bloqueos tras añadir el ítem
        calcularCantidadTotal();
    }

    function removeMed(id) {
        document.getElementById(`fila_${id}`).remove();
        document.getElementById(`hidden_${id}`).remove();
    }

// --- 5. GUARDAR / ACTUALIZAR ANTECEDENTES MANUAL CON RESET DE ADVERTENCIA ---
    async function guardarAntecedentesManual(event) {
        const statusLabel = document.getElementById('save-status');
        const btn = event.currentTarget;
        const pacienteId = document.getElementById('paciente_id_global').value;
        
        if (!pacienteId) {
            alert("Error: No se encontró el ID del paciente.");
            return;
        }

        const textoMedico = document.querySelector('textarea[name="Medico"]').value.trim();
        const textoQuirurgico = document.querySelector('textarea[name="Quirúrgico"]').value.trim();
        const textoAlergia = document.querySelector('textarea[name="Alergia"]').value.trim();
        const textoMedicacion = document.querySelector('textarea[name="Medicación"]').value.trim();

        btn.disabled = true;
        statusLabel.className = 'text-muted';
        statusLabel.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-1"></i>Guardando...';
        
        const payload = {
            paciente_id: pacienteId,
            Medico: textoMedico,
            Quirúrgico: textoQuirurgico,
            Alergia: textoAlergia,
            Medicación: textoMedicacion
        };
        
        try {
            const response = await fetch("{{ route('antecedentes.guardar_todo') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            
            if (response.ok && result.status === 'success') {
                statusLabel.className = 'text-success fw-bold';
                statusLabel.innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardado correctamente';

                // 1. Refrescar la lista lateral "Referencia Histórica" en vivo
                const listaReferencia = document.getElementById('lista-referencia');
                if (listaReferencia) {
                    let nuevoHtml = '';
                    if (textoMedico) {
                        nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                            <strong class="text-primary d-block small">MÉDICO</strong><span>${textoMedico}</span></li>`;
                    }
                    if (textoQuirurgico) {
                        nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                            <strong class="text-primary d-block small">QUIRÚRGICO</strong><span>${textoQuirurgico}</span></li>`;
                    }
                    if (textoAlergia) {
                        nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                            <strong class="text-danger d-block small">ALERGIA</strong><span>${textoAlergia}</span></li>`;
                    }
                    if (textoMedicacion) {
                        nuevoHtml += `<li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                            <strong class="text-success d-block small">MEDICACIÓN</strong><span>${textoMedicacion}</span></li>`;
                    }
                    if (!nuevoHtml) {
                        nuevoHtml = '<li class="list-group-item bg-transparent text-muted fst-italic">Sin registros previos.</li>';
                    }
                    listaReferencia.innerHTML = nuevoHtml;
                }

                // =======================================================================
                // 🔄 SOLUCIÓN AL AVISO: Apagamos la bandera de cambios pendientes
                // =======================================================================
                formChanged = false;
                // =======================================================================

                setTimeout(() => { statusLabel.innerHTML = ''; }, 3000);
            } else {
                throw new Error(result.message || 'Error desconocido');
            }
        } catch (error) {
            statusLabel.className = 'text-danger fw-bold';
            statusLabel.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i>Error al guardar`;
        } finally {
            btn.disabled = false;
        }
    }

    const atencionForm = document.getElementById('formAtencionMedica');
    atencionForm.addEventListener('input', () => {
        formChanged = true;
    });

    document.addEventListener('click', function (e) {
        const target = e.target.closest('a');
        if (formChanged && target && target.href && !target.hasAttribute('data-bs-toggle')) {
            const confirmacion = confirm("⚠️ No se han guardado los cambios de la atención actual. ¿Está seguro de que desea salir sin guardar?");
            if (!confirmacion) {
                e.preventDefault();
            }
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = ''; 
        }
    });

    atencionForm.addEventListener('submit', () => {
        formChanged = false; 
    });
</script>

<style>
    .nav-tabs .nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; transition: all 0.3s ease; padding: 12px 25px; border-radius: 8px 8px 0 0; }
    .nav-tabs .nav-link.active { color: #ffffff !important; background-color: #2c3e50 !important; border-bottom: 3px solid #1a252f; }
    .nav-tabs .nav-link#receta-tab.active { background-color: #c0392b !important; }
    textarea.form-control { resize: none; border-radius: 8px; }
    .btn-outline-danger.rounded-circle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0; }
</style>
@endsection