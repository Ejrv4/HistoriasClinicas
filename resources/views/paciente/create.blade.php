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
    <form action="{{ route('pacientes.store') }}" method="POST" class="card shadow-sm border-0 p-4">
        @csrf
        
        <h5 class="text-primary border-bottom pb-2 fw-bold">Datos de Identidad</h5>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label fw-medium">DNI</label>
                <input type="text" name="dni" 
                    class="form-control @error('dni') is-invalid @enderror" 
                    value="{{ old('dni') }}" required>
                @error('dni')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Género</label>
                <select name="genero" class="form-select" required>
                    <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="Otros" {{ old('genero') == 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
            </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Lugar de Nacimiento</label>
                    <input type="text" name="pais_nacimiento" class="form-control" value="{{ old('pais_nacimiento') }}" required>
                </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Nombres</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Apellidos</label>
                <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium text-primary">Ocupación / Trabajo</label>
                <input type="text" name="trabajo" class="form-control" value="{{ old('trabajo') }}" placeholder="Ej: Estudiante, Ingeniero, etc.">
            </div>
        </div>

        <h5 class="text-primary border-bottom pb-2 mt-4 fw-bold">Contacto y Ubicación</h5>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Celular Personal</label>
                <input type="text" name="celular_personal" class="form-control" value="{{ old('celular_personal') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="ejemplo@correo.com">
            </div>
            {{-- SECCIÓN MODIFICADA: Se eliminó Dirección y se ajustó Distrito --}}
            <div class="col-md-4">
                <label class="form-label fw-medium">Distrito</label>
                <input type="text" name="distrito" class="form-control" value="{{ old('distrito') }}" required>
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