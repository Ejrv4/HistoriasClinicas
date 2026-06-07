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

        /* ── SIDEBAR ─────────────────────────────────────────────────── */
        .sidebar {
            height: 100vh;
            width: 290px;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #e3e6f0;
            padding: 0;
            z-index: 1000;
        }

        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #f8f9fa; }

        .nav-link {
            padding: 0.8rem 1.2rem;
            color: #4e73df;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            min-height: 50px;
        }

        .nav-link i { font-size: 1.2rem; margin-right: 12px; flex-shrink: 0; }
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
            padding: 1.5rem 1.2rem 0.5rem;
        }

        /* ── CONTENIDO PRINCIPAL ────────────────────────────────────── */
        .main-content { margin-left: 290px; min-height: 100vh; }

        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 2rem;
        }

        /* ── MOTOR DE ANIMACIÓN: INTERCALA LABEL COMPLETO ───────────── */

        /* Contenedor del texto animado — ocupa el espacio disponible */
        .link-label-wrapper {
            position: relative;
            height: 22px;
            overflow: hidden;
            flex: 1;
        }

        /* Ambos textos se superponen en absolute */
        .link-text-base,
        .link-text-alert {
            position: absolute;
            left: 0;
            top: 0;
            white-space: nowrap;
            line-height: 22px;
            width: 100%;
        }

        /* Estado inicial garantizado antes de que arranque la animación */
        .link-text-base  { opacity: 1; }
        .link-text-alert { opacity: 0; }
        .alert-dot-small { opacity: 0; }

        /* ── CITAS: anima label y dot ───────────────────────────────── */
        .citas-a   { animation: navFadeA 7s 1s infinite ease-in-out; }
        .citas-b   { animation: navFadeB 7s 1s infinite ease-in-out; }
        .citas-dot { animation: dotAppear 7s 1s infinite ease-in-out; }

        /* ── HISTORIAS: offset de 0.4s para que no sincronicen igual ── */
        .hist-a    { animation: navFadeA 7s 1.4s infinite ease-in-out; }
        .hist-b    { animation: navFadeB 7s 1.4s infinite ease-in-out; }
        .hist-dot  { animation: dotAppear 7s 1.4s infinite ease-in-out; }

        @keyframes navFadeA {
            0%, 40%  { opacity: 1; transform: translateY(0); }
            48%, 92% { opacity: 0; transform: translateY(-8px); }
            100%     { opacity: 1; transform: translateY(0); }
        }

        @keyframes navFadeB {
            0%, 40%  { opacity: 0; transform: translateY(8px); }
            48%, 92% { opacity: 1; transform: translateY(0); }
            100%     { opacity: 0; transform: translateY(8px); }
        }

        /* Punto indicador de color — aparece junto al texto de alerta */
        .alert-dot-small {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .dot-warning { background: #ffc107; }
        .dot-danger  { background: #dc3545; }

        @keyframes dotAppear {
            0%, 40%  { opacity: 0; transform: scale(0.4); }
            48%, 92% { opacity: 1; transform: scale(1); }
            100%     { opacity: 0; transform: scale(0.4); }
        }

        /* Color del texto de alerta (fuera del estado active) */
        .nav-link:not(.active) .text-alert-warning { color: #92610a; font-weight: 600; }
        .nav-link:not(.active) .text-alert-danger  { color: #991b1b; font-weight: 600; }

        /* Cuando el link está active, el texto de alerta hereda white */
        .nav-link.active .link-text-alert { color: inherit; font-weight: 600; }
    </style>
</head>
<body>
    <div class="sidebar shadow-sm">
        <div class="sidebar-header text-center">
            <h4 class="text-primary fw-bold mb-0"><i class="bi bi-hospital"></i> Sistema Médico</h4>
        </div>

        <div class="nav flex-column mt-2">

            {{-- ── ENLACE: CALENDARIO DE CITAS ─────────────────────── --}}
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">

                <div class="d-flex align-items-center" style="flex:1; overflow:hidden; min-width:0;">
                    <i class="bi bi-speedometer2"></i>

                    @if(isset($citasPendientesHoyCount) && $citasPendientesHoyCount > 0)
                        {{-- Con alerta: anima entre el nombre y el conteo --}}
                        <div class="link-label-wrapper">
                            <span class="link-text-base citas-a">Calendario de Citas</span>
                            <span class="link-text-alert citas-b text-alert-warning">
                                {{ $citasPendientesHoyCount === 1
                                    ? '1 Atención pendiente'
                                    : $citasPendientesHoyCount . ' Atenciones pendientes' }}
                            </span>
                        </div>
                    @else
                        {{-- Sin alerta: texto estático normal --}}
                        <span class="text-truncate">Calendario de Citas</span>
                    @endif
                </div>

                @if(isset($citasPendientesHoyCount) && $citasPendientesHoyCount > 0)
                    <span class="alert-dot-small dot-warning citas-dot"></span>
                @endif
            </a>

            <div class="section-title">Gestión</div>

            {{-- ── ENLACE: HISTORIAS CLÍNICAS ───────────────────────── --}}
            <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('pacientes.*') ? 'active' : '' }}"
               href="{{ route('pacientes.index') }}">

                <div class="d-flex align-items-center" style="flex:1; overflow:hidden; min-width:0;">
                    <i class="bi bi-people"></i>

                    @if(isset($pacientesIncompletosCount) && $pacientesIncompletosCount > 0)
                        {{-- Con alerta: anima entre el nombre y el conteo --}}
                        <div class="link-label-wrapper">
                            <span class="link-text-base hist-a">Historias Clínicas</span>
                            <span class="link-text-alert hist-b text-alert-danger">
                                {{ $pacientesIncompletosCount === 1
                                    ? '1 Registro pendiente'
                                    : $pacientesIncompletosCount . ' Registros pendientes' }}
                            </span>
                        </div>
                    @else
                        {{-- Sin alerta: texto estático normal --}}
                        <span class="text-truncate">Historias Clínicas</span>
                    @endif
                </div>

                @if(isset($pacientesIncompletosCount) && $pacientesIncompletosCount > 0)
                    <span class="alert-dot-small dot-danger hist-dot"></span>
                @endif
            </a>

            <a class="nav-link {{ request()->routeIs('medicamentos.*') ? 'active' : '' }}"
               href="{{ route('medicamentos.index') }}">
                <i class="bi bi-capsule-pill"></i>
                <span class="text-truncate">Gestión de Medicamentos</span>
            </a>

            <a class="nav-link {{ request()->routeIs('cie10.*') ? 'active' : '' }}"
               href="{{ route('cie10.index') }}">
                <i class="bi bi-clipboard2-pulse"></i>
                <span class="text-truncate">Gestión de CIE-10</span>
            </a>

        </div>
    </div>

    <div class="main-content">
        <nav class="topbar">
            <span class="text-gray-600 fw-medium">Sistema de Gestión Médica</span>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 small fw-medium text-muted text-capitalize">
                    {{ now()->locale('es')->translatedFormat('l, d F Y') }}
                </span>
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