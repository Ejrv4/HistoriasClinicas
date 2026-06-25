<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Médico - @yield('title', 'Inicio')</title>
    
    {{-- Tipografía y Estilos Base --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- 🚨 BLOQUE CRÍTICO: Evita el parpadeo blanco leyendo el tema antes de renderizar --}}
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const preferDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && preferDark)) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bs-body-bg); 
            color: var(--bs-body-color);
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* ── SIDEBAR FLOTANTE MODERNO ─────────────────────────────────── */
        .sidebar {
            height: calc(100vh - 2rem);
            width: 280px;
            position: fixed;
            top: 1rem;
            left: 1rem;
            background: var(--bs-card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--bs-border-color-translucent);
            padding: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header { 
            padding: 1.75rem 1.5rem; 
            border-bottom: 1px solid var(--bs-border-color-translucent); 
        }

        .sidebar-header h4 {
            font-size: 1.15rem;
            letter-spacing: -0.5px;
        }

        .sidebar-scroll-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        /* Contenedor de configuración fijo al fondo */
        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid var(--bs-border-color-translucent);
            background-color: var(--bs-card-cap-bg);
            border-radius: 0 0 16px 16px;
        }

        .nav-link {
            padding: 0.75rem 1rem;
            color: var(--bs-secondary-color);
            font-weight: 600;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            border-radius: 10px;
            margin-bottom: 0.25rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 46px;
        }

        .nav-link i { 
            font-size: 1.25rem; 
            margin-right: 12px; 
            flex-shrink: 0;
            transition: color 0.2s;
        }
        
        .nav-link:hover { 
            background-color: var(--bs-light-bg-subtle); 
            color: var(--bs-link-hover-color); 
            transform: translateX(4px);
        }
        .nav-link:hover i {
            color: var(--bs-link-color);
        }

        /* Pestaña seleccionada (Modo Claro - Azul degradado original) */
        .nav-link.active {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: #ffffff !important;
            box-shadow: 0 8px 16px -4px rgba(78, 115, 223, 0.3);
        }
        .nav-link.active i {
            color: #ffffff !important;
        }
        .nav-link.active:hover {
            transform: none;
        }

        .section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--bs-secondary-color);
            font-weight: 800;
            padding: 1.25rem 1rem 0.5rem;
            opacity: 0.7;
        }

        /* ── CONTENIDO PRINCIPAL AJUSTADO ────────────────────────────── */
        .main-content { 
            margin-left: 312px;
            min-height: 100vh;
            padding-right: 1rem;
            margin-top: 1.5rem; 
        }

        /* ── PRESERVACIÓN DEL MOTOR DE ANIMACIÓN ORIGINAL ───────────── */
        .link-label-wrapper {
            position: relative;
            height: 22px;
            overflow: hidden;
            flex: 1;
        }

        .link-text-base,
        .link-text-alert {
            position: absolute;
            left: 0;
            top: 0;
            white-space: nowrap;
            line-height: 22px;
            width: 100%;
        }

        .link-text-base  { opacity: 1; }
        .link-text-alert { opacity: 0; }
        .alert-dot-small { opacity: 0; }

        .citas-a   { animation: navFadeA 7s 1s infinite ease-in-out; }
        .citas-b   { animation: navFadeB 7s 1s infinite ease-in-out; }
        .citas-dot { animation: dotAppear 7s 1s infinite ease-in-out; }

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

        .alert-dot-small {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .dot-warning { background: #ffb020; box-shadow: 0 0 0 3px rgba(255, 176, 32, 0.25); }
        .dot-danger  { background: #fa5252; box-shadow: 0 0 0 3px rgba(250, 82, 82, 0.25); }

        @keyframes dotAppear {
            0%, 40%  { opacity: 0; transform: scale(0.4); }
            48%, 92% { opacity: 1; transform: scale(1); }
            100%     { opacity: 0; transform: scale(0.4); }
        }

        .nav-link:not(.active) .text-alert-warning { color: var(--bs-warning-text-emphasis); font-weight: 700; }
        .nav-link:not(.active) .text-alert-danger  { color: var(--bs-danger-text-emphasis); font-weight: 700; }
        .nav-link.active .link-text-alert { color: inherit; font-weight: 700; }

        @media (max-width: 991.98px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 0 1rem; margin-top: 1rem; }
        }

        /* ==========================================================================
           🌙 AJUSTES EXCLUSIVOS E INYECCIÓN DE COLORES - MODO OSCURO
           ========================================================================== */
        [data-bs-theme="dark"] {
            /* Forzar que el texto base del body e hijos sea blanco puro */
            --bs-body-color: #ffffff;
            --bs-body-bg: #0f172a; /* Fondo pizarra muy oscuro profesional */
        }

        [data-bs-theme="dark"] .sidebar {
            background: #1e293b; /* Sidebar un tono más claro que el fondo general */
        }

        [data-bs-theme="dark"] .sidebar-footer {
            background-color: #0f172a;
        }

        /* 🔄 Cambio solicitado: Pestaña activa pasa a Azul Marino Oscuro en Dark Mode */
        [data-bs-theme="dark"] .nav-link.active {
            background: #1e3a8a !important; /* Azul marino oscuro premium (Tailwind blue-900) */
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.5); /* Sombra atenuada */
            border: 1px solid rgba(59, 130, 246, 0.3); /* Sutileza de borde para resaltar */
        }

        [data-bs-theme="dark"] .nav-link:not(.active) {
            color: #94a3b8; /* Gris intermedio para los links inactivos */
        }

        [data-bs-theme="dark"] .nav-link:not(.active):hover {
            background-color: #334155;
            color: #ffffff;
        }
    </style>
</head>
<body>

    {{-- SIDEBAR FLOTANTE --}}
    <div class="sidebar shadow-sm">
        <div class="sidebar-header text-center">
            <h4 class="text-primary fw-bold mb-0 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-heart-pulse-fill text-danger fs-4"></i> 
                <span>SISTEMA MÉDICO</span>
            </h4>
        </div>

        <div class="sidebar-scroll-container">
            <div class="nav flex-column">

                {{-- CALENDARIO DE CITAS --}}
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div class="d-flex align-items-center" style="flex:1; overflow:hidden; min-width:0;">
                        <i class="bi bi-grid-1x2-fill"></i>

                        @if(isset($citasPendientesHoyCount) && $citasPendientesHoyCount > 0)
                            <div class="link-label-wrapper">
                                <span class="link-text-base citas-a">Calendario de Citas</span>
                                <span class="link-text-alert citas-b text-alert-warning">
                                    {{ $citasPendientesHoyCount === 1 ? '1 Cita Pendiente' : $citasPendientesHoyCount . ' Citas Pendientes' }}
                                </span>
                            </div>
                        @else
                            <span class="text-truncate">Calendario de Citas</span>
                        @endif
                    </div>

                    @if(isset($citasPendientesHoyCount) && $citasPendientesHoyCount > 0)
                        <span class="alert-dot-small dot-warning citas-dot"></span>
                    @endif
                </a>

                <div class="section-title">Módulos de Gestión</div>

                {{-- HISTORIAS CLÍNICAS --}}
                <a class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('pacientes.*') ? 'active' : '' }}" href="{{ route('pacientes.index') }}">
                    <div class="d-flex align-items-center" style="flex:1; overflow:hidden; min-width:0;">
                        <i class="bi bi-folder-fill"></i>

                        @if(isset($pacientesIncompletosCount) && $pacientesIncompletosCount > 0)
                            <div class="link-label-wrapper">
                                <span class="link-text-base hist-a">Historias Clínicas</span>
                                <span class="link-text-alert hist-b text-alert-danger">
                                    {{ $pacientesIncompletosCount === 1 ? '1 Ficha Incompleta' : $pacientesIncompletosCount . ' Fichas Incompletas' }}
                                </span>
                            </div>
                        @else
                            <span class="text-truncate">Historias Clínicas</span>
                        @endif
                    </div>

                    @if(isset($pacientesIncompletosCount) && $pacientesIncompletosCount > 0)
                        <span class="alert-dot-small dot-danger hist-dot"></span>
                    @endif
                </a>

                {{-- MEDICAMENTOS --}}
                <a class="nav-link {{ request()->routeIs('medicamentos.*') ? 'active' : '' }}" href="{{ route('medicamentos.index') }}">
                    <i class="bi bi-capsule"></i>
                    <span class="text-truncate">Gestión de Medicamentos</span>
                </a>

                {{-- CIE-10 --}}
                <a class="nav-link {{ request()->routeIs('cie10.*') ? 'active' : '' }}" href="{{ route('cie10.index') }}">
                    <i class="bi bi-shield-plus"></i>
                    <span class="text-truncate">Catálogo CIE-10</span>
                </a>

            </div>
        </div>

        {{-- SECCIÓN INFERIOR DE CONFIGURACIÓN --}}
        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 text-secondary fw-bold" style="font-size: 0.82rem;">
                    <i class="bi bi-gear-fill text-primary fs-5"></i>
                    <span class="font-monospace uppercase tracking-wider">Modo Oscuro</span>
                </div>
                <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="darkModeToggle" style="width: 2.5em; height: 1.25em; margin-left: 0;">
                </div>
            </div>
        </div>
    </div>

    {{-- VIEWPORT PRINCIPAL --}}
    <div class="main-content">
        <div class="container-fluid px-2">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('darkModeToggle');
            const htmlEl = document.documentElement;

            // Sincronizar switch con el tema del sistema cargado
            if (htmlEl.getAttribute('data-bs-theme') === 'dark') {
                toggle.checked = true;
            }

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    htmlEl.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    htmlEl.setAttribute('data-bs-theme', 'light');
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>
</body>
</html>