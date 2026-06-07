@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Registrar Nuevo Paciente</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULARIO BLINDADO CONTRA HISTORIAL NATIVO --}}
    <form action="{{ route('pacientes.store') }}" method="POST" class="card shadow-sm border-0 p-4" autocomplete="off">
        @csrf
        
        <h5 class="text-primary border-bottom pb-2 fw-bold">Datos de Identidad</h5>
        <div class="row mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium">DNI</label>
                <input type="text" name="dni" 
                    class="form-control @error('dni') is-invalid @enderror" 
                    value="{{ old('dni') }}">
                @error('dni')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            
            {{-- IMPLEMENTACIÓN: GÉNERO CON EL NUEVO COMPONENTE --}}
            <div class="col-md-3">
                <x-custom-search-dropdown 
                    label="Género"
                    name="genero"
                    id="paciente_genero_select"
                    placeholder="-- No especificado --"
                    :options="[
                        ['id' => 'Masculino', 'nombre' => 'Masculino'],
                        ['id' => 'Femenino', 'nombre' => 'Femenino'],
                        ['id' => 'Otros', 'nombre' => 'Otros']
                    ]"
                    :selectedValue="old('genero')"
                />
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-medium">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Lugar de Nacimiento</label>
                <input type="text" name="pais_nacimiento" class="form-control" value="{{ old('pais_nacimiento') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Apellidos <span class="text-danger">*</span></label>
                <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Ocupación / Trabajo</label>
                <input type="text" name="trabajo" class="form-control" value="{{ old('trabajo') }}" placeholder="Ej: Estudiante, Ingeniero, etc.">
            </div>
        </div>

        <h5 class="text-primary border-bottom pb-2 mt-4 fw-bold">Contacto y Ubicación</h5>
        <div class="row mb-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-medium">Celular Personal</label>
                <input type="text" name="celular_personal" class="form-control" value="{{ old('celular_personal') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="ejemplo@correo.com">
            </div>
            
            {{-- IMPLEMENTACIÓN: DISTRITO CON EL NUEVO COMPONENTE --}}
            <div class="col-md-4">
                <x-custom-search-dropdown 
                    label="Distrito de Residencia"
                    name="distrito"
                    id="paciente_distrito_select"
                    placeholder="Escriba para buscar..."
                    :options="[
                        ['id' => 'Lima', 'nombre' => 'Lima'],
                        ['id' => 'Ancón', 'nombre' => 'Ancón'],
                        ['id' => 'Ate', 'nombre' => 'Ate'],
                        ['id' => 'Barranco', 'nombre' => 'Barranco'],
                        ['id' => 'Breña', 'nombre' => 'Breña'],
                        ['id' => 'Carabayllo', 'nombre' => 'Carabayllo'],
                        ['id' => 'Chaclacayo', 'nombre' => 'Chaclacayo'],
                        ['id' => 'Chorrillos', 'nombre' => 'Chorrillos'],
                        ['id' => 'Cieneguilla', 'nombre' => 'Cieneguilla'],
                        ['id' => 'Comas', 'nombre' => 'Comas'],
                        ['id' => 'El Agustino', 'nombre' => 'El Agustino'],
                        ['id' => 'Independencia', 'nombre' => 'Independencia'],
                        ['id' => 'Jesús María', 'nombre' => 'Jesús María'],
                        ['id' => 'La Molina', 'nombre' => 'La Molina'],
                        ['id' => 'La Victoria', 'nombre' => 'La Victoria'],
                        ['id' => 'Lince', 'nombre' => 'Lince'],
                        ['id' => 'Los Olivos', 'nombre' => 'Los Olivos'],
                        ['id' => 'Lurigancho-Chosica', 'nombre' => 'Lurigancho-Chosica'],
                        ['id' => 'Lurín', 'nombre' => 'Lurín'],
                        ['id' => 'Magdalena del Mar', 'nombre' => 'Magdalena del Mar'],
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
                        ['id' => 'Villa El Salvador', 'nombre' => 'Villa El Salvador'],
                        ['id' => 'Villa María del Triunfo', 'nombre' => 'Villa María del Triunfo']
                    ]"
                    :selectedValue="old('distrito')"
                    :uppercase="true"
                />
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                Guardar Registro
            </button>

            <button type="submit" name="crear_cita_ahora" value="1" class="btn btn-success px-4 shadow-sm fw-bold">
                <i class="bi bi-calendar-plus me-2"></i>Registrar y Crear Cita Ahora
            </button>

            <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection