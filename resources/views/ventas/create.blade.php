@extends('layouts.app')

@section('title', 'Nueva Venta / Pedido')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="brand-font mb-1 text-dark">Nueva Venta / Pedido de Muebles</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Punto de Venta (POS), personalización de tapices y despacho</span>
        </div>
        <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-light border px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Ventas
        </a>
    </div>

    <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
        @csrf
        <div class="row g-4">
            <!-- Columna Izquierda: Selección de Cliente y Muebles -->
            <div class="col-lg-8">
                <!-- Tarjeta 1: Cliente & Comprobante -->
                <div class="stat-card mb-4" id="cardCliente" style="position: relative; z-index: 40;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <h5 class="brand-font mb-0 text-dark">1. Datos del Cliente & Comprobante</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBusquedaAvanzada">
                                <i class="fa-solid fa-table-list me-1"></i> Búsqueda Avanzada
                            </button>
                            <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background: var(--primary-orange);" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                <i class="fa-solid fa-user-plus me-1"></i> + Nuevo Cliente
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Buscador Inteligente Reactivo de Cliente -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center" style="font-size: 13px;">
                                <span>Cliente <span class="text-danger">*</span></span>
                                <span id="clienteSearchHint" class="text-muted fw-normal" style="font-size: 11px;">Escribe para buscar...</span>
                            </label>

                            <!-- Caja de Búsqueda Reactiva -->
                            <div id="clienteSearchBox" class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>
                                <input type="text" id="clienteSearchInput" class="form-control border-start-0" 
                                       placeholder="Buscar por Nombre, DNI, RUC, Teléfono o Empresa..." 
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="btnClearSearch" style="display: none;" title="Limpiar búsqueda">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <!-- Lista Desplegable Flotante de Resultados -->
                            <div id="clienteDropdownResults" class="dropdown-menu w-100 shadow-lg p-1 mt-1 border" 
                                 style="display: none; max-height: 290px; overflow-y: auto; z-index: 1060; border-radius: 12px; box-shadow: 0 16px 36px rgba(0,0,0,0.18) !important; background: #ffffff;">
                                <!-- Se llena dinámicamente con JS -->
                            </div>

                            <!-- Tarjeta de Cliente Seleccionado -->
                            <div id="clienteSeleccionadoCard" class="p-2 rounded-3 border align-items-center justify-content-between" 
                                 style="display: none; background: #f0fdf4; border-color: #86efac !important;">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <div class="avatar-circle flex-shrink-0" style="width: 38px; height: 38px; font-size: 14px; background: #16a34a; color: white;">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate" id="txtClienteNombre" style="font-size: 13px;">-</div>
                                        <div class="d-flex flex-wrap gap-2 text-muted align-items-center" style="font-size: 11px;">
                                            <span class="badge bg-white text-dark border" id="txtClienteDoc">-</span>
                                            <span id="txtClienteTel"><i class="fa-solid fa-phone me-1"></i>-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0 ms-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnCambiarCliente" title="Cambiar cliente">
                                        <i class="fa-solid fa-pen me-1"></i> Cambiar
                                    </button>
                                </div>
                            </div>

                            <!-- Input oculto obligatorio para el POST -->
                            <input type="hidden" name="cliente_id" id="selectedClienteId" required value="{{ old('cliente_id') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Tipo Comprobante</label>
                            <select name="tipo_comprobante" id="selectTipoComprobante" class="form-select" required>
                                <option value="boleta">Boleta de Venta</option>
                                <option value="factura">Factura</option>
                                <option value="nota_venta">Nota de Venta</option>
                                <option value="cotizacion">Cotización</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Método de Pago</label>
                            <select name="condicion_pago" class="form-select" required>
                                <option value="contado">Efectivo / Contado</option>
                                <option value="tarjeta">Tarjeta Débito/Crédito</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="yape_plin">Yape / Plin</option>
                                <option value="credito_30d">Crédito 30 Días</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2: Selección de Muebles / Partidas -->
                <div class="stat-card mb-4" id="cardMuebles" style="position: relative; z-index: 30;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h5 class="brand-font mb-0 text-dark">2. Muebles a Comprar</h5>
                            <span class="text-secondary" style="font-size: 12px;">Búsqueda avanzada por nombre, modelo, SKU, material o categoría</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBusquedaMuebles">
                                <i class="fa-solid fa-couch me-1"></i> Catálogo Visual de Muebles
                            </button>
                            <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background: var(--primary-orange);" id="btnAgregarItem">
                                <i class="fa-solid fa-plus me-1"></i> Agregar Otro Mueble
                            </button>
                        </div>
                    </div>

                    <div id="itemsContainer">
                        <!-- Fila 1 de Mueble con Búsqueda Avanzada -->
                        <div class="item-row p-3 rounded-3 mb-3 border position-relative" id="itemRow_0" data-row-index="0" style="background: var(--bg-body); z-index: 1;">
                            <div class="row g-2 align-items-end">
                                <!-- Columna de Búsqueda Avanzada de Mueble -->
                                <div class="col-md-5 position-relative prod-select-col">
                                    <label class="form-label fw-semibold d-flex justify-content-between align-items-center mb-1" style="font-size: 12px;">
                                        <span>Mueble en Catálogo <span class="text-danger">*</span></span>
                                        <span class="prod-search-hint text-muted fw-normal" style="font-size: 11px;">Buscar por modelo, SKU...</span>
                                    </label>

                                    <!-- Caja de Búsqueda de Mueble -->
                                    <div class="input-group input-group-sm prod-search-box">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa-solid fa-couch text-muted" style="font-size: 11px;"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 prod-search-input" 
                                               placeholder="Buscar por Nombre, SKU, Material, Sala..." 
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary btn-clear-prod" type="button" style="display: none;" title="Limpiar">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-open-catalogo-row" type="button" title="Abrir catálogo visual" data-row-index="0">
                                            <i class="fa-solid fa-table-cells"></i>
                                        </button>
                                    </div>

                                    <!-- Dropdown Flotante de Resultados de Muebles -->
                                    <div class="dropdown-menu w-100 shadow-lg p-1 mt-1 border prod-dropdown-results" 
                                         style="display: none; max-height: 290px; overflow-y: auto; z-index: 1060; border-radius: 12px; box-shadow: 0 16px 36px rgba(0,0,0,0.18) !important; background: #ffffff;">
                                        <!-- Se llena dinámicamente con JS -->
                                    </div>

                                    <!-- Tarjeta de Mueble Seleccionado -->
                                    <div class="p-2 rounded-3 border align-items-center justify-content-between prod-selected-card" 
                                         style="display: none; background: #fff8f1; border-color: #ffd8a8 !important;">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <div class="avatar-circle flex-shrink-0" style="width: 34px; height: 34px; font-size: 13px; background: rgba(255, 122, 24, 0.15); color: var(--primary-orange);">
                                                <i class="fa-solid fa-couch"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark text-truncate prod-txt-nombre" style="font-size: 12.5px;">-</div>
                                                <div class="d-flex flex-wrap gap-1 text-muted align-items-center" style="font-size: 11px;">
                                                    <span class="badge bg-white text-dark border prod-txt-sku">-</span>
                                                    <span class="badge bg-success-subtle text-success prod-txt-stock">Stock: -</span>
                                                    <span class="fw-semibold text-primary prod-txt-precio">S/. 0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-shrink-0 ms-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-cambiar-prod" title="Cambiar mueble" style="font-size: 11px;">
                                                <i class="fa-solid fa-pen me-1"></i> Cambiar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Input Oculto Obligatorio para el POST -->
                                    <input type="hidden" name="productos[0][producto_id]" class="producto-id-hidden" required>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">Cantidad</label>
                                    <input type="number" name="productos[0][cantidad]" class="form-control form-control-sm cantidad-input" value="1" min="1" required onchange="calcularTotales()">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">P. Unitario (S/.)</label>
                                    <input type="number" step="0.01" name="productos[0][precio_unitario]" class="form-control form-control-sm precio-input" value="0.00" required onchange="calcularTotales()">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">Subtotal (S/.)</label>
                                    <input type="text" class="form-control form-control-sm subtotal-item bg-light" readonly value="S/. 0.00">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="eliminarFilaMueble(this)" title="Eliminar mueble">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <div class="col-12 mt-2">
                                    <input type="text" name="productos[0][color_acabado]" class="form-control form-control-sm" placeholder="Personalización: Color de tapiz solicitado / Acabado de barniz (Opcional)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 3: Logística de Entrega y Armado -->
                <div class="stat-card" style="position: relative; z-index: 20;">
                    <h5 class="brand-font mb-3 text-dark">3. Logística de Mueblería (Despacho & Armado)</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="requiere_entrega" id="requiereEntrega" value="1" onchange="toggleEntregaFields(this.checked)">
                                <label class="form-check-label fw-bold text-dark" for="requiereEntrega">
                                    <i class="fa-solid fa-truck-fast text-primary me-1"></i> Requiere Envío a Domicilio
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="requiere_armado" id="requiereArmado" value="1">
                                <label class="form-check-label fw-bold text-dark" for="requiereArmado">
                                    <i class="fa-solid fa-wrench text-warning me-1"></i> Incluye Servicio de Armado In-Situ
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6" id="entregaCampos" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 12.5px;">Fecha Programada de Entrega</label>
                                <input type="date" name="fecha_entrega_programada" class="form-control form-control-sm" min="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="form-label fw-semibold" style="font-size: 12.5px;">Dirección de Entrega</label>
                                <input type="text" name="direccion_entrega" class="form-control form-control-sm" placeholder="Av / Calle, Nro, Dpto, Distrito">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 12.5px;">Notas / Observaciones de Fabricación y Acceso</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej. Escaleras estrechas en 3er piso, coordinar ingreso con conserjería..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Resumen de Totales y Confirmación -->
            <div class="col-lg-4">
                <div class="stat-card position-sticky" style="top: 96px;">
                    <h5 class="brand-font mb-3 text-dark">Resumen de Venta</h5>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Subtotal Neto:</span>
                        <strong class="text-dark" id="txtSubtotal">S/. 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">IGV ({{ $config->porcentaje_igv ?? 18 }}%):</span>
                        <strong class="text-dark" id="txtIgv">S/. 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between py-3 mb-4">
                        <span class="fs-5 fw-bold text-dark">Total a Cobrar:</span>
                        <span class="fs-4 fw-bold text-primary" id="txtTotal">S/. 0.00</span>
                    </div>

                    <button type="submit" class="btn w-100 text-white fw-bold py-3 rounded-3 shadow-sm mb-2" style="background: linear-gradient(135deg, #ff7a18 0%, #ff5200 100%);">
                        <i class="fa-solid fa-check-circle me-2"></i> Confirmar & Emitir Pedido
                    </button>

                    <a href="{{ route('ventas.index') }}" class="btn btn-light border w-100 py-2">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Búsqueda Avanzada de Clientes -->
