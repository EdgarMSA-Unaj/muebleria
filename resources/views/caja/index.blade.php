@extends('layouts.app')

@section('title', 'Caja & Gastos Operativos')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Caja Diaria & Gastos Operativos</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Control de flujo de efectivo, fletes de taller y arqueo de turnos</span>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-danger text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevoGastoModal">
                <i class="fa-solid fa-receipt"></i>
                <span>+ Registrar Gasto</span>
            </button>
            @if(!$cajaActual)
                <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#abrirCajaModal" style="background: var(--primary-orange);">
                    <i class="fa-solid fa-lock-open"></i>
                    <span>Abrir Caja Turno</span>
                </button>
            @else
                <button class="btn btn-dark fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#cerrarCajaModal">
                    <i class="fa-solid fa-lock"></i>
                    <span>Cerrar Caja / Arqueo</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Estado de Caja Actual -->
    @if($cajaActual)
        <div class="stat-card mb-4 border-primary" style="border-left: 4px solid var(--primary-orange) !important;">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 122, 24, 0.15); color: var(--primary-orange); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <h5 class="brand-font mb-0 text-dark">{{ $cajaActual->nombre_caja }}</h5>
                        <span class="text-muted" style="font-size: 12px;">
                            Aperturado por {{ $cajaActual->usuario->name ?? 'Admin' }} el {{ $cajaActual->fecha_apertura->format('d/m/Y H:i A') }}
                        </span>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> Caja Abierta
                </span>
            </div>

            <div class="row g-3 text-center">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="p-2 bg-light rounded-3">
                        <div class="text-muted" style="font-size: 11px;">Monto Inicial</div>
                        <div class="fw-bold text-dark fs-6">S/. {{ number_format($cajaActual->monto_inicial, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="p-2 bg-light rounded-3">
                        <div class="text-muted" style="font-size: 11px;">Ventas Efectivo</div>
                        <div class="fw-bold text-success fs-6">+ S/. {{ number_format($cajaActual->monto_ventas_efectivo, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="p-2 bg-light rounded-3">
                        <div class="text-muted" style="font-size: 11px;">Ventas Digital (POS/Yape)</div>
                        <div class="fw-bold text-primary fs-6">+ S/. {{ number_format($cajaActual->monto_ventas_digital, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 col-6">
                    <div class="p-2 bg-light rounded-3">
                        <div class="text-muted" style="font-size: 11px;">Gastos / Salidas</div>
                        <div class="fw-bold text-danger fs-6">- S/. {{ number_format($cajaActual->monto_gastos, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                        <div style="font-size: 11px; opacity: 0.8;">Efectivo Esperado en Gaveta</div>
                        <div class="fw-bold fs-5 text-warning">S/. {{ number_format($cajaActual->monto_final_esperado, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning p-4 rounded-4 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-lock fs-1 text-warning"></i>
                <div>
                    <h5 class="fw-bold mb-0">La caja se encuentra cerrada</h5>
                    <div style="font-size: 13px;">Debe aperturar un turno de caja para registrar pagos en efectivo y movimientos del día.</div>
                </div>
            </div>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#abrirCajaModal">Aperturar Turno Ahora</button>
        </div>
    @endif

    <!-- Tabla de Gastos Operativos Recientes -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="brand-font mb-0 text-dark">Gastos Operativos & Egresos de Taller</h5>
            <span class="badge bg-light text-secondary border">Total Mes: S/. {{ number_format($totalGastosMes, 2) }}</span>
        </div>
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Categoría de Gasto</th>
                        <th>Descripción del Gasto</th>
                        <th>Comprobante</th>
                        <th>Monto (S/.)</th>
                        <th>Registrado Por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $gasto)
                        <tr>
                            <td>{{ $gasto->fecha_gasto->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $gasto->categoria_gasto }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $gasto->descripcion }}</td>
                            <td>
                                <span class="font-monospace text-muted" style="font-size: 11.5px;">
                                    {{ $gasto->comprobante_tipo ?? '-' }} {{ $gasto->comprobante_numero ?? '' }}
                                </span>
                            </td>
                            <td class="fw-bold text-danger">- S/. {{ number_format($gasto->monto, 2) }}</td>
                            <td class="text-secondary" style="font-size: 12px;">{{ $gasto->usuario->name ?? 'Admin' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se registran gastos recientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar Gasto -->
<div class="modal fade" id="nuevoGastoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Registrar Gasto / Egreso de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caja.gasto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoría del Gasto</label>
                        <select name="categoria_gasto" class="form-select" required>
                            <option value="Transporte & Fletes">Transporte & Fletes de Muebles</option>
                            <option value="Insumos de Taller">Insumos de Taller (Lijas, Colas, Tornillos)</option>
                            <option value="Mantenimiento Maquinaria">Mantenimiento de Maquinaria (Sierras, Tupis)</option>
                            <option value="Servicios Básicos">Servicios Básicos (Luz de Taller, Agua, Internet)</option>
                            <option value="Refrigerio / Viáticos">Refrigerio / Viáticos de Instaladores</option>
                            <option value="Otros Egresos">Otros Egresos Varios</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción Detallada</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej. Combustible de camión repartidor para ruta San Isidro" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Monto (S/.)</label>
                            <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00" min="0.1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha_gasto" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipo Comprobante</label>
                            <select name="comprobante_tipo" class="form-select">
                                <option value="Recibo Simple">Recibo Simple</option>
                                <option value="Factura">Factura</option>
                                <option value="Boleta">Boleta</option>
                                <option value="Ticket">Ticket</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">N° Comprobante</label>
                            <input type="text" name="comprobante_numero" class="form-control" placeholder="Ej. B001-4821">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Abrir Caja -->
<div class="modal fade" id="abrirCajaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Aperturar Turno de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caja.abrir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre / Turno de Caja</label>
                        <input type="text" name="nombre_caja" class="form-control" value="Caja Principal - Turno Mañana" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto Inicial (Suelto / Sencillo en Gaveta)</label>
                        <div class="input-group">
                            <span class="input-group-text">S/.</span>
                            <input type="number" step="0.01" name="monto_inicial" class="form-control" value="500.00" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Aperturar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->
@if($cajaActual)
<div class="modal fade" id="cerrarCajaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Cierre y Arqueo de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caja.cerrar', $cajaActual->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="text-muted" style="font-size: 12px;">Efectivo calculado por sistema:</div>
                        <div class="fw-bold fs-4 text-dark">S/. {{ number_format($cajaActual->monto_final_esperado, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Efectivo Real Contado en Gaveta (S/.)</label>
                        <input type="number" step="0.01" name="monto_final_real" class="form-control" placeholder="Monto contado físicamente" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observaciones de Cierre</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Comentarios de diferencia de centavos o billetes deteriorados..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Confirmar Cierre de Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
