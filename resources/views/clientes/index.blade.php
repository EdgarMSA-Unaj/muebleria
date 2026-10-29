@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="brand-font mb-1 text-dark">Directorio de Clientes (CRM)</h3>
            <span class="text-secondary" style="font-size: 13.5px;">Gestión de clientes particulares, inmobiliarias, créditos y compras</span>
        </div>
        <button class="btn text-white fw-semibold rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#nuevoClienteModal" style="background: var(--primary-orange);">
            <i class="fa-solid fa-user-plus"></i>
            <span>+ Nuevo Cliente</span>
        </button>
    </div>

    <!-- KPIs Rápidos -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Clientes Registrados</div>
                <div class="fs-4 fw-bold text-dark">{{ $totalClientes }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Clientes con Línea de Crédito Activa</div>
                <div class="fs-4 fw-bold text-primary">{{ $clientesConSaldo }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card py-3">
                <div class="text-muted" style="font-size: 12px;">Total Cuentas por Cobrar</div>
                <div class="fs-4 fw-bold text-warning">S/. {{ number_format($saldoTotalPendiente, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla Clientes -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="custom-table mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre / Razón Social</th>
                        <th>Documento</th>
                        <th>Contacto</th>
                        <th>Dirección de Entrega</th>
                        <th>Límite Crédito</th>
                        <th>Pedidos Realizados</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $cli->codigo }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cli->nombre_completo }}</div>
                                <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #2563eb; font-size: 10.5px;">
                                    {{ $cli->tipo === 'empresa' ? 'Empresa / Corporativo' : 'Persona Natural' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $cli->documento_numero ?? '-' }}</div>
                                <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ $cli->documento_tipo }}</span>
                            </td>
                            <td>
                                <div style="font-size: 13px;"><i class="fa-solid fa-phone me-1 text-muted"></i> {{ $cli->celular ?? $cli->telefono ?? '-' }}</div>
                                <div class="text-muted" style="font-size: 11px;"><i class="fa-regular fa-envelope me-1"></i> {{ $cli->email ?? '-' }}</div>
                            </td>
                            <td style="font-size: 12.5px;">
                                <div>{{ $cli->direccion ?? 'Sin registrar' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $cli->distrito ? $cli->distrito . ', ' : '' }}{{ $cli->ciudad ?? '' }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">S/. {{ number_format($cli->credito_limite, 2) }}</div>
                                @if($cli->saldo_pendiente > 0)
                                    <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">Debe: S/. {{ number_format($cli->saldo_pendiente, 2) }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success" style="font-size: 10px;">Al día</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fs-6">{{ $cli->ventas_count }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    @if($cli->saldo_pendiente > 0)
                                        <button class="btn btn-sm btn-success px-2 py-1" onclick='abrirAbono({{ $cli->id }}, @json($cli->nombre_completo), {{ $cli->saldo_pendiente }}, @json($cli->codigo))' title="Registrar abono o amortización de deuda" style="font-size: 11.5px;">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Abonar
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-light border px-2 py-1" onclick='abrirEditarCliente(@json($cli))' title="Editar información del cliente">
                                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    </button>
                                    @if($cli->ventas_count == 0)
                                        <form action="{{ route('clientes.destroy', $cli->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar al cliente {{ $cli->nombre_completo }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border px-2 py-1 text-danger" title="Eliminar cliente">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No hay clientes registrados en la base de datos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $clientes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="nuevoClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Cliente</label>
                            <select name="tipo" class="form-select" id="tipoClienteSelect" required onchange="toggleTipoCliente(this.value, 'nuevo')">
                                <option value="persona_natural">Persona Natural</option>
                                <option value="empresa">Empresa / Razón Social</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipo Documento</label>
                            <select name="documento_tipo" class="form-select" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">Carnet Extranjería</option>
                                <option value="PASAPORTE">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">N° Documento</label>
                            <input type="text" name="documento_numero" class="form-control" placeholder="Ej. 45892147">
                        </div>

                        <div class="col-md-6" id="nuevoDivNombre">
                            <label class="form-label fw-semibold">Nombres</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Carolina">
                        </div>
                        <div class="col-md-6" id="nuevoDivApellidos">
                            <label class="form-label fw-semibold">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Ej. Ramos Vega">
                        </div>
                        <div class="col-12" id="nuevoDivRazonSocial" style="display: none;">
                            <label class="form-label fw-semibold">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" placeholder="Ej. Diseños & Decoraciones S.A.C.">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" name="celular" class="form-control" placeholder="+51 987 654 321">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="cliente@correo.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección de Entrega</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Calle / Av, Nro, Urb.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Distrito</label>
                            <input type="text" name="distrito" class="form-control" placeholder="Ej. Miraflores">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" value="Lima">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Límite de Crédito (S/.)</label>
                            <input type="number" step="0.01" name="credito_limite" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: var(--primary-orange);">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Cliente -->
<div class="modal fade" id="editarClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font">Editar Cliente <span id="editCodigoBadge" class="badge bg-light text-dark border ms-2"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editClienteForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Cliente</label>
                            <select name="tipo" class="form-select" id="editTipoClienteSelect" required onchange="toggleTipoCliente(this.value, 'edit')">
                                <option value="persona_natural">Persona Natural</option>
                                <option value="empresa">Empresa / Razón Social</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipo Documento</label>
                            <select name="documento_tipo" id="editDocumentoTipo" class="form-select" required>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="CE">Carnet Extranjería</option>
                                <option value="PASAPORTE">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">N° Documento</label>
                            <input type="text" name="documento_numero" id="editDocumentoNumero" class="form-control">
                        </div>

                        <div class="col-md-6" id="editDivNombre">
                            <label class="form-label fw-semibold">Nombres</label>
                            <input type="text" name="nombre" id="editNombre" class="form-control" required>
                        </div>
                        <div class="col-md-6" id="editDivApellidos">
                            <label class="form-label fw-semibold">Apellidos</label>
                            <input type="text" name="apellidos" id="editApellidos" class="form-control">
                        </div>
                        <div class="col-12" id="editDivRazonSocial" style="display: none;">
                            <label class="form-label fw-semibold">Razón Social</label>
                            <input type="text" name="razon_social" id="editRazonSocial" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" name="celular" id="editCelular" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dirección de Entrega</label>
                            <input type="text" name="direccion" id="editDireccion" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Distrito</label>
                            <input type="text" name="distrito" id="editDistrito" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" name="ciudad" id="editCiudad" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Límite de Crédito (S/.)</label>
                            <input type="number" step="0.01" name="credito_limite" id="editCreditoLimite" class="form-control">
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

<!-- Modal Registrar Abono -->
<div class="modal fade" id="abonoClienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title brand-font text-success">
                    <i class="fa-solid fa-hand-holding-dollar me-2"></i> Registrar Abono de Deuda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="abonoForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="text-muted" style="font-size: 12px;">Cliente</div>
                        <div class="fw-bold text-dark fs-6" id="abonoClienteNombre">-</div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="text-secondary" style="font-size: 13px;">Deuda Pendiente Actual:</span>
                            <span class="badge bg-danger text-white fs-6" id="abonoSaldoBadge">S/. 0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Monto del Abono (S/.)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">S/.</span>
                            <input type="number" step="0.01" min="0.50" name="monto" id="abonoMontoInput" class="form-control form-control-lg fw-bold text-success" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="pagarTotalidad()">Pagar Todo</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Método de Cobro</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="efectivo">Efectivo (Entra a Caja)</option>
                            <option value="yape_plin">Yape / Plin</option>
                            <option value="transferencia">Transferencia BCP / BBVA / Interbank</option>
                            <option value="tarjeta">Tarjeta Débito / Crédito (POS)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notas / Comprobante de Abono</label>
                        <input type="text" name="notas" class="form-control" placeholder="Ej. Operación Yape #184920 o Recibo de Caja">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4">
                        <i class="fa-solid fa-check me-1"></i> Confirmar Abono
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let saldoPendienteActual = 0;

function toggleTipoCliente(tipo, prefix = 'nuevo') {
    const isEmpresa = tipo === 'empresa';
    document.getElementById(prefix + 'DivRazonSocial').style.display = isEmpresa ? 'block' : 'none';
    document.getElementById(prefix + 'DivApellidos').style.display = isEmpresa ? 'none' : 'block';
}

function abrirEditarCliente(cliente) {
    const form = document.getElementById('editClienteForm');
    form.action = `/clientes/${cliente.id}`;

    document.getElementById('editCodigoBadge').textContent = cliente.codigo;
    document.getElementById('editTipoClienteSelect').value = cliente.tipo;
    document.getElementById('editDocumentoTipo').value = cliente.documento_tipo || 'DNI';
    document.getElementById('editDocumentoNumero').value = cliente.documento_numero || '';
    document.getElementById('editNombre').value = cliente.nombre || '';
    document.getElementById('editApellidos').value = cliente.apellidos || '';
    document.getElementById('editRazonSocial').value = cliente.razon_social || '';
    document.getElementById('editCelular').value = cliente.celular || cliente.telefono || '';
    document.getElementById('editEmail').value = cliente.email || '';
    document.getElementById('editDireccion').value = cliente.direccion || '';
    document.getElementById('editDistrito').value = cliente.distrito || '';
    document.getElementById('editCiudad').value = cliente.ciudad || 'Lima';
    document.getElementById('editCreditoLimite').value = parseFloat(cliente.credito_limite || 0).toFixed(2);

    toggleTipoCliente(cliente.tipo, 'edit');

    const modal = new bootstrap.Modal(document.getElementById('editarClienteModal'));
    modal.show();
}

function abrirAbono(id, nombre, saldo, codigo) {
    saldoPendienteActual = parseFloat(saldo);
    const form = document.getElementById('abonoForm');
    form.action = `/clientes/${id}/abono`;

    document.getElementById('abonoClienteNombre').textContent = `${codigo} - ${nombre}`;
    document.getElementById('abonoSaldoBadge').textContent = 'S/. ' + saldoPendienteActual.toFixed(2);
    
    const input = document.getElementById('abonoMontoInput');
    input.value = saldoPendienteActual.toFixed(2);
    input.max = saldoPendienteActual.toFixed(2);

    const modal = new bootstrap.Modal(document.getElementById('abonoClienteModal'));
    modal.show();
}

function pagarTotalidad() {
    document.getElementById('abonoMontoInput').value = saldoPendienteActual.toFixed(2);
}
</script>
@endsection