<div class="modal fade" id="modalBusquedaAvanzada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title brand-font mb-0 text-dark">Búsqueda Avanzada de Clientes</h5>
                    <span class="text-secondary" style="font-size: 12px;">Filtre clientes por Nombre, RUC, DNI, Teléfono o Dirección</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text" id="filtroTablaClientes" class="form-control border-start-0" placeholder="Escribe para filtrar clientes en tiempo real (ej. 2055, Morales, Miraflores)...">
                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('filtroTablaClientes').value=''; filtrarTablaClientes();">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 380px;">
                    <table class="table table-hover align-middle mb-0" id="tablaClientesAvanzada" style="font-size: 12.5px;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Cliente / Razón Social</th>
                                <th>Documento</th>
                                <th>Contacto & Dirección</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyClientesAvanzada">
                            @foreach($clientes as $cli)
                                <tr class="cliente-fila" 
                                    data-id="{{ $cli->id }}"
                                    data-nombre="{{ strtolower($cli->nombre_completo) }}"
                                    data-doc="{{ strtolower($cli->documento_numero ?? '') }}"
                                    data-tel="{{ strtolower($cli->celular ?? $cli->telefono ?? '') }}"
                                    data-dir="{{ strtolower($cli->direccion ?? '') }}">
                                    <td>
                                        <div class="fw-bold text-dark">{{ $cli->nombre_completo }}</div>
                                        <small class="text-muted">{{ $cli->tipo === 'empresa' ? 'Empresa / Razón Social' : 'Persona Natural' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $cli->documento_tipo === 'RUC' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $cli->documento_tipo }}: {{ $cli->documento_numero ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div><i class="fa-solid fa-phone text-muted me-1" style="font-size: 11px;"></i> {{ $cli->celular ?? $cli->telefono ?? 'Sin teléfono' }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;">
                                            <i class="fa-solid fa-location-dot me-1" style="font-size: 10px;"></i> {{ $cli->direccion ?? 'Sin dirección' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-success px-3 rounded-pill btn-seleccionar-modal" data-id="{{ $cli->id }}">
                                            <i class="fa-solid fa-check me-1"></i> Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <span class="text-muted" style="font-size: 12px;" id="contadorResultadosClientes">{{ count($clientes) }} clientes en catálogo</span>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registro Rápido de Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title brand-font mb-0 text-dark">Registro Rápido de Cliente</h5>
                    <span class="text-secondary" style="font-size: 12px;">Se guardará y se seleccionará automáticamente en esta venta</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoClienteRapido">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Tipo de Cliente</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoPersona" value="persona_natural" checked onchange="toggleTipoCliente(this.value)">
                                <label class="form-check-label" for="tipoPersona">Persona Natural</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipoEmpresa" value="empresa" onchange="toggleTipoCliente(this.value)">
                                <label class="form-check-label" for="tipoEmpresa">Empresa (Factura)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Tipo Doc.</label>
                            <select name="documento_tipo" id="modalDocTipo" class="form-select form-select-sm" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">C.E.</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-semibold" style="font-size: 12px;">N° Documento</label>
                            <input type="text" name="documento_numero" id="modalDocNumero" class="form-control form-control-sm" placeholder="8 u 11 dígitos" required>
                        </div>
                    </div>

                    <div id="campoPersona" class="mb-2">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Nombres</label>
                                <input type="text" name="nombre" id="modalNombre" class="form-control form-control-sm" placeholder="Ej. Carlos" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Apellidos</label>
                                <input type="text" name="apellidos" id="modalApellidos" class="form-control form-control-sm" placeholder="Ej. Gómez Silva">
                            </div>
                        </div>
                    </div>

                    <div id="campoEmpresa" class="mb-2" style="display: none;">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Razón Social</label>
                        <input type="text" name="razon_social" id="modalRazonSocial" class="form-control form-control-sm" placeholder="Ej. Constructora & Diseño S.A.C.">
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Celular / WhatsApp</label>
                            <input type="text" name="celular" id="modalCelular" class="form-control form-control-sm" placeholder="+51 987 654 321">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Email</label>
                            <input type="email" name="email" id="modalEmail" class="form-control form-control-sm" placeholder="cliente@correo.com">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Dirección de Entrega</label>
                        <input type="text" name="direccion" id="modalDireccion" class="form-control form-control-sm" placeholder="Av. / Calle, Nro, Dpto / Urbanización">
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Distrito</label>
                            <input type="text" name="distrito" id="modalDistrito" class="form-control form-control-sm" placeholder="Ej. Miraflores">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Ciudad</label>
                            <input type="text" name="ciudad" id="modalCiudad" class="form-control form-control-sm" value="Lima">
                        </div>
                    </div>

                    <div id="nuevoClienteError" class="alert alert-danger py-1 px-2 mt-2" style="display: none; font-size: 11.5px;"></div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm text-white px-3" style="background: var(--primary-orange);" id="btnGuardarClienteRapido">
                        <i class="fa-solid fa-save me-1"></i> Guardar y Seleccionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Búsqueda Avanzada / Catálogo Visual de Muebles -->
<div class="modal fade" id="modalBusquedaMuebles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-circle" style="width: 40px; height: 40px; background: rgba(255, 122, 24, 0.15); color: var(--primary-orange);">
                        <i class="fa-solid fa-couch fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title brand-font mb-0 text-dark">Catálogo & Búsqueda Avanzada de Muebles</h5>
                        <span class="text-secondary" style="font-size: 12px;">Filtre por categoría, modelo, SKU o acabados y seleccione el mueble</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <!-- Barra de Filtros del Catálogo -->
                <div class="bg-white p-3 rounded-3 shadow-sm mb-3 border">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>
                                <input type="text" id="filtroModalMuebles" class="form-control border-start-0" 
                                       placeholder="Buscar por Nombre (ej. Seccional, Comedor), SKU, Material (ej. Roble, Cedro, Cuero)...">
                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('filtroModalMuebles').value=''; filtrarCatalogoMuebles();">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-1 overflow-auto pb-1" id="pillsCategorias">
                                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1 flex-shrink-0 btn-filtro-cat active" data-cat-id="todas" onclick="filtrarPorCategoria('todas', this)">
                                    Todos ({{ $productos->count() }})
                                </button>
                                @foreach($categorias as $cat)
                                    @php $cantProds = $productos->where('categoria_id', $cat->id)->count(); @endphp
                                    @if($cantProds > 0)
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 flex-shrink-0 btn-filtro-cat" data-cat-id="{{ $cat->id }}" onclick="filtrarPorCategoria('{{ $cat->id }}', this)">
                                            {{ $cat->nombre }} ({{ $cantProds }})
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Muebles en Catálogo -->
                <div class="row g-3" id="gridMueblesCatalogo">
                    @foreach($productos as $prod)
                        <div class="col-md-6 col-lg-4 mueble-card-col" 
                             data-id="{{ $prod->id }}"
                             data-cat-id="{{ $prod->categoria_id }}"
                             data-nombre="{{ strtolower($prod->nombre) }}"
                             data-sku="{{ strtolower($prod->sku ?? '') }}"
                             data-categoria="{{ strtolower($prod->categoria->nombre ?? '') }}"
                             data-material="{{ strtolower($prod->material ?? '') }}"
                             data-color="{{ strtolower($prod->color ?? '') }}"
                             data-precio="{{ $prod->precio_venta }}"
                             data-stock="{{ $prod->stock_actual }}">
                            <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary-subtle text-primary fw-semibold" style="font-size: 11px;">
                                            {{ $prod->categoria->nombre ?? 'Catálogo' }}
                                        </span>
                                        <span class="badge {{ $prod->stock_actual > 3 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border" style="font-size: 11px;">
                                            <i class="fa-solid fa-boxes-stacked me-1"></i> Stock: {{ $prod->stock_actual }}
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">{{ $prod->nombre }}</h6>
                                    <div class="text-muted mb-2" style="font-size: 11.5px;">
                                        <span class="badge bg-light text-secondary border me-1">SKU: {{ $prod->sku }}</span>
                                        @if($prod->material)
                                            <span><i class="fa-solid fa-tree me-1"></i>{{ $prod->material }}</span>
                                        @endif
                                        @if($prod->color)
                                            <span class="ms-1"><i class="fa-solid fa-palette me-1"></i>{{ $prod->color }}</span>
                                        @endif
                                    </div>
                                    @if($prod->largo && $prod->ancho && $prod->alto)
                                        <div class="text-muted mb-2" style="font-size: 11px;">
                                            <i class="fa-solid fa-ruler-combined me-1"></i> {{ $prod->largo }} x {{ $prod->ancho }} x {{ $prod->alto }} cm
                                        </div>
                                    @endif
                                </div>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <span class="text-muted d-block" style="font-size: 10.5px;">Precio Venta</span>
                                        <span class="fs-6 fw-bold text-dark">S/. {{ number_format($prod->precio_venta, 2) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm text-white px-3 rounded-pill" 
                                            style="background: var(--primary-orange);" 
                                            onclick="seleccionarMuebleDesdeModal({{ $prod->id }})">
                                        <i class="fa-solid fa-check me-1"></i> Seleccionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="noMueblesMsg" class="p-4 text-center text-muted" style="display: none;">
                    <i class="fa-solid fa-couch fs-2 text-secondary mb-2 d-block"></i>
                    No se encontraron muebles que coincidan con la búsqueda.
                </div>
            </div>
            <div class="modal-footer border-top bg-white">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <span class="text-muted" style="font-size: 12px;" id="contadorResultadosMuebles">{{ $productos->count() }} muebles en catálogo</span>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Datos en memoria de todos los clientes
let clientesData = [
    @foreach($clientes as $cli)
    {
        id: {{ $cli->id }},
        codigo: "{{ $cli->codigo }}",
        nombre_completo: "{{ addslashes($cli->nombre_completo) }}",
        tipo: "{{ $cli->tipo }}",
        documento_tipo: "{{ $cli->documento_tipo }}",
        documento_numero: "{{ $cli->documento_numero ?? '' }}",
        celular: "{{ $cli->celular ?? $cli->telefono ?? '' }}",
        email: "{{ $cli->email ?? '' }}",
        direccion: "{{ addslashes($cli->direccion ?? '') }}",
        distrito: "{{ addslashes($cli->distrito ?? '') }}",
        ciudad: "{{ addslashes($cli->ciudad ?? '') }}"
    },
    @endforeach
];

let clienteSeleccionadoActual = null;

// Elementos del DOM para búsqueda
const searchInput = document.getElementById('clienteSearchInput');
const searchResults = document.getElementById('clienteDropdownResults');
const clearSearchBtn = document.getElementById('btnClearSearch');
const clienteCard = document.getElementById('clienteSeleccionadoCard');
const searchBox = document.getElementById('clienteSearchBox');
const selectedInput = document.getElementById('selectedClienteId');
const searchHint = document.getElementById('clienteSearchHint');
const btnCambiar = document.getElementById('btnCambiarCliente');

// 1. Filtrado Reactivo en el Input de Búsqueda
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        
        if (query.length === 0) {
            searchResults.style.display = 'none';
            clearSearchBtn.style.display = 'none';
            return;
        }

        clearSearchBtn.style.display = 'block';

        const filtered = clientesData.filter(c => {
            const nombre = c.nombre_completo.toLowerCase();
            const doc = (c.documento_numero || '').toLowerCase();
            const tel = (c.celular || '').toLowerCase();
            const cod = (c.codigo || '').toLowerCase();
            const dir = (c.direccion || '').toLowerCase();
            return nombre.includes(query) || doc.includes(query) || tel.includes(query) || cod.includes(query) || dir.includes(query);
        });

        renderDropdownResults(filtered, query);
    });

    // Cerrar dropdown al hacer click afuera
    document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Abrir dropdown si hay texto al enfocar
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0) {
            searchResults.style.display = 'block';
        }
    });
}

function renderDropdownResults(clientes, query) {
    if (clientes.length === 0) {
        searchResults.innerHTML = `
            <div class="p-3 text-center text-muted" style="font-size: 12px;">
                <i class="fa-solid fa-user-slash fs-4 d-block mb-1 text-secondary"></i>
                No se encontró ningún cliente para "<strong>${query}</strong>"
                <div class="mt-2">
                    <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background: var(--primary-orange); font-size: 11px;" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                        <i class="fa-solid fa-user-plus me-1"></i> Registrar como Nuevo Cliente
                    </button>
                </div>
            </div>
        `;
    } else {
        let html = `<div class="px-2 py-1 text-muted border-bottom" style="font-size: 11px;">Resultados encontrados (${clientes.length})</div>`;
        clientes.slice(0, 10).forEach(c => {
            const isEmpresa = c.tipo === 'empresa';
            const icon = isEmpresa ? 'fa-building' : 'fa-user';
            const badgeColor = c.documento_tipo === 'RUC' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary';
            
            html += `
                <button type="button" class="dropdown-item p-2 d-flex align-items-center justify-content-between rounded-2 border-bottom text-start" onclick="seleccionarClientePorId(${c.id})">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="avatar-circle flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px; background: rgba(255, 122, 24, 0.12); color: var(--primary-orange);">
                            <i class="fa-solid ${icon}"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 12.5px;">${c.nombre_completo}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                <span class="badge ${badgeColor}" style="font-size: 10px;">${c.documento_tipo}: ${c.documento_numero || '-'}</span>
                                ${c.celular ? `<span class="ms-1"><i class="fa-solid fa-phone me-1"></i>${c.celular}</span>` : ''}
                                ${c.distrito ? `<span class="ms-1 text-truncate"><i class="fa-solid fa-location-dot me-1"></i>${c.distrito}</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <span class="badge bg-light text-success border ms-2 flex-shrink-0" style="font-size: 11px;">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </button>
            `;
        });
        if (clientes.length > 10) {
            html += `<div class="p-2 text-center text-muted" style="font-size: 11px;">+ ${clientes.length - 10} resultados más. Usa la búsqueda avanzada para ver todos.</div>`;
        }
        searchResults.innerHTML = html;
    }
    searchResults.style.display = 'block';
}

if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchResults.style.display = 'none';
        this.style.display = 'none';
        searchInput.focus();
    });
}

// 2. Función Central para Seleccionar Cliente
function seleccionarCliente(cliente) {
    if (!cliente) return;
    clienteSeleccionadoActual = cliente;

    // Actualizar campos ocultos y textos
    selectedInput.value = cliente.id;
    document.getElementById('txtClienteNombre').innerText = cliente.nombre_completo;
    document.getElementById('txtClienteDoc').innerText = (cliente.documento_tipo || 'DOC') + ': ' + (cliente.documento_numero || 'Sin número');
    document.getElementById('txtClienteTel').innerHTML = '<i class="fa-solid fa-phone me-1"></i>' + (cliente.celular || cliente.telefono || 'Sin teléfono');

    // Cambiar visibilidad de componentes
    searchBox.style.display = 'none';
    searchResults.style.display = 'none';
    clienteCard.style.display = 'flex';
    searchHint.innerText = 'Cliente asignado al pedido';

    // Sugerencia inteligente de comprobante: Si es RUC -> Factura, si es DNI -> Boleta
    const selectComprobante = document.getElementById('selectTipoComprobante');
    if (selectComprobante) {
        if (cliente.documento_tipo === 'RUC' || (cliente.documento_numero && cliente.documento_numero.length === 11 && cliente.documento_numero.startsWith('20'))) {
            selectComprobante.value = 'factura';
        } else if (cliente.documento_tipo === 'DNI') {
            selectComprobante.value = 'boleta';
        }
    }

    // Si tiene dirección y ya se activó envío a domicilio, autorellenar
    const dirInput = document.querySelector('input[name="direccion_entrega"]');
    if (dirInput && cliente.direccion && dirInput.value.trim() === '') {
        const fullDir = cliente.direccion + (cliente.distrito ? ', ' + cliente.distrito : '') + (cliente.ciudad ? ' - ' + cliente.ciudad : '');
        dirInput.value = fullDir;
    }
}

function seleccionarClientePorId(id) {
    const cli = clientesData.find(c => c.id == id);
    if (cli) {
        seleccionarCliente(cli);
    }
}

// 3. Cambiar / Quitar Cliente
if (btnCambiar) {
    btnCambiar.addEventListener('click', function() {
        clienteSeleccionadoActual = null;
        selectedInput.value = '';
        clienteCard.style.display = 'none';
        searchBox.style.display = 'flex';
        searchHint.innerText = 'Escribe para buscar...';
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        searchInput.focus();
    });
}

// 4. Modal Búsqueda Avanzada: Filtrado de tabla
const filtroTabla = document.getElementById('filtroTablaClientes');
if (filtroTabla) {
    filtroTabla.addEventListener('input', filtrarTablaClientes);
}

function filtrarTablaClientes() {
    const q = filtroTabla.value.trim().toLowerCase();
    const filas = document.querySelectorAll('.cliente-fila');
    let visibles = 0;

    filas.forEach(fila => {
        const nombre = fila.getAttribute('data-nombre') || '';
        const doc = fila.getAttribute('data-doc') || '';
        const tel = fila.getAttribute('data-tel') || '';
        const dir = fila.getAttribute('data-dir') || '';

        if (q === '' || nombre.includes(q) || doc.includes(q) || tel.includes(q) || dir.includes(q)) {
            fila.style.display = '';
            visibles++;
        } else {
            fila.style.display = 'none';
        }
    });

    document.getElementById('contadorResultadosClientes').innerText = `${visibles} clientes encontrados`;
}

// Delegación de click para botones seleccionar del modal
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-seleccionar-modal')) {
        const btn = e.target.closest('.btn-seleccionar-modal');
        const id = btn.getAttribute('data-id');
        seleccionarClientePorId(id);

        // Cerrar modal
        const modalEl = document.getElementById('modalBusquedaAvanzada');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
});

// 5. Modal Registro Rápido: Cambio entre Persona y Empresa
function toggleTipoCliente(tipo) {
    const campoPersona = document.getElementById('campoPersona');
    const campoEmpresa = document.getElementById('campoEmpresa');
    const docTipo = document.getElementById('modalDocTipo');
    const inputNombre = document.getElementById('modalNombre');
    const inputRazon = document.getElementById('modalRazonSocial');

    if (tipo === 'empresa') {
        campoPersona.style.display = 'none';
        campoEmpresa.style.display = 'block';
        docTipo.value = 'RUC';
        inputNombre.required = false;
        inputRazon.required = true;
    } else {
        campoPersona.style.display = 'block';
        campoEmpresa.style.display = 'none';
        docTipo.value = 'DNI';
        inputNombre.required = true;
        inputRazon.required = false;
    }
}

// 6. Envío AJAX del formulario de Nuevo Cliente Rápido
const formNuevoCliente = document.getElementById('formNuevoClienteRapido');
if (formNuevoCliente) {
    formNuevoCliente.addEventListener('submit', function(e) {
        e.preventDefault();
        const btnGuardar = document.getElementById('btnGuardarClienteRapido');
        const errorBox = document.getElementById('nuevoClienteError');
        
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Guardando...';
        errorBox.style.display = 'none';

        const formData = new FormData(this);

        fetch("{{ route('clientes.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.cliente) {
                // Agregar al array en memoria
                clientesData.unshift(data.cliente);

                // Auto-seleccionar el cliente creado
                seleccionarCliente(data.cliente);

                // Cerrar modal y limpiar form
                const modalEl = document.getElementById('modalNuevoCliente');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
                formNuevoCliente.reset();
                toggleTipoCliente('persona_natural');

                // Notificación visual ligera
                const toast = document.createElement('div');
                toast.className = 'alert alert-success position-fixed top-0 end-0 m-4 shadow-lg';
                toast.style.zIndex = '9999';
                toast.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i> Cliente <strong>${data.cliente.nombre_completo}</strong> registrado y seleccionado con éxito.`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }
        })
        .catch(err => {
            let msg = 'Ocurrió un error al guardar el cliente.';
            if (err.errors) {
                msg = Object.values(err.errors).flat().join('<br>');
            } else if (err.message) {
                msg = err.message;
            }
            errorBox.innerHTML = msg;
            errorBox.style.display = 'block';
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fa-solid fa-save me-1"></i> Guardar y Seleccionar';
        });
    });
}

