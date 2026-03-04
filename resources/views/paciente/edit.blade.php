@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Paciente: {{ $paciente->nombre }} {{ $paciente->apellido }}</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('pacientes.update', $paciente->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Información de Identidad</h6>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">DNI</label>
                        <input type="text" name="dni" class="form-control" value="{{ $paciente->dni }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Género</label>
                        <select name="genero" class="form-select" required>
                            <option value="Masculino" {{ $paciente->genero == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ $paciente->genero == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Otros" {{ $paciente->genero == 'Otros' ? 'selected' : '' }}>Otros</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $paciente->fecha_nacimiento }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">País de Nacimiento</label>
                        <input type="text" name="pais_nacimiento" class="form-control" value="{{ $paciente->pais_nacimiento }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $paciente->nombre }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellido" class="form-control" value="{{ $paciente->apellido }}" required>
                    </div>
                </div>

                <h6 class="text-muted text-uppercase small fw-bold mb-3">Contacto y Ubicación</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Celular Personal</label>
                        <input type="text" name="celular_personal" class="form-control" value="{{ $paciente->celular_personal }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="{{ $paciente->correo }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Distrito</label>
                        <input type="text" name="distrito" class="form-control" value="{{ $paciente->distrito }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="{{ $paciente->direccion }}" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">Actualizar Datos</button>
                    <a href="{{ route('pacientes.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection