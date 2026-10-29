@extends('layouts.app')

@section('title', 'Taller de Fabricación')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Taller de Fabricación & Carpintería</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Control de órdenes de producción, ensamblado, laqueado y tapizado de muebles</span>
        </div>
        <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevaOrdenTallerModal" style="background: var(--primary-orange);">
            <i class="fa-solid fa-hammer"></i>
            <span>+ Enviar Mueble a Fabricación</span>
        </button>
    </div>

    <!-- KPIs del Taller -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Muebles en Fabricación Activa</div>
                <div class="fs-4 fw-bold text-dark">{{ $enTallerTotal }} órdenes</div>
                <span class="text-primary fw-bold" style="font-size: 11px;">En planta de taller</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">En Proceso de Tapicería / Encolado</div>
                <div class="fs-4 fw-bold text-warning">{{ $enTapiceria }} muebles</div>
                <span class="text-muted" style="font-size: 11px;">Espuma & Telas</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Muebles Terminados Recientes</div>
                <div class="fs-4 fw-bold text-success">{{ $terminadosRecientes }} listos</div>
                <span class="badge bg-success-subtle text-success" style="font-size: 10px;">Para showroom / despacho</span>
            </div>
        </div>
    </div>

    <!-- Filtros de Fase -->
    <div class="stat-card mb-4 p-3">
        <form method="GET" action="{{ route('produccion.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <select name="fase" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">Todas las Fases de Producción</option>
                    <option value="corte_madera" {{ request('fase') == 'corte_madera' ? 'selected' : '' }}>1. Corte de Madera & Habilitado</option>
                    <option value="armado_esqueleto" {{ request('fase') == 'armado_esqueleto' ? 'selected' : '' }}>2. Armado de Estructura / Esqueleto</option>
                    <option value="laqueado_pintura" {{ request('fase') == 'laqueado_pintura' ? 'selected' : '' }}>3. Pintura & Laqueado Poliuretano</option>
                    <option value="tapizado" {{ request('fase') == 'tapizado' ? 'selected' : '' }}>4. Tapizado & Encolado de Espuma</option>
                    <option value="control_calidad" {{ request('fase') == 'control_calidad' ? 'selected' : '' }}>5. Control de Calidad & Acabados</option>
                    <option value="terminado" {{ request('fase') == 'terminado' ? 'selected' : '' }}>6. Terminado / Listo para Entrega</option>
                </select>
            </div>
            <div class="col-md-3">
                <a href="{{ route('produccion.index') }}" class="btn btn-sm btn-light border w-100">Restablecer</a>
            </div>
        </form>
    </div>

    <!-- Lista de Órdenes de Producción -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>N° Orden</th>
                        <th>Mueble a Fabricar</th>
                        <th>Maestros Responsables</th>
                        <th>Avance</th>
                        <th>Fase Actual de Producción</th>
                        <th>Fechas (Inicio / Término)</th>
                        <th>Especificaciones de Acabado</th>
                        <th>Acción de Taller</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordenes as $op)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark font-monospace">{{ $op->codigo }}</span>
                                @if($op->venta)
                                    <div class="text-primary fw-semibold" style="font-size: 11px;">
                                        <i class="fa-solid fa-user me-1"></i> {{ $op->venta->cliente->nombre_completo ?? 'Cliente' }}
                                    </div>
                                @else
                                    <span class="badge bg-light text-secondary border" style="font-size: 10px;">Para Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $op->producto->nombre ?? 'Mueble' }}</div>
                                <span class="badge bg-light text-secondary border" style="font-size: 11px;">
                                    {{ $op->producto->categoria->nombre ?? 'Mueblería' }}
                                </span>
                            </td>
                            <td style="font-size: 12.5px;">
                                <div><i class="fa-solid fa-screwdriver text-muted me-1"></i> Carp: <strong>{{ $op->maestro_carpintero ?? 'Taller 1' }}</strong></div>
                                <div><i class="fa-solid fa-scissors text-muted me-1"></i> Tapiz: <strong>{{ $op->maestro_tapicero ?? 'Taller 2' }}</strong></div>
                            </td>
                            <td style="min-width: 140px;">
                                <div class="d-flex justify-content-between text-secondary mb-1" style="font-size: 11px; font-weight: 600;">
                                    <span>Avance</span>
                                    <span>{{ $op->avance_porcentaje }}%</span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: var(--border-color); border-radius: 6px;">
                                    <div class="progress-bar {{ $op->avance_porcentaje >= 80 ? 'bg-success' : ($op->avance_porcentaje >= 50 ? 'bg-primary' : 'bg-warning') }}" role="progressbar" style="width: {{ $op->avance_porcentaje }}%; border-radius: 6px;"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $op->fase === 'terminado' ? 'bg-success-subtle text-success' : ($op->fase === 'tapizado' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning-emphasis') }} px-2 py-1" style="font-size: 11.5px;">
                                    {{ $op->fase_label }}
                                </span>
                            </td>
                            <td style="font-size: 12px;">
                                <div><strong>Inicio:</strong> {{ $op->fecha_inicio->format('d/m/Y') }}</div>
                                <div class="text-muted"><strong>Meta:</strong> {{ $op->fecha_estimada_termino ? $op->fecha_estimada_termino->format('d/m/Y') : 'Por definir' }}</div>
                            </td>
                            <td style="font-size: 12px; max-width: 200px;">
                                <div class="text-truncate" title="{{ $op->especificaciones_acabado }}">
                                    {{ $op->especificaciones_acabado ?? 'Acabado estándar según catálogo' }}
                                </div>
                            </td>
                            <td>
                                @if($op->fase !== 'terminado')
                                    <form action="{{ route('produccion.avanzar', $op->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary py-1 px-2 d-flex align-items-center gap-1" title="Avanzar a la siguiente fase de taller">
                                            <span>Siguiente Fase</span>
                                            <i class="fa-solid fa-forward-step"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-success text-white py-1 px-2">
                                        <i class="fa-solid fa-circle-check me-1"></i> Concluido
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No hay órdenes de fabricación registradas en el taller.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $ordenes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Nueva Orden de Fabricación -->
<div class="modal fade" id="nuevaOrdenTallerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Enviar Mueble a Taller de Fabricación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('produccion.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Mueble del Catálogo</label>
                            <select name="producto_id" class="form-select" required>
                                <option value="">Seleccione Mueble a Fabricar...</option>
                                @foreach($productos as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->nombre }} ({{ $pr->material }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Asociado a Pedido / Cliente (Opcional)</label>
                            <select name="venta_id" class="form-select">
                                <option value="">Fabricación para Stock de Almacén</option>
                                @foreach($ventas as $vt)
                                    <option value="{{ $vt->id }}">
                                        {{ $vt->numero_comprobante }} - {{ $vt->cliente->nombre_completo ?? 'Cliente' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maestro Carpintero (Estructura)</label>
                            <input type="text" name="maestro_carpintero" class="form-control" placeholder="Ej. Don Santos Huamán">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maestro Tapicero / Acabados</label>
                            <input type="text" name="maestro_tapicero" class="form-control" placeholder="Ej. Ricardo Córdova">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Inicio en Taller</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha Estimada de Término</label>
                            <input type="date" name="fecha_estimada_termino" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Especificaciones de Acabado (Madera, Tinte, Tapiz, Densidad)</label>
                            <input type="text" name="especificaciones_acabado" class="form-control" placeholder="Ej. Lino Suizo Gris Perla, patas laqueadas en roble oscuro, espuma 28kg Zebra">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones Internas de Taller</label>
                            <textarea name="observaciones_taller" class="form-control" rows="2" placeholder="Indicaciones sobre ensamblado, tornillos pasantes, refuerzo en esquinas..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Enviar a Taller</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
