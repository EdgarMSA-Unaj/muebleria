@extends('layouts.app')

@section('title', 'Copias de Seguridad y Reseteo')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de Página -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="brand-font mb-1 text-dark">
                <i class="fa-solid fa-server text-primary me-2"></i>Copias de Seguridad & Reseteo del Sistema
            </h3>
            <span class="text-secondary" style="font-size: 13.5px;">
                Gestión de respaldos SQL, empaquetado de archivos multimedia, restauración de integridad y configuración de nueva empresa.
            </span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCrearBackup">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span class="fw-semibold">Crear Respaldo Ahora</span>
            </button>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-left: 4px solid #10b981 !important;">
            <i class="fa-solid fa-circle-check fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-left: 4px solid #ef4444 !important;">
            <i class="fa-solid fa-triangle-exclamation fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="fw-semibold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Por favor verifique los siguientes campos:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjetas de Métricas Rápidas (KPIs) -->
    <div class="row g-3 mb-4">
        <!-- Tarjeta 1: Total Copias -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100 d-flex flex-column justify-content-between p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 13px;">Respaldos Guardados</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(112, 71, 235, 0.12); width: 40px; height: 40px;">
                        <i class="fa-solid fa-box-archive fs-5 text-purple" style="color: #7047eb;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="brand-font mb-1 text-dark">{{ count($backups) }}</h3>
                    <span class="text-muted" style="font-size: 12px;">Archivos en servidor</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Espacio Ocupado -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100 d-flex flex-column justify-content-between p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 13px;">Espacio en Disco</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(59, 130, 246, 0.12); width: 40px; height: 40px;">
                        <i class="fa-solid fa-hard-drive fs-5 text-primary"></i>
                    </div>
                </div>
                <div>
                    <h3 class="brand-font mb-1 text-dark">{{ $totalSizeFormatted }}</h3>
                    <span class="text-muted" style="font-size: 12px;">Total almacenamiento copias</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Tamaño Base de Datos -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100 d-flex flex-column justify-content-between p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 13px;">Base de Datos Actual</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.12); width: 40px; height: 40px;">
                        <i class="fa-solid fa-database fs-5 text-success"></i>
                    </div>
                </div>
                <div>
                    <h3 class="brand-font mb-1 text-dark">{{ $dbSizeFormatted }}</h3>
                    <span class="text-muted" style="font-size: 12px;">{{ $totalTables }} tablas relacionales</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Último Respaldo -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100 d-flex flex-column justify-content-between p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 13px;">Último Respaldo</span>
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(255, 122, 24, 0.12); width: 40px; height: 40px;">
                        <i class="fa-solid fa-clock-rotate-left fs-5" style="color: var(--primary-orange);"></i>
                    </div>
                </div>
                <div>
                    <h5 class="brand-font mb-1 text-dark" style="font-size: 16px;">
                        {{ $lastBackup ? $lastBackup['created_at_human'] : 'Ninguno' }}
                    </h5>
                    <span class="text-muted" style="font-size: 12px;">
                        {{ $lastBackup ? date('d/m/Y H:i', strtotime($lastBackup['created_at'])) : 'No hay copias aún' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas de Navegación del Módulo -->
    <div class="stat-card p-0 overflow-hidden mb-4 shadow-sm">
        <div class="p-3 border-bottom bg-light bg-opacity-50">
            <ul class="nav nav-pills gap-2" id="backupTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="backups-tab" data-bs-toggle="pill" data-bs-target="#tab-backups" type="button" role="tab">
                        <i class="fa-solid fa-folder-open"></i>
                        <span>Copias de Seguridad ({{ count($backups) }})</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 fw-semibold d-flex align-items-center gap-2" id="restore-tab" data-bs-toggle="pill" data-bs-target="#tab-restore" type="button" role="tab">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Restaurar Sistema</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 fw-semibold d-flex align-items-center gap-2 text-danger" id="reset-tab" data-bs-toggle="pill" data-bs-target="#tab-reset" type="button" role="tab">
                        <i class="fa-solid fa-building-flag"></i>
                        <span>Inicializar Nueva Empresa</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-4" id="backupTabsContent">
            <!-- TAB 1: LISTADO DE RESPALDOS -->
            <div class="tab-pane fade show active" id="tab-backups" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h5 class="brand-font mb-1 text-dark">Historial de Copias de Seguridad</h5>
                        <p class="text-secondary mb-0" style="font-size: 13px;">Archivos almacenados localmente en el servidor (`storage/app/backups`).</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalCrearBackup">
                            <i class="fa-solid fa-plus me-1"></i> Nueva Copia
                        </button>
                    </div>
                </div>

                @if(count($backups) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <th class="ps-3">Nombre del Respaldo</th>
                                    <th>Tipo de Contenido</th>
                                    <th>Tamaño</th>
                                    <th>Fecha de Creación</th>
                                    <th class="text-end pe-3">Acciones Rápidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($backup['extension'] === 'zip')
                                                    <div class="rounded-2 p-2 text-warning" style="background: rgba(245, 158, 11, 0.12);">
                                                        <i class="fa-solid fa-file-zipper fs-5"></i>
                                                    </div>
                                                @else
                                                    <div class="rounded-2 p-2 text-primary" style="background: rgba(59, 130, 246, 0.12);">
                                                        <i class="fa-solid fa-file-code fs-5"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark font-monospace" style="font-size: 13.5px;">
                                                        {{ $backup['filename'] }}
                                                    </div>
                                                    <span class="text-muted" style="font-size: 11.5px;">
                                                        @if(str_contains($backup['filename'], 'preventivo'))
                                                            <span class="badge bg-secondary bg-opacity-75">Respaldo Automático Preventivo</span>
                                                        @else
                                                            Respaldo Manual Programado
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($backup['extension'] === 'zip')
                                                <span class="badge bg-warning bg-opacity-25 text-dark fw-semibold px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-box-open me-1 text-warning"></i> Completo (BD + Fotos)
                                                </span>
                                            @else
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-database me-1"></i> Base de Datos SQL
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-secondary" style="font-size: 13px;">
                                            {{ $backup['size_formatted'] }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark" style="font-size: 13px;">
                                                {{ date('d/m/Y H:i:s', strtotime($backup['created_at'])) }}
                                            </div>
                                            <span class="text-muted" style="font-size: 11.5px;">{{ $backup['created_at_human'] }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Descargar -->
                                                <a href="{{ route('respaldos.descargar', $backup['filename']) }}" class="btn btn-light border text-primary" title="Descargar archivo">
                                                    <i class="fa-solid fa-download me-1"></i> Descargar
                                                </a>
                                                <!-- Restaurar desde este archivo -->
                                                <button type="button" class="btn btn-light border text-success" onclick="abrirModalRestaurarServidor('{{ $backup['filename'] }}')" title="Restaurar este punto">
                                                    <i class="fa-solid fa-rotate-left me-1"></i> Restaurar
                                                </button>
                                                <!-- Eliminar -->
                                                <button type="button" class="btn btn-light border text-danger" onclick="abrirModalEliminar('{{ $backup['filename'] }}')" title="Eliminar copia">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="rounded-circle mx-auto p-4 mb-3 d-inline-flex align-items-center justify-content-center" style="background: rgba(112, 71, 235, 0.08); width: 80px; height: 80px;">
                            <i class="fa-solid fa-box-archive fs-1 text-primary"></i>
                        </div>
                        <h5 class="brand-font text-dark mb-1">Aún no se han generado copias de seguridad</h5>
                        <p class="text-muted mx-auto mb-3" style="max-width: 420px; font-size: 13.5px;">
                            Es una buena práctica mantener respaldos periódicos de la base de datos y archivos multimedia para proteger la información de su mueblería.
                        </p>
                        <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearBackup">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Crear Primera Copia de Seguridad
                        </button>
                    </div>
                @endif
            </div>

            <!-- TAB 2: RESTAURAR COPIA -->
            <div class="tab-pane fade" id="tab-restore" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="p-4 rounded-3 border" style="background: #ffffff;">
                            <h5 class="brand-font mb-2 text-dark">
                                <i class="fa-solid fa-file-import text-primary me-2"></i>Restaurar desde Archivo Externo
                            </h5>
                            <p class="text-secondary mb-4" style="font-size: 13.5px;">
                                Cargue un archivo de respaldo generado anteriormente (formato <code>.sql</code> o <code>.zip</code>). El sistema reemplazará los datos vigentes por los contenidos en dicho archivo.
                            </p>

                            <form action="{{ route('respaldos.restaurar') }}" method="POST" enctype="multipart/form-data" id="formRestaurarArchivo">
                                @csrf
                                <input type="hidden" name="fuente" value="archivo">

                                <!-- Zona Dropzone / Selector de archivo -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Seleccionar archivo (.sql o .zip)</label>
                                    <div class="border-2 border-dashed rounded-3 p-4 text-center" style="border-color: #cbd5e1; background: #f8fafc; cursor: pointer;" onclick="document.getElementById('inputBackupFile').click()">
                                        <i class="fa-solid fa-cloud-arrow-up fs-2 text-secondary mb-2"></i>
                                        <h6 class="fw-bold mb-1 text-dark">Haga clic aquí para seleccionar el archivo</h6>
                                        <span class="text-muted" style="font-size: 12px;">Archivos admitidos: .sql (Base de datos) o .zip (Completo). Máx. 100MB</span>
                                        <input type="file" name="backup_file" id="inputBackupFile" class="d-none" accept=".sql,.zip" required onchange="mostrarNombreArchivo(this)">
                                        <div id="fileSelectedBadge" class="mt-3 d-none">
                                            <span class="badge bg-primary px-3 py-2 fs-6 fw-normal">
                                                <i class="fa-solid fa-paperclip me-1"></i> <span id="fileNameSpan"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contraseña de Autorización -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-danger">
                                        <i class="fa-solid fa-key me-1"></i> Ingrese su Contraseña de Administrador para confirmar
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••••••" required>
                                    <div class="form-text text-muted">Esta verificación es obligatoria para prevenir restauraciones no autorizadas.</div>
                                </div>

                                <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold shadow-sm w-100" onclick="return confirm('¿Está completamente seguro de restaurar el sistema? Toda la información actual será sobrescrita por el respaldo cargado.')">
                                    <i class="fa-solid fa-rotate-left me-2"></i> Ejecutar Restauración Ahora
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Panel Informativo Lateral -->
                    <div class="col-lg-5">
                        <div class="p-4 rounded-3 border h-100" style="background: rgba(255, 122, 24, 0.04); border-color: rgba(255, 122, 24, 0.2) !important;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fa-solid fa-shield-halved fs-4" style="color: var(--primary-orange);"></i>
                                <h6 class="brand-font mb-0 text-dark">Medidas de Integridad del Proceso</h6>
                            </div>

                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0" style="font-size: 13px; color: var(--text-secondary);">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-check text-success mt-1"></i>
                                    <span><strong>Desactivación de Claves Foráneas:</strong> Se inhabilita temporalmente el chequeo de foreign keys para evitar bloqueos por dependencias circulares.</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-check text-success mt-1"></i>
                                    <span><strong>Descompresión Multimedia:</strong> Al subir un archivo <code>.zip</code>, las imágenes de productos y logotipos se restauran en <code>storage/app/public</code> automáticamente.</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-check text-success mt-1"></i>
                                    <span><strong>Limpieza de Caché:</strong> Las vistas compiladas y cachés del sistema se limpian al finalizar para que los cambios tomen efecto inmediato.</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-triangle-exclamation text-warning mt-1"></i>
                                    <span><strong>Acción No Reversible:</strong> Recomendamos crear un respaldo fresco antes de ejecutar una restauración si desea conservar el estado presente.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: RESETEAR SISTEMA / NUEVA EMPRESA -->
            <div class="tab-pane fade" id="tab-reset" role="tabpanel">
                <div class="border border-danger border-opacity-25 rounded-3 p-4 mb-4" style="background: rgba(239, 68, 68, 0.03);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-3 p-2 bg-danger text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                        </div>
                        <div>
                            <h5 class="brand-font text-danger mb-0">Zona Crítica: Reinicio y Configuración de Nueva Empresa</h5>
                            <span class="text-muted" style="font-size: 13px;">Seleccione la modalidad de limpieza y configure los nuevos parámetros del negocio.</span>
                        </div>
                    </div>

                    <form action="{{ route('respaldos.resetear') }}" method="POST" enctype="multipart/form-data" id="formResetSistema">
                        @csrf

                        <!-- Selección de Modo de Reseteo -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card h-100 border p-3 rounded-3 cursor-pointer option-reset-card active" id="cardModoTransaccional" onclick="seleccionarModoReset('transaccional')">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="modo" id="modoTransaccional" value="transaccional" checked onchange="toggleFormEmpresa(this.value)">
                                        <label class="form-check-label fw-bold text-dark fs-6" for="modoTransaccional">
                                            1. Limpieza Transaccional (Reinicio de Operaciones)
                                        </label>
                                    </div>
                                    <p class="text-secondary mb-0 ps-4" style="font-size: 12.8px;">
                                        Ideal para iniciar un nuevo año contable o período fiscal. 
                                        <strong>Elimina:</strong> Ventas, compras, despachos, órdenes de fabricación, kardex, sesiones de caja y gastos.
                                        <br>
                                        <span class="text-success"><i class="fa-solid fa-check me-1"></i> Mantiene catálogo de muebles, categorías, clientes, proveedores y configuración. Pone los stocks en 0.</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border p-3 rounded-3 cursor-pointer option-reset-card" id="cardModoNuevaEmpresa" onclick="seleccionarModoReset('nueva_empresa')">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="modo" id="modoNuevaEmpresa" value="nueva_empresa" onchange="toggleFormEmpresa(this.value)">
                                        <label class="form-check-label fw-bold text-danger fs-6" for="modoNuevaEmpresa">
                                            2. Reseteo de Fábrica para Nueva Empresa (Total)
                                        </label>
                                    </div>
                                    <p class="text-secondary mb-0 ps-4" style="font-size: 12.8px;">
                                        Inicialización completa desde cero para implementar el ERP en una <strong>nueva empresa o cliente</strong>.
                                        <strong>Elimina:</strong> Todo el catálogo de muebles, categorías, clientes, proveedores y operaciones transaccionales.
                                        <br>
                                        <span class="text-primary"><i class="fa-solid fa-arrows-rotate me-1"></i> Reinicia series de comprobantes (B001, F001) y aplica los nuevos datos comerciales.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Parámetros de la Nueva Empresa (visible solo si modo = nueva_empresa) -->
                        <div id="seccionNuevaEmpresa" class="d-none p-4 rounded-3 border mb-4 shadow-sm" style="background: #ffffff;">
                            <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                <i class="fa-solid fa-building text-primary fs-5"></i>
                                <h5 class="brand-font mb-0 text-dark">Datos de la Nueva Empresa</h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Razón Comercial / Nombre de Empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre_empresa" class="form-control" value="{{ old('nombre_empresa', $config->nombre_empresa) }}" placeholder="Ej. Mueblería Central SAC">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">RUC / NIT <span class="text-danger">*</span></label>
                                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $config->ruc) }}" placeholder="Ej. 20601234567">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Dirección Comercial Principal <span class="text-danger">*</span></label>
                                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $config->direccion) }}" placeholder="Ej. Av. Principal 1234, Lima">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Teléfono de Contacto <span class="text-danger">*</span></label>
                                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $config->telefono) }}" placeholder="Ej. 987654321">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $config->email) }}" placeholder="contacto@nuevaempresa.pe">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Sitio Web / Catálogo</label>
                                    <input type="text" name="sitio_web" class="form-control" value="{{ old('sitio_web', $config->sitio_web) }}" placeholder="https://nuevaempresa.pe">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Símbolo Moneda <span class="text-danger">*</span></label>
                                    <input type="text" name="moneda_simbolo" class="form-control" value="{{ old('moneda_simbolo', 'S/.') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">% Impuesto IGV <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="porcentaje_igv" class="form-control" value="{{ old('porcentaje_igv', 18) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Serie Boleta <span class="text-danger">*</span></label>
                                    <input type="text" name="serie_boleta" class="form-control" value="{{ old('serie_boleta', 'B001') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Serie Factura <span class="text-danger">*</span></label>
                                    <input type="text" name="serie_factura" class="form-control" value="{{ old('serie_factura', 'F001') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Serie Nota Venta</label>
                                    <input type="text" name="serie_nota_venta" class="form-control" value="{{ old('serie_nota_venta', 'NV01') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Serie Cotización</label>
                                    <input type="text" name="serie_cotizacion" class="form-control" value="{{ old('serie_cotizacion', 'COT1') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Logotipo Oficial</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <!-- Salvaguardas de Seguridad Obligatorias -->
                        <div class="card p-4 border-danger border-opacity-50 rounded-3 mb-3" style="background: rgba(239, 68, 68, 0.02);">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-2 text-success fw-semibold mb-2" style="font-size: 13.5px;">
                                        <i class="fa-solid fa-shield-heart fs-5"></i>
                                        <span>Protección Preventiva Activa: Se creará un respaldo sellado automático antes de aplicar los cambios.</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-danger">
                                        1. Escriba la palabra <span class="badge bg-danger">RESETEAR</span> para confirmar:
                                    </label>
                                    <input type="text" name="confirm_phrase" class="form-control form-control-lg border-danger" placeholder="RESETEAR" required autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-danger">
                                        2. Contraseña del Administrador actual:
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-lg border-danger" placeholder="••••••••••••" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger btn-lg px-4 shadow-sm fw-bold d-flex align-items-center gap-2" onclick="return confirm('¿Confirma que desea ejecutar esta acción destructiva? Se generará un respaldo preventivo de seguridad.')">
                                <i class="fa-solid fa-power-off"></i>
                                <span>Ejecutar Proceso de Reseteo</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CREAR NUEVA COPIA DE SEGURIDAD -->