// 7. Auto-selección inicial si viene con old('cliente_id')
document.addEventListener('DOMContentLoaded', function() {
    const oldId = "{{ old('cliente_id') }}";
    if (oldId) {
        seleccionarClientePorId(oldId);
    }
});

// ==========================================
// BÚSQUEDA AVANZADA & GESTIÓN DE MUEBLES (POS)
// ==========================================
let productosData = [
    @foreach($productos as $prod)
    {
        id: {{ $prod->id }},
        sku: "{{ $prod->sku }}",
        nombre: "{{ addslashes($prod->nombre) }}",
        categoria: "{{ addslashes($prod->categoria->nombre ?? 'Catálogo') }}",
        categoria_id: {{ $prod->categoria_id ?? 'null' }},
        material: "{{ addslashes($prod->material ?? '') }}",
        color: "{{ addslashes($prod->color ?? '') }}",
        precio_venta: {{ (float) $prod->precio_venta }},
        stock_actual: {{ (int) $prod->stock_actual }}
    },
    @endforeach
];

let targetRowIndexForModal = null;
let itemCount = 1;

function inicializarFilaMueble(row) {
    if (!row) return;
    const rowIndex = row.getAttribute('data-row-index');
    const searchInput = row.querySelector('.prod-search-input');
    const dropdown = row.querySelector('.prod-dropdown-results');
    const clearBtn = row.querySelector('.btn-clear-prod');
    const searchBox = row.querySelector('.prod-search-box');
    const cardSelected = row.querySelector('.prod-selected-card');
    const hiddenInput = row.querySelector('.producto-id-hidden');
    const btnCambiar = row.querySelector('.btn-cambiar-prod');
    const btnOpenCat = row.querySelector('.btn-open-catalogo-row');
    const searchHint = row.querySelector('.prod-search-hint');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            if (query.length === 0) {
                dropdown.style.display = 'none';
                clearBtn.style.display = 'none';
                row.style.zIndex = '1';
                return;
            }
            clearBtn.style.display = 'block';
            row.style.zIndex = '35';

            const filtered = productosData.filter(p => {
                const nom = p.nombre.toLowerCase();
                const sku = (p.sku || '').toLowerCase();
                const cat = (p.categoria || '').toLowerCase();
                const mat = (p.material || '').toLowerCase();
                const col = (p.color || '').toLowerCase();
                return nom.includes(query) || sku.includes(query) || cat.includes(query) || mat.includes(query) || col.includes(query);
            });

            renderProdDropdownResults(row, filtered, query);
        });

        searchInput.addEventListener('focus', function() {
            row.style.zIndex = '35';
            if (this.value.trim().length > 0) {
                dropdown.style.display = 'block';
            }
        });

        document.addEventListener('click', function(e) {
            if (!row.contains(e.target)) {
                dropdown.style.display = 'none';
                row.style.zIndex = '1';
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            dropdown.style.display = 'none';
            this.style.display = 'none';
            row.style.zIndex = '1';
            searchInput.focus();
        });
    }

    if (btnCambiar) {
        btnCambiar.addEventListener('click', function() {
            hiddenInput.value = '';
            cardSelected.style.display = 'none';
            searchBox.style.display = 'flex';
            if (searchHint) searchHint.innerText = 'Buscar por modelo, SKU...';
            searchInput.value = '';
            clearBtn.style.display = 'none';
            row.querySelector('.precio-input').value = '0.00';
            calcularTotales();
            searchInput.focus();
        });
    }

    if (btnOpenCat) {
        btnOpenCat.addEventListener('click', function() {
            targetRowIndexForModal = rowIndex;
            const modalEl = document.getElementById('modalBusquedaMuebles');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        });
    }
}

