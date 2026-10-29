<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($venta->tipo_comprobante) }} {{ $venta->numero_comprobante }} | {{ $config->nombre_empresa ?? 'Mueblería Elegance' }}</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 30px;
        }

        h1, h2, h3, h4, h5, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        .invoice-card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .brand-logo-box {
            width: 48px;
            height: 48px;
            background: #161a29;
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
        }

        .invoice-header-box {
            border: 2px solid #1e293b;
            border-radius: 12px;
            padding: 16px 20px;
            text-align: center;
            background: #f8fafc;
        }

        .table-invoice th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 14px;
        }

        .table-invoice td {
            padding: 12px 14px;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .guarantee-box {
            background-color: #f8fafc;
            border-left: 4px solid #ff7a18;
            padding: 14px 18px;
            border-radius: 0 10px 10px 0;
            font-size: 12px;
            color: #475569;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Botones de Acción no imprimibles -->
<div class="no-print d-flex justify-content-center gap-2 mb-4">
    <button onclick="window.print()" class="btn btn-dark px-4 py-2 fw-semibold rounded-pill shadow-sm">
        <i class="fa-solid fa-print me-2"></i> Imprimir Comprobante
    </button>
    <a href="{{ route('ventas.pdf', $venta->id) }}" class="btn btn-danger px-4 py-2 fw-semibold rounded-pill shadow-sm">
        <i class="fa-solid fa-file-pdf me-2"></i> Descargar PDF Oficial
    </a>
    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver a Ventas
    </a>
</div>

<div class="invoice-card">
    <!-- Encabezado de la Factura -->
    <div class="row align-items-start mb-4">
        <div class="col-7">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="brand-logo-box">.D</div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $config->nombre_empresa ?? 'Mueblería & Diseño Elegance' }}</h3>
                    <div class="text-muted" style="font-size: 12px;">Fabricación y Venta de Muebles Finos</div>
                </div>
            </div>
            <div class="text-secondary" style="font-size: 12.5px; line-height: 1.5;">
                <div><i class="fa-solid fa-location-dot me-1 text-muted"></i> {{ $config->direccion ?? 'Av. Javier Prado Este 2850, San Borja, Lima' }}</div>
                <div><i class="fa-solid fa-phone me-1 text-muted"></i> {{ $config->telefono ?? '+51 987 654 321' }} | <i class="fa-regular fa-envelope me-1 text-muted"></i> {{ $config->email ?? 'ventas@muebleria.com' }}</div>
                <div><i class="fa-solid fa-globe me-1 text-muted"></i> {{ $config->sitio_web ?? 'https://muebleselegance.pe' }}</div>
            </div>
        </div>

        <div class="col-5">
            <div class="invoice-header-box">
                <div class="fw-bold text-dark fs-6">R.U.C. {{ $config->ruc ?? '20608945123' }}</div>
                <div class="fw-bold text-uppercase fs-5 my-1" style="color: #ff7a18;">
                    {{ match($venta->tipo_comprobante) {
                        'factura' => 'FACTURA ELECTRÓNICA',
                        'boleta' => 'BOLETA DE VENTA',
                        'cotizacion' => 'COTIZACIÓN / PROFORMA',
                        default => 'NOTA DE VENTA'
                    } }}
                </div>
                <div class="fs-5 fw-bold font-monospace text-dark">{{ $venta->numero_comprobante }}</div>
            </div>
        </div>
    </div>

    <!-- Datos del Cliente y Fechas -->
    <div class="p-3 bg-light rounded-3 mb-4" style="border: 1px solid #e2e8f0; font-size: 13px;">
        <div class="row g-2">
            <div class="col-md-7">
                <div><strong>Señor(es):</strong> {{ $venta->cliente->nombre_completo ?? 'Cliente Mostrador' }}</div>
                <div><strong>{{ $venta->cliente->documento_tipo ?? 'DNI/RUC' }}:</strong> {{ $venta->cliente->documento_numero ?? '-' }}</div>
                <div><strong>Dirección:</strong> {{ $venta->cliente->direccion ?? 'Lima, Perú' }}</div>
            </div>
            <div class="col-md-5">
                <div><strong>Fecha de Emisión:</strong> {{ $venta->fecha_emision ? $venta->fecha_emision->format('d/m/Y') : date('d/m/Y') }}</div>
                <div><strong>Condición de Pago:</strong> {{ ucfirst(str_replace('_', ' ', $venta->condicion_pago)) }}</div>
                <div><strong>Asesor Comercial:</strong> {{ $venta->usuario->name ?? 'Ventas' }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ítems / Muebles -->
    <table class="w-100 table-invoice mb-4">
        <thead>
            <tr>
                <th style="width: 8%;">Cant.</th>
                <th style="width: 47%;">Descripción del Mueble & Acabados</th>
                <th style="width: 25%;">Dimensiones & Material</th>
                <th style="width: 10%; text-align: right;">P. Unit</th>
                <th style="width: 10%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $item)
                <tr>
                    <td class="text-center fw-bold">{{ $item->cantidad }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->producto->nombre ?? 'Mueble' }}</div>
                        @if($item->color_acabado)
                            <div class="text-muted" style="font-size: 11.5px;">Acabado: <em>{{ $item->color_acabado }}</em></div>
                        @endif
                    </td>
                    <td style="font-size: 12px;">
                        <div>{{ $item->producto->dimensiones }}</div>
                        <div class="text-muted">{{ $item->producto->material ?? '-' }}</div>
                    </td>
                    <td class="text-end">S/. {{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-end fw-bold">S/. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales y Logística -->
    <div class="row g-4 mb-4">
        <div class="col-7">
            <!-- Bloque de Entrega y Armado -->
            <div class="p-3 bg-light rounded-3 mb-3 border">
                <h6 class="fw-bold text-dark mb-2" style="font-size: 12.5px;">
                    <i class="fa-solid fa-truck-fast text-primary me-1"></i> Logística de Transporte & Armado
                </h6>
                <div style="font-size: 12px; color: #475569;">
                    @if($venta->requiere_entrega)
                        <div><strong>Dirección de Entrega:</strong> {{ $venta->direccion_entrega ?? $venta->cliente->direccion }}</div>
                        <div><strong>Fecha Programada:</strong> {{ $venta->fecha_entrega_programada ? $venta->fecha_entrega_programada->format('d/m/Y') : 'A coordinar' }}</div>
                        <div><strong>Servicio de Armado In-Situ:</strong> {{ $venta->requiere_armado ? 'SÍ, INCLUIDO' : 'No requerido' }}</div>
                        @if($venta->repartidor_asignado)
                            <div><strong>Transportista:</strong> {{ $venta->repartidor_asignado }}</div>
                        @endif
                    @else
                        <div>Retiro directo en sala de exhibición / almacén central.</div>
                    @endif
                </div>
            </div>

            <!-- Términos de Garantía -->
            <div class="guarantee-box">
                <strong><i class="fa-solid fa-shield-halved me-1 text-warning"></i> Garantía del Fabricante:</strong>
                <div>{{ $config->terminos_garantia ?? 'Garantía estructural de 2 años en madera maciza y 1 año en tapicería y esponjas de alta densidad contra defectos de fábrica.' }}</div>
            </div>
        </div>

        <div class="col-5">
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between py-1" style="font-size: 13px;">
                    <span class="text-secondary">Subtotal Neto:</span>
                    <strong class="text-dark">S/. {{ number_format($venta->subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1" style="font-size: 13px;">
                    <span class="text-secondary">I.G.V. ({{ $config->porcentaje_igv ?? 18 }}%):</span>
                    <strong class="text-dark">S/. {{ number_format($venta->igv, 2) }}</strong>
                </div>
                @if($venta->costo_envio > 0)
                    <div class="d-flex justify-content-between py-1" style="font-size: 13px;">
                        <span class="text-secondary">Flete / Transporte:</span>
                        <strong class="text-dark">S/. {{ number_format($venta->costo_envio, 2) }}</strong>
                    </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between py-1">
                    <span class="fs-5 fw-bold text-dark">TOTAL:</span>
                    <span class="fs-4 fw-bold text-dark font-monospace">S/. {{ number_format($venta->total, 2) }}</span>
                </div>
            </div>

            <!-- Firma / Sello -->
            <div class="text-center mt-4 pt-4 border-top" style="font-size: 11.5px; color: #64748b;">
                _____________________________
                <div class="fw-bold mt-1">{{ $config->nombre_empresa ?? 'Muebles & Diseño Elegance' }}</div>
                <div>Emisión de Comprobante Autorizado</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
