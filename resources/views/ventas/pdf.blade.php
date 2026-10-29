<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($venta->tipo_comprobante) }} - {{ $venta->numero_comprobante }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .brand-info {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }
        .doc-box {
            border: 2px solid #0f172a;
            border-radius: 6px;
            text-align: center;
            padding: 12px 10px;
            background-color: #f8fafc;
        }
        .doc-box .ruc {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .doc-box .doc-type {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #0f172a;
            color: #ffffff;
            padding: 4px 6px;
            margin: 4px 0;
            border-radius: 3px;
            letter-spacing: 1px;
        }
        .doc-box .doc-number {
            font-size: 13px;
            font-weight: bold;
            color: #ea580c;
            margin-top: 5px;
        }
        .client-card {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 12px 0 15px 0;
            background-color: #fcfcfd;
        }
        .client-card table td {
            padding: 3px 4px;
            font-size: 10.5px;
        }
        .label {
            font-weight: bold;
            color: #334155;
            width: 18%;
        }
        .items-table {
            margin-top: 10px;
            border: 1px solid #cbd5e1;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 7px 8px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .totals-table {
            width: 45%;
            float: right;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 10.5px;
        }
        .totals-table .total-row {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #0f172a;
        }
        .clear {
            clear: both;
        }
        .footer-section {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .terms-box {
            font-size: 8.5px;
            color: #64748b;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            line-height: 1.35;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>

    <!-- Header / Membrete y Cuadro Fiscal -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; padding-right: 20px;">
                <table style="width: 100%; border: none;">
                    <tr>
                        @if(isset($config->logo) && $config->logo && file_exists(public_path('storage/' . $config->logo)))
                            <td style="width: 65px; vertical-align: top; padding-right: 10px;">
                                <img src="{{ public_path('storage/' . $config->logo) }}" style="max-height: 55px; max-width: 65px; border-radius: 4px;">
                            </td>
                        @endif
                        <td style="vertical-align: top;">
                            <div class="brand-title">{{ $config->nombre_empresa ?? 'MUEBLES & DISEÑO ELEGANCE' }}</div>
                            <div class="brand-info">
                                <strong>Dirección:</strong> {{ $config->direccion ?? 'Av. Principal Showroom Central' }}<br>
                                <strong>Teléfono:</strong> {{ $config->telefono ?? '+51 (01) 485-9920' }} | <strong>Email:</strong> {{ $config->email ?? 'ventas@muebleselegance.pe' }}<br>
                                <strong>Web:</strong> {{ $config->sitio_web ?? 'https://muebleselegance.pe' }}<br>
                                <strong>Especialidad:</strong> Muebles finos en maderas selectas, tapicería premium y diseño a medida.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;">
                <div class="doc-box">
                    <div class="ruc">R.U.C. N° {{ $config->ruc ?? '20608945123' }}</div>
                    <div class="doc-type">{{ strtoupper(str_replace('_', ' ', $venta->tipo_comprobante)) }}</div>
                    <div class="doc-number">N° {{ $venta->numero_comprobante }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Ficha del Cliente y Venta -->
    <div class="client-card">
        <table>
            <tr>
                <td class="label">CLIENTE:</td>
                <td style="width: 45%;"><strong>{{ $venta->cliente->nombre_completo ?? 'Cliente Mostrador' }}</strong></td>
                <td class="label">FECHA EMISIÓN:</td>
                <td>{{ $venta->fecha_emision ? $venta->fecha_emision->format('d/m/Y') : date('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ $venta->cliente->documento_tipo ?? 'DOC' }}:</td>
                <td>{{ $venta->cliente->documento_numero ?? 'S/N' }}</td>
                <td class="label">CONDICIÓN PAGO:</td>
                <td>{{ ucfirst(str_replace('_', ' ', $venta->condicion_pago)) }}</td>
            </tr>
            <tr>
                <td class="label">DIRECCIÓN:</td>
                <td>{{ $venta->cliente->direccion ?? 'Mostrador' }}{{ $venta->cliente->distrito ? ', ' . $venta->cliente->distrito : '' }}</td>
                <td class="label">ESTADO:</td>
                <td>
                    <span class="badge {{ $venta->estado == 'completada' ? 'badge-success' : 'badge-warning' }}">
                        {{ strtoupper(str_replace('_', ' ', $venta->estado)) }}
                    </span>
                </td>
            </tr>
            @if($venta->requiere_entrega)
            <tr>
                <td class="label">DESPACHO:</td>
                <td colspan="3">
                    <strong>Entrega a domicilio:</strong> {{ $venta->direccion_entrega ?? $venta->cliente->direccion }}
                    @if($venta->fecha_entrega_programada)
                        | <strong>Fecha Prog:</strong> {{ $venta->fecha_entrega_programada->format('d/m/Y') }}
                    @endif
                    @if($venta->requiere_armado)
                        | <span style="color: #0284c7; font-weight: bold;">(Incluye armado de muebles)</span>
                    @endif
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Detalle de Productos -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">ITEM</th>
                <th style="width: 14%;">CÓDIGO/SKU</th>
                <th style="width: 45%;">DESCRIPCIÓN / MUEBLE</th>
                <th class="text-center" style="width: 9%;">CANT.</th>
                <th class="text-right" style="width: 13%;">P. UNIT ({{ $config->moneda_simbolo ?? 'S/.' }})</th>
                <th class="text-right" style="width: 13%;">TOTAL ({{ $config->moneda_simbolo ?? 'S/.' }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->producto->sku ?? '-' }}</strong></td>
                    <td>
                        <strong>{{ $item->producto->nombre ?? 'Mueble' }}</strong>
                        @if($item->color_acabado)
                            <div style="font-size: 8.5px; color: #475569; margin-top: 2px;">
                                <em>Acabado / Personalización: {{ $item->color_acabado }}</em>
                            </div>
                        @endif
                        @if($item->producto->material)
                            <div style="font-size: 8px; color: #64748b;">
                                Material: {{ $item->producto->material }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $item->cantidad }}</strong></td>
                    <td class="text-right">{{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <table class="totals-table">
        <tr>
            <td class="text-right"><strong>SUBTOTAL:</strong></td>
            <td class="text-right">{{ $config->moneda_simbolo ?? 'S/.' }} {{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>IGV ({{ $config->porcentaje_igv ?? 18 }}%):</strong></td>
            <td class="text-right">{{ $config->moneda_simbolo ?? 'S/.' }} {{ number_format($venta->igv, 2) }}</td>
        </tr>
        @if($venta->descuento > 0)
        <tr>
            <td class="text-right" style="color: #dc2626;"><strong>DESCUENTO:</strong></td>
            <td class="text-right" style="color: #dc2626;">- {{ $config->moneda_simbolo ?? 'S/.' }} {{ number_format($venta->descuento, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td class="text-right">IMPORTE TOTAL:</td>
            <td class="text-right">{{ $config->moneda_simbolo ?? 'S/.' }} {{ number_format($venta->total, 2) }}</td>
        </tr>
        @if($venta->saldo > 0)
        <tr>
            <td class="text-right" style="color: #ea580c; font-weight: bold;">SALDO PENDIENTE:</td>
            <td class="text-right" style="color: #ea580c; font-weight: bold;">{{ $config->moneda_simbolo ?? 'S/.' }} {{ number_format($venta->saldo, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="clear"></div>

    <!-- Garantía y Notas -->
    <div class="footer-section">
        <div class="terms-box">
            <strong>TÉRMINOS DE GARANTÍA Y CONDICIONES:</strong><br>
            {{ $config->terminos_garantia ?? 'Garantía estructural de 2 años en madera maciza y 1 año en tapicería y esponjas de alta densidad contra defectos de fábrica.' }}<br>
            <em>* Todo mueble debe ser revisado en el momento de la entrega. Conserve este comprobante para hacer válida cualquier garantía o servicio técnico.</em>
        </div>
        <table style="margin-top: 15px;">
            <tr>
                <td style="font-size: 8.5px; color: #64748b;">
                    Atendido por: <strong>{{ $venta->usuario->name ?? 'Ventas' }}</strong> | Generado el {{ date('d/m/Y H:i:s') }}
                </td>
                <td class="text-right" style="font-size: 8.5px; color: #64748b;">
                    {{ $config->nombre_empresa ?? 'Muebles Elegance' }} - Sistema de Gestión ERP
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
