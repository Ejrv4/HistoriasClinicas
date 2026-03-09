@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-vcard me-2"></i>
                        {{ $cita->paciente->apellido }}, {{ $cita->paciente->nombre }} 
                        <span class="text-dark">({{ \Carbon\Carbon::parse($cita->paciente->fecha_nacimiento)->age }} años)</span> 
                        <span class="badge bg-info-subtle text-info border border-info-subtle ms-2" style="font-size: 0.8rem;">
                            {{ $cita->paciente->genero }}
                        </span>
                    </h4>
                    <span class="badge bg-dark">HC N° {{ str_pad($cita->paciente->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge bg-primary ms-1">CITA N° {{ str_pad($cita->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-muted ms-3 small">DNI: {{ $cita->paciente->dni }}</span>
                </div>
                <div class="col-md-4 text-md-end">
                    <div id="save-status" class="me-4 d-inline-block">
                        <span class="text-muted small"><i class="bi bi-cloud-check"></i> Listo</span>
                    </div>
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
        <input type="hidden" name="paciente_id" value="{{ $cita->paciente_id }}">

        <div class="tab-content" id="hcTabsContent">
            
            <div class="tab-pane fade show active" id="pestana-antecedentes" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-file-earmark-medical me-2"></i>Antecedentes del Paciente</h5>
                        <button type="button" onclick="guardarAntecedentesManual(event)" class="btn btn-success fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>GUARDAR ANTECEDENTES
                        </button>
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
                                        <li class="list-group-item bg-transparent text-muted italic">Sin registros previos.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="card border-0 shadow-sm p-4">
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Anamnesis</label>
                            <textarea name="anamnesis" class="form-control mb-3 auto-save" rows="4"></textarea>
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Examen Físico</label>
                            <textarea name="examen_fisico" class="form-control mb-3 auto-save" rows="4"></textarea>
                            <div class="row g-2 mb-3">
                                <div class="col-md-8">
                                    <label class="fw-bold text-secondary small mb-2 text-uppercase">Diagnóstico</label>
                                    <input type="text" name="diagnostico" class="form-control form-control-lg auto-save">
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold text-secondary small mb-2 text-uppercase">CIE-10</label>
                                    <input type="text" name="cie_10" class="form-control form-control-lg auto-save">
                                </div>
                            </div>
                            <label class="fw-bold text-secondary small mb-2 text-uppercase">Plan / Tratamiento</label>
                            <textarea name="plan" class="form-control mb-4 auto-save" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pestana-receta" role="tabpanel">
                <div class="card border-0 shadow-sm p-4">
                    <div class="bg-light p-3 rounded border mb-4">
                        <div class="row g-3">
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
                                <label class="small fw-bold text-muted">PRESENTACIÓN</label>
                                <input type="text" id="rec_pres" class="form-control" list="lista_pres_med" autocomplete="off">
                                <datalist id="lista_pres_med">
                                    @foreach($medicamentosLista->unique('presentacion') as $m)
                                        <option value="{{ $m->presentacion }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">DOSIS (Cant.)</label>
                                <input type="number" id="rec_dos" class="form-control calc-trigger" step="0.1">
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold text-muted">VÍA</label>
                                <select id="rec_via" class="form-select">
                                    <option>Via Oral</option><option>Intramuscular</option><option>Sublingual</option><option>Tópico</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-muted">FRECUENCIA</label>
                                <div class="input-group">
                                    <input type="number" id="f_n" class="form-control calc-trigger" placeholder="Cada...">
                                    <select id="f_t" class="form-select calc-trigger">
                                        <option value="Horas">Horas</option>
                                        <option value="Días">Días</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                                <input type="text" id="rec_total" class="form-control fw-bold border-primary text-center" readonly value="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" onclick="addMedicamento()" class="btn btn-danger w-100 fw-bold shadow-sm">AÑADIR</button>
                            </div>
                        </div>
                    </div>

                    <table class="table border align-middle mb-4 shadow-sm">
                        <thead class="table-dark small text-center">
                            <tr>
                                <th class="text-start">Medicamento</th>
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

                            @if($hist->cita && $hist->cita->recetas->count() > 0)
                                <h6 class="fw-bold text-danger small text-uppercase mb-2"><i class="bi bi-prescription2 me-1"></i>Medicamentos Recetados</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle bg-light" style="font-size: 0.85rem;">
                                        <thead class="table-secondary text-muted small text-center">
                                            <tr>
                                                <th class="text-start">Medicamento / Presentación</th>
                                                <th>Dosis/Vía</th>
                                                <th>Frecuencia</th>
                                                <th>Duración</th>
                                                <th>Cant.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($hist->cita->recetas as $r)
                                                <tr class="text-center">
                                                    <td class="text-start">
                                                        <strong>{{ $r->medicamento }}</strong><br>
                                                        <small class="text-muted">{{ $r->presentacion }}</small>
                                                    </td>
                                                    <td>{{ $r->dosis }} - {{ $r->via_administracion }}</td>
                                                    <td>Cada {{ $r->frecuencia }}</td>
                                                    <td>Por {{ $r->duracion }}</td>
                                                    <td class="fw-bold text-primary">{{ $r->cantidad_total ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border text-center py-5 text-muted small">No hay atenciones registradas para este paciente.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    // --- AUTOCOMPLETADO INTELIGENTE ---
    const baseMedicamentos = @json($medicamentosLista);
    const inputMed = document.getElementById('rec_med');
    const inputPres = document.getElementById('rec_pres');
    const datalistPres = document.getElementById('lista_pres_med');

    inputMed.addEventListener('input', function(e) {
        const val = e.target.value.trim().toLowerCase();
        
        // 1. Filtrar medicamentos que coincidan exactamente con el nombre escrito
        const presentacionesFiltradas = baseMedicamentos.filter(m => 
            m.nombre.toLowerCase() === val
        );

        // 2. Limpiar el datalist de presentaciones
        datalistPres.innerHTML = '';

        if (val === '' || presentacionesFiltradas.length === 0) {
            // Si está en blanco o no hay coincidencias, mostrar TODAS las presentaciones únicas
            const todasLasPres = [...new Set(baseMedicamentos.map(m => m.presentacion))];
            todasLasPres.forEach(p => {
                datalistPres.innerHTML += `<option value="${p}">`;
            });
        } else {
            // Si hay coincidencia de nombre, mostrar SOLO las presentaciones de ese medicamento
            presentacionesFiltradas.forEach(m => {
                datalistPres.innerHTML += `<option value="${m.presentacion}">`;
            });

            // Autoseleccionar si solo hay una opción para ahorrar tiempo al Dr. Román
            if (presentacionesFiltradas.length === 1) {
                inputPres.value = presentacionesFiltradas[0].presentacion;
            }
        }
    });

    // --- CÁLCULO DE DOSIS REACTIVO ---
    const camposCalculo = document.querySelectorAll('.calc-trigger');
    camposCalculo.forEach(el => {
        el.addEventListener('input', calcularCantidadTotal);
        el.addEventListener('change', calcularCantidadTotal); // Para capturar cambios en el Select
    });

    function calcularCantidadTotal() {
        const dosis = parseFloat(document.getElementById('rec_dos').value) || 0;
        const f_num = parseFloat(document.getElementById('f_n').value) || 0;
        const f_tipo = document.getElementById('f_t').value;
        const d_num = parseFloat(document.getElementById('d_n').value) || 0;
        const d_tipo = document.getElementById('d_t').value;

        if (dosis > 0 && f_num > 0 && d_num > 0) {
            let tomasAlDia = (f_tipo === 'Horas') ? (24 / f_num) : (1 / f_num);
            let diasTotales = d_num;
            if (d_tipo === 'Semanas') diasTotales = d_num * 7;
            if (d_tipo === 'Meses') diasTotales = d_num * 30;

            const resultado = dosis * tomasAlDia * diasTotales;
            document.getElementById('rec_total').value = Math.ceil(resultado);
            
            document.getElementById('rec_total').classList.add('bg-primary-subtle');
            setTimeout(() => document.getElementById('rec_total').classList.remove('bg-primary-subtle'), 400);
        } else {
            document.getElementById('rec_total').value = 0;
        }
    }

    // --- MANEJO DE RECETAS ---
    let recIdx = 0;
    function addMedicamento() {
        const med = document.getElementById('rec_med').value;
        const pres = document.getElementById('rec_pres').value;
        const dos = document.getElementById('rec_dos').value;
        const via = document.getElementById('rec_via').value;
        const freq = document.getElementById('f_n').value + ' ' + document.getElementById('f_t').value;
        const dur = document.getElementById('d_n').value + ' ' + document.getElementById('d_t').value;
        const total = document.getElementById('rec_total').value;

        if(!med || !dos || total <= 0) return alert("Complete los datos y verifique el cálculo.");

        const fila = `
            <tr id="fila_${recIdx}" class="align-middle text-center">
                <td class="text-start"><strong>${med}</strong><br><small class="text-muted">${pres}</small></td>
                <td>${dos} - ${via}</td>
                <td>${freq}</td>
                <td>${dur}</td>
                <td class="fw-bold text-primary">${total} <small>unid.</small></td>
                <td>
                    <button type="button" onclick="removeMed(${recIdx})" class="btn btn-outline-danger btn-sm rounded-circle">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            </tr>`;
        
        document.getElementById('listaRecetaVisual').insertAdjacentHTML('beforeend', fila);

        const hiddens = `
            <div id="hidden_${recIdx}">
                <input type="hidden" name="recetas[${recIdx}][medicamento]" value="${med}">
                <input type="hidden" name="recetas[${recIdx}][presentacion]" value="${pres}">
                <input type="hidden" name="recetas[${recIdx}][dosis]" value="${dos}">
                <input type="hidden" name="recetas[${recIdx}][via_administracion]" value="${via}">
                <input type="hidden" name="recetas[${recIdx}][frecuencia]" value="${freq}">
                <input type="hidden" name="recetas[${recIdx}][duracion]" value="${dur}">
                <input type="hidden" name="recetas[${recIdx}][cantidad_total]" value="${total}">
            </div>`;
        
        document.getElementById('inputs-receta-ocultos').insertAdjacentHTML('beforeend', hiddens);
        recIdx++;
        
        // Limpiar campos
        document.getElementById('rec_med').value = ''; 
        document.getElementById('rec_pres').value = '';
        document.getElementById('rec_dos').value = '';
        document.getElementById('rec_total').value = '0';
        document.getElementById('rec_med').focus();
    }

    function removeMed(id) {
        document.getElementById(`fila_${id}`).remove();
        document.getElementById(`hidden_${id}`).remove();
    }

    // --- ANTECEDENTES Y AUTOGUARDADO (Mantenidos) ---
    async function guardarAntecedentesManual(event) {
        const statusLabel = document.getElementById('save-status');
        const btn = event.currentTarget;
        const formDiv = document.getElementById('formAntecedentesContenedor');
        const inputs = formDiv.querySelectorAll('textarea');
        const pacienteId = document.querySelector('input[name="paciente_id"]').value;

        btn.disabled = true;
        statusLabel.innerHTML = 'Guardando...';

        const payload = { paciente_id: pacienteId };
        inputs.forEach(i => payload[i.name] = i.value);

        try {
            const response = await fetch("{{ route('antecedentes.guardar_todo') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                statusLabel.innerHTML = '<span class="text-success fw-bold">Guardado</span>';
                actualizarReferenciaLateral();
                setTimeout(() => { btn.disabled = false; }, 1000);
            }
        } catch (error) { btn.disabled = false; }
    }

    function actualizarReferenciaLateral() {
        const lista = document.getElementById('lista-referencia');
        const textAreas = document.querySelectorAll('#formAntecedentesContenedor textarea');
        lista.innerHTML = '';
        textAreas.forEach(ta => {
            if(ta.value.trim() !== '') {
                lista.innerHTML += `<li class="list-group-item bg-transparent py-2 border-0 border-bottom">
                    <strong class="text-primary d-block small">${ta.name.toUpperCase()}</strong><span>${ta.value}</span></li>`;
            }
        });
    }

    let debounceTimer;
    document.querySelectorAll('.auto-save').forEach(campo => {
        campo.addEventListener('input', () => {
            document.getElementById('save-status').innerHTML = 'Escribiendo...';
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                const form = document.getElementById('formAtencionMedica');
                try {
                    await fetch("{{ route('historias.autoguardar') }}", {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
                    });
                    document.getElementById('save-status').innerHTML = '<span class="text-success">Guardado</span>';
                } catch (e) {}
            }, 2000);
        });
    });
</script>

<style>
    .nav-tabs .nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; transition: all 0.3s ease; padding: 12px 25px; border-radius: 8px 8px 0 0; }
    .nav-tabs .nav-link.active { color: #ffffff !important; background-color: #2c3e50 !important; border-bottom: 3px solid #1a252f; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .nav-tabs .nav-link#receta-tab.active { background-color: #c0392b !important; border-bottom: 3px solid #962d22; }
    textarea.form-control { resize: none; border-radius: 8px; border: 1px solid #dee2e6; }
    .accordion-button:not(.collapsed) { background-color: #f8f9fc; color: #4e73df; }
    .btn-outline-danger.rounded-circle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0; }
</style>
@endsection