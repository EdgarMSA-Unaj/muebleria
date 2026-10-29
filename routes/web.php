<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\DespachosController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\BackupController;

// Redirección inicial
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Protegidas del Sistema
Route::middleware(['auth'])->group(function () {
    
    // 1. Dashboard Principal (Accesible para todos los usuarios autenticados)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Módulo de Ventas, Pedidos, Despachos y Clientes (Vendedores y Administradores)
    Route::middleware(['role:Administrador|Vendedor'])->group(function () {
        // Ventas
        Route::get('/ventas', [VentasController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/crear', [VentasController::class, 'create'])->name('ventas.create');
        Route::get('/ventas/exportar-csv', [VentasController::class, 'exportarCsv'])->name('ventas.exportarCsv');
        Route::post('/ventas', [VentasController::class, 'store'])->name('ventas.store');
        Route::get('/ventas/{venta}/imprimir', [VentasController::class, 'imprimir'])->name('ventas.imprimir');
        Route::get('/ventas/{venta}/pdf', [VentasController::class, 'descargarPdf'])->name('ventas.pdf');
        Route::post('/ventas/{venta}/cambiar-estado', [VentasController::class, 'cambiarEstado'])->name('ventas.cambiarEstado');

        // Despachos
        Route::get('/despachos', [DespachosController::class, 'index'])->name('despachos.index');
        Route::post('/despachos/{venta}/asignar', [DespachosController::class, 'asignarRepartidor'])->name('despachos.asignar');

        // Clientes (CRM)
        Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
        Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');
        Route::put('/clientes/{cliente}', [ClientesController::class, 'update'])->name('clientes.update');
        Route::post('/clientes/{cliente}/abono', [ClientesController::class, 'registrarAbono'])->name('clientes.abono');
        Route::delete('/clientes/{cliente}', [ClientesController::class, 'destroy'])->name('clientes.destroy');
    });

    // 3. Módulo de Inventario (Consulta abierta a Vendedor, Almacenero y Admin; Edición para Almacenero y Admin)
    Route::middleware(['role:Administrador|Almacenero|Vendedor'])->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/kardex', [InventarioController::class, 'kardex'])->name('inventario.kardex');
        Route::get('/inventario/exportar-csv', [InventarioController::class, 'exportarCsv'])->name('inventario.exportarCsv');
    });

    Route::middleware(['role:Administrador|Almacenero'])->group(function () {
        // Gestión y Ajustes de Inventario
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
        Route::put('/inventario/{producto}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::delete('/inventario/{producto}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
        Route::post('/inventario/{producto}/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('inventario.ajustarStock');

        // Categorías de Muebles
        Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
        Route::post('/categorias', [CategoriasController::class, 'store'])->name('categorias.store');
        Route::put('/categorias/{categoria}', [CategoriasController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoria}', [CategoriasController::class, 'destroy'])->name('categorias.destroy');

        // Taller de Fabricación & Producción
        Route::get('/produccion', [ProduccionController::class, 'index'])->name('produccion.index');
        Route::post('/produccion', [ProduccionController::class, 'store'])->name('produccion.store');
        Route::post('/produccion/{orden}/avanzar', [ProduccionController::class, 'cambiarFase'])->name('produccion.avanzar');

        // Compras a Proveedores (Abastecimiento)
        Route::get('/compras', [ComprasController::class, 'index'])->name('compras.index');
        Route::get('/compras/crear', [ComprasController::class, 'create'])->name('compras.create');
        Route::post('/compras', [ComprasController::class, 'store'])->name('compras.store');
        Route::post('/compras/{compra}/recibir', [ComprasController::class, 'recibir'])->name('compras.recibir');
    });

    // 4. Proveedores y Cuentas por Pagar (Almaceneros para consulta, Contadores y Administradores para pagos)
    Route::middleware(['role:Administrador|Almacenero|Contador'])->group(function () {
        Route::get('/proveedores', [ProveedoresController::class, 'index'])->name('proveedores.index');
        Route::post('/proveedores', [ProveedoresController::class, 'store'])->name('proveedores.store');
        Route::put('/proveedores/{proveedor}', [ProveedoresController::class, 'update'])->name('proveedores.update');
        Route::delete('/proveedores/{proveedor}', [ProveedoresController::class, 'destroy'])->name('proveedores.destroy');
    });

    // 5. Finanzas, Caja, Amortización a Proveedores y Reportes (Contador y Administrador)
    Route::middleware(['role:Administrador|Contador'])->group(function () {
        // Caja y Gastos Operativos
        Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('/caja/abrir', [CajaController::class, 'abrirCaja'])->name('caja.abrir');
        Route::post('/caja/{caja}/cerrar', [CajaController::class, 'cerrarCaja'])->name('caja.cerrar');
        Route::post('/caja/gasto', [CajaController::class, 'registrarGasto'])->name('caja.gasto');

        // Amortización / Pagos a Proveedores
        Route::post('/proveedores/{proveedor}/pago', [ProveedoresController::class, 'registrarPago'])->name('proveedores.pago');

        // Reportes Financieros
        Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar-csv', [ReportesController::class, 'exportarCsv'])->name('reportes.exportarCsv');
    });

    // 6. Configuración y Gestión de Usuarios (Exclusivo Administrador)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::post('/usuarios/{usuario}/toggle', [UsuariosController::class, 'toggleActivo'])->name('usuarios.toggle');
        Route::delete('/usuarios/{usuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        // 7. Copias de Seguridad, Restauración y Reseteo del Sistema
        Route::get('/respaldos', [BackupController::class, 'index'])->name('respaldos.index');
        Route::post('/respaldos/crear', [BackupController::class, 'crearBackup'])->name('respaldos.crear');
        Route::get('/respaldos/descargar/{filename}', [BackupController::class, 'descargar'])->name('respaldos.descargar');
        Route::post('/respaldos/restaurar', [BackupController::class, 'restaurar'])->name('respaldos.restaurar');
        Route::delete('/respaldos/{filename}', [BackupController::class, 'eliminar'])->name('respaldos.eliminar');
        Route::post('/respaldos/resetear', [BackupController::class, 'resetear'])->name('respaldos.resetear');
    });
});
