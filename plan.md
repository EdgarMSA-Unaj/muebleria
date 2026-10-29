# Sistema de Gestión — Mueblería (Laravel 11 + MySQL)

## Descripción General

Aplicación web empresarial completa para gestión de una mueblería, desarrollada con **Laravel 11**, **MySQL**, y un diseño tipo dashboard profesional inspirado en la imagen de referencia adjunta. El sistema cubre todas las áreas funcionales del negocio: ventas, inventario, compras, clientes, proveedores, reportes y administración.

---

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.3 + Laravel 11 |
| Frontend | Blade + Bootstrap 5.3 + Chart.js |
| Base de datos | MySQL 8 (`dev_muebleria`) |
| Autenticación | Laravel Breeze (session-based) |
| Permisos | Spatie Laravel Permission |
| Iconos | Font Awesome 6 |
| Gráficas | Chart.js 4 |
| Datatable | DataTables.js |
| Notificaciones | SweetAlert2 + Toast |

---

## Módulos del Sistema (Menú Vertical)

### 🏠 Dashboard
- KPIs en tiempo real (ventas del día, clientes, pedidos, gastos)
- Gráfica de ventas mensuales (línea)
- Gráfica de categorías más vendidas (dona)
- Tabla de últimas ventas recientes
- Stock con alertas de bajo inventario
- Pedidos pendientes de entrega

### 📦 Inventario
- **Productos** — Catálogo con imágenes, SKU, dimensiones, colores, materiales
- **Categorías** — Salas, Dormitorios, Comedores, Oficinas, Decoración, etc.
- **Unidades de Medida**
- **Almacenes / Ubicaciones**
- **Movimientos de Stock** — Entradas, Salidas, Traslados
- **Alertas de Stock Mínimo**
- **Ajustes de Inventario**

### 🛒 Ventas
- **Nueva Venta (POS)** — Punto de Venta con búsqueda rápida de productos
- **Pedidos de Venta** — Cotizaciones → Pedidos → Confirmado
- **Facturación** — Generación de facturas/boletas PDF
- **Devoluciones / Notas de Crédito**
- **Entregas** — Gestión de entregas a domicilio
- **Historial de Ventas**

### 🛍️ Compras
- **Órdenes de Compra** — A proveedores
- **Recepción de Mercancía**
- **Devoluciones a Proveedor**
- **Historial de Compras**

### 👥 Clientes (CRM)
- **Ficha de Cliente** — Datos, historial, saldo
- **Seguimiento de Leads** — Prospectos
- **Créditos a Clientes** — Cuentas por cobrar
- **Historial de Compras por Cliente**

### 🏭 Proveedores
- **Ficha de Proveedor**
- **Cuentas por Pagar**
- **Historial de Compras por Proveedor**

### 💰 Caja y Finanzas
- **Apertura/Cierre de Caja**
- **Registro de Gastos**
- **Cuentas por Cobrar**
- **Cuentas por Pagar**
- **Flujo de Caja**

### 📊 Reportes
- Ventas por período
- Ventas por producto / categoría
- Inventario valorizado
- Ranking de productos más vendidos
- Clientes top
- Estado de cuenta por cliente/proveedor
- Exportar PDF / Excel

### 👤 Usuarios y Seguridad
- **Gestión de Usuarios**
- **Roles** (Admin, Vendedor, Almacén, Contador)
- **Permisos por módulo**
- **Log de Actividad**

### ⚙️ Configuración
- Datos de la empresa (logo, RUC/NIT, dirección)
- Series de facturación
- Tipos de pago
- Impuestos / IGV
- Configuración general

---

## Diseño Visual

- **Sidebar izquierdo oscuro** (dark navy `#1e2a3b`) con logo de la empresa
- **Header top-bar** claro con buscador, notificaciones, perfil de usuario
- **Cards de KPIs** con iconos de color, valor principal y tendencia (%)
- **Paleta de colores**: Primario `#FF6B35` (naranja cálido madera), Secundario `#2C3E50` (azul oscuro), Acentos en verde y violeta
- **Google Fonts**: Outfit (headings) + Inter (body)
- **Animaciones suaves** en hover de menú, cards y botones
- **Responsive** adaptable a tablet

---

## Fase de Desarrollo (Prioridad)

### FASE 1 — Fundamentos (Esta sesión)
1. ✅ Crear proyecto Laravel 11
2. ✅ Configurar BD `dev_muebleria` + migraciones base
3. ✅ Sistema de autenticación (Login profesional)
4. ✅ Layout principal con sidebar + header
5. ✅ Dashboard con KPIs, gráficas y tablas
6. ✅ Módulo de Usuarios y Roles (Spatie)

### FASE 2 — Core Business
7. ✅ Módulo Inventario (Categorías, Productos, Stock, Kardex valorizado)
8. ✅ Módulo Clientes (CRM, créditos y abonos)
9. ✅ Módulo Proveedores (Cuentas por pagar y amortizaciones)

### FASE 3 — Transaccional
10. ✅ Módulo Ventas (POS, facturación/boletas, comprobantes PDF e impresión)
11. ✅ Módulo Compras (Órdenes de compra y recepción de mercadería)
12. ✅ Módulo Caja y Finanzas (Apertura/cierre, flujo de caja y egresos)
13. ✅ Módulo Producción (Órdenes de fabricación por etapas)
14. ✅ Módulo Despachos (Asignación y seguimiento de entregas)

### FASE 4 — Análisis y Configuración
15. ✅ Módulo Reportes (Exportación CSV, analítica financiera y de rotación)
16. ✅ Configuración general del sistema (Parámetros de empresa y series de comprobante)

---

## Base de Datos — Configuración

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dev_muebleria
DB_USERNAME=root
DB_PASSWORD=
```

---

## Verificación

- Login funcional con redirección al Dashboard
- Sidebar con todos los módulos expandibles
- Dashboard con datos dinámicos (seeder de prueba)
- Migraciones ejecutadas correctamente
- Seeders con datos de ejemplo (productos, clientes, ventas)
