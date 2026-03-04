@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Registrar Nuevo Paciente</h2>
    
    <form action="{{ route('pacientes.store') }}" method="POST" class="card shadow-sm border-0 p-4">
        @csrf
        
        <h5 class="text-primary border-bottom pb-2 fw-bold">Datos de Identidad</h5>
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label fw-medium">DNI</label>
                <input type="text" name="dni" 
                    class="form-control @error('dni') is-invalid @enderror" 
                    value="{{ old('dni') }}">
                @error('dni')
                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Género</label>
                <select name="genero" class="form-select" required>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Fecha Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">País de Nacimiento</label>
                <input type="text" name="pais_nacimiento" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-medium">Nombres</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Apellidos</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>
        </div>

        <h5 class="text-primary border-bottom pb-2 mt-4 fw-bold">Contacto y Ubicación</h5>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Celular Personal</label>
                <input type="text" name="celular_personal" class="form-control" required>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-medium">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" placeholder="ejemplo@correo.com">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Distrito</label>
                <input type="text" name="distrito" class="form-control" required>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-medium">Dirección</label>
                <input type="text" name="direccion" class="form-control" required>
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