<div class="modal fade" id="modalCrearBackup" tabindex="-1" aria-labelledby="modalCrearBackupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title brand-font text-dark" id="modalCrearBackupLabel">
                    <i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i>Generar Nueva Copia de Seguridad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('respaldos.crear') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-secondary mb-3" style="font-size: 13.5px;">
                        Seleccione el formato del archivo de respaldo que desea generar:
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <!-- Opción SQL -->
                        <label class="card border p-3 rounded-3 cursor-pointer d-flex flex-row align-items-center gap-3">
                            <input type="radio" name="tipo" value="sql" class="form-check-input mt-0" checked>
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-primary" style="background: rgba(59, 130, 246, 0.1); width: 44px; height: 44px;">
                                <i class="fa-solid fa-database fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-0">Base de Datos SQL (.sql)</div>
                                <span class="text-muted" style="font-size: 12px;">Rápido y ligero. Contiene la estructura y datos de todas las tablas.</span>
                            </div>
                        </label>

                        <!-- Opción ZIP -->
                        <label class="card border p-3 rounded-3 cursor-pointer d-flex flex-row align-items-center gap-3">
                            <input type="radio" name="tipo" value="zip" class="form-check-input mt-0">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-warning" style="background: rgba(245, 158, 11, 0.1); width: 44px; height: 44px;">
                                <i class="fa-solid fa-file-zipper fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-0">Respaldo Completo con Multimedia (.zip)</div>
                                <span class="text-muted" style="font-size: 12px;">Incluye el dump SQL más fotos de productos y logos corporativos.</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="fa-solid fa-gear me-1"></i> Iniciar Respaldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: RESTAURAR COPIA DEL SERVIDOR -->