function renderProdDropdownResults(row, prods, query) {
    const dropdown = row.querySelector('.prod-dropdown-results');
    const rowIndex = row.getAttribute('data-row-index');

    if (prods.length === 0) {
        dropdown.innerHTML = `
            <div class="p-3 text-center text-muted" style="font-size: 12px;">
                <i class="fa-solid fa-couch fs-4 d-block mb-1 text-secondary"></i>
                No se encontró ningún mueble para "<strong>${query}</strong>"
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 11px;" onclick="abrirModalCatalogoParaFila(${rowIndex})">
                        <i class="fa-solid fa-table-cells me-1"></i> Explorar Catálogo Completo
                    </button>
                </div>
            </div>
        `;
    } else {
        let html = `<div class="px-2 py-1 text-muted border-bottom d-flex justify-content-between" style="font-size: 11px;">
            <span>Muebles encontrados (${prods.length})</span>
            <span class="text-primary" style="cursor: pointer;" onclick="abrirModalCatalogoParaFila(${rowIndex})">Ver catálogo <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></span>
        </div>`;

        prods.slice(0, 8).forEach(p => {
            const stockColor = p.stock_actual > 3 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
            html += `
                <button type="button" class="dropdown-item p-2 d-flex align-items-center justify-content-between rounded-2 border-bottom text-start" onclick="seleccionarProductoEnFila(${rowIndex}, ${p.id})">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="avatar-circle flex-shrink-0" style="width: 32px; height: 32px; font-size: 12px; background: rgba(255, 122, 24, 0.12); color: var(--primary-orange);">
                            <i class="fa-solid fa-couch"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 12.5px;">${p.nombre}</div>
                            <div class="text-muted" style="font-size: 11px;">
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">SKU: ${p.sku}</span>
                                <span class="badge ${stockColor}" style="font-size: 10px;">Stock: ${p.stock_actual}</span>
                                <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">${p.categoria}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end ms-2 flex-shrink-0">
                        <span class="fw-bold text-dark d-block" style="font-size: 12.5px;">S/. ${p.precio_venta.toFixed(2)}</span>
                        <span class="badge bg-light text-success border" style="font-size: 10px;"><i class="fa-solid fa-check me-1"></i>Elegir</span>
                    </div>
                </button>
            `;
        });

        if (prods.length > 8) {
            html += `<div class="p-2 text-center text-muted" style="font-size: 11px; cursor: pointer;" onclick="abrirModalCatalogoParaFila(${rowIndex})">+ ${prods.length - 8} resultados más. Clic para ver en el catálogo.</div>`;
        }
        dropdown.innerHTML = html;
    }
    dropdown.style.display = 'block';
}

