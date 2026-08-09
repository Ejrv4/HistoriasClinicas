@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    {{-- Botón Regresar de Cortesía --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-secondary fw-bold ps-0 transition-row-normal d-inline-flex align-items-center" style="font-size: 0.88rem;">
            <i class="bi bi-arrow-left me-2 fs-5"></i>REGRESAR AL CALENDARIO
        </a>
    </div>

    {{-- ENCABEZADO VIBRANTE COMPACTO --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
        <div class="card-body py-3.5 px-4">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 bg-white-50 rounded-3 text-white" style="background: rgba(255,255,255,0.15);">
                    <i class="bi bi-person-plus-fill fs-3"></i>
                </div>
                <div>
                    <h2 class="fw-black text-uppercase m-0 tracking-tight" style="font-size: 1.6rem; letter-spacing: -0.5px; font-weight: 900;">Registrar Nuevo Paciente</h2>
                    <p class="m-0 small opacity-75 fw-medium">Apertura de expediente clínico base en el sistema de gestión médica.</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- MENSAJES DE ERROR DE VALIDACIÓN --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                <i class="bi bi-exclamation-octagon-fill"></i> Corrige los siguientes campos obligatorios:
            </div>
            <ul class="mb-0 small ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FORMULARIO PRINCIPAL --}}
    <form action="{{ route('pacientes.store') }}" method="POST" class="card border-0 shadow-sm rounded-4 p-4" autocomplete="off" style="background: #ffffff;">
        @csrf
        
        {{-- SECCIÓN 1: IDENTIDAD --}}
        <h5 class="text-dark fw-bold border-bottom pb-2 mb-3.5 d-flex align-items-center" style="font-size: 1.05rem;">
            <i class="bi bi-person-vcard text-primary me-2 fs-5"></i> Datos de Identidad Básica
        </h5>
        
        <div class="row g-3 mb-3.5 align-items-end">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Documento de Identidad (DNI)</label>
                <input type="text" name="dni" maxlength="8" class="form-control rounded-3 font-monospace fw-semibold py-2 @error('dni') is-invalid @enderror" value="{{ old('dni') }}" placeholder="Ej: 74589632" style="font-size: 0.92rem; height: 38px;">
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
                    :selectedValue="old('genero')"
                />
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control rounded-3 py-2 font-monospace fw-semibold" value="{{ old('fecha_nacimiento') }}" style="font-size: 0.92rem; height: 38px;">
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Lugar de Nacimiento (País)</label>
                <input type="text" name="pais_nacimiento" class="form-control rounded-3 py-2 fw-semibold" value="{{ old('pais_nacimiento', 'Perú') }}" placeholder="Ej: Perú" style="font-size: 0.92rem; height: 38px;">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Nombres <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control rounded-3 py-2 fw-semibold text-uppercase" value="{{ old('nombre') }}" placeholder="Nombres del paciente" required style="font-size: 0.92rem; height: 38px;">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Apellidos <span class="text-danger">*</span></label>
                <input type="text" name="apellido" class="form-control rounded-3 py-2 fw-semibold text-uppercase" value="{{ old('apellido') }}" placeholder="Apellidos paterno y materno" required style="font-size: 0.92rem; height: 38px;">
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Ocupación / Trabajo</label>
                <input type="text" name="trabajo" class="form-control rounded-3 py-2 fw-semibold" value="{{ old('trabajo') }}" placeholder="Ej: Estudiante, Empleado, etc." style="font-size: 0.92rem; height: 38px;">
            </div>
        </div>

        {{-- SECCIÓN 2: CONTACTO Y UBICACIÓN --}}
        <h5 class="text-dark fw-bold border-bottom pb-2 mt-2 mb-3.5 d-flex align-items-center" style="font-size: 1.05rem;">
            <i class="bi bi-geo-alt-fill text-primary me-2 fs-5"></i> Datos de Contacto y Ubicación
        </h5>
        
        <div class="row g-3 mb-4.5 align-items-end">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Celular Personal</label>
                <input type="text" name="celular_personal" id="celular_personal_input" maxlength="15" class="form-control rounded-3 py-2 font-monospace fw-semibold" value="{{ old('celular_personal') }}" placeholder="Ej: 987654321" style="font-size: 0.92rem; height: 38px;">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label font-monospace uppercase fw-bold text-secondary" style="font-size: 0.72rem; letter-spacing: 0.5px;">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control rounded-3 py-2 fw-semibold" value="{{ old('correo') }}" placeholder="ejemplo@correo.com" style="font-size: 0.92rem; height: 38px;">
            </div>
            
            <div class="col-12 col-md-12 col-lg-4">
                <x-custom-search-dropdown 
                    label="Distrito de Residencia"
                    name="distrito"
                    id="paciente_distrito_select"
                    placeholder="Escriba para buscar distrito..."
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
                    :selectedValue="old('distrito')"
                    :uppercase="true"
                />
            </div>
        </div>

        {{-- BLOQUE DE ACCIONES --}}
        <div class="pt-4 border-top border-light d-flex flex-wrap gap-2.5 justify-content-end">
            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-3 px-4 py-2 border small fw-semibold text-secondary" style="font-size: 0.88rem;">Cancelar</a>
            
            <button type="submit" class="btn btn-outline-primary rounded-3 px-4 py-2 fw-bold" style="font-size: 0.88rem;">
                <i class="bi bi-save-fill me-1.5"></i> Guardar Expediente
            </button>

            <button type="submit" name="crear_cita_ahora" value="1" class="btn btn-primary rounded-3 px-4 py-2 fw-bold shadow-sm border-0" style="background: linear-gradient(135deg, #224abe 0%, #1e40af 100%); font-size: 0.88rem;">
                <i class="bi bi-calendar-plus-fill me-1.5"></i> Registrar y Crear Cita Ahora
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initSingleCustomDropdown === 'function') {
            window.initSingleCustomDropdown('paciente_genero_select');
            window.initSingleCustomDropdown('paciente_distrito_select');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initSingleCustomDropdown === 'function') {
        window.initSingleCustomDropdown('paciente_genero_select');
        window.initSingleCustomDropdown('paciente_distrito_select');
    }

    // ── LÓGICA DE LIMPIEZA AUTOMÁTICA PARA EL CELULAR ──
    const phoneInput = document.getElementById('celular_personal_input');

    if (phoneInput) {
        function limpiarNumeroPeratado(valor) {
            // 1. Convertir a string y eliminar espacios, guiones o paréntesis
            let soloNumeros = valor.replace(/\D/g, '');

            // 2. Si empieza con 51 (código de Perú) y tiene más de 9 dígitos, remover el 51 inicial
            if (soloNumeros.startsWith('51') && soloNumeros.length > 9) {
                soloNumeros = soloNumeros.substring(2);
            }

            // 3. Limitar estrictamente a los primeros 9 dígitos
            return soloNumeros.substring(0, 9);
        }

        // Evento al Pegar (Ctrl + V o menú contextual)
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            this.value = limpiarNumeroPeratado(pastedText);
        });

        // Evento al escribir o modificar manualmente
        phoneInput.addEventListener('input', function() {
            this.value = limpiarNumeroPeratado(this.value);
        });
    }
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .transition-row-normal { transition: opacity 0.15s ease; }
    .transition-row-normal:hover { opacity: 0.85; }

    input.form-control, select.form-select {
        border: 1px solid #cbd5e1;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    input.form-control:focus, select.form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection