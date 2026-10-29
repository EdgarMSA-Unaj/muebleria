@extends('layouts.app')

@section('title', 'Nueva Orden de Compra')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="brand-font mb-1 text-dark">Nueva Orden de Compra a Proveedor</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Reabastecimiento de insumos, madera y piezas terminadas</span>
        </div>
        <a href="{{ route('compras.index') }}" class="btn btn-sm btn-light border px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Compras
        </a>
    </div>

    <form action="{{ route('compras.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="stat-card mb-4">
                    <h5 class="brand-font mb-3 text-dark">1. Datos del Proveedor y Destino</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Proveedor Homologado</label>
                            <select name="proveedor_id" class="form-select" required>
                                <option value="">Seleccione Proveedor...</option>
                                @foreach($proveedores as $prv)
                                    <option value="{{ $prv->id }}">
                                        {{ $prv->razon_social }} (RUC: {{ $prv->ruc }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Almacén Destino de Recepción</label>
                            <select name="almacen_id" class="form-select" required>
                                @foreach($almacenes as $alm)
                                    <option value="{{ $alm->id }}">{{ $alm->nombre }} ({{ $alm->ubicacion }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de Emisión</label>
                            <input type="date" name="fecha_emision" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha Esperada de Llegada</label>
                            <input type="date" name="fecha_entrega_esperada" class="form-control" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Factura Proveedor (Opcional)</label>
                            <input type="text" name="factura_proveedor" class="form-control" placeholder="Ej. F001-98412">
                        </div>
                    </div>
                </div>

                <div class="stat-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="brand-font mb-0 text-dark">2. Partidas & Muebles a Ingresar</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAgregarCompraItem">
                            <i class="fa-solid fa-plus me-1"></i> Agregar Ítem
                        </button>
                    </div>

                    <div id="compraItemsContainer">
                        <div class="compra-item-row p-3 rounded-3 mb-3 border" style="background: var(--bg-body);">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">Mueble / Producto</label>
                                    <select name="productos[0][producto_id]" class="form-select form-select-sm" required onchange="setCompraCosto(this, 0)">
                                        <option value="">Seleccione Producto...</option>
                                        @foreach($productos as $p)
                                            <option value="{{ $p->id }}" data-costo="{{ $p->precio_compra }}">
                                                {{ $p->nombre }} (Costo Actual: S/. {{ number_format($p->precio_compra, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">Cantidad</label>
                                    <input type="number" name="productos[0][cantidad]" class="form-control form-control-sm cantidad-compra-input" value="1" min="1" required onchange="calcularCompraTotales()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold" style="font-size: 12px;">Costo Unit. (S/.)</label>
                                    <input type="number" step="0.01" name="productos[0][costo_unitario]" class="form-control form-control-sm costo-compra-input" value="0.00" required onchange="calcularCompraTotales()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <h5 class="brand-font mb-3 text-dark">3. Estado de Recepción y Observaciones</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Condición de Pago al Proveedor</label>
                            <select name="condicion_pago" class="form-select" required>
                                <option value="contado">Pago de Contado / Anticipo</option>
                                <option value="credito_15d">Crédito 15 Días</option>
                                <option value="credito_30d" selected>Crédito 30 Días</option>
                                <option value="credito_60d">Crédito 60 Días</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado de Recepción Inmediata</label>
                            <select name="estado" class="form-select" required>
                                <option value="recibido">Recibido Ahora Mismo (Sumar stock al almacén)</option>
                                <option value="solicitado" selected>Solicitado / En Tránsito (Aún no sumar stock)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones del Pedido</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Especificaciones de madera seca al horno, acabado de cantos, etc."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="stat-card position-sticky" style="top: 96px;">
                    <h5 class="brand-font mb-3 text-dark">Totales de Compra</h5>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Subtotal Neto:</span>
                        <strong class="text-dark" id="txtCompraSubtotal">S/. 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">IGV ({{ $config->porcentaje_igv ?? 18 }}%):</span>
                        <strong class="text-dark" id="txtCompraIgv">S/. 0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between py-3 mb-4">
                        <span class="fs-5 fw-bold text-dark">Total Orden:</span>
                        <span class="fs-4 fw-bold text-danger" id="txtCompraTotal">S/. 0.00</span>
                    </div>

                    <button type="submit" class="btn w-100 text-white fw-bold py-3 rounded-3 shadow-sm" style="background: linear-gradient(135deg, #ff7a18 0%, #ff5200 100%);">
                        <i class="fa-solid fa-check-circle me-2"></i> Generar Orden de Compra
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let compraItemIndex = 1;

function setCompraCosto(selectElement, index) {
    const opt = selectElement.options[selectElement.selectedIndex];
    const costo = opt.getAttribute('data-costo') || 0;
    const parent = selectElement.closest('.compra-item-row');
    const input = parent.querySelector('.costo-compra-input');
    if (input) {
        input.value = parseFloat(costo).toFixed(2);
    }
    calcularCompraTotales();
}

function calcularCompraTotales() {
    let subtotal = 0;
    document.querySelectorAll('.compra-item-row').forEach(row => {
        const cant = parseFloat(row.querySelector('.cantidad-compra-input').value) || 0;
        const costo = parseFloat(row.querySelector('.costo-compra-input').value) || 0;
        subtotal += cant * costo;
    });

    const igv = subtotal * ({{ $config->porcentaje_igv ?? 18 }} / 100);
    const total = subtotal + igv;

    document.getElementById('txtCompraSubtotal').innerText = 'S/. ' + subtotal.toFixed(2);
    document.getElementById('txtCompraIgv').innerText = 'S/. ' + igv.toFixed(2);
    document.getElementById('txtCompraTotal').innerText = 'S/. ' + total.toFixed(2);
}

document.getElementById('btnAgregarCompraItem').addEventListener('click', function() {
    const container = document.getElementById('compraItemsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'compra-item-row p-3 rounded-3 mb-3 border';
    newRow.style.background = 'var(--bg-body)';

    newRow.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold" style="font-size: 12px;">Mueble / Producto</label>
                <select name="productos[${compraItemIndex}][producto_id]" class="form-select form-select-sm" required onchange="setCompraCosto(this, ${compraItemIndex})">
                    <option value="">Seleccione Producto...</option>
                    @foreach($productos as $p)
                        <option value="{{ $p->id }}" data-costo="{{ $p->precio_compra }}">
                            {{ $p->nombre }} (Costo Actual: S/. {{ number_format($p->precio_compra, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold" style="font-size: 12px;">Cantidad</label>
                <input type="number" name="productos[${compraItemIndex}][cantidad]" class="form-control form-control-sm cantidad-compra-input" value="1" min="1" required onchange="calcularCompraTotales()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size: 12px;">Costo Unit. (S/.)</label>
                <input type="number" step="0.01" name="productos[${compraItemIndex}][costo_unitario]" class="form-control form-control-sm costo-compra-input" value="0.00" required onchange="calcularCompraTotales()">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.compra-item-row').remove(); calcularCompraTotales();">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(newRow);
    compraItemIndex++;
});
</script>
@endpush