function seleccionarProductoEnFila(rowIndex, prodOrId) {
    let p = typeof prodOrId === 'object' ? prodOrId : productosData.find(x => x.id == prodOrId);
    if (!p) return;

    const row = document.querySelector(`.item-row[data-row-index="${rowIndex}"]`);
    if (!row) return;

    row.querySelector('.producto-id-hidden').value = p.id;
    row.querySelector('.prod-txt-nombre').innerText = p.nombre;
    row.querySelector('.prod-txt-sku').innerText = 'SKU: ' + p.sku;
    row.querySelector('.prod-txt-stock').innerText = 'Stock: ' + p.stock_actual;
    row.querySelector('.prod-txt-precio').innerText = 'S/. ' + p.precio_venta.toFixed(2);

    row.querySelector('.prod-search-box').style.display = 'none';
    row.querySelector('.prod-dropdown-results').style.display = 'none';
    row.querySelector('.prod-selected-card').style.display = 'flex';
    const hint = row.querySelector('.prod-search-hint');
    if (hint) hint.innerText = 'Mueble seleccionado';

    // Auto-completar precio unitario
    const precioInput = row.querySelector('.precio-input');
    if (precioInput) {
        precioInput.value = p.precio_venta.toFixed(2);
    }
    row.style.zIndex = '1';

    calcularTotales();
}

