@extends('layouts.app')

@section('title', 'Ventas & Pedidos')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Ventas, Pedidos & Entregas</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Control de transacciones, despachos a domicilio y armado de muebles</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.exportarCsv', request()->query()) }}" class="btn btn-light border px-3 py-2 fw-semibold rounded-3 d-flex align-items-center gap-2" title="Exportar reporte de ventas en CSV">
                <i class="fa-solid fa-file-excel text-success"></i>
                <span>Exportar Ventas</span>
            </a>
            <a href="{{ route('ventas.create') }}" class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" style="background: var(--primary-orange);">
                <i class="fa-solid fa-cart-plus"></i>
                <span>+ Nueva Venta / Pedido</span>
            </a>
        </div>
    </div>

    <!-- KPIs Rápidos -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Recaudado en Ventas</div>
                <div class="fs-4 fw-bold text-dark">S/. {{ number_format($totalRecaudado, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Pedidos en Ruta / Por Entregar</div>
                <div class="fs-4 fw-bold text-primary">{{ $pedidosPendientes }} pedidos</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Cotizaciones Abiertas</div>
                <div class="fs-4 fw-bold text-warning">{{ $cotizacionesCount }} cotizaciones</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('ventas.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Buscar por número de comprobante o cliente..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Estados</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="en_entrega" {{ request('estado') == 'en_entrega' ? 'selected' : '' }}>En Entrega / Ruta</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="cotizacion" {{ request('estado') == 'cotizacion' ? 'selected' : '' }}>Cotización</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="tipo_comprobante" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Comprobantes</option>
                    <option value="boleta" {{ request('tipo_comprobante') == 'boleta' ? 'selected' : '' }}>Boleta</option>
                    <option value="factura" {{ request('tipo_comprobante') == 'factura' ? 'selected' : '' }}>Factura</option>
                    <option value="nota_venta" {{ request('tipo_comprobante') == 'nota_venta' ? 'selected' : '' }}>Nota de Venta</option>
                    <option value="cotizacion" {{ request('tipo_comprobante') == 'cotizacion' ? 'selected' : '' }}>Cotización</option>
                </select>
            </div>
            <div class="col-md-1">
                <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-light border w-100">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabla de Ventas -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>N° Comprobante</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Muebles / Líneas</th>
                        <th>Condición</th>
                        <th>Total</th>
                        <th>Logística Mueblería</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $venta->numero_comprobante }}</div>
                                <span class="badge bg-light text-secondary border" style="font-size: 11px;">
                                    {{ ucfirst(str_replace('_', ' ', $venta->tipo_comprobante)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $venta->cliente->nombre_completo ?? 'Cliente Mostrador' }}</div>
                                <div class="text-muted" style="font-size: 11.5px;">{{ $venta->cliente->documento_numero ?? '-' }}</div>
                            </td>
                            <td>{{ $venta->fecha_emision ? $venta->fecha_emision->format('d/m/Y') : '-' }}</td>
                            <td>
                                @foreach($venta->detalles as $det)
                                    <div class="text-truncate" style="max-width: 220px; font-size: 12px;">
                                        • {{ $det->producto->nombre ?? 'Mueble' }} (x{{ $det->cantidad }})
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $venta->condicion_pago)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">S/. {{ number_format($venta->total, 2) }}</div>
                                @if($venta->saldo > 0)
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size: 10px;">Saldo: S/. {{ number_format($venta->saldo, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($venta->requiere_entrega)
                                    <div style="font-size: 12px;" class="text-primary">
                                        <i class="fa-solid fa-truck-ramp-box"></i>
                                        <strong>Entrega:</strong> {{ $venta->fecha_entrega_programada ? $venta->fecha_entrega_programada->format('d/m/Y') : 'Por programar' }}
                                    </div>
                                    @if($venta->requiere_armado)
                                        <span class="badge bg-info-subtle text-info fw-semibold" style="font-size: 10px;">
                                            <i class="fa-solid fa-wrench"></i> Incluye Armado
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size: 11.5px;">Recojo en local</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill {{ $venta->estado }}">
                                    {{ ucfirst(str_replace('_', ' ', $venta->estado)) }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 10px;">
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('ventas.imprimir', $venta->id) }}" target="_blank" style="font-size: 13px;">
                                                <i class="fa-solid fa-print text-secondary me-2"></i> Imprimir Comprobante
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('ventas.pdf', $venta->id) }}" style="font-size: 13px;">
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i> Descargar PDF
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if($venta->estado !== 'anulada')
                                            @if($venta->estado !== 'en_entrega' && $venta->requiere_entrega)
                                                <li>
                                                    <form action="{{ route('ventas.cambiarEstado', $venta->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="en_entrega">
                                                        <button type="submit" class="dropdown-item py-2 text-primary" style="font-size: 13px;">
                                                            <i class="fa-solid fa-truck me-2"></i> Poner En Ruta
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            @if($venta->estado !== 'completada')
                                                <li>
                                                    <form action="{{ route('ventas.cambiarEstado', $venta->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="completada">
                                                        <button type="submit" class="dropdown-item py-2 text-success" style="font-size: 13px;">
                                                            <i class="fa-solid fa-check-double me-2"></i> Marcar Entregado / Completado
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('ventas.cambiarEstado', $venta->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de anular el comprobante {{ $venta->numero_comprobante }}? Los muebles vendidos se reintegrarán al stock de almacén.')">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="anulada">
                                                    <button type="submit" class="dropdown-item py-2 text-danger" style="font-size: 13px;">
                                                        <i class="fa-solid fa-ban me-2"></i> Anular Venta (Devolver Stock)
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('ventas.cambiarEstado', $venta->id) }}" method="POST" onsubmit="return confirm('¿Desea reactivar esta venta? Se volverá a descontar el stock.')">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="completada">
                                                    <button type="submit" class="dropdown-item py-2 text-primary" style="font-size: 13px;">
                                                        <i class="fa-solid fa-rotate-left me-2"></i> Reactivar Venta
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No se encontraron ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $ventas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
