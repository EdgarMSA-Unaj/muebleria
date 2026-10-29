<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Mueblería Elegance ERP</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-dashboard.css') }}">
    
    @stack('styles')
</head>
<body>

<div class="app-wrapper">
    <!-- SIDEBAR VERTICAL -->
    <aside class="app-sidebar" id="appSidebar">
        <!-- Brand / Logo Header -->
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            @php 
                $appConfig = \App\Models\Configuracion::first(); 
            @endphp
            @if($appConfig && $appConfig->logo && file_exists(public_path('storage/' . $appConfig->logo)))
                <div class="brand-icon-box" style="background: transparent; border: 1px solid rgba(255,255,255,0.1); padding: 2px;">
                    <img src="{{ asset('storage/' . $appConfig->logo) }}" alt="Logo" style="max-height: 36px; max-width: 36px; object-fit: contain; border-radius: 8px;">
                </div>
            @else
                <div class="brand-icon-box">
                    <span class="dot-logo">.D</span>
                </div>
            @endif
            <div class="brand-text">
                <h5>{{ $appConfig->nombre_empresa ?? 'Dashboard' }}</h5>
                <span>Gestión de Mueblería</span>
            </div>
            <!-- Botón de cierre para móviles -->
            <button type="button" class="btn btn-sm d-lg-none text-white-50 ms-auto p-1 border-0" id="sidebarCloseBtn" aria-label="Cerrar Menú">
                <i class="fa-solid fa-xmark fs-5"></i>
            </button>
        </a>

        <!-- Primary Action Button (+ Create Project / Nueva Venta) -->
        @hasanyrole('Administrador|Vendedor')
        <a href="{{ route('ventas.create') }}" class="btn-sidebar-action">
            <i class="fa-solid fa-circle-plus"></i>
            <span>+ Nueva Venta</span>
        </a>
        @endhasanyrole

        <!-- Menu Section 1 -->
        <div class="sidebar-section-title">Menú Principal</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @hasanyrole('Administrador|Almacenero|Vendedor')
            <li class="sidebar-nav-item">
                <a href="{{ route('inventario.index') }}" class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-couch"></i>
                    <span>Inventario Muebles</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('Administrador|Almacenero')
            <li class="sidebar-nav-item">
                <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categorías Líneas</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('produccion.index') }}" class="nav-link {{ request()->routeIs('produccion.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hammer"></i>
                    <span>Taller de Fabricación</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('Administrador|Vendedor')
            <li class="sidebar-nav-item">
                <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Ventas & Pedidos</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('despachos.index') }}" class="nav-link {{ request()->routeIs('despachos.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Despachos & Armado</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Clientes (CRM)</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('Administrador|Almacenero')
            <li class="sidebar-nav-item">
                <a href="{{ route('compras.index') }}" class="nav-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Compras Proveedores</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('Administrador|Almacenero|Contador')
            <li class="sidebar-nav-item">
                <a href="{{ route('proveedores.index') }}" class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck"></i>
                    <span>Proveedores</span>
                </a>
            </li>
            @endhasanyrole

            @hasanyrole('Administrador|Contador')
            <li class="sidebar-nav-item">
                <a href="{{ route('caja.index') }}" class="nav-link {{ request()->routeIs('caja.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Caja & Gastos</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Reportes</span>
                </a>
            </li>
            @endhasanyrole
        </ul>

        @hasrole('Administrador')
        <!-- Menu Section 2: Configuration -->
        <div class="sidebar-section-title">Configuración</div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Usuarios & Roles</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('respaldos.index') }}" class="nav-link {{ request()->routeIs('respaldos.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-database"></i>
                    <span>Copias & Respaldo</span>
                </a>
            </li>
        </ul>
        @endhasrole

        <!-- Theme Toggle Footer (Light Mode / Dark Mode) -->
        <div class="sidebar-footer">
            <div class="theme-toggle-box">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-half-stroke text-warning"></i>
                    <span id="themeLabel">Modo Claro</span>
                </div>
                <div class="form-check form-switch m-0 p-0">
                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="themeSwitch" style="cursor: pointer;">
                </div>
            </div>
        </div>
    </aside>

    <!-- BACKDROP PARA DISPOSITIVOS MÓVILES -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="app-main">
        <!-- TOPBAR HEADER -->
        <header class="app-topbar">
            <!-- Mobile Menu Toggle Button -->
            <button class="btn btn-sm d-lg-none text-dark me-2" id="sidebarToggleBtn">
                <i class="fa-solid fa-bars fs-5"></i>
            </button>

            <!-- Search Bar Pill -->
            <form action="{{ route('inventario.index') }}" method="GET" class="topbar-search m-0">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" placeholder="Buscar en el sistema..." id="globalSearchInput" value="{{ request('search') }}">
            </form>

            <!-- Topbar Right Actions -->
            <div class="topbar-actions">
                <!-- Chat / Message Icon with Dot -->
                <a href="#" class="icon-btn-circle" title="Mensajes">
                    <i class="fa-regular fa-comment-dots"></i>
                    <span class="badge-dot"></span>
                </a>

                <!-- Notification Bell with Dot -->
                <a href="#" class="icon-btn-circle" title="Notificaciones de Stock & Entregas">
                    <i class="fa-regular fa-bell"></i>
                    <span class="badge-dot"></span>
                </a>

                <!-- Settings Cog -->
                <a href="{{ route('configuracion.index') }}" class="icon-btn-circle" title="Ajustes">
                    <i class="fa-solid fa-gear"></i>
                </a>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <div class="user-profile-widget" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle">
                            {{ substr(auth()->user()->name ?? 'JD', 0, 2) }}
                        </div>
                        <div class="user-info-text d-none d-md-block">
                            <span class="user-greet">Hola,</span>
                            <span class="user-name">{{ auth()->user()->name ?? 'Usuario' }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted ms-1" style="font-size: 11px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark">{{ auth()->user()->name ?? 'Usuario' }}</div>
                            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('usuarios.index') }}">
                                <i class="fa-solid fa-user me-2 text-primary"></i> Mi Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('configuracion.index') }}">
                                <i class="fa-solid fa-sliders me-2 text-warning"></i> Preferencias
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="app-content">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="background-color: #e6f9f0; color: #047857;">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="background-color: #e0f2fe; color: #0369a1;">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert" style="background-color: #fee2e2; color: #b91c1c;">
                    <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <span>Por favor corrige los siguientes inconvenientes:</span>
                    </div>
                    <ul class="mb-0 ps-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js 4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<!-- Theme Switcher & Sidebar Toggle JS -->
<script>
    // Dark / Light Mode Switcher
    const themeSwitch = document.getElementById('themeSwitch');
    const themeLabel = document.getElementById('themeLabel');
    const savedTheme = localStorage.getItem('app-theme') || 'light';

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('app-theme', theme);
        if (themeSwitch && themeLabel) {
            themeSwitch.checked = (theme === 'dark');
            themeLabel.textContent = (theme === 'dark') ? 'Modo Oscuro' : 'Modo Claro';
        }
    }

    setTheme(savedTheme);

    if (themeSwitch) {
        themeSwitch.addEventListener('change', function() {
            setTheme(this.checked ? 'dark' : 'light');
        });
    }

    // Sidebar Mobile Toggle & Backdrop
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const appSidebar = document.getElementById('appSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        if (appSidebar) appSidebar.classList.add('show');
        if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
    }

    function closeSidebar() {
        if (appSidebar) appSidebar.classList.remove('show');
        if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (appSidebar && appSidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', closeSidebar);
    }
</script>

@stack('scripts')
</body>
</html>