function abrirModalCatalogoParaFila(rowIndex) {
    targetRowIndexForModal = rowIndex;
    const modalEl = document.getElementById('modalBusquedaMuebles');
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInstance.show();
}

function agregarNuevaFilaMueble(prod = null) {
    const container = document.getElementById('itemsContainer');
    const index = itemCount++;
    const newRow = document.createElement('div');
    newRow.className = 'item-row p-3 rounded-3 mb-3 border position-relative';
    newRow.id = `itemRow_${index}`;
    newRow.setAttribute('data-row-index', index);
    newRow.style.background = 'var(--bg-body)';
    newRow.style.zIndex = '1';

    newRow.innerHTML = `
        <div class="row g-2 align-items-end">
            <!-- Columna de Búsqueda Avanzada de Mueble -->
            <div class="col-md-5 position-relative prod-select-col">
                <label class="form-label fw-semibold d-flex justify-content-between align-items-center mb-1" style="font-size: 12px;">
                    <span>Mueble en Catálogo <span class="text-danger">*</span></span>
                    <span class="prod-search-hint text-muted fw-normal" style="font-size: 11px;">Buscar por modelo, SKU...</span>
                </label>

                <!-- Caja de Búsqueda de Mueble -->
                <div class="input-group input-group-sm prod-search-box">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fa-solid fa-couch text-muted" style="font-size: 11px;"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 prod-search-input" 
                           placeholder="Buscar por Nombre, SKU, Material, Sala..." 
                           autocomplete="off">
                    <button class="btn btn-outline-secondary btn-clear-prod" type="button" style="display: none;" title="Limpiar">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <button class="btn btn-outline-primary btn-open-catalogo-row" type="button" title="Abrir catálogo visual" data-row-index="${index}">
                        <i class="fa-solid fa-table-cells"></i>
                    </button>
                </div>

                <!-- Dropdown Flotante de Resultados de Muebles -->
                <div class="dropdown-menu w-100 shadow-lg p-1 mt-1 border prod-dropdown-results" 
                     style="display: none; max-height: 290px; overflow-y: auto; z-index: 1060; border-radius: 12px; box-shadow: 0 16px 36px rgba(0,0,0,0.18) !important; background: #ffffff;">
                </div>

                <!-- Tarjeta de Mueble Seleccionado -->
                <div class="p-2 rounded-3 border align-items-center justify-content-between prod-selected-card" 
                     style="display: none; background: #fff8f1; border-color: #ffd8a8 !important;">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="avatar-circle flex-shrink-0" style="width: 34px; height: 34px; font-size: 13px; background: rgba(255, 122, 24, 0.15); color: var(--primary-orange);">
                            <i class="fa-solid fa-couch"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-bold text-dark text-truncate prod-txt-nombre" style="font-size: 12.5px;">-</div>
                            <div class="d-flex flex-wrap gap-1 text-muted align-items-center" style="font-size: 11px;">
                                <span class="badge bg-white text-dark border prod-txt-sku">-</span>
                                <span class="badge bg-success-subtle text-success prod-txt-stock">Stock: -</span>
                                <span class="fw-semibold text-primary prod-txt-precio">S/. 0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0 ms-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-cambiar-prod" title="Cambiar mueble" style="font-size: 11px;">
                            <i class="fa-solid fa-pen me-1"></i> Cambiar
                        </button>
                    </div>
                </div>

                <!-- Input Oculto Obligatorio para el POST -->
                <input type="hidden" name="productos[${index}][producto_id]" class="producto-id-hidden" required>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size: 12px;">Cantidad</label>
                <input type="number" name="productos[${index}][cantidad]" class="form-control form-control-sm cantidad-input" value="1" min="1" required onchange="calcularTotales()">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size: 12px;">P. Unitario (S/.)</label>
                <input type="number" step="0.01" name="productos[${index}][precio_unitario]" class="form-control form-control-sm precio-input" value="0.00" required onchange="calcularTotales()">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size: 12px;">Subtotal (S/.)</label>
                <input type="text" class="form-control form-control-sm subtotal-item bg-light" readonly value="S/. 0.00">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="eliminarFilaMueble(this)" title="Eliminar mueble">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            <div class="col-12 mt-2">
                <input type="text" name="productos[${index}][color_acabado]" class="form-control form-control-sm" placeholder="Personalización: Color de tapiz solicitado / Acabado de barniz (Opcional)">
            </div>
        </div>
    `;

    container.appendChild(newRow);
    inicializarFilaMueble(newRow);

    if (prod) {
        seleccionarProductoEnFila(index, prod);
    } else {
        newRow.querySelector('.prod-search-input').focus();
    }
    return newRow;
}

