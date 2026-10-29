@extends('layouts.app')

@section('title', 'Reportes & Analítica Financiera')

@section('content')
<div class="container-fluid p-0">
    <!-- Header y Acciones -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Reportes & Estado de Resultados</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Rentabilidad neta, costos de materiales y muebles más vendidos</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reportes.exportarCsv', ['fecha_desde' => $fechaDesde, 'fecha_hasta' => $fechaHasta]) }}" class="btn btn-sm btn-success text-white fw-semibold d-flex align-items-center gap-2 px-3 rounded-3 shadow-sm">
                <i class="fa-solid fa-file-excel"></i> Exportar a Excel (CSV)
            </a>
            <button class="btn btn-sm btn-light border px-3 d-flex align-items-center gap-1 rounded-3" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Barra de Filtros por Fecha -->
    <div class="stat-card mb-4 py-3">
        <form action="{{ route('reportes.index') }}" method="GET" id="filtroForm" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 12px;">Fecha Desde:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-days text-secondary"></i></span>
                    <input type="date" name="fecha_desde" id="fechaDesdeInput" class="form-control" value="{{ $fechaDesde }}" required>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 12px;">Fecha Hasta:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-days text-secondary"></i></span>
                    <input type="date" name="fecha_hasta" id="fechaHastaInput" class="form-control" value="{{ $fechaHasta }}" required>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-sm text-white w-100 fw-semibold rounded-3" style="background: var(--primary-orange);">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar Datos
                </button>
            </div>
            <!-- Atajos rápidos -->
            <div class="col-md-4 col-sm-6 d-flex gap-1 justify-content-md-end">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 11px;" onclick="setFechas('hoy')">Hoy</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 11px;" onclick="setFechas('este_mes')">Este Mes</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 11px;" onclick="setFechas('mes_anterior')">Mes Pasado</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 11px;" onclick="setFechas('este_ano')">Este Año</button>
            </div>
        </form>
    </div>

    <!-- Balance Financiero (Cards de KPIs) -->
    <div class="row g-3 mb-4">
        <!-- Ingresos Ventas -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card py-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted" style="font-size: 12px;">Ingresos por Ventas</span>
                    <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">{{ $totalPedidos }} pedidos</span>
                </div>
                <div class="fs-4 fw-bold text-dark">S/. {{ number_format($totalVentas, 2) }}</div>
                <span class="text-success fw-bold" style="font-size: 11px;"><i class="fa-solid fa-arrow-trend-up me-1"></i>Ventas netas confirmadas</span>
            </div>
        </div>

        <!-- Costo de Mercancía -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card py-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted" style="font-size: 12px;">Costo de Mercancía / Madera</span>
                    <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">Costo Adq.</span>
                </div>
                <div class="fs-4 fw-bold text-danger">S/. {{ number_format($totalCostos, 2) }}</div>
                <span class="text-muted" style="font-size: 11px;">Margen Bruto: <strong>{{ $margenBruto }}%</strong></span>
            </div>
        </div>

        <!-- Gastos Operativos -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card py-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted" style="font-size: 12px;">Gastos Operativos & Fletes</span>
                    <span class="badge bg-warning-subtle text-warning" style="font-size: 10px;">Egresos</span>
                </div>
                <div class="fs-4 fw-bold text-warning">S/. {{ number_format($totalGastos, 2) }}</div>
                <span class="text-muted" style="font-size: 11px;">Fletes, suministros y taller</span>
            </div>
        </div>

        <!-- Utilidad Neta -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card py-3 h-100" style="background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted" style="font-size: 12px;">Utilidad Neta Real</span>
                    <span class="badge bg-success text-white" style="font-size: 10px;">Margen {{ $margenNeto }}%</span>
                </div>
                <div class="fs-4 fw-bold text-success">S/. {{ number_format($utilidadNeta, 2) }}</div>
                <span class="badge bg-success text-white px-2 py-1" style="font-size: 10px;">Ganancia Líquida Neta</span>
            </div>
        </div>
    </div>

    <!-- Tablas Analíticas de Desglose -->
    <div class="row g-4">
        <!-- Ranking de Muebles Más Vendidos -->
        <div class="col-lg-4 col-md-6">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="brand-font mb-0 text-dark">
                        <i class="fa-solid fa-trophy text-warning me-2"></i> Muebles Más Vendidos
                    </h5>
                    <span class="badge bg-light text-dark border">Top 5</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px;">
                                <th>Mueble</th>
                                <th>Precio</th>
                                <th class="text-end">Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProductos as $tp)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="{{ $tp->nombre }}">{{ $tp->nombre }}</div>
                                        <span class="text-muted" style="font-size: 10.5px;">{{ $tp->categoria->nombre ?? 'General' }}</span>
                                    </td>
                                    <td class="text-muted" style="font-size: 12px;">S/. {{ number_format($tp->precio_venta, 2) }}</td>
                                    <td class="text-end"><span class="badge bg-primary text-white">{{ $tp->venta_detalles_count }} und</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted" style="font-size: 12px;">Sin ventas en el período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Clientes Principales -->
        <div class="col-lg-4 col-md-6">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="brand-font mb-0 text-dark">
                        <i class="fa-solid fa-users text-primary me-2"></i> Clientes Principales
                    </h5>
                    <span class="badge bg-light text-dark border">Top 5</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px;">
                                <th>Cliente</th>
                                <th>Documento</th>
                                <th class="text-end">Compras</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topClientes as $tc)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="{{ $tc->nombre_completo }}">{{ $tc->nombre_completo }}</div>
                                        <span class="text-muted" style="font-size: 10.5px;">{{ $tc->celular ?? $tc->ciudad ?? '-' }}</span>
                                    </td>
                                    <td><span class="font-monospace text-muted" style="font-size: 11px;">{{ $tc->documento_numero ?? '-' }}</span></td>
                                    <td class="text-end"><span class="badge bg-success text-white">{{ $tc->ventas_count }} ped.</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted" style="font-size: 12px;">Sin clientes registrados en el período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Desglose de Gastos por Categoría -->
        <div class="col-lg-4 col-md-12">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="brand-font mb-0 text-dark">
                        <i class="fa-solid fa-receipt text-danger me-2"></i> Gastos por Categoría
                    </h5>
                    <span class="badge bg-light text-dark border">Egresos</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px;">
                                <th>Rubro / Concepto</th>
                                <th class="text-end">Total Pagado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gastosPorCategoria as $gc)
                                <tr>
                                    <td class="text-dark fw-semibold" style="font-size: 12px;">
                                        <i class="fa-solid fa-circle-dot text-warning me-1" style="font-size: 8px;"></i>
                                        {{ ucfirst(str_replace('_', ' ', $gc->categoria_gasto)) }}
                                    </td>
                                    <td class="text-end fw-bold text-danger" style="font-size: 12.5px;">
                                        S/. {{ number_format($gc->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted" style="font-size: 12px;">No se registraron gastos en el período</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setFechas(tipo) {
    const hoy = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const toYMD = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    let desde, hasta = toYMD(hoy);

    if (tipo === 'hoy') {
        desde = toYMD(hoy);
    } else if (tipo === 'este_mes') {
        desde = `${hoy.getFullYear()}-${pad(hoy.getMonth() + 1)}-01`;
    } else if (tipo === 'mes_anterior') {
        const primerDiaMesAnt = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
        const ultimoDiaMesAnt = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        desde = toYMD(primerDiaMesAnt);
        hasta = toYMD(ultimoDiaMesAnt);
    } else if (tipo === 'este_ano') {
        desde = `${hoy.getFullYear()}-01-01`;
    }

    document.getElementById('fechaDesdeInput').value = desde;
    document.getElementById('fechaHastaInput').value = hasta;
    document.getElementById('filtroForm').submit();
}
</script>
@endpush
@endsection