<div class="modal fade" id="modalRestaurarServidor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title brand-font text-danger">
                    <i class="fa-solid fa-rotate-left me-2"></i>Restaurar Punto de Respaldo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('respaldos.restaurar') }}" method="POST">
                @csrf
                <input type="hidden" name="fuente" value="servidor">
                <input type="hidden" name="existing_filename" id="modalRestoreFilename">

                <div class="modal-body py-4">
                    <div class="alert alert-warning border-0 d-flex gap-2 align-items-start mb-3" style="font-size: 13px;">
                        <i class="fa-solid fa-triangle-exclamation fs-5 text-warning mt-1"></i>
                        <div>
                            Se sobrescribirán las tablas de la base de datos con los datos de este respaldo.
                            <div class="fw-bold mt-1" id="modalRestoreFilenameDisplay"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-danger">Ingrese su Contraseña de Administrador</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••••••" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold">
                        Confirmar y Restaurar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: ELIMINAR COPIA -->
<div class="modal fade" id="modalEliminarCopia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-3">
            <div class="modal-body py-3">
                <div class="rounded-circle mx-auto p-3 mb-3 d-inline-flex align-items-center justify-content-center text-danger" style="background: rgba(239, 68, 68, 0.1); width: 60px; height: 60px;">
                    <i class="fa-solid fa-trash-can fs-3"></i>
                </div>
                <h5 class="brand-font text-dark mb-1">¿Eliminar Respaldo?</h5>
                <p class="text-secondary mb-3" style="font-size: 12.5px;" id="modalDeleteFilenameDisplay"></p>

                <form id="formEliminarCopia" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-sm px-3 fw-semibold">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.cursor-pointer {
    cursor: pointer;
}
.option-reset-card {
    transition: all 0.2s ease-in-out;
    border-width: 2px !important;
}
.option-reset-card:hover {
    border-color: #cbd5e1 !important;
    background-color: #fafafa;
}
.option-reset-card.active {
    border-color: var(--primary-orange) !important;
    background-color: rgba(255, 122, 24, 0.02);
}
</style>
@endpush

