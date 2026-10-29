@extends('layouts.app')

@section('title', 'Líneas & Categorías de Muebles')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Líneas & Categorías de Muebles</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Clasificación de catálogos: Salas, Comedores, Dormitorios, Oficinas y Personalizados</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.index') }}" class="btn btn-light border px-3 py-2 fw-semibold rounded-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-left text-secondary"></i>
                <span>Volver al Catálogo</span>
            </a>
            <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal" style="background: var(--primary-orange);">
                <i class="fa-solid fa-plus"></i>
                <span>+ Nueva Categoría</span>
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Líneas de Muebles Registradas</div>
                <div class="fs-4 fw-bold text-dark">{{ $totalCategorias }} categorías</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Líneas Activas en Sala de Venta</div>
                <div class="fs-4 fw-bold text-success">{{ $categoriasActivas }} activas</div>
            </div>
        </div>
    </div>

    <!-- Filtro de Búsqueda -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('categorias.index') }}" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar categoría por nombre o descripción..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100">Buscar</button>
                    @if(request('search'))
                        <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-light border">Limpiar</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de Categorías -->
    <div class="row g-3 mb-4">
        @forelse($categorias as $cat)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="stat-card h-100 p-0 overflow-hidden d-flex flex-column border">
                    <div class="p-3 d-flex align-items-center justify-content-between" style="border-bottom: 2px solid {{ $cat->color ?? '#ff6a3d' }}; background: rgba(0,0,0,0.015);">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 42px; height: 42px; background: {{ $cat->color ?? '#ff6a3d' }}; font-size: 16px;">
                                <i class="fa-solid {{ $cat->icono ?? 'fa-couch' }}"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $cat->nombre }}</h6>
                                <span class="badge {{ $cat->activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size: 10px;">
                                    {{ $cat->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border fw-semibold" title="Muebles en esta categoría" style="font-size: 12px;">
                            {{ $cat->productos_count }} muebles
                        </span>
                    </div>
                    <div class="p-3 flex-grow-1">
                        <p class="text-secondary mb-0" style="font-size: 13px; min-height: 38px;">
                            {{ $cat->descripcion ?: 'Línea de mobiliario y carpintería para interiores.' }}
                        </p>
                    </div>
                    <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                        <a href="{{ route('inventario.index', ['categoria_id' => $cat->id]) }}" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" style="font-size: 12.5px;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Ver Catálogo
                        </a>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-light border px-2 py-1" onclick='abrirEditarCategoria(@json($cat))' title="Editar categoría">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>
                            @if($cat->productos_count == 0)
                                <form action="{{ route('categorias.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar la categoría {{ $cat->nombre }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border px-2 py-1 text-danger" title="Eliminar categoría">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="stat-card text-center py-5 text-muted">
                    <i class="fa-solid fa-folder-open fs-1 mb-3 text-secondary opacity-50"></i>
                    <h5>No se encontraron categorías</h5>
                    <p class="mb-0">Puede registrar nuevas líneas de muebles haciendo clic en "+ Nueva Categoría".</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($categorias->hasPages())
        <div class="d-flex justify-content-end mb-4">
            {{ $categorias->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Nueva Categoría -->
<div class="modal fade" id="nuevaCategoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Crear Nueva Línea / Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Categoría</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Centros de Entretenimiento & TV" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve detalle de los muebles incluidos en esta categoría..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ícono Representativo</label>
                            <select name="icono" class="form-select" id="nuevoIconoSelect">
                                <option value="fa-couch">🛋️ Sofá / Sala (fa-couch)</option>
                                <option value="fa-bed">🛏️ Cama / Dormitorio (fa-bed)</option>
                                <option value="fa-chair">🪑 Silla / Comedor (fa-chair)</option>
                                <option value="fa-table">🪵 Mesa / Tableros (fa-table)</option>
                                <option value="fa-door-closed">🚪 Roperos / Closets (fa-door-closed)</option>
                                <option value="fa-briefcase">💼 Oficina / Escritorios (fa-briefcase)</option>
                                <option value="fa-tv">📺 Muebles TV / Audio (fa-tv)</option>
                                <option value="fa-boxes-stacked">📦 Modular / Repisas (fa-boxes-stacked)</option>
                                <option value="fa-gem">✨ Línea Premium / Lujo (fa-gem)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Color Identificador</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="color" id="nuevoColorInput" class="form-control form-control-color p-1" value="#ff6a3d" style="width: 50px; height: 38px;">
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #ff6a3d; cursor: pointer;" onclick="setColor('nuevoColorInput', '#ff6a3d')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #161a29; cursor: pointer;" onclick="setColor('nuevoColorInput', '#161a29')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #4f46e5; cursor: pointer;" onclick="setColor('nuevoColorInput', '#4f46e5')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #10b981; cursor: pointer;" onclick="setColor('nuevoColorInput', '#10b981')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #d97706; cursor: pointer;" onclick="setColor('nuevoColorInput', '#d97706')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #f43f5e; cursor: pointer;" onclick="setColor('nuevoColorInput', '#f43f5e')"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Categoría -->
<div class="modal fade" id="editarCategoriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Editar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoriaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la Categoría</label>
                        <input type="text" name="nombre" id="editCatNombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="editCatDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ícono Representativo</label>
                            <select name="icono" id="editCatIcono" class="form-select">
                                <option value="fa-couch">🛋️ Sofá / Sala (fa-couch)</option>
                                <option value="fa-bed">🛏️ Cama / Dormitorio (fa-bed)</option>
                                <option value="fa-chair">🪑 Silla / Comedor (fa-chair)</option>
                                <option value="fa-table">🪵 Mesa / Tableros (fa-table)</option>
                                <option value="fa-door-closed">🚪 Roperos / Closets (fa-door-closed)</option>
                                <option value="fa-briefcase">💼 Oficina / Escritorios (fa-briefcase)</option>
                                <option value="fa-tv">📺 Muebles TV / Audio (fa-tv)</option>
                                <option value="fa-boxes-stacked">📦 Modular / Repisas (fa-boxes-stacked)</option>
                                <option value="fa-gem">✨ Línea Premium / Lujo (fa-gem)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Color Identificador</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="color" id="editCatColor" class="form-control form-control-color p-1" style="width: 50px; height: 38px;">
                                <div class="d-flex gap-1 flex-wrap">
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #ff6a3d; cursor: pointer;" onclick="setColor('editCatColor', '#ff6a3d')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #161a29; cursor: pointer;" onclick="setColor('editCatColor', '#161a29')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #4f46e5; cursor: pointer;" onclick="setColor('editCatColor', '#4f46e5')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #10b981; cursor: pointer;" onclick="setColor('editCatColor', '#10b981')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #d97706; cursor: pointer;" onclick="setColor('editCatColor', '#d97706')"></span>
                                    <span class="rounded-circle d-inline-block border" style="width: 22px; height: 22px; background: #f43f5e; cursor: pointer;" onclick="setColor('editCatColor', '#f43f5e')"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="activo" id="editCatActivo" value="1">
                        <label class="form-check-label fw-semibold" for="editCatActivo">Categoría Activa en Sala de Ventas</label>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setColor(inputId, color) {
    document.getElementById(inputId).value = color;
}

function abrirEditarCategoria(cat) {
    const form = document.getElementById('editCategoriaForm');
    form.action = `/categorias/${cat.id}`;

    document.getElementById('editCatNombre').value = cat.nombre;
    document.getElementById('editCatDescripcion').value = cat.descripcion || '';
    document.getElementById('editCatIcono').value = cat.icono || 'fa-couch';
    document.getElementById('editCatColor').value = cat.color || '#ff6a3d';
    document.getElementById('editCatActivo').checked = !!cat.activo;

    const modal = new bootstrap.Modal(document.getElementById('editarCategoriaModal'));
    modal.show();
}
</script>
@endsection
