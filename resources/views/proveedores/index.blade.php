@extends('layouts.app')

@section('title', 'Proveedores')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Proveedores de Maderas, Insumos & Telas</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Abastecimiento de materias primas para taller y muebles de catálogo</span>
        </div>
        <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevoProveedorModal" style="background: var(--primary-orange);">
            <i class="fa-solid fa-truck-ramp-box"></i>
            <span>+ Nuevo Proveedor</span>
        </button>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Proveedores Homologados</div>
                <div class="fs-4 fw-bold text-dark">{{ $totalProveedores }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Saldo Total por Pagar (Crédito Proveedor)</div>
                <div class="fs-4 fw-bold text-danger">S/. {{ number_format($saldoPorPagar, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla Proveedores -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Razón Social / Comercial</th>
                        <th>RUC</th>
                        <th>Contacto Principal</th>
                        <th>Banco & Cuenta</th>
                        <th>Días Crédito</th>
                        <th>Órdenes</th>
                        <th>Saldo Deudor</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $prov)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $prov->codigo }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $prov->razon_social }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $prov->nombre_comercial ?? '-' }}</div>
                            </td>
                            <td><span class="font-monospace fw-bold text-dark">{{ $prov->ruc ?? '-' }}</span></td>
                            <td>
                                <div class="text-dark fw-semibold" style="font-size: 13px;">{{ $prov->contacto_nombre ?? '-' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $prov->celular ?? $prov->telefono ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="text-dark" style="font-size: 12.5px;">{{ $prov->banco ?? 'No especificado' }}</div>
                                <div class="text-muted font-monospace" style="font-size: 11px;">{{ $prov->cuenta_bancaria ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $prov->dias_credito }} días</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fs-6">{{ $prov->compras_count }}</span>
                            </td>
                            <td>
                                @if($prov->saldo_pendiente > 0)
                                    <span class="badge bg-danger-subtle text-danger fw-bold" style="font-size: 12px;">
                                        S/. {{ number_format($prov->saldo_pendiente, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success" style="font-size: 11px;">
                                        <i class="fa-solid fa-check me-1"></i> Al día
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($prov->saldo_pendiente > 0)
                                        <button class="btn btn-sm btn-success text-white px-2 py-1" onclick='abrirPagarProveedor(@json($prov))' title="Registrar pago / amortización">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pagar
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-light border px-2 py-1" onclick='abrirEditarProveedor(@json($prov))' title="Editar información del proveedor">
                                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    </button>
                                    @if($prov->compras_count == 0)
                                        <form action="{{ route('proveedores.destroy', $prov->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar al proveedor {{ $prov->razon_social }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border px-2 py-1 text-danger" title="Eliminar proveedor">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No hay proveedores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $proveedores->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Nuevo Proveedor -->
<div class="modal fade" id="nuevoProveedorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Registrar Proveedor de Insumos / Madera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" placeholder="Ej. Maderas y Herrajes del Norte S.A.C." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" class="form-control" placeholder="Ej. Madera Norte">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">RUC (11 dígitos)</label>
                            <input type="text" name="ruc" class="form-control" placeholder="20123456789">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contacto Comercial</label>
                            <input type="text" name="contacto_nombre" class="form-control" placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" name="telefono" class="form-control" placeholder="+51 987 654 321">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="ventas@proveedor.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección Fiscal / Almacén</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Av. Industrial 450, Lima">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Banco</label>
                            <input type="text" name="banco" class="form-control" placeholder="Ej. BCP Soles">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Cuenta Bancaria / CCI</label>
                            <input type="text" name="cuenta_bancaria" class="form-control" placeholder="193-45892147-0-12">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Días de Crédito</label>
                            <input type="number" name="dias_credito" class="form-control" value="30" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Proveedor -->
<div class="modal fade" id="editarProveedorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Editar Proveedor <span id="editProvCodigoBadge" class="badge bg-light text-dark border ms-2"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProveedorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Razón Social</label>
                            <input type="text" name="razon_social" id="editProvRazonSocial" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" id="editProvNombreComercial" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">RUC (11 dígitos)</label>
                            <input type="text" name="ruc" id="editProvRuc" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contacto Comercial</label>
                            <input type="text" name="contacto_nombre" id="editProvContactoNombre" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" name="telefono" id="editProvTelefono" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="email" id="editProvEmail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección Fiscal / Almacén</label>
                            <input type="text" name="direccion" id="editProvDireccion" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Banco</label>
                            <input type="text" name="banco" id="editProvBanco" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Cuenta Bancaria / CCI</label>
                            <input type="text" name="cuenta_bancaria" id="editProvCuentaBancaria" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Días de Crédito</label>
                            <input type="number" name="dias_credito" id="editProvDiasCredito" class="form-control" min="0">
                        </div>
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

<!-- Modal Pagar Deuda / Abono a Proveedor -->
<div class="modal fade" id="pagarProveedorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title brand-font mb-0 text-dark">Registrar Pago a Proveedor</h5>
                    <span class="text-muted" style="font-size: 12px;" id="pagoProvRazonSocial"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pagarProveedorForm" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Resumen del Saldo -->
                    <div class="p-3 bg-danger-subtle rounded-3 mb-3 text-danger border border-danger-subtle">
                        <div style="font-size: 11.5px;">Saldo Deudor Actual Pendiente:</div>
                        <div class="fs-4 fw-bold" id="pagoProvSaldoTexto">S/. 0.00</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Monto a Pagar (S/.)</label>
                        <input type="number" step="0.01" name="monto" id="pagoProvMontoInput" class="form-control form-control-lg fw-bold text-success" required min="0.50">
                        <div class="form-text" style="font-size: 11px;">Monto máximo permitido: <strong id="pagoProvMontoMax"></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Método de Pago / Salida de Dinero</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="efectivo">Efectivo (Gaveta de Caja Abierta)</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="yape_plin">Yape / Plin</option>
                            <option value="cheque">Cheque de Gerencia</option>
                        </select>
                        <div class="form-text" style="font-size: 11px;">Los pagos en efectivo descontarán automáticamente de la caja en turno.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">N° Operación / Comprobante de Pago</label>
                        <input type="text" name="comprobante_numero" class="form-control" placeholder="Ej. OP-49821 / Recibo N° 102">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Notas / Glosa</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones adicionales sobre la amortización"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: var(--primary-orange);">
                        <i class="fa-solid fa-check me-1"></i> Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirEditarProveedor(prov) {
    const form = document.getElementById('editProveedorForm');
    form.action = `/proveedores/${prov.id}`;

    document.getElementById('editProvCodigoBadge').textContent = prov.codigo;
    document.getElementById('editProvRazonSocial').value = prov.razon_social || '';
    document.getElementById('editProvNombreComercial').value = prov.nombre_comercial || '';
    document.getElementById('editProvRuc').value = prov.ruc || '';
    document.getElementById('editProvContactoNombre').value = prov.contacto_nombre || '';
    document.getElementById('editProvTelefono').value = prov.celular || prov.telefono || '';
    document.getElementById('editProvEmail').value = prov.email || '';
    document.getElementById('editProvDireccion').value = prov.direccion || '';
    document.getElementById('editProvBanco').value = prov.banco || '';
    document.getElementById('editProvCuentaBancaria').value = prov.cuenta_bancaria || '';
    document.getElementById('editProvDiasCredito').value = prov.dias_credito ?? 30;

    const modal = new bootstrap.Modal(document.getElementById('editarProveedorModal'));
    modal.show();
}

function abrirPagarProveedor(prov) {
    const form = document.getElementById('pagarProveedorForm');
    form.action = `/proveedores/${prov.id}/pago`;

    document.getElementById('pagoProvRazonSocial').textContent = `${prov.codigo} - ${prov.razon_social}`;
    const saldo = parseFloat(prov.saldo_pendiente) || 0;
    document.getElementById('pagoProvSaldoTexto').textContent = `S/. ${saldo.toFixed(2)}`;
    document.getElementById('pagoProvMontoMax').textContent = `S/. ${saldo.toFixed(2)}`;

    const montoInput = document.getElementById('pagoProvMontoInput');
    montoInput.max = saldo.toFixed(2);
    montoInput.value = saldo > 0 ? saldo.toFixed(2) : '';

    const modal = new bootstrap.Modal(document.getElementById('pagarProveedorModal'));
    modal.show();
}
</script>
@endsection