function eliminarFilaMueble(btn) {
    const rows = document.querySelectorAll('.item-row');
    const row = btn.closest('.item-row');
    if (rows.length > 1) {
        row.remove();
    } else {
        // Resetear la única fila
        row.querySelector('.producto-id-hidden').value = '';
        row.querySelector('.prod-selected-card').style.display = 'none';
        row.querySelector('.prod-search-box').style.display = 'flex';
        row.querySelector('.prod-search-input').value = '';
        row.querySelector('.btn-clear-prod').style.display = 'none';
        row.querySelector('.prod-dropdown-results').style.display = 'none';
        const hint = row.querySelector('.prod-search-hint');
        if (hint) hint.innerText = 'Buscar por modelo, SKU...';
        row.querySelector('.cantidad-input').value = 1;
        row.querySelector('.precio-input').value = '0.00';
        row.querySelector('.subtotal-item').value = 'S/. 0.00';
    }
    calcularTotales();
}

// Botón Agregar Fila
document.getElementById('btnAgregarItem').addEventListener('click', function() {
    agregarNuevaFilaMueble();
});

// Filtrado de Catálogo en Modal
let categoriaSeleccionadaModal = 'todas';

function filtrarPorCategoria(catId, btn) {
    categoriaSeleccionadaModal = catId;
    document.querySelectorAll('.btn-filtro-cat').forEach(b => {
        b.classList.remove('btn-dark', 'active');
        b.classList.add('btn-light');
    });
    btn.classList.remove('btn-light');
    btn.classList.add('btn-dark', 'active');
    filtrarCatalogoMuebles();
}

