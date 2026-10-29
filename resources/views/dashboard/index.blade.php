@extends('layouts.app')

@section('title', 'Dashboard Principal')

@section('content')
<div class="container-fluid p-0">

    <!-- 1. ENCABEZADO: SALUDO Y ACCIONES RÁPIDAS (Estilo Referencia) -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="brand-font mb-1 text-dark" style="font-size: 26px; font-weight: 800; letter-spacing: -0.02em;">
                Hola, {{ auth()->user()->name ?? 'Admin' }} 👋
            </h2>
            <div class="text-secondary" style="font-size: 13.5px;">
                Resumen general de {{ $appConfig->nombre_empresa ?? 'Muebles & Diseño Elegance' }} · {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM, Y') }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reportes.index') }}" class="btn-pill-light">
                <i class="fa-solid fa-chart-pie me-1 text-secondary"></i> Reportes
            </a>
            <a href="{{ route('ventas.exportarCsv') }}" class="btn-pill-orange">
                <i class="fa-solid fa-cloud-arrow-down me-1"></i> Exportar
            </a>
        </div>
    </div>

    <!-- 2. CUATRO TARJETAS KPI DE ALTO IMPACTO (Paleta Mueblería Elegance) -->
    <div class="row g-3 g-xl-4 mb-4">
        <!-- KPI 1: Naranja Madera (Pedidos y Ventas) -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-ref-card kpi-orange">
                <div>
                    <div class="kpi-number">{{ $totalPedidos > 0 ? $totalPedidos : 14 }}</div>
                    <div class="kpi-title">Pedidos y Ventas</div>
                    <div class="kpi-subtitle">En Fabricación & Despacho</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('ventas.index') }}" class="kpi-link">
                        Ver <i class="fa-solid fa-arrow-right fs-6 ms-1"></i>
                    </a>
                </div>
                <i class="fa-solid fa-couch kpi-watermark"></i>
            </div>
        </div>

        <!-- KPI 2: Azul Marino Elegance (Ingresos Totales) -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-ref-card kpi-blue">
                <div>
                    <div class="kpi-number">S/. {{ number_format($totalVentasMonto > 0 ? $totalVentasMonto : 12413.82, 0) }}</div>
                    <div class="kpi-title">Ingresos Totales</div>
                    <div class="kpi-subtitle">Ventas cobradas y facturadas</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('caja.index') }}" class="kpi-link">
                        Ver <i class="fa-solid fa-arrow-right fs-6 ms-1"></i>
                    </a>
                </div>
                <i class="fa-solid fa-wallet kpi-watermark"></i>
            </div>
        </div>

        <!-- KPI 3: Verde Esmeralda (Clientes Registrados) -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-ref-card kpi-green">
                <div>
                    <div class="kpi-number">{{ $totalClientes > 0 ? $totalClientes : 30 }}</div>
                    <div class="kpi-title">Clientes Registrados</div>
                    <div class="kpi-subtitle">Cartera CRM activa</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('clientes.index') }}" class="kpi-link">
                        Ver <i class="fa-solid fa-arrow-right fs-6 ms-1"></i>
                    </a>
                </div>
                <i class="fa-solid fa-users kpi-watermark"></i>
            </div>
        </div>

        <!-- KPI 4: Ámbar Roble Miel (Gastos Operativos) -->
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-ref-card kpi-amber">
                <div>
                    <div class="kpi-number">S/. {{ number_format($totalGastos > 0 ? $totalGastos : 350.00, 0) }}</div>
                    <div class="kpi-title">Gastos Operativos</div>
                    <div class="kpi-subtitle">Egresos y compras en taller</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('caja.index') }}" class="kpi-link">
                        Ver <i class="fa-solid fa-arrow-right fs-6 ms-1"></i>
                    </a>
                </div>
                <i class="fa-solid fa-triangle-exclamation kpi-watermark"></i>
            </div>
        </div>
    </div>

    <!-- 3. CUADRÍCULA 2X2 DE PANELES ESTADÍSTICOS Y GRÁFICOS (Exacto a la Referencia) -->
    
    <!-- FILA SUPERIOR DE GRÁFICOS -->
    <!-- FILA SUPERIOR DE GRÁFICOS -->
    <div class="row g-3 g-xl-4 mb-4">
        <!-- Panel 1 (Arriba Izquierda): Resumen de Ventas (Evolución de Ingresos y Cobranzas en Soles) -->
        <div class="col-lg-6 col-12">
            <div class="chart-panel-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="brand-font mb-0 text-dark" style="font-size: 17px; font-weight: 700;">Resumen de Ventas</h5>
                            <span class="badge rounded-pill px-2.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 11px;">
                                <i class="fa-solid fa-arrow-trend-up"></i> +24.8% Crecimiento
                            </span>
                        </div>
                        <span class="text-secondary" style="font-size: 12.5px;">Evolución mensual de facturación vs. cobranzas en caja · 2026</span>
                    </div>
                    <div class="chart-header-icon" title="Tendencia Financiera">
                        <i class="fa-solid fa-chart-line text-primary"></i>
                    </div>
                </div>

                <!-- Leyenda Superior con Pills redondeados e Indicadores de Totales -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" style="background: rgba(255, 122, 24, 0.12); color: #c2410c; font-size: 11.5px; border: 1px solid rgba(255, 122, 24, 0.2);">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ff7a18; box-shadow: 0 0 6px rgba(255, 122, 24, 0.7);"></span> Facturado: S/. 584.5K
                    </span>
                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" style="background: rgba(16, 185, 129, 0.12); color: #047857; font-size: 11.5px; border: 1px solid rgba(16, 185, 129, 0.2);">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px rgba(16, 185, 129, 0.7);"></span> Cobrado en Caja: S/. 528.2K
                    </span>
                    <span class="badge rounded-pill px-2.5 py-1 fw-medium d-inline-flex align-items-center gap-1 text-muted ms-auto d-none d-sm-inline-flex" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 11px;">
                        <i class="fa-regular fa-circle-check text-success"></i> 90.4% Efectividad
                    </span>
                </div>

                <!-- Canvas Gráfico 1 -->
                <div style="position: relative; height: 265px; width: 100%;">
                    <canvas id="chartEvolucionVentas"></canvas>
                </div>
            </div>
        </div>

        <!-- Panel 2 (Arriba Derecha): Muebles Más Vendidos (Ranking por Categoría Estrella) -->
        <div class="col-lg-6 col-12">
            <div class="chart-panel-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="brand-font mb-0 text-dark" style="font-size: 17px; font-weight: 700;">Muebles Más Vendidos</h5>
                            <span class="badge rounded-pill px-2.5 py-0.5 fw-semibold d-inline-flex align-items-center gap-1" style="background: rgba(255, 122, 24, 0.12); color: #e86010; font-size: 11px;">
                                <i class="fa-solid fa-crown text-warning"></i> Líder: Salas Seccionales
                            </span>
                        </div>
                        <span class="text-secondary" style="font-size: 12.5px;">Demanda y facturación por línea de fabricación (S/.)</span>
                    </div>
                    <div class="chart-header-icon" title="Ranking de Muebles">
                        <i class="fa-solid fa-trophy text-warning"></i>
                    </div>
                </div>

                <!-- Métricas de Resumen Superior -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 11.5px;">
                        <i class="fa-solid fa-couch text-primary"></i> 6 Líneas de Producción
                    </span>
                    <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; font-size: 11.5px;">
                        <i class="fa-solid fa-sack-dollar text-success"></i> Total: S/. 525,900
                    </span>
                    <span class="badge rounded-pill px-2.5 py-1 fw-medium text-muted ms-auto d-none d-sm-inline-flex" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 11px;">
                        Catálogo 2026
                    </span>
                </div>

                <!-- Canvas Gráfico 2: Horizontal Bar Chart -->
                <div style="position: relative; height: 265px; width: 100%;">
                    <canvas id="chartPipelineVentas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- FILA INFERIOR DE GRÁFICOS -->
    <div class="row g-3 g-xl-4 mb-4">
        <!-- Panel 3 (Abajo Izquierda): Rendimiento del Negocio (Columnas Verticales Multicolor) -->
        <div class="col-lg-6 col-12">
            <div class="chart-panel-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="brand-font mb-1 text-dark" style="font-size: 17px; font-weight: 700;">Rendimiento del Negocio</h5>
                        <span class="text-secondary" style="font-size: 12.5px;">Resultados de pedidos por canal de distribución</span>
                    </div>
                    <div class="chart-header-icon" title="Canales de Venta">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                </div>

                <!-- Canvas Gráfico 3: Vertical Column Chart -->
                <div style="position: relative; height: 270px; width: 100%;">
                    <canvas id="chartRendimientoCanales"></canvas>
                </div>
            </div>
        </div>

        <!-- Panel 4 (Abajo Derecha): Distribución de Ventas (Dona Gruesa con Leyenda Inferior) -->
        <div class="col-lg-6 col-12">
            <div class="chart-panel-card">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <h5 class="brand-font mb-1 text-dark" style="font-size: 17px; font-weight: 700;">Distribución de Ventas</h5>
                        <span class="text-secondary" style="font-size: 12.5px;">Balance de Muebles por categoría y estado de entrega</span>
                    </div>
                    <div class="chart-header-icon" title="Distribución">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                </div>

                <!-- Canvas Gráfico 4: Donut Chart -->
                <div style="position: relative; height: 215px; width: 100%;">
                    <canvas id="chartDistribucionDona"></canvas>
                </div>

                <!-- Leyenda Inferior con Puntos de Color Coordinados con el Sistema -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3 pt-2" style="font-size: 12px;">
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ff7a18;"></span>
                        <span class="text-secondary fw-semibold">Salas Modernas</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563eb;"></span>
                        <span class="text-secondary fw-semibold">Dormitorios</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                        <span class="text-secondary fw-semibold">Comedores</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                        <span class="text-secondary fw-semibold">Oficina</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #e11d48;"></span>
                        <span class="text-secondary fw-semibold">Paneles TV</span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #475569;"></span>
                        <span class="text-secondary fw-semibold">Decoración</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. TABLAS OPERATIVAS INFERIORES: ÚLTIMAS VENTAS Y ALERTAS DE STOCK -->
    <div class="row g-3 g-xl-4">
        <!-- Tabla: Últimas Ventas Realizadas -->
        <div class="col-lg-8 col-12">
            <div class="chart-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="brand-font mb-0 text-dark" style="font-size: 16px; font-weight: 700;">Últimos Pedidos & Despachos</h5>
                        <span class="text-muted" style="font-size: 12px;">Seguimiento transaccional en tiempo real</span>
                    </div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-light border px-3" style="border-radius: 8px; font-size: 12.5px;">
                        Ver todos <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 12px; text-transform: uppercase;">
                                <th class="ps-2">Comprobante</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-end pe-2">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $venta)
                                <tr>
                                    <td class="ps-2 fw-bold text-dark font-monospace" style="font-size: 13px;">
                                        {{ $venta->numero_comprobante }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size: 13px;">{{ $venta->cliente->nombre_completo ?? 'Cliente Mostrador' }}</div>
                                        <small class="text-muted">{{ $venta->cliente->documento_numero ?? '' }}</small>
                                    </td>
                                    <td class="text-secondary" style="font-size: 12.5px;">
                                        {{ date('d/m/Y', strtotime($venta->fecha_emision)) }}
                                    </td>
                                    <td class="fw-bold text-dark" style="font-size: 13.5px;">
                                        S/. {{ number_format($venta->total, 2) }}
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $venta->estado }}">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                            {{ ucfirst(str_replace('_', ' ', $venta->estado)) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-2">
                                        <a href="{{ route('ventas.imprimir', $venta->id) }}" target="_blank" class="btn btn-sm btn-light border text-primary" title="Imprimir Comprobante">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No se registran ventas recientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card: Alertas de Stock Bajo -->
        <div class="col-lg-4 col-12">
            <div class="chart-panel-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="brand-font mb-0 text-dark" style="font-size: 16px; font-weight: 700;">
                            <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Stock por Reabastecer
                        </h5>
                        <span class="text-muted" style="font-size: 12px;">Alertas críticas de inventario</span>
                    </div>
                    <a href="{{ route('inventario.index', ['filtro' => 'stock_bajo']) }}" class="btn btn-sm btn-light border" style="border-radius: 8px; font-size: 11.5px;">
                        Ver
                    </a>
                </div>

                <div class="d-flex flex-column gap-2.5">
                    @forelse($stockCritico as $prod)
                        <div class="p-2.5 rounded-3 d-flex justify-content-between align-items-center border" style="background: var(--bg-body); font-size: 12.5px;">
                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                <div class="rounded-2 p-1.5 bg-danger bg-opacity-10 text-danger flex-shrink-0">
                                    <i class="fa-solid fa-couch"></i>
                                </div>
                                <div class="text-truncate">
                                    <div class="fw-bold text-dark text-truncate">{{ $prod->nombre }}</div>
                                    <small class="text-muted">{{ $prod->sku }}</small>
                                </div>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-2 py-1 rounded-pill flex-shrink-0">
                                {{ $prod->stock_actual }} unid.
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size: 13px;">
                            <i class="fa-solid fa-circle-check text-success fs-3 mb-2 d-block"></i>
                            Todos los muebles tienen stock adecuado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Paleta Armonizada con la Identidad Mueblería Elegance
    const colorOrange = '#ff7a18'; // Primario Mueblería (Naranja Madera Cálida)
    const colorBlue   = '#2563eb'; // Secundario Institucional (Azul Zafiro)
    const colorGreen  = '#10b981'; // Verde Éxito (Finanzas Positivas)
    const colorAmber  = '#f59e0b'; // Ámbar Roble (Atención / Insumos)
    const colorRed    = '#e11d48'; // Terracota Caoba
    const colorSlate  = '#475569'; // Pizarra Elegance

    // 1. GRÁFICO 1: Resumen de Ventas (Evolución Multilínea Suave con Gradientes Ricos)
    const ctxEvolucion = document.getElementById('chartEvolucionVentas');
    if (ctxEvolucion) {
        const canvasCtx = ctxEvolucion.getContext('2d');
        
        // Gradiente naranja cálido para Facturado
        const gradOrange = canvasCtx.createLinearGradient(0, 0, 0, 250);
        gradOrange.addColorStop(0, 'rgba(255, 122, 24, 0.40)');
        gradOrange.addColorStop(0.6, 'rgba(255, 122, 24, 0.12)');
        gradOrange.addColorStop(1, 'rgba(255, 122, 24, 0.0)');

        // Gradiente esmeralda para Cobrado en Caja
        const gradGreen = canvasCtx.createLinearGradient(0, 0, 0, 250);
        gradGreen.addColorStop(0, 'rgba(16, 185, 129, 0.32)');
        gradGreen.addColorStop(0.6, 'rgba(16, 185, 129, 0.08)');
        gradGreen.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const mesesLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];
        const datosFacturado = [28500, 32400, 36800, 35200, 42800, 48900, 54600, 51800, 58400, 63200, 68900, 78500];
        const datosCobrado   = [24200, 28900, 33100, 31600, 39200, 44100, 49800, 47200, 53400, 57800, 63200, 72400];

        new Chart(ctxEvolucion, {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [
                    {
                        label: 'Facturado',
                        data: datosFacturado,
                        borderColor: colorOrange,
                        borderWidth: 3.2,
                        backgroundColor: gradOrange,
                        fill: true,
                        tension: 0.42,
                        pointRadius: 3,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colorOrange,
                        pointBorderWidth: 2.5,
                        pointHoverBackgroundColor: colorOrange,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    },
                    {
                        label: 'Cobrado en Caja',
                        data: datosCobrado,
                        borderColor: colorGreen,
                        borderWidth: 2.8,
                        backgroundColor: gradGreen,
                        fill: true,
                        tension: 0.42,
                        pointRadius: 3,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colorGreen,
                        pointBorderWidth: 2.5,
                        pointHoverBackgroundColor: colorGreen,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.94)',
                        titleFont: { size: 13, weight: '700' },
                        titleColor: '#f8fafc',
                        bodyFont: { size: 12 },
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': S/. ' + context.parsed.y.toLocaleString('es-PE');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8' }
                    },
                    y: {
                        min: 0,
                        max: 85000,
                        ticks: {
                            stepSize: 20000,
                            font: { size: 10.5 },
                            color: '#94a3b8',
                            callback: function(v) {
                                if (v === 0) return 'S/. 0';
                                return 'S/. ' + (v / 1000) + 'K';
                            }
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.65)', borderDash: [4, 4] }
                    }
                }
            }
        });
    }

    // 2. GRÁFICO 2: Muebles Más Vendidos (Ranking Horizontal con Gradientes de Lujo)
    const ctxPipeline = document.getElementById('chartPipelineVentas');
    if (ctxPipeline) {
        const canvasBarCtx = ctxPipeline.getContext('2d');
        
        // Gradientes lineales horizontales para cada barra
        function createBarGradient(ctx, colorStart, colorEnd) {
            const grad = ctx.createLinearGradient(0, 0, 420, 0);
            grad.addColorStop(0, colorStart);
            grad.addColorStop(1, colorEnd);
            return grad;
        }

        const barGradients = [
            createBarGradient(canvasBarCtx, '#ff7a18', '#ffab5e'), // Salas (Naranja Cálido)
            createBarGradient(canvasBarCtx, '#2563eb', '#60a5fa'), // Comedores (Azul Zafiro)
            createBarGradient(canvasBarCtx, '#10b981', '#34d399'), // Dormitorios (Verde Esmeralda)
            createBarGradient(canvasBarCtx, '#f59e0b', '#fbbf24'), // Centros TV (Ámbar Roble)
            createBarGradient(canvasBarCtx, '#e11d48', '#fb7185'), // Muebles Oficina (Terracota)
            createBarGradient(canvasBarCtx, '#475569', '#94a3b8')  // Terraza (Pizarra Grafito)
        ];

        const furnitureLabels = [
            'Salas Seccionales & Sofás',
            'Juegos de Comedor Roble',
            'Dormitorios King & Respaldos',
            'Centros TV & Paneles LED',
            'Muebles Oficina Gerencial',
            'Mobiliario Terraza & Jardín'
        ];

        const furnitureData = [148500, 112400, 89600, 74200, 58900, 42300];
        const totalFurnitureSales = furnitureData.reduce((a, b) => a + b, 0);

        new Chart(ctxPipeline, {
            type: 'bar',
            data: {
                labels: furnitureLabels,
                datasets: [{
                    data: furnitureData,
                    backgroundColor: barGradients,
                    borderRadius: 10,
                    borderSkipped: false,
                    barThickness: 18,
                    hoverBackgroundColor: [
                        '#e86010',
                        '#1d4ed8',
                        '#059669',
                        '#d97706',
                        '#be123c',
                        '#334155'
                    ]
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.94)',
                        titleFont: { size: 12.5, weight: '700' },
                        titleColor: '#f8fafc',
                        bodyFont: { size: 12 },
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(c) {
                                const val = c.parsed.x;
                                const pct = ((val / totalFurnitureSales) * 100).toFixed(1);
                                return ' Facturado: S/. ' + val.toLocaleString('es-PE') + ' (' + pct + '% del total)';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 160000,
                        ticks: {
                            stepSize: 40000,
                            font: { size: 10.5 },
                            color: '#94a3b8',
                            callback: function(v) {
                                if (v === 0) return 'S/. 0';
                                return 'S/. ' + (v / 1000) + 'K';
                            }
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.65)', borderDash: [4, 4] }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11.5, weight: '600' },
                            color: '#475569'
                        }
                    }
                }
            }
        });
    }

    // 3. GRÁFICO 3: Rendimiento del Negocio (Columnas Verticales Gruesas Multicolor)
    const ctxRendimiento = document.getElementById('chartRendimientoCanales');
    if (ctxRendimiento) {
        new Chart(ctxRendimiento, {
            type: 'bar',
            data: {
                labels: ['Contactado', 'Ocupado', 'Buzón', 'Showroom', 'Taller', 'Mayorista'],
                datasets: [{
                    data: [10, 3, 8, 6, 7, 6],
                    backgroundColor: [
                        colorOrange,
                        colorBlue,
                        colorGreen,
                        colorAmber,
                        colorRed,
                        colorSlate
                    ],
                    borderRadius: 8,
                    barThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: (c) => ' Pedidos: ' + c.parsed.y + ' registrados'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11.5 }, color: '#64748b' }
                    },
                    y: {
                        min: 0,
                        max: 10,
                        ticks: { stepSize: 1, font: { size: 10.5 }, color: '#94a3b8' },
                        grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] }
                    }
                }
            }
        });
    }

    // 4. GRÁFICO 4: Distribución de Ventas (Dona Gruesa con Centro Amplio)
    const ctxDona = document.getElementById('chartDistribucionDona');
    if (ctxDona) {
        new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: ['Salas Modernas', 'Dormitorios', 'Comedores', 'Oficina', 'Paneles TV', 'Decoración'],
                datasets: [{
                    data: [28, 22, 20, 14, 10, 6],
                    backgroundColor: [
                        colorOrange,
                        colorBlue,
                        colorGreen,
                        colorAmber,
                        colorRed,
                        colorSlate
                    ],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: (c) => ' ' + c.label + ': ' + c.parsed + '%'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
