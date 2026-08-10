@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Botón Regresar Limpio --}}
    <div class="mb-3">
        <a href="{{ route('pacientes.index') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0 transition-row-normal d-inline-flex align-items-center" style="font-size: 0.88rem;">
            <i class="bi bi-arrow-left me-2 fs-5"></i>REGRESAR AL DIRECTORIO
        </a>
    </div>

    {{-- ENCABEZADO VIBRANTE COMPACTO --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white card-header-directorio">
        <div class="card-body py-3.5 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 rounded-3 text-white" style="background: rgba(255,255,255,0.15);">
                    <i class="bi bi-pencil-square fs-3"></i>
                </div>
                <div>
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.6rem; letter-spacing: -0.5px; font-weight: 900;">Editar Expediente Base</h2>
                    <p class="m-0 small opacity-75 fw-medium">Modificando el registro de: <span class="fw-bold text-warning">{{ $paciente->apellido }}, {{ $paciente->nombre }}</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- FORMULARIO PRINCIPAL EN PARRILLA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-card-custom">
        <div class="card-body p-4">
            <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST" autocomplete="off" class="m-0">
                @csrf
                @method('PUT')
                
                {{-- SECCIÓN 1: IDENTIDAD --}}
                <h5 class="text-dark fw-bold border-bottom pb-2 mb-3.5 d-flex align-items-center text-body-card" style="font-size: 1.05rem;">
                    <i class="bi bi-person-vcard text-primary me-2 fs-5"></i> Información de Identidad
                </h5>
                
                <div class="row g-3 mb-3.5 align-items-end">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Documento de Identidad (DNI)</label>
                        <input type="text" name="dni" maxlength="20" class="form-control rounded-3 font-monospace fw-semibold py-2 bg-card-custom text-body-card @error('dni') is-invalid @enderror" value="{{ old('dni', $paciente->dni) }}" style="font-size: 0.92rem; height: 38px;">
                        @error('dni')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <x-custom-search-dropdown 
                            label="Género Clínico"
                            name="genero"
                            id="paciente_genero_select"
                            placeholder="-- Seleccionar --"
                            :options="[
                                ['id' => 'Masculino', 'nombre' => 'Masculino'],
                                ['id' => 'Femenino', 'nombre' => 'Femenino'],
                                ['id' => 'Otros', 'nombre' => 'Otros']
                            ]"
                            :selectedValue="old('genero', $paciente->genero)"
                        />
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control rounded-3 py-2 font-monospace fw-semibold bg-card-custom text-body-card" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}" style="font-size: 0.92rem; height: 38px;">
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">País de Nacimiento</label>
                        <input type="text" name="pais_nacimiento" class="form-control rounded-3 py-2 fw-semibold bg-card-custom text-body-card" value="{{ old('pais_nacimiento', $paciente->pais_nacimiento) }}" style="font-size: 0.92rem; height: 38px;">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Nombres <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control rounded-3 py-2 fw-semibold text-uppercase bg-card-custom text-body-card" value="{{ old('nombre', $paciente->nombre) }}" required style="font-size: 0.92rem; height: 38px;">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" name="apellido" class="form-control rounded-3 py-2 fw-semibold text-uppercase bg-card-custom text-body-card" value="{{ old('apellido', $paciente->apellido) }}" required style="font-size: 0.92rem; height: 38px;">
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Ocupación / Trabajo</label>
                        <input type="text" name="trabajo" class="form-control rounded-3 py-2 fw-semibold bg-card-custom text-body-card" value="{{ old('trabajo', $paciente->trabajo) }}" placeholder="Ej: Ingeniero, Empleado, etc." style="font-size: 0.92rem; height: 38px;">
                    </div>
                </div>

                {{-- SECCIÓN 2: CONTACTO Y UBICACIÓN --}}
                <h5 class="text-dark fw-bold border-bottom pb-2 mt-2 mb-3.5 d-flex align-items-center text-body-card" style="font-size: 1.05rem;">
                    <i class="bi bi-geo-alt-fill text-primary me-2 fs-5"></i> Contacto y Ubicación de Residencia
                </h5>
                
                <div class="row g-3 mb-3.5 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Celular Personal</label>
                        <input type="text" name="celular_personal" id="celular_personal_input" maxlength="20" class="form-control rounded-3 py-2 font-monospace fw-semibold bg-card-custom text-body-card" value="{{ old('celular_personal', $paciente->celular_personal) }}" placeholder="Ej: 987654321" style="font-size: 0.92rem; height: 38px;">
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control rounded-3 py-2 fw-semibold bg-card-custom text-body-card" value="{{ old('correo', $paciente->correo) }}" placeholder="ejemplo@correo.com" style="font-size: 0.92rem; height: 38px;">
                    </div>
                </div>

                <div class="row g-3 mb-4.5">
                    <div class="col-12 col-md-4">
                        <x-custom-search-dropdown 
                            label="Distrito"
                            name="distrito"
                            id="paciente_distrito_select"
                            placeholder="Buscar distrito..."
                            :options="[
                                ['id' => 'Ancón', 'nombre' => 'Ancón'],
                                ['id' => 'Ate', 'nombre' => 'Ate'],
                                ['id' => 'Barranco', 'nombre' => 'Barranco'],
                                ['id' => 'Bellavista', 'nombre' => 'Bellavista'],
                                ['id' => 'Breña', 'nombre' => 'Breña'],
                                ['id' => 'Callao', 'nombre' => 'Callao'],
                                ['id' => 'Carabayllo', 'nombre' => 'Carabayllo'],
                                ['id' => 'Carmen de La Legua-Reynoso', 'nombre' => 'Carmen de La Legua-Reynoso'],
                                ['id' => 'Chaclacayo', 'nombre' => 'Chaclacayo'],
                                ['id' => 'Chorrillos', 'nombre' => 'Chorrillos'],
                                ['id' => 'Cieneguilla', 'nombre' => 'Cieneguilla'],
                                ['id' => 'Comas', 'nombre' => 'Comas'],
                                ['id' => 'El Agustino', 'nombre' => 'El Agustino'],
                                ['id' => 'Independencia', 'nombre' => 'Independencia'],
                                ['id' => 'Jesús María', 'nombre' => 'Jesús María'],
                                ['id' => 'La Molina', 'nombre' => 'La Molina'],
                                ['id' => 'La Perla', 'nombre' => 'La Perla'],
                                ['id' => 'La Punta', 'nombre' => 'La Punta'],
                                ['id' => 'La Victoria', 'nombre' => 'La Victoria'],
                                ['id' => 'Lima', 'nombre' => 'Lima'],
                                ['id' => 'Lince', 'nombre' => 'Lince'],
                                ['id' => 'Los Olivos', 'nombre' => 'Los Olivos'],
                                ['id' => 'Lurigancho-Chosica', 'nombre' => 'Lurigancho-Chosica'],
                                ['id' => 'Lurín', 'nombre' => 'Lurín'],
                                ['id' => 'Magdalena del Mar', 'nombre' => 'Magdalena del Mar'],
                                ['id' => 'Mi Perú', 'nombre' => 'Mi Perú'],
                                ['id' => 'Miraflores', 'nombre' => 'Miraflores'],
                                ['id' => 'Pachacámac', 'nombre' => 'Pachacámac'],
                                ['id' => 'Pucusana', 'nombre' => 'Pucusana'],
                                ['id' => 'Pueblo Libre', 'nombre' => 'Pueblo Libre'],
                                ['id' => 'Puente Piedra', 'nombre' => 'Puente Piedra'],
                                ['id' => 'Punta Hermosa', 'nombre' => 'Punta Hermosa'],
                                ['id' => 'Punta Negra', 'nombre' => 'Punta Negra'],
                                ['id' => 'Rímac', 'nombre' => 'Rímac'],
                                ['id' => 'San Bartolo', 'nombre' => 'San Bartolo'],
                                ['id' => 'San Borja', 'nombre' => 'San Borja'],
                                ['id' => 'San Isidro', 'nombre' => 'San Isidro'],
                                ['id' => 'San Juan de Lurigancho', 'nombre' => 'San Juan de Lurigancho'],
                                ['id' => 'San Juan de Miraflores', 'nombre' => 'San Juan de Miraflores'],
                                ['id' => 'San Luis', 'nombre' => 'San Luis'],
                                ['id' => 'San Martín de Porres', 'nombre' => 'San Martín de Porres'],
                                ['id' => 'San Miguel', 'nombre' => 'San Miguel'],
                                ['id' => 'Santa Anita', 'nombre' => 'Santa Anita'],
                                ['id' => 'Santa María del Mar', 'nombre' => 'Santa María del Mar'],
                                ['id' => 'Santa Rosa', 'nombre' => 'Santa Rosa'],
                                ['id' => 'Santiago de Surco', 'nombre' => 'Santiago de Surco'],
                                ['id' => 'Surquillo', 'nombre' => 'Surquillo'],
                                ['id' => 'Ventanilla', 'nombre' => 'Ventanilla'],
                                ['id' => 'Villa El Salvador', 'nombre' => 'Villa El Salvador'],
                                ['id' => 'Villa María del Triunfo', 'nombre' => 'Villa María del Triunfo']
                            ]"
                            :selectedValue="old('distrito', $paciente->distrito)"
                            :uppercase="true"
                        />
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Dirección Domiciliaria Completa</label>
                        <input type="text" name="direccion" class="form-control rounded-3 py-2 fw-semibold bg-card-custom text-body-card" value="{{ old('direccion', $paciente->direccion) }}" placeholder="Ej: Av. Principal 123 - Dpto 402" style="font-size: 0.92rem; height: 38px;">
                    </div>
                </div>

                {{-- ACCIONES --}}
                <div class="pt-4 border-top border-light d-flex gap-2.5 justify-content-end">
                    <a href="{{ route('pacientes.index') }}" class="btn btn-light rounded-3 px-4 py-2 border small fw-semibold text-secondary" style="font-size: 0.88rem;">Cancelar</a>
                    
                    <button type="submit" class="btn btn-primary rounded-3 px-5 py-2 fw-bold border-0 shadow-sm" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); font-size: 0.88rem;">
                        <i class="bi bi-save2-fill me-1.5"></i> Actualizar Expediente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initSingleCustomDropdown === 'function') {
            window.initSingleCustomDropdown('paciente_genero_select');
            window.initSingleCustomDropdown('paciente_distrito_select');
        }

        // ── LIMPIEZA DE NÚMERO TELEFÓNICO AL PEGAR/ESCRIBIR ──
        const phoneInput = document.getElementById('celular_personal_input');
        if (phoneInput) {
            function limpiarNumero(valor) {
                let soloNums = valor.replace(/\D/g, '');
                if (soloNums.startsWith('51') && soloNums.length > 9) {
                    soloNums = soloNums.substring(2);
                }
                // Permitimos hasta 20 caracteres según tu requerimiento ampliado
                return soloNums.substring(0, 20);
            }

            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
                this.value = limpiarNumero(textoPegado);
            });

            phoneInput.addEventListener('input', function() {
                this.value = limpiarNumero(this.value);
            });
        }
    });
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: opacity 0.15s ease; }
    .transition-row-normal:hover { opacity: 0.85; }

    .bg-card-custom { background-color: #ffffff; color: #0f172a; }
    .text-body-card { color: #0f172a; }
    .card-header-directorio { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important; }

    input.form-control, select.form-select {
        border: 1px solid #cbd5e1;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    input.form-control:focus, select.form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* 🌙 ADAPTACIÓN MODO OSCURO (data-bs-theme="dark") */
    [data-bs-theme="dark"] .bg-card-custom,
    [data-bs-theme="dark"] .card { 
        background-color: #1e293b !important; 
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .text-body-card,
    [data-bs-theme="dark"] .text-dark { color: #ffffff !important; }
    [data-bs-theme="dark"] .text-secondary { color: #94a3b8 !important; }
    [data-bs-theme="dark"] .card-header-directorio { background: #1e3a8a !important; }

    [data-bs-theme="dark"] input.form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #ffffff !important;
    }
</style>
@endsection