function filtrarCatalogoMuebles() {
    const q = (document.getElementById('filtroModalMuebles').value || '').trim().toLowerCase();
    const cols = document.querySelectorAll('.mueble-card-col');
    let visibles = 0;

    cols.forEach(col => {
        const catId = col.getAttribute('data-cat-id');
        const nombre = col.getAttribute('data-nombre') || '';
        const sku = col.getAttribute('data-sku') || '';
        const cat = col.getAttribute('data-categoria') || '';
        const mat = col.getAttribute('data-material') || '';
        const colTxt = col.getAttribute('data-color') || '';

        const matchCat = categoriaSeleccionadaModal === 'todas' || catId == categoriaSeleccionadaModal;
        const matchText = q === '' || nombre.includes(q) || sku.includes(q) || cat.includes(q) || mat.includes(q) || colTxt.includes(q);

        if (matchCat && matchText) {
            col.style.display = '';
            visibles++;
        } else {
            col.style.display = 'none';
        }
    });

    const counter = document.getElementById('contadorResultadosMuebles');
    if (counter) counter.innerText = `${visibles} muebles encontrados`;
    const noMsg = document.getElementById('noMueblesMsg');
    if (noMsg) noMsg.style.display = visibles === 0 ? 'block' : 'none';
}

const filtroModalMueblesInput = document.getElementById('filtroModalMuebles');
if (filtroModalMueblesInput) {
    filtroModalMueblesInput.addEventListener('input', filtrarCatalogoMuebles);
}

function seleccionarMuebleDesdeModal(prodId) {
    const p = productosData.find(x => x.id == prodId);
    if (!p) return;

    if (targetRowIndexForModal !== null) {
        seleccionarProductoEnFila(targetRowIndexForModal, p);
    } else {
        const firstRow = document.querySelector('.item-row');
        const firstHidden = firstRow ? firstRow.querySelector('.producto-id-hidden') : null;
        if (firstRow && (!firstHidden || !firstHidden.value)) {
            const idx = firstRow.getAttribute('data-row-index');
            seleccionarProductoEnFila(idx, p);
        } else {
            agregarNuevaFilaMueble(p);
        }
    }

    const modalEl = document.getElementById('modalBusquedaMuebles');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
        modalInstance.hide();
    }
    targetRowIndexForModal = null;
}

// Inicializar Fila 0 y viejo cliente al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const row0 = document.getElementById('itemRow_0');
    if (row0) {
        inicializarFilaMueble(row0);
    }
});

function calcularTotales() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const cant = parseFloat(row.querySelector('.cantidad-input').value) || 0;
        const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
        const rowSub = cant * precio;
        const subDisplay = row.querySelector('.subtotal-item');
        if (subDisplay) {
            subDisplay.value = 'S/. ' + rowSub.toFixed(2);
        }
        subtotal += rowSub;
    });

    const igvPorc = {{ $config->porcentaje_igv ?? 18 }} / 100;
    const igv = subtotal * igvPorc;
    const total = subtotal + igv;

    document.getElementById('txtSubtotal').innerText = 'S/. ' + subtotal.toFixed(2);
    document.getElementById('txtIgv').innerText = 'S/. ' + igv.toFixed(2);
    document.getElementById('txtTotal').innerText = 'S/. ' + total.toFixed(2);
}

function toggleEntregaFields(checked) {
    document.getElementById('entregaCampos').style.display = checked ? 'block' : 'none';
    
    if (checked && clienteSeleccionadoActual && clienteSeleccionadoActual.direccion) {
        const dirInput = document.querySelector('input[name="direccion_entrega"]');
        if (dirInput && dirInput.value.trim() === '') {
            const fullDir = clienteSeleccionadoActual.direccion + 
                (clienteSeleccionadoActual.distrito ? ', ' + clienteSeleccionadoActual.distrito : '') + 
                (clienteSeleccionadoActual.ciudad ? ' - ' + clienteSeleccionadoActual.ciudad : '');
            dirInput.value = fullDir;
        }
    }
}
</script>
@endpush
