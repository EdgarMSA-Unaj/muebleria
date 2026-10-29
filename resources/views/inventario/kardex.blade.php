@extends('layouts.app')

@section('title', 'Kardex de Inventario')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Kardex de Inventario & Movimientos</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Trazabilidad completa de ingresos por fábrica/compras y salidas por ventas</span>
        </div>
        <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-light border px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Catálogo
        </a>
    </div>

    <!-- Filtros -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('inventario.kardex') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="producto_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Muebles del Catálogo</option>
                    @foreach($productos as $pr)
                        <option value="{{ $pr->id }}" {{ request('producto_id') == $pr->id ? 'selected' : '' }}>
                            {{ $pr->nombre }} (SKU: {{ $pr->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="tipo_movimiento" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todos los Tipos de Movimiento</option>
                    <option value="entrada_compra" {{ request('tipo_movimiento') == 'entrada_compra' ? 'selected' : '' }}>Entrada por Compra / Proveedor</option>
                    <option value="salida_venta" {{ request('tipo_movimiento') == 'salida_venta' ? 'selected' : '' }}>Salida por Venta / Despacho</option>
                    <option value="ajuste_positivo" {{ request('tipo_movimiento') == 'ajuste_positivo' ? 'selected' : '' }}>Ajuste Positivo (Fabricación Taller)</option>
                    <option value="ajuste_negativo" {{ request('tipo_movimiento') == 'ajuste_negativo' ? 'selected' : '' }}>Ajuste Negativo (Merma / Daño)</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <a href="{{ route('inventario.kardex') }}" class="btn btn-sm btn-light border w-100">Restablecer Filtro</a>
            </div>
        </form>
    </div>

    <!-- Tabla Kardex -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Fecha & Hora</th>
                        <th>Mueble & Categoría</th>
                        <th>Almacén</th>
                        <th>Tipo de Movimiento</th>
                        <th>Cant.</th>
                        <th>Stock Anterior</th>
                        <th>Stock Resultante</th>
                        <th>Motivo / Referencia</th>
                        <th>Operador</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td class="text-nowrap" style="font-size: 12px;">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $mov->producto->nombre ?? 'Mueble' }}</div>
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">{{ $mov->producto->sku ?? '-' }}</span>
                            </td>
                            <td>{{ $mov->almacen->nombre ?? 'Almacén Central' }}</td>
                            <td>
                                @if(str_contains($mov->tipo_movimiento, 'entrada') || str_contains($mov->tipo_movimiento, 'positivo'))
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-arrow-down me-1"></i> {{ ucfirst(str_replace('_', ' ', $mov->tipo_movimiento)) }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="fa-solid fa-arrow-up me-1"></i> {{ ucfirst(str_replace('_', ' ', $mov->tipo_movimiento)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="fw-bold fs-6 {{ str_contains($mov->tipo_movimiento, 'entrada') || str_contains($mov->tipo_movimiento, 'positivo') ? 'text-success' : 'text-danger' }}">
                                {{ str_contains($mov->tipo_movimiento, 'entrada') || str_contains($mov->tipo_movimiento, 'positivo') ? '+' : '-' }}{{ $mov->cantidad }}
                            </td>
                            <td class="text-secondary">{{ $mov->stock_anterior }}</td>
                            <td class="fw-bold text-dark">{{ $mov->stock_nuevo }}</td>
                            <td style="font-size: 12px;">
                                <div class="text-dark">{{ $mov->motivo ?? 'Sin observaciones' }}</div>
                                @if($mov->referencia_tipo)
                                    <span class="text-muted" style="font-size: 10.5px;">Ref: {{ $mov->referencia_tipo }} #{{ $mov->referencia_id }}</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 12px;">{{ $mov->usuario->name ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No se registran movimientos en el kardex.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $movimientos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
