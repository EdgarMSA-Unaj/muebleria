@extends('layouts.app')

@section('title', 'Despachos & Armado de Muebles')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Logística de Despacho & Armado de Muebles</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Programación de rutas, móviles de reparto y ensamblado en el domicilio del cliente</span>
        </div>
    </div>

    <!-- KPIs de Entrega -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Muebles en Ruta / Pendientes</div>
                <div class="fs-4 fw-bold text-primary">{{ $pendientesEntrega }} entregas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Servicios con Armado Incluido</div>
                <div class="fs-4 fw-bold text-warning">{{ $conArmado }} pedidos con armado</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Entregados & Conformados este Mes</div>
                <div class="fs-4 fw-bold text-success">{{ $entregadosMes }} entregas</div>
            </div>
        </div>
    </div>

    <!-- Filtro por Fecha y Estado -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('despachos.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label text-muted mb-0" style="font-size: 11px;">Fecha de Entrega</label>
                <input type="date" name="fecha" class="form-control form-control-sm bg-light" value="{{ request('fecha') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted mb-0" style="font-size: 11px;">Estado del Envío</label>
                <select name="estado" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente de Asignación</option>
                    <option value="en_entrega" {{ request('estado') == 'en_entrega' ? 'selected' : '' }}>En Ruta / Móvil Asignado</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Entregado y Armado</option>
                </select>
            </div>
            <div class="col-md-4 text-end pt-3">
                <a href="{{ route('despachos.index') }}" class="btn btn-sm btn-light border px-3">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Lista de Despachos -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente & Teléfono</th>
                        <th>Dirección de Destino</th>
                        <th>Fecha de Entrega</th>
                        <th>Muebles a Instalar</th>
                        <th>Móvil / Repartidor</th>
                        <th>Armado</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($despachos as $item)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $item->numero_comprobante }}</span>
                                <div class="text-muted" style="font-size: 11px;">{{ ucfirst($item->tipo_comprobante) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->cliente->nombre_completo ?? 'Cliente' }}</div>
                                <div class="text-muted" style="font-size: 11.5px;">
                                    <i class="fa-solid fa-phone me-1"></i>{{ $item->cliente->celular ?? $item->cliente->telefono ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                    {{ $item->direccion_entrega ?? $item->cliente->direccion ?? 'Dirección en tienda' }}
                                </div>
                                @if($item->referencia_entrega)
                                    <div class="text-muted" style="font-size: 11px;">Ref: {{ $item->referencia_entrega }}</div>
                                @endif
                            </td>
                            <td>
                                @if($item->fecha_entrega_programada)
                                    <span class="badge bg-light text-dark border fw-bold" style="font-size: 12px;">
                                        {{ $item->fecha_entrega_programada->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Sin programar</span>
                                @endif
                            </td>
                            <td>
                                @foreach($item->detalles as $det)
                                    <div class="text-truncate" style="max-width: 180px; font-size: 11.5px;">
                                        <i class="fa-solid fa-couch text-muted me-1"></i>
                                        {{ $det->producto->nombre ?? 'Mueble' }} (x{{ $det->cantidad }})
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @if($item->repartidor_asignado)
                                    <span class="badge bg-primary-subtle text-primary fw-semibold" style="font-size: 11px;">
                                        <i class="fa-solid fa-truck me-1"></i> {{ $item->repartidor_asignado }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 11px;">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($item->requiere_armado)
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size: 11px;">
                                        <i class="fa-solid fa-screwdriver-wrench me-1"></i> Requiere Armado
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 11px;">Solo descarga</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill {{ $item->estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->estado)) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light border py-1 px-2" title="Asignar Móvil / Chofer" data-bs-toggle="modal" data-bs-target="#asignarModal{{ $item->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No hay pedidos pendientes de despacho a domicilio.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $despachos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modales de Asignación de Despacho (Fuera de la tabla) -->
@foreach($despachos as $item)
    <div class="modal fade" id="asignarModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title brand-font">Programar Entrega — {{ $item->numero_comprobante }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('despachos.asignar', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3 bg-light rounded-3 mb-3" style="font-size: 12.5px;">
                            <div><strong>Cliente:</strong> {{ $item->cliente->nombre_completo ?? 'Cliente' }}</div>
                            <div><strong>Dirección:</strong> {{ $item->direccion_entrega ?? $item->cliente->direccion }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha Programada de Entrega</label>
                            <input type="date" name="fecha_entrega_programada" class="form-control" value="{{ $item->fecha_entrega_programada ? $item->fecha_entrega_programada->format('Y-m-d') : date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Móvil / Repartidor e Instalador Asignado</label>
                            <input type="text" name="repartidor_asignado" class="form-control" value="{{ $item->repartidor_asignado ?? 'Camión 1 - Transportes Ramos (Instalador Jorge)' }}" placeholder="Ej. Camión 2 - Juan y Pedro" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Confirmar Asignación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
