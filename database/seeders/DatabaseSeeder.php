<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\MovimientoStock;
use App\Models\Caja;
use App\Models\Gasto;
use App\Models\Configuracion;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles y Permisos
        $roleAdmin = Role::create(['name' => 'Administrador']);
        $roleVendedor = Role::create(['name' => 'Vendedor']);
        $roleAlmacen = Role::create(['name' => 'Almacenero']);
        $roleContador = Role::create(['name' => 'Contador']);

        $permisos = [
            'ver dashboard',
            'gestionar inventario',
            'gestionar ventas',
            'gestionar compras',
            'gestionar clientes',
            'gestionar proveedores',
            'gestionar caja',
            'gestionar gastos',
            'ver reportes',
            'gestionar usuarios',
            'configuracion sistema'
        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        $roleAdmin->syncPermissions($permisos);
        $roleVendedor->syncPermissions(['ver dashboard', 'gestionar ventas', 'gestionar clientes']);
        $roleAlmacen->syncPermissions(['ver dashboard', 'gestionar inventario', 'gestionar compras', 'gestionar proveedores']);
        $roleContador->syncPermissions(['ver dashboard', 'gestionar caja', 'gestionar gastos', 'ver reportes']);

        // 2. Usuarios
        $admin = User::create([
            'name' => 'John Doe (Gerente)',
            'email' => 'admin@muebleria.com',
            'password' => Hash::make('admin123'),
            'phone' => '+51 987 111 222',
            'avatar' => 'avatar-admin.png',
            'is_active' => true,
        ]);
        $admin->assignRole($roleAdmin);

        $vendedor = User::create([
            'name' => 'Sofia Ramos',
            'email' => 'vendedor@muebleria.com',
            'password' => Hash::make('vendedor123'),
            'phone' => '+51 987 333 444',
            'avatar' => 'avatar-vendedor.png',
            'is_active' => true,
        ]);
        $vendedor->assignRole($roleVendedor);

        $almacenero = User::create([
            'name' => 'Miguel Torres',
            'email' => 'almacen@muebleria.com',
            'password' => Hash::make('almacen123'),
            'phone' => '+51 987 555 666',
            'avatar' => 'avatar-almacen.png',
            'is_active' => true,
        ]);
        $almacenero->assignRole($roleAlmacen);

        // 3. Configuración Inicial
        Configuracion::create([
            'nombre_empresa' => 'Muebles & Diseño Elegance',
            'ruc' => '20608945123',
            'direccion' => 'Av. Javier Prado Este 2850, San Borja, Lima',
            'telefono' => '+51 (01) 485-9920',
            'email' => 'ventas@muebleselegance.pe',
            'sitio_web' => 'https://muebleselegance.pe',
            'moneda_simbolo' => 'S/.',
            'moneda_nombre' => 'Soles',
            'porcentaje_igv' => 18.00,
            'serie_boleta' => 'B001',
            'serie_factura' => 'F001',
            'serie_nota_venta' => 'NV01',
            'serie_cotizacion' => 'COT1',
            'terminos_garantia' => 'Garantía estructural de 2 años en madera maciza y 1 año en tapicería y esponjas de alta densidad.',
        ]);

        // 4. Almacenes
        $almCentral = Almacen::create([
            'nombre' => 'Almacén Central & Fábrica',
            'descripcion' => 'Planta principal de fabricación y acopio de piezas',
            'ubicacion' => 'Zona Industrial Villa El Salvador Mz C Lote 4',
            'principal' => true,
            'activo' => true
        ]);

        $almShowroom1 = Almacen::create([
            'nombre' => 'Showroom Miraflores',
            'descripcion' => 'Exhibición y tienda comercial premium',
            'ubicacion' => 'Av. José Pardo 640, Miraflores',
            'principal' => false,
            'activo' => true
        ]);

        $almShowroom2 = Almacen::create([
            'nombre' => 'Showroom San Isidro',
            'descripcion' => 'Espacio de diseño y ventas corporativas',
            'ubicacion' => 'Av. Conquistadores 410, San Isidro',
            'principal' => false,
            'activo' => true
        ]);

        // 5. Categorías
        $catSalas = Categoria::create([
            'nombre' => 'Salas & Sofás',
            'descripcion' => 'Sofás seccionales, 3 cuerpos, butacas y sillones reclinables',
            'icono' => 'fa-couch',
            'color' => '#FF6B35',
            'activo' => true
        ]);

        $catComedores = Categoria::create([
            'nombre' => 'Comedores & Sillas',
            'descripcion' => 'Mesas extensibles, juegos de comedor de 6 y 8 sillas, aparadores',
            'icono' => 'fa-chair',
            'color' => '#3B82F6',
            'activo' => true
        ]);

        $catDormitorios = Categoria::create([
            'nombre' => 'Dormitorios & Camas',
            'descripcion' => 'Cabeceras tapizadas, camas queen/king, cómodas y veladores',
            'icono' => 'fa-bed',
            'color' => '#8B5CF6',
            'activo' => true
        ]);

        $catOficina = Categoria::create([
            'nombre' => 'Oficina & Estudios',
            'descripcion' => 'Escritorios ergonómicos, libreros flotantes y sillas gerenciales',
            'icono' => 'fa-briefcase',
            'color' => '#10B981',
            'activo' => true
        ]);

        $catTV = Categoria::create([
            'nombre' => 'Centros de TV & Racks',
            'descripcion' => 'Muebles flotantes para Smart TV, paneles alistonados y vitrinas',
            'icono' => 'fa-tv',
            'color' => '#F59E0B',
            'activo' => true
        ]);

        $catDecoracion = Categoria::create([
            'nombre' => 'Mesas de Centro & Decoración',
            'descripcion' => 'Mesas auxiliares de mármol/madera, repisas y espejos con marco de madera',
            'icono' => 'fa-lightbulb',
            'color' => '#EC4899',
            'activo' => true
        ]);

        // 6. Productos Realistas de Mueblería
        $p1 = Producto::create([
            'sku' => 'SOF-ROB-001',
            'nombre' => 'Sofá Seccional Nórdico 4 Cuerpos Roble',
            'descripcion' => 'Estructura en madera tornillo seca al horno, espuma Zebra 28kg de alta resiliencia y tapiz lino antiarañazos.',
            'categoria_id' => $catSalas->id,
            'marca' => 'Elegance Design',
            'material' => 'Madera Tornillo y Lino Suizo',
            'color' => 'Gris Perla',
            'largo' => 260.00,
            'ancho' => 170.00,
            'alto' => 85.00,
            'peso' => 68.00,
            'precio_compra' => 1650.00,
            'precio_venta' => 3199.00,
            'precio_minimo' => 2950.00,
            'igv' => 18.00,
            'stock_actual' => 8,
            'stock_minimo' => 3,
            'stock_maximo' => 20,
            'unidad_medida' => 'UND',
            'activo' => true,
            'destacado' => true
        ]);

        $p2 = Producto::create([
            'sku' => 'COM-CED-002',
            'nombre' => 'Juego de Comedor Imperial 6 Sillas Cedro',
            'descripcion' => 'Mesa de madera cedro macizo acabado poliuretano semi-mate con 6 sillas ergonómicas tapizadas en terciopelo beige.',
            'categoria_id' => $catComedores->id,
            'marca' => 'Master Wood',
            'material' => 'Cedro Selecto & Terciopelo',
            'color' => 'Nogal Clásico / Beige',
            'largo' => 180.00,
            'ancho' => 95.00,
            'alto' => 78.00,
            'peso' => 85.00,
            'precio_compra' => 2200.00,
            'precio_venta' => 4490.00,
            'precio_minimo' => 4100.00,
            'igv' => 18.00,
            'stock_actual' => 4,
            'stock_minimo' => 2,
            'stock_maximo' => 15,
            'unidad_medida' => 'JGO',
            'activo' => true,
            'destacado' => true
        ]);

        $p3 = Producto::create([
            'sku' => 'DOR-KIN-003',
            'nombre' => 'Cama King Size Flotante con Cabecera Led',
            'descripcion' => 'Cama suspendida visualmente con estructura de fierro reforzado y madera roble, cabecera acolchada en cuero sintético y luz cálida indirecta.',
            'categoria_id' => $catDormitorios->id,
            'marca' => 'Elegance Design',
            'material' => 'Roble Americano & Cuero Sintético',
            'color' => 'Miel & Marrón Café',
            'largo' => 210.00,
            'ancho' => 200.00,
            'alto' => 110.00,
            'peso' => 92.00,
            'precio_compra' => 1950.00,
            'precio_venta' => 3850.00,
            'precio_minimo' => 3500.00,
            'igv' => 18.00,
            'stock_actual' => 3,
            'stock_minimo' => 3, // ALERTA: Igual al mínimo
            'stock_maximo' => 10,
            'unidad_medida' => 'UND',
            'activo' => true,
            'destacado' => true
        ]);

        $p4 = Producto::create([
            'sku' => 'OFI-ESC-004',
            'nombre' => 'Escritorio Gerencial en L con Gavetero Móvil',
            'descripcion' => 'Tablero de melamina Vesto 25mm con canto grueso, estructura metálica con pintura electrostática negra mate y pasacables integrado.',
            'categoria_id' => $catOficina->id,
            'marca' => 'Modulart',
            'material' => 'Melamina 25mm & Tubo Estructural',
            'color' => 'Carvalho & Negro',
            'largo' => 160.00,
            'ancho' => 140.00,
            'alto' => 75.00,
            'peso' => 45.00,
            'precio_compra' => 580.00,
            'precio_venta' => 1250.00,
            'precio_minimo' => 1100.00,
            'igv' => 18.00,
            'stock_actual' => 12,
            'stock_minimo' => 4,
            'stock_maximo' => 25,
            'unidad_medida' => 'UND',
            'activo' => true,
            'destacado' => false
        ]);

        $p5 = Producto::create([
            'sku' => 'PAN-TV-005',
            'nombre' => 'Panel TV Alistonado Flotante hasta 75 Pulgadas',
            'descripcion' => 'Panel acústico alistonado con acabado amaderado, repisa flotante con puertas biseladas y orificios ocultos para cableado.',
            'categoria_id' => $catTV->id,
            'marca' => 'Elegance Design',
            'material' => 'MDF Enchapado y Poliuretano',
            'color' => 'Roble Claro & Grafito',
            'largo' => 200.00,
            'ancho' => 35.00,
            'alto' => 180.00,
            'peso' => 54.00,
            'precio_compra' => 890.00,
            'precio_venta' => 1980.00,
            'precio_minimo' => 1800.00,
            'igv' => 18.00,
            'stock_actual' => 1, // ALERTA: Bajo stock!
            'stock_minimo' => 4,
            'stock_maximo' => 15,
            'unidad_medida' => 'UND',
            'activo' => true,
            'destacado' => true
        ]);

        $p6 = Producto::create([
            'sku' => 'DEC-CEN-006',
            'nombre' => 'Mesa de Centro Nido Mármol y Madera Maciza',
            'descripcion' => 'Juego de 2 mesas redondas auxiliares encajables. Base de fresno macizo con tapa de cuarzo blanco calacatta.',
            'categoria_id' => $catDecoracion->id,
            'marca' => 'Artisans Studio',
            'material' => 'Madera Fresno & Cuarzo Calacatta',
            'color' => 'Natural / Mármol Blanco',
            'largo' => 80.00,
            'ancho' => 80.00,
            'alto' => 45.00,
            'peso' => 28.00,
            'precio_compra' => 420.00,
            'precio_venta' => 950.00,
            'precio_minimo' => 850.00,
            'igv' => 18.00,
            'stock_actual' => 15,
            'stock_minimo' => 5,
            'stock_maximo' => 30,
            'unidad_medida' => 'JGO',
            'activo' => true,
            'destacado' => false
        ]);

        // 7. Clientes Reales
        $c1 = Cliente::create([
            'codigo' => 'CLI-0001',
            'tipo' => 'persona_natural',
            'nombre' => 'Valeria',
            'apellidos' => 'Gutierrez Palma',
            'documento_tipo' => 'DNI',
            'documento_numero' => '45892147',
            'email' => 'valeria.gutierrez@gmail.com',
            'telefono' => '01-4458921',
            'celular' => '984123890',
            'direccion' => 'Calle Los Nogales 245 Dpto 402',
            'distrito' => 'San Isidro',
            'ciudad' => 'Lima',
            'credito_limite' => 5000.00,
            'saldo_pendiente' => 0.00,
            'activo' => true
        ]);

        $c2 = Cliente::create([
            'codigo' => 'CLI-0002',
            'tipo' => 'empresa',
            'nombre' => 'Constructora e Inmobiliaria',
            'razon_social' => 'Inversiones Arquitectura & Hogar S.A.C.',
            'documento_tipo' => 'RUC',
            'documento_numero' => '20554189201',
            'email' => 'compras@arquihogar.pe',
            'telefono' => '01-6194400',
            'celular' => '998765412',
            'direccion' => 'Av. Primavera 1280 Piso 8',
            'distrito' => 'Surco',
            'ciudad' => 'Lima',
            'credito_limite' => 25000.00,
            'saldo_pendiente' => 4500.00,
            'activo' => true
        ]);

        $c3 = Cliente::create([
            'codigo' => 'CLI-0003',
            'tipo' => 'persona_natural',
            'nombre' => 'Rodrigo',
            'apellidos' => 'Alvarez De La Puente',
            'documento_tipo' => 'DNI',
            'documento_numero' => '09845120',
            'email' => 'rodrigo.alvarez@outlook.com',
            'celular' => '975412368',
            'direccion' => 'Jr. Batalla de Junín 320',
            'distrito' => 'Barranco',
            'ciudad' => 'Lima',
            'credito_limite' => 3000.00,
            'saldo_pendiente' => 0.00,
            'activo' => true
        ]);

        // 8. Proveedores
        $prov1 = Proveedor::create([
            'codigo' => 'PRV-001',
            'razon_social' => 'Maderera El Bosque Amazónico S.R.L.',
            'nombre_comercial' => 'Maderas El Bosque',
            'ruc' => '20492817450',
            'email' => 'pedidos@maderaselbosque.pe',
            'telefono' => '01-5341290',
            'celular' => '994112233',
            'contacto_nombre' => 'Ing. Juan Carlos Paredes',
            'direccion' => 'Carretera Central Km 18.5',
            'ciudad' => 'Lima',
            'banco' => 'BCP Soles',
            'cuenta_bancaria' => '193-45892147-0-12',
            'dias_credito' => 30,
            'activo' => true
        ]);

        $prov2 = Proveedor::create([
            'codigo' => 'PRV-002',
            'razon_social' => 'Textiles y Espumas Industriales S.A.',
            'nombre_comercial' => 'TexFoam Perú',
            'ruc' => '20108955214',
            'email' => 'ventas@texfoamperu.com',
            'telefono' => '01-3269874',
            'celular' => '987654320',
            'contacto_nombre' => 'Patricia Valdivia',
            'direccion' => 'Av. Colonial 1845',
            'ciudad' => 'Callao',
            'banco' => 'Interbank Soles',
            'cuenta_bancaria' => '200-3004589124',
            'dias_credito' => 15,
            'activo' => true
        ]);

        // 9. Caja Abierta
        $caja = Caja::create([
            'user_id' => $admin->id,
            'nombre_caja' => 'Caja Principal - Showroom Miraflores',
            'fecha_apertura' => now()->startOfDay()->addHours(8),
            'monto_inicial' => 800.00,
            'monto_ventas_efectivo' => 3199.00,
            'monto_ventas_digital' => 5440.00,
            'monto_gastos' => 350.00,
            'monto_final_esperado' => 3649.00,
            'estado' => 'abierta',
            'observaciones' => 'Turno mañana aperturado con sencillo para cambio en billetes de 20 y 50.'
        ]);

        // 10. Gastos Operativos
        Gasto::create([
            'user_id' => $admin->id,
            'caja_id' => $caja->id,
            'categoria_gasto' => 'Transporte & Flete',
            'descripcion' => 'Flete de despacho de madera tornillo desde almacén central a taller',
            'monto' => 180.00,
            'comprobante_tipo' => 'Recibo',
            'comprobante_numero' => 'REC-0089',
            'fecha_gasto' => now()->format('Y-m-d')
        ]);

        Gasto::create([
            'user_id' => $admin->id,
            'caja_id' => $caja->id,
            'categoria_gasto' => 'Insumos de Taller',
            'descripcion' => 'Lijas al agua, cola sintética D3 y diluyente para lacas',
            'monto' => 170.00,
            'comprobante_tipo' => 'Factura',
            'comprobante_numero' => 'F002-45812',
            'fecha_gasto' => now()->format('Y-m-d')
        ]);

        // 11. Ventas Registradas
        $v1 = Venta::create([
            'numero_comprobante' => 'B001-0000142',
            'tipo_comprobante' => 'boleta',
            'cliente_id' => $c1->id,
            'user_id' => $admin->id,
            'fecha_emision' => now()->subDays(1),
            'condicion_pago' => 'tarjeta',
            'estado' => 'en_entrega',
            'subtotal' => 2711.02,
            'igv' => 487.98,
            'descuento' => 0.00,
            'total' => 3199.00,
            'monto_pagado' => 3199.00,
            'saldo' => 0.00,
            'requiere_entrega' => true,
            'requiere_armado' => true,
            'fecha_entrega_programada' => now()->addDays(2),
            'direccion_entrega' => 'Calle Los Nogales 245 Dpto 402, San Isidro',
            'referencia_entrega' => 'Frente al parque Olivar, edificio blanco',
            'costo_envio' => 80.00,
            'repartidor_asignado' => 'Transportes & Mudanzas Ramos',
            'observaciones' => 'Subir por escalera caracol o ascensor de servicio amplio.'
        ]);

        VentaDetalle::create([
            'venta_id' => $v1->id,
            'producto_id' => $p1->id,
            'almacen_id' => $almShowroom1->id,
            'cantidad' => 1,
            'precio_unitario' => 3199.00,
            'descuento' => 0.00,
            'subtotal' => 3199.00,
            'color_acabado' => 'Gris Perla / Lino Suizo',
            'notas_personalizacion' => 'Patas de madera laqueadas en roble oscuro a pedido del cliente.'
        ]);

        $v2 = Venta::create([
            'numero_comprobante' => 'F001-0000085',
            'tipo_comprobante' => 'factura',
            'cliente_id' => $c2->id,
            'user_id' => $vendedor->id,
            'fecha_emision' => now()->subDays(3),
            'condicion_pago' => 'transferencia',
            'estado' => 'completada',
            'subtotal' => 4567.80,
            'igv' => 822.20,
            'descuento' => 100.00,
            'total' => 5440.00,
            'monto_pagado' => 5440.00,
            'saldo' => 0.00,
            'requiere_entrega' => true,
            'requiere_armado' => true,
            'fecha_entrega_programada' => now()->subDays(1),
            'direccion_entrega' => 'Av. Primavera 1280 Piso 8, Surco',
            'referencia_entrega' => 'Recepción Corporativa',
            'costo_envio' => 0.00,
            'repartidor_asignado' => 'Móvil Interno 2',
            'observaciones' => 'Entregado conforme y armado en sala de directorio.'
        ]);

        VentaDetalle::create([
            'venta_id' => $v2->id,
            'producto_id' => $p2->id,
            'almacen_id' => $almCentral->id,
            'cantidad' => 1,
            'precio_unitario' => 4490.00,
            'descuento' => 0.00,
            'subtotal' => 4490.00,
            'color_acabado' => 'Nogal Clásico',
            'notas_personalizacion' => 'Mesa de reuniones de 6 puestos para oficina principal.'
        ]);

        VentaDetalle::create([
            'venta_id' => $v2->id,
            'producto_id' => $p6->id,
            'almacen_id' => $almCentral->id,
            'cantidad' => 1,
            'precio_unitario' => 950.00,
            'descuento' => 0.00,
            'subtotal' => 950.00,
            'color_acabado' => 'Mármol Blanco Calacatta',
            'notas_personalizacion' => 'Mesas nido para sala de espera de gerencia.'
        ]);

        $v3 = Venta::create([
            'numero_comprobante' => 'COT1-0000054',
            'tipo_comprobante' => 'cotizacion',
            'cliente_id' => $c3->id,
            'user_id' => $vendedor->id,
            'fecha_emision' => now(),
            'condicion_pago' => 'contado',
            'estado' => 'cotizacion',
            'subtotal' => 4940.68,
            'igv' => 889.32,
            'descuento' => 0.00,
            'total' => 5830.00,
            'monto_pagado' => 0.00,
            'saldo' => 5830.00,
            'requiere_entrega' => true,
            'requiere_armado' => true,
            'direccion_entrega' => 'Jr. Batalla de Junín 320, Barranco',
            'costo_envio' => 60.00,
            'observaciones' => 'Cotización válida por 15 días calendario.'
        ]);

        VentaDetalle::create([
            'venta_id' => $v3->id,
            'producto_id' => $p3->id,
            'almacen_id' => $almShowroom1->id,
            'cantidad' => 1,
            'precio_unitario' => 3850.00,
            'descuento' => 0.00,
            'subtotal' => 3850.00,
            'color_acabado' => 'Miel & Marrón Café',
            'notas_personalizacion' => 'Cama King con veladores a juego.'
        ]);

        VentaDetalle::create([
            'venta_id' => $v3->id,
            'producto_id' => $p5->id,
            'almacen_id' => $almShowroom1->id,
            'cantidad' => 1,
            'precio_unitario' => 1980.00,
            'descuento' => 0.00,
            'subtotal' => 1980.00,
            'color_acabado' => 'Roble Claro',
            'notas_personalizacion' => 'Panel flotante para TV 65 pulgadas.'
        ]);

        // 12. Compras a Proveedores
        $compra1 = Compra::create([
            'numero_orden' => 'OC-2026-00045',
            'factura_proveedor' => 'F003-0009821',
            'proveedor_id' => $prov1->id,
            'user_id' => $admin->id,
            'almacen_id' => $almCentral->id,
            'fecha_emision' => now()->subDays(10),
            'fecha_entrega_esperada' => now()->subDays(7),
            'condicion_pago' => 'credito_30d',
            'estado' => 'recibido',
            'subtotal' => 8474.58,
            'igv' => 1525.42,
            'total' => 10000.00,
            'monto_pagado' => 4000.00,
            'observaciones' => 'Lote de 200 pies cúbicos de madera cedro selecto y tornillo seco al horno.'
        ]);

        CompraDetalle::create([
            'compra_id' => $compra1->id,
            'producto_id' => $p2->id,
            'cantidad' => 3,
            'cantidad_recibida' => 3,
            'costo_unitario' => 2200.00,
            'subtotal' => 6600.00
        ]);

        // 13. Movimientos Kardex
        MovimientoStock::create([
            'producto_id' => $p1->id,
            'almacen_id' => $almShowroom1->id,
            'user_id' => $admin->id,
            'tipo_movimiento' => 'salida_venta',
            'cantidad' => 1,
            'stock_anterior' => 9,
            'stock_nuevo' => 8,
            'referencia_tipo' => 'Venta',
            'referencia_id' => $v1->id,
            'motivo' => 'Venta Comprobante ' . $v1->numero_comprobante
        ]);
    }
}
