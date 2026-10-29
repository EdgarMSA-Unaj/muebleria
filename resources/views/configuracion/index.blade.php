@extends('layouts.app')

@section('title', 'Configuración de la Empresa')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <h3 class="brand-font mb-1 text-dark">Configuración General de la Mueblería</h3>
        <span class="text-secondary" style="font-size: 13.5px;">Parámetros de facturación, impuestos, datos fiscales y términos de garantía</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="stat-card">
                <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <h5 class="brand-font mb-3 text-dark border-bottom pb-2">1. Información Comercial & Fiscal</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Empresa / Razón Comercial</label>
                            <input type="text" name="nombre_empresa" class="form-control" value="{{ old('nombre_empresa', $config->nombre_empresa) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RUC (Registro Único de Contribuyente)</label>
                            <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $config->ruc) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección Principal / Showroom Central</label>
                            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $config->direccion) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono de Atención</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $config->telefono) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Electrónico de Ventas</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $config->email) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sitio Web / Catálogo Virtual</label>
                            <input type="text" name="sitio_web" class="form-control" value="{{ old('sitio_web', $config->sitio_web) }}" placeholder="https://muebleriaperu.com">
                        </div>
                    </div>

                    <h5 class="brand-font mb-3 text-dark border-bottom pb-2">2. Logotipo e Imagen Corporativa</h5>
                    <div class="row g-3 mb-4 align-items-center">
                        <div class="col-md-3 text-center">
                            @if($config->logo && file_exists(public_path('storage/' . $config->logo)))
                                <img src="{{ asset('storage/' . $config->logo) }}" alt="Logo Empresa" class="img-thumbnail rounded-3 p-2 shadow-sm" style="max-height: 90px; max-width: 100%;">
                            @else
                                <div class="border border-2 border-dashed rounded-3 p-3 text-muted" style="background: var(--bg-body);">
                                    <i class="fa-solid fa-image fs-3 mb-1 text-secondary"></i>
                                    <div style="font-size: 11px;">Sin logotipo cargado</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">Subir / Reemplazar Logotipo Oficial</label>
                            <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            <div class="form-text">Recomendado: Imagen PNG o WEBP con fondo transparente o blanco (máx. 2MB). Se utilizará en el sistema y comprobantes PDF.</div>
                        </div>
                    </div>

                    <h5 class="brand-font mb-3 text-dark border-bottom pb-2">3. Parámetros de Facturación & Series</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Símbolo Moneda</label>
                            <input type="text" name="moneda_simbolo" class="form-control" value="{{ old('moneda_simbolo', $config->moneda_simbolo ?? 'S/.') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Impuesto IGV (%)</label>
                            <input type="number" step="0.1" name="porcentaje_igv" class="form-control" value="{{ old('porcentaje_igv', $config->porcentaje_igv ?? 18) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Serie Cotizaciones</label>
                            <input type="text" name="serie_cotizacion" class="form-control" value="{{ old('serie_cotizacion', $config->serie_cotizacion ?? 'COT1') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Serie Boleta</label>
                            <input type="text" name="serie_boleta" class="form-control" value="{{ old('serie_boleta', $config->serie_boleta ?? 'B001') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Serie Factura</label>
                            <input type="text" name="serie_factura" class="form-control" value="{{ old('serie_factura', $config->serie_factura ?? 'F001') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Serie Nota Venta</label>
                            <input type="text" name="serie_nota_venta" class="form-control" value="{{ old('serie_nota_venta', $config->serie_nota_venta ?? 'NV01') }}" required>
                        </div>
                    </div>

                    <h5 class="brand-font mb-3 text-dark border-bottom pb-2">4. Términos de Garantía en Muebles</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Condiciones de Garantía Impresas en Comprobante</label>
                            <textarea name="terminos_garantia" class="form-control" rows="3">{{ old('terminos_garantia', $config->terminos_garantia) }}</textarea>
                            <div class="form-text">Texto que aparecerá en las boletas, facturas y cotizaciones impresas a los clientes.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn text-white px-4 py-2 rounded-3 fw-semibold" style="background: var(--primary-orange);">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card mb-4">
                <h5 class="brand-font mb-3 text-dark">Información del Sistema</h5>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 13px;">
                    <li class="d-flex justify-content-between">
                        <span class="text-secondary">Framework:</span>
                        <span class="fw-bold">Laravel 11.x / PHP 8.3</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-secondary">Base de Datos:</span>
                        <span class="fw-bold">MySQL (dev_muebleria)</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-secondary">Seguridad:</span>
                        <span class="fw-bold">Spatie Permissions</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-secondary">Sesión:</span>
                        <span class="badge bg-success-subtle text-success">Activa</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
