@extends('layouts.app')

@section('title', 'Compras a Proveedores')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Órdenes de Compra a Proveedores</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Adquisición de lotes de madera, espumas, telas y recepción en almacén</span>
        </div>
        <a href="{{ route('compras.create') }}" class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" style="background: var(--primary-orange);">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>+ Nueva Orden de Compra</span>
        </a>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Compras Realizadas</div>
                <div class="fs-4 fw-bold text-dark">S/. {{ number_format($totalCompras, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Órdenes Pendientes de Recepción</div>
                <div class="fs-4 fw-bold text-warning">{{ $comprasPendientes }} por recibir</div>
            </div>
        </div>
    </div>

    <!-- Tabla Compras -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>N° Orden</th>
                        <th>Proveedor</th>
                        <th>Fecha Emisión</th>
                        <th>Entrega Esperada</th>
                        <th>Muebles / Partidas</th>
                        <th>Total (S/.)</th>
                        <th>Condición</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark font-monospace">{{ $compra->numero_orden }}</span>
                                @if($compra->factura_proveedor)
                                    <div class="text-muted" style="font-size: 11px;">Fact: {{ $compra->factura_proveedor }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $compra->proveedor->razon_social ?? 'Proveedor' }}</div>
                                <div class="text-muted" style="font-size: 11px;">RUC: {{ $compra->proveedor->ruc ?? '-' }}</div>
                            </td>
                            <td>{{ $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($compra->fecha_entrega_esperada)
                                    <span class="badge bg-light text-secondary border">{{ $compra->fecha_entrega_esperada->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @foreach($compra->detalles as $det)
                                    <div class="text-truncate" style="max-width: 200px; font-size: 12px;">
                                        • {{ $det->producto->nombre ?? 'Insumo' }} ({{ $det->cantidad }} unds)
                                    </div>
                                @endforeach
                            </td>
                            <td class="fw-bold text-dark">S/. {{ number_format($compra->total, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $compra->condicion_pago)) }}</span>
                            </td>
                            <td>
                                @if($compra->estado === 'recibido')
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-check me-1"></i> Recibido
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">
                                        <i class="fa-solid fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($compra->estado !== 'recibido')
                                    <form action="{{ route('compras.recibir', $compra->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2" title="Recibir en Almacén e Incrementar Stock">
                                            <i class="fa-solid fa-boxes-packing me-1"></i> Recibir
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted" style="font-size: 12px;"><i class="fa-solid fa-circle-check text-success"></i> Ingresado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No se registran órdenes de compra.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $compras->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
