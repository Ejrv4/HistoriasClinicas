<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Médico - @yield('title', 'Inicio')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        
        /* Sidebar mejorada */
        .sidebar { 
            height: 100vh; 
            width: 260px; 
            position: fixed; 
            background: #ffffff;
            border-right: 1px solid #e3e6f0; 
            padding: 0;
            z-index: 1000;
        }
        
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #f8f9fa; }
        
        .nav-link { 
            padding: 0.8rem 1.5rem; 
            color: #4e73df; 
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }
        
        .nav-link:hover { background-color: #f8f9fc; color: #224abe; }
        
        .nav-link.active { 
            background-color: #4e73df; 
            color: white !important; 
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);
        }

        .section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #b7b9cc;
            font-weight: 700;
            padding: 1.5rem 1.5rem 0.5rem;
        }

        /* Contenido Principal */
        .main-content { margin-left: 260px; min-height: 100vh; }
        
        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="sidebar shadow-sm">
        <div class="sidebar-header text-center">
            <h4 class="text-primary fw-bold mb-0"><i class="bi bi-hospital"></i> Sistema Médico</h4>
        </div>
        
        <div class="nav flex-column mt-2">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Gestion de Citas
            </a>
            <div class="section-title">Gestión</div>
            <a class="nav-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}">
                <i class="bi bi-people"></i> Historias Clinicas
            </a>
            <a class="nav-link {{ request()->is('medicamentos*') ? 'active' : '' }}" href="{{ route('medicamentos.index') }}">
                <i class="bi bi-capsule-pill me-2"></i> Gestionar Medicamentos
            </a>
        </div>
    </div>

    <div class="main-content">
        <nav class="topbar">
            <span class="text-gray-600">Sistema de Gestión Médica v1.0</span>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 small fw-medium text-muted">{{ now()->format('l, d F Y') }}</span>
                <div class="vr me-3"></div>
                <i class="bi bi-person-circle fs-4 text-primary"></i>
            </div>
        </nav>

        <div class="container-fluid px-4 pb-5">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>