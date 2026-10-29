@extends('layouts.app')

@section('title', 'Inventario de Muebles')

@section('content')
<div class="container-fluid p-0">
    <!-- Header de Módulo -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Inventario & Catálogo de Muebles</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Gestión de stock, dimensiones, materiales y reposición para fábrica</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.exportarCsv') }}" class="btn btn-light border px-3 py-2 fw-semibold rounded-3 d-flex align-items-center gap-2" title="Descargar reporte en formato Excel/CSV">
                <i class="fa-solid fa-file-excel text-success"></i>
                <span>Exportar Excel</span>
            </a>
            <a href="{{ route('categorias.index') }}" class="btn btn-light border px-3 py-2 fw-semibold rounded-3 d-flex align-items-center gap-2" title="Administrar líneas y categorías de muebles">
                <i class="fa-solid fa-layer-group text-primary"></i>
                <span>Categorías</span>
            </a>
            <a href="{{ route('inventario.kardex') }}" class="btn btn-light border px-3 py-2 fw-semibold rounded-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-secondary"></i>
                <span>Ver Kardex</span>
            </a>
            <button class="btn btn-primary-orange px-3 py-2 text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevoMuebleModal" style="background: var(--primary-orange); border: none;">
                <i class="fa-solid fa-plus"></i>
                <span>Nuevo Mueble</span>
            </button>
        </div>
    </div>

    <!-- KPIs Rápidos de Inventario -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total de Modelos</div>
                <div class="fs-4 fw-bold text-dark">{{ $totalProductos }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Unidades en Stock</div>
                <div class="fs-4 fw-bold text-dark">{{ $totalStock }} unds</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Valorización Inventario</div>
                <div class="fs-4 fw-bold text-dark">S/. {{ number_format($valorInventario, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Alertas Stock Mínimo</div>
                <div class="fs-4 fw-bold text-danger">{{ $productosBajoStock }} muebles</div>
            </div>
        </div>
    </div>

    <!-- Filtros y Buscador -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('inventario.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por nombre, SKU o material..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="filtro" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Estados de Stock</option>
                    <option value="stock_bajo" {{ request('filtro') == 'stock_bajo' ? 'selected' : '' }}>Solo Bajo Stock / Críticos</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-light border w-100">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>SKU / Código</th>
                        <th>Mueble & Categoría</th>
                        <th>Material & Acabado</th>
                        <th>Dimensiones (L x A x H)</th>
                        <th>Precio Venta</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $prod)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">{{ $prod->sku }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $prod->nombre }}</div>
                                <span class="badge" style="background: rgba(255, 122, 24, 0.12); color: var(--primary-orange); font-size: 11px;">
                                    <i class="fa-solid {{ $prod->categoria->icono ?? 'fa-couch' }} me-1"></i>
                                    {{ $prod->categoria->nombre ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark" style="font-size: 13px;">{{ $prod->material ?? 'Madera' }}</div>
                                <div class="text-muted" style="font-size: 11.5px;">{{ $prod->color ?? 'Color estándar' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border" style="font-size: 11.5px;">
                                    <i class="fa-solid fa-ruler-combined me-1"></i> {{ $prod->dimensiones }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">S/. {{ number_format($prod->precio_venta, 2) }}</div>
                                <div class="text-muted" style="font-size: 10.5px;">Costo: S/. {{ number_format($prod->precio_compra, 2) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold {{ $prod->stock_actual <= $prod->stock_minimo ? 'text-danger' : 'text-dark' }}">
                                    {{ $prod->stock_actual }} {{ $prod->unidad_medida }}
                                </div>
                                <div class="text-muted" style="font-size: 10.5px;">Mínimo: {{ $prod->stock_minimo }}</div>
                            </td>
                            <td>
                                @if($prod->stock_actual <= 0)
                                    <span class="badge bg-danger-subtle text-danger">Agotado</span>
                                @elseif($prod->stock_actual <= $prod->stock_minimo)
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Stock Bajo</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Disponible</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-light border py-1 px-2" title="Ficha Técnica del Mueble" data-bs-toggle="modal" data-bs-target="#fichaMuebleModal{{ $prod->id }}">
                                        <i class="fa-regular fa-eye text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border py-1 px-2" title="Editar Mueble" data-bs-toggle="modal" data-bs-target="#editarMuebleModal{{ $prod->id }}">
                                        <i class="fa-solid fa-pen-to-square text-warning-emphasis"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border py-1 px-2" title="Ajustar Stock" data-bs-toggle="modal" data-bs-target="#ajusteStockModal{{ $prod->id }}">
                                        <i class="fa-solid fa-sliders text-secondary"></i>
                                    </button>
                                    <form action="{{ route('inventario.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de enviar este mueble a la papelera?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2 text-danger" title="Eliminar Mueble">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-couch fs-2 text-secondary mb-2 d-block"></i>
                                No se encontraron muebles en el inventario con los criterios seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-end">
            {{ $productos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ==============================================================================
     MODALES POR PRODUCTO (COLOCADOS FUERA DE LA TABLA PARA EVITAR PARPADEO/LOOP)
================================================================================== -->
@foreach($productos as $prod)
    <!-- Modal Ficha Técnica del Mueble -->
    <div class="modal fade" id="fichaMuebleModal{{ $prod->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title brand-font mb-0 text-dark">{{ $prod->nombre }}</h5>
                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 11px;">SKU: {{ $prod->sku }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Columna Visual -->
                        <div class="col-md-5">
                            <div class="p-4 rounded-4 text-center d-flex flex-column align-items-center justify-content-center" style="background: linear-gradient(135deg, #1e293b, #0f172a); min-height: 220px; color: #ffffff;">
                                @if($prod->imagen)
                                    <img src="{{ asset('storage/' . $prod->imagen) }}" alt="{{ $prod->nombre }}" class="img-fluid rounded-3 mb-2" style="max-height: 180px; object-fit: cover;">
                                @else
                                    <i class="fa-solid {{ $prod->categoria->icono ?? 'fa-couch' }} fs-1 mb-3 text-warning"></i>
                                    <span class="badge px-3 py-1 rounded-pill" style="background: var(--primary-orange);">
                                        {{ $prod->categoria->nombre ?? 'Muebles Finos' }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-3 text-center">
                                <div class="text-muted" style="font-size: 11px;">PRECIO DE VENTA SUGERIDO</div>
                                <div class="fw-bold text-dark fs-3 font-monospace">S/. {{ number_format($prod->precio_venta, 2) }}</div>
                                <span class="badge bg-success-subtle text-success">Costo: S/. {{ number_format($prod->precio_compra, 2) }}</span>
                            </div>
                        </div>

                        <!-- Columna Datos Técnicos -->
                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Especificaciones de Fabricación</h6>
                            
                            <div class="row g-2 mb-3" style="font-size: 13px;">
                                <div class="col-6">
                                    <span class="text-secondary d-block" style="font-size: 11px;">MATERIAL ESTRUCTURAL</span>
                                    <strong class="text-dark">{{ $prod->material ?? 'Madera Maciza' }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-secondary d-block" style="font-size: 11px;">COLOR / TAPIZ</span>
                                    <strong class="text-dark">{{ $prod->color ?? 'Estándar' }}</strong>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="p-2 rounded-3 bg-light border">
                                        <span class="text-secondary d-block mb-1" style="font-size: 11px;">DIMENSIONES (PLANIFICACIÓN DE TRANSPORTE)</span>
                                        <div class="d-flex gap-3 text-dark fw-bold">
                                            <span><i class="fa-solid fa-arrows-left-right text-primary me-1"></i> Largo: {{ $prod->largo ?? 0 }} cm</span>
                                            <span><i class="fa-solid fa-arrows-up-down text-primary me-1"></i> Ancho: {{ $prod->ancho ?? 0 }} cm</span>
                                            <span><i class="fa-solid fa-up-down text-primary me-1"></i> Alto: {{ $prod->alto ?? 0 }} cm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-secondary d-block" style="font-size: 11px;">DESCRIPCIÓN DEL MODELO</span>
                                <p class="text-dark mb-0" style="font-size: 13px; line-height: 1.5;">
                                    {{ $prod->descripcion ?? 'Mueble de diseño ergonómico de alta resistencia para uso residencial o comercial.' }}
                                </p>
                            </div>

                            <div class="p-3 rounded-3 mb-3 border d-flex justify-content-between align-items-center" style="background: var(--bg-body);">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">DISPONIBILIDAD EN TIENDA</span>
                                    <span class="fw-bold fs-5 {{ $prod->stock_actual <= $prod->stock_minimo ? 'text-danger' : 'text-success' }}">
                                        {{ $prod->stock_actual }} {{ $prod->unidad_medida }}
                                    </span>
                                </div>
                                @if($prod->stock_actual <= $prod->stock_minimo)
                                    <span class="badge bg-danger text-white">¡Nivel Crítico de Stock!</span>
                                @else
                                    <span class="badge bg-success text-white">Stock Óptimo</span>
                                @endif
                            </div>

                            <!-- Botones de Acción Comercial -->
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/?text={{ urlencode('Hola, quisiera consultar sobre el mueble: ' . $prod->nombre . ' (SKU: ' . $prod->sku . ') - Precio: S/. ' . number_format($prod->precio_venta, 2)) }}" target="_blank" class="btn btn-success flex-fill rounded-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-brands fa-whatsapp fs-5"></i>
                                    <span>Compartir por WhatsApp</span>
                                </a>
                                <a href="{{ route('ventas.create') }}" class="btn text-white flex-fill rounded-3 d-flex align-items-center justify-content-center gap-2" style="background: var(--primary-orange);">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span>Vender / Cotizar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajuste de Stock para este Mueble -->
    <div class="modal fade" id="ajusteStockModal{{ $prod->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title brand-font">Ajustar Stock — {{ $prod->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('inventario.ajustarStock', $prod->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Stock actual:</span>
                                <strong class="fs-5 text-dark">{{ $prod->stock_actual }} {{ $prod->unidad_medida }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo de Ajuste</label>
                            <select name="tipo" class="form-select" required>
                                <option value="agregar">Entrada (+ Sumar al stock por fabricación/taller)</option>
                                <option value="reducir">Salida (- Descontar por merma, daño o ajuste)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required placeholder="Ej. 2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motivo del Ajuste</label>
                            <input type="text" name="motivo" class="form-control" placeholder="Ej. Finalización de lote en taller de carpintería" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Ajuste</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Mueble -->
    <div class="modal fade" id="editarMuebleModal{{ $prod->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title brand-font text-dark">Editar Mueble — {{ $prod->nombre }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('inventario.update', $prod->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Código SKU</label>
                                <input type="text" name="sku" class="form-control" value="{{ $prod->sku }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nombre del Mueble</label>
                                <input type="text" name="nombre" class="form-control" value="{{ $prod->nombre }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select name="categoria_id" class="form-select" required>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ $prod->categoria_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Material Principal</label>
                                <input type="text" name="material" class="form-control" value="{{ $prod->material }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Color / Tapiz</label>
                                <input type="text" name="color" class="form-control" value="{{ $prod->color }}">
                            </div>

                            <!-- Dimensiones -->
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3">
                                    <label class="fw-bold text-dark d-block mb-2">Dimensiones para Transporte (cm)</label>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="number" step="0.1" name="largo" class="form-control form-control-sm" value="{{ $prod->largo }}" placeholder="Largo (cm)">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.1" name="ancho" class="form-control form-control-sm" value="{{ $prod->ancho }}" placeholder="Ancho (cm)">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.1" name="alto" class="form-control form-control-sm" value="{{ $prod->alto }}" placeholder="Alto (cm)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Precio Costo / Compra (S/.)</label>
                                <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ $prod->precio_compra }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Precio de Venta (S/.)</label>
                                <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ $prod->precio_venta }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Stock Mínimo (Alerta Taller)</label>
                                <input type="number" name="stock_minimo" class="form-control" value="{{ $prod->stock_minimo }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Actualizar Foto / Imagen</label>
                                <input type="file" name="imagen" class="form-control" accept="image/*">
                                <div class="form-text">Dejar vacío para conservar la imagen actual.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2">{{ $prod->descripcion }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Nuevo Mueble -->
<div class="modal fade" id="nuevoMuebleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Registrar Nuevo Mueble en Catálogo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('inventario.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="Ej. SOF-MOD-007" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Mueble</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Sofá Seccional 3 Cuerpos Chesterfield" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Seleccione Categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Material Principal</label>
                            <input type="text" name="material" class="form-control" placeholder="Ej. Roble / Cuero Legitimo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Color / Tapiz</label>
                            <input type="text" name="color" class="form-control" placeholder="Ej. Azul Petróleo">
                        </div>
                        
                        <!-- Dimensiones Especiales para Mueblería -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <label class="fw-bold text-dark d-block mb-2">Dimensiones para Transporte & Planificación (cm)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="number" step="0.1" name="largo" class="form-control form-control-sm" placeholder="Largo (cm)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.1" name="ancho" class="form-control form-control-sm" placeholder="Ancho (cm)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.1" name="alto" class="form-control form-control-sm" placeholder="Alto (cm)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Precio Compra / Costo</label>
                            <input type="number" step="0.01" name="precio_compra" class="form-control" placeholder="S/. 0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Precio de Venta</label>
                            <input type="number" step="0.01" name="precio_venta" class="form-control" placeholder="S/. 0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Inicial</label>
                            <input type="number" name="stock_actual" class="form-control" value="1" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Mínimo (Alerta)</label>
                            <input type="number" name="stock_minimo" class="form-control" value="2" min="1" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto / Imagen de Catálogo del Mueble</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, WebP.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción y Especificaciones Técnicas</label>
                            <textarea name="descripcion" class="form-control" rows="2" placeholder="Detalles de ensamblado, esponja, tipo de tornillería..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Registrar Mueble</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