@push('scripts')
<script>
function mostrarNombreArchivo(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameSpan').textContent = input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
        document.getElementById('fileSelectedBadge').classList.remove('d-none');
    }
}

function abrirModalRestaurarServidor(filename) {
    document.getElementById('modalRestoreFilename').value = filename;
    document.getElementById('modalRestoreFilenameDisplay').textContent = filename;
    const modal = new bootstrap.Modal(document.getElementById('modalRestaurarServidor'));
    modal.show();
}

function abrirModalEliminar(filename) {
    document.getElementById('modalDeleteFilenameDisplay').textContent = filename;
    document.getElementById('formEliminarCopia').action = "{{ url('/respaldos') }}/" + encodeURIComponent(filename);
    const modal = new bootstrap.Modal(document.getElementById('modalEliminarCopia'));
    modal.show();
}

function seleccionarModoReset(modo) {
    if (modo === 'transaccional') {
        document.getElementById('modoTransaccional').checked = true;
        document.getElementById('cardModoTransaccional').classList.add('active');
        document.getElementById('cardModoNuevaEmpresa').classList.remove('active');
        document.getElementById('seccionNuevaEmpresa').classList.add('d-none');
    } else {
        document.getElementById('modoNuevaEmpresa').checked = true;
        document.getElementById('cardModoNuevaEmpresa').classList.add('active');
        document.getElementById('cardModoTransaccional').classList.remove('active');
        document.getElementById('seccionNuevaEmpresa').classList.remove('d-none');
    }
}

function toggleFormEmpresa(valor) {
    seleccionarModoReset(valor);
}
</script>
@endpush
@endsection
