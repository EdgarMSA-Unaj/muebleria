-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para dev_muebleria
CREATE DATABASE IF NOT EXISTS `dev_muebleria` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `dev_muebleria`;

-- Volcando estructura para tabla dev_muebleria.almacenes
CREATE TABLE IF NOT EXISTS `almacenes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.almacenes: ~3 rows (aproximadamente)
DELETE FROM `almacenes`;
INSERT INTO `almacenes` (`id`, `nombre`, `descripcion`, `ubicacion`, `principal`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Almacén Central & Fábrica', 'Planta principal de fabricación y acopio de piezas', 'Zona Industrial Villa El Salvador Mz C Lote 4', 1, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(2, 'Showroom Miraflores', 'Exhibición y tienda comercial premium', 'Av. José Pardo 640, Miraflores', 0, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(3, 'Showroom San Isidro', 'Espacio de diseño y ventas corporativas', 'Av. Conquistadores 410, San Isidro', 0, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07');

-- Volcando estructura para tabla dev_muebleria.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.cache: ~0 rows (aproximadamente)
DELETE FROM `cache`;

-- Volcando estructura para tabla dev_muebleria.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.cache_locks: ~0 rows (aproximadamente)
DELETE FROM `cache_locks`;

-- Volcando estructura para tabla dev_muebleria.caja
CREATE TABLE IF NOT EXISTS `caja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nombre_caja` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Caja Principal',
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_inicial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_ventas_efectivo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_ventas_digital` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_gastos` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_final_esperado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_final_real` decimal(12,2) DEFAULT NULL,
  `diferencia` decimal(12,2) DEFAULT NULL,
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caja_user_id_foreign` (`user_id`),
  CONSTRAINT `caja_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.caja: ~1 rows (aproximadamente)
DELETE FROM `caja`;
INSERT INTO `caja` (`id`, `user_id`, `nombre_caja`, `fecha_apertura`, `fecha_cierre`, `monto_inicial`, `monto_ventas_efectivo`, `monto_ventas_digital`, `monto_gastos`, `monto_final_esperado`, `monto_final_real`, `diferencia`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Caja Principal - Showroom Miraflores', '2026-09-11 08:00:00', NULL, 800.00, 6973.82, 5440.00, 350.00, 7423.82, NULL, NULL, 'abierta', 'Turno mañana aperturado con sencillo para cambio en billetes de 20 y 50.', '2026-09-11 20:54:07', '2026-09-12 11:43:02');

-- Volcando estructura para tabla dev_muebleria.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-couch',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#FF6B35',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.categorias: ~6 rows (aproximadamente)
DELETE FROM `categorias`;
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`, `color`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Salas & Sofás', 'Sofás seccionales, 3 cuerpos, butacas y sillones reclinables', 'fa-couch', '#FF6B35', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(2, 'Comedores & Sillas', 'Mesas extensibles, juegos de comedor de 6 y 8 sillas, aparadores', 'fa-chair', '#3B82F6', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(3, 'Dormitorios & Camas', 'Cabeceras tapizadas, camas queen/king, cómodas y veladores', 'fa-bed', '#8B5CF6', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(4, 'Oficina & Estudios', 'Escritorios ergonómicos, libreros flotantes y sillas gerenciales', 'fa-briefcase', '#10B981', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(5, 'Centros de TV & Racks', 'Muebles flotantes para Smart TV, paneles alistonados y vitrinas', 'fa-tv', '#F59E0B', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(6, 'Mesas de Centro & Decoración', 'Mesas auxiliares de mármol/madera, repisas y espejos con marco de madera', 'fa-lightbulb', '#EC4899', 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL);

-- Volcando estructura para tabla dev_muebleria.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('persona_natural','empresa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persona_natural',
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento_tipo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DNI',
  `documento_numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distrito` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Perú',
  `fecha_nacimiento` date DEFAULT NULL,
  `credito_limite` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_codigo_unique` (`codigo`),
  UNIQUE KEY `clientes_documento_numero_unique` (`documento_numero`),
  KEY `clientes_activo_documento_numero_index` (`activo`,`documento_numero`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.clientes: ~3 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `codigo`, `tipo`, `nombre`, `apellidos`, `razon_social`, `documento_tipo`, `documento_numero`, `email`, `telefono`, `celular`, `direccion`, `distrito`, `ciudad`, `pais`, `fecha_nacimiento`, `credito_limite`, `saldo_pendiente`, `notas`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'CLI-0001', 'persona_natural', 'Valeria', 'Gutierrez Palma', NULL, 'DNI', '45892147', 'valeria.gutierrez@gmail.com', '01-4458921', '984123890', 'Calle Los Nogales 245 Dpto 402', 'San Isidro', 'Lima', 'Perú', NULL, 5000.00, 0.00, NULL, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(2, 'CLI-0002', 'empresa', 'Constructora e Inmobiliaria', NULL, 'Inversiones Arquitectura & Hogar S.A.C.', 'RUC', '20554189201', 'compras@arquihogar.pe', '01-6194400', '998765412', 'Av. Primavera 1280 Piso 8', 'Surco', 'Lima', 'Perú', NULL, 25000.00, 4500.00, NULL, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(3, 'CLI-0003', 'persona_natural', 'Rodrigo', 'Alvarez De La Puente', NULL, 'DNI', '09845120', 'rodrigo.alvarez@outlook.com', NULL, '975412368', 'Jr. Batalla de Junín 320', 'Barranco', 'Lima', 'Perú', NULL, 3000.00, 0.00, NULL, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL);

-- Volcando estructura para tabla dev_muebleria.compras
CREATE TABLE IF NOT EXISTS `compras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `factura_proveedor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `almacen_id` bigint unsigned DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_entrega_esperada` date DEFAULT NULL,
  `condicion_pago` enum('contado','credito_15d','credito_30d','credito_60d') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contado',
  `estado` enum('solicitado','aprobado','recibido_parcial','recibido','anulado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recibido',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_numero_orden_unique` (`numero_orden`),
  KEY `compras_proveedor_id_foreign` (`proveedor_id`),
  KEY `compras_user_id_foreign` (`user_id`),
  KEY `compras_almacen_id_foreign` (`almacen_id`),
  CONSTRAINT `compras_almacen_id_foreign` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compras_proveedor_id_foreign` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.compras: ~0 rows (aproximadamente)
DELETE FROM `compras`;
INSERT INTO `compras` (`id`, `numero_orden`, `factura_proveedor`, `proveedor_id`, `user_id`, `almacen_id`, `fecha_emision`, `fecha_entrega_esperada`, `condicion_pago`, `estado`, `subtotal`, `igv`, `total`, `monto_pagado`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'OC-2026-00045', 'F003-0009821', 1, 1, 1, '2026-09-01', '2026-09-04', 'credito_30d', 'recibido', 8474.58, 1525.42, 10000.00, 4000.00, 'Lote de 200 pies cúbicos de madera cedro selecto y tornillo seco al horno.', '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL);

-- Volcando estructura para tabla dev_muebleria.compra_detalles
CREATE TABLE IF NOT EXISTS `compra_detalles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int NOT NULL,
  `cantidad_recibida` int NOT NULL DEFAULT '0',
  `costo_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_detalles_compra_id_foreign` (`compra_id`),
  KEY `compra_detalles_producto_id_foreign` (`producto_id`),
  CONSTRAINT `compra_detalles_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compra_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.compra_detalles: ~0 rows (aproximadamente)
DELETE FROM `compra_detalles`;
INSERT INTO `compra_detalles` (`id`, `compra_id`, `producto_id`, `cantidad`, `cantidad_recibida`, `costo_unitario`, `subtotal`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 3, 3, 2200.00, 6600.00, '2026-09-11 20:54:07', '2026-09-11 20:54:07');

-- Volcando estructura para tabla dev_muebleria.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Mueblería Artesanal & Diseño',
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '20601234567',
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Av. Los Diseñadores 789, Lima',
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+51 987 654 321',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contacto@muebleriadiseno.com',
  `sitio_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'https://muebleriadiseno.com',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda_simbolo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/.',
  `moneda_nombre` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Soles',
  `porcentaje_igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `serie_boleta` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B001',
  `serie_factura` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F001',
  `serie_nota_venta` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NV01',
  `serie_cotizacion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COT1',
  `terminos_garantia` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.configuracion: ~0 rows (aproximadamente)
DELETE FROM `configuracion`;
INSERT INTO `configuracion` (`id`, `nombre_empresa`, `ruc`, `direccion`, `telefono`, `email`, `sitio_web`, `logo`, `moneda_simbolo`, `moneda_nombre`, `porcentaje_igv`, `serie_boleta`, `serie_factura`, `serie_nota_venta`, `serie_cotizacion`, `terminos_garantia`, `created_at`, `updated_at`) VALUES
	(1, 'Muebles & Diseño Elegance', '20608945123', 'Av. Javier Prado Este 2850, San Borja, Lima', '+51 (01) 485-9920', 'ventas@muebleselegance.pe', 'https://muebleselegance.pe', NULL, 'S/.', 'Soles', 18.00, 'B001', 'F001', 'NV01', 'COT1', 'Garantía estructural de 2 años en madera maciza y 1 año en tapicería y esponjas de alta densidad.', '2026-09-11 20:54:07', '2026-09-11 20:54:07');

-- Volcando estructura para tabla dev_muebleria.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.failed_jobs: ~0 rows (aproximadamente)
DELETE FROM `failed_jobs`;

-- Volcando estructura para tabla dev_muebleria.gastos
CREATE TABLE IF NOT EXISTS `gastos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `caja_id` bigint unsigned DEFAULT NULL,
  `categoria_gasto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `comprobante_tipo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_numero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_gasto` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gastos_user_id_foreign` (`user_id`),
  KEY `gastos_caja_id_foreign` (`caja_id`),
  CONSTRAINT `gastos_caja_id_foreign` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`) ON DELETE SET NULL,
  CONSTRAINT `gastos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.gastos: ~2 rows (aproximadamente)
DELETE FROM `gastos`;
INSERT INTO `gastos` (`id`, `user_id`, `caja_id`, `categoria_gasto`, `descripcion`, `monto`, `comprobante_tipo`, `comprobante_numero`, `fecha_gasto`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Transporte & Flete', 'Flete de despacho de madera tornillo desde almacén central a taller', 180.00, 'Recibo', 'REC-0089', '2026-09-11', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(2, 1, 1, 'Insumos de Taller', 'Lijas al agua, cola sintética D3 y diluyente para lacas', 170.00, 'Factura', 'F002-45812', '2026-09-11', '2026-09-11 20:54:07', '2026-09-11 20:54:07');

-- Volcando estructura para tabla dev_muebleria.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.jobs: ~0 rows (aproximadamente)
DELETE FROM `jobs`;

-- Volcando estructura para tabla dev_muebleria.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.job_batches: ~0 rows (aproximadamente)
DELETE FROM `job_batches`;

-- Volcando estructura para tabla dev_muebleria.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.migrations: ~18 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_09_11_154748_create_almacenes_table', 1),
	(5, '2026_09_11_154749_create_categorias_table', 1),
	(6, '2026_09_11_154750_create_productos_table', 1),
	(7, '2026_09_11_154751_create_clientes_table', 1),
	(8, '2026_09_11_154752_create_proveedores_table', 1),
	(9, '2026_09_11_154753_create_ventas_table', 1),
	(10, '2026_09_11_154754_create_venta_detalles_table', 1),
	(11, '2026_09_11_154755_create_compras_table', 1),
	(12, '2026_09_11_154756_create_compra_detalles_table', 1),
	(13, '2026_09_11_154758_create_movimientos_stock_table', 1),
	(14, '2026_09_11_154759_create_caja_table', 1),
	(15, '2026_09_11_154800_create_gastos_table', 1),
	(16, '2026_09_11_154801_create_configuracion_table', 1),
	(17, '2026_09_11_155107_create_permission_tables', 1),
	(18, '2026_09_11_161143_create_ordenes_produccion_table', 2);

-- Volcando estructura para tabla dev_muebleria.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.model_has_permissions: ~0 rows (aproximadamente)
DELETE FROM `model_has_permissions`;

-- Volcando estructura para tabla dev_muebleria.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.model_has_roles: ~3 rows (aproximadamente)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3);

-- Volcando estructura para tabla dev_muebleria.movimientos_stock
CREATE TABLE IF NOT EXISTS `movimientos_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint unsigned NOT NULL,
  `almacen_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `tipo_movimiento` enum('entrada_compra','salida_venta','ajuste_positivo','ajuste_negativo','traslado_origen','traslado_destino','devolucion_cliente','devolucion_proveedor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `referencia_tipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_id` bigint unsigned DEFAULT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_stock_almacen_id_foreign` (`almacen_id`),
  KEY `movimientos_stock_user_id_foreign` (`user_id`),
  KEY `movimientos_stock_producto_id_almacen_id_index` (`producto_id`,`almacen_id`),
  CONSTRAINT `movimientos_stock_almacen_id_foreign` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `movimientos_stock_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `movimientos_stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.movimientos_stock: ~0 rows (aproximadamente)
DELETE FROM `movimientos_stock`;
INSERT INTO `movimientos_stock` (`id`, `producto_id`, `almacen_id`, `user_id`, `tipo_movimiento`, `cantidad`, `stock_anterior`, `stock_nuevo`, `referencia_tipo`, `referencia_id`, `motivo`, `created_at`, `updated_at`) VALUES
	(1, 1, 2, 1, 'salida_venta', 1, 9, 8, 'Venta', 1, 'Venta Comprobante B001-0000142', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(2, 1, 1, 1, 'salida_venta', 1, 8, 7, 'Venta', 4, 'Venta B001-0000002', '2026-09-12 11:43:02', '2026-09-12 11:43:02');

-- Volcando estructura para tabla dev_muebleria.ordenes_produccion
CREATE TABLE IF NOT EXISTS `ordenes_produccion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `venta_id` bigint unsigned DEFAULT NULL,
  `maestro_carpintero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maestro_tapicero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_estimada_termino` date DEFAULT NULL,
  `fase` enum('corte_madera','armado_esqueleto','laqueado_pintura','tapizado','control_calidad','terminado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'corte_madera',
  `avance_porcentaje` int NOT NULL DEFAULT '10',
  `especificaciones_acabado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones_taller` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ordenes_produccion_codigo_unique` (`codigo`),
  KEY `ordenes_produccion_producto_id_foreign` (`producto_id`),
  KEY `ordenes_produccion_venta_id_foreign` (`venta_id`),
  CONSTRAINT `ordenes_produccion_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `ordenes_produccion_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.ordenes_produccion: ~2 rows (aproximadamente)
DELETE FROM `ordenes_produccion`;
INSERT INTO `ordenes_produccion` (`id`, `codigo`, `producto_id`, `venta_id`, `maestro_carpintero`, `maestro_tapicero`, `fecha_inicio`, `fecha_estimada_termino`, `fase`, `avance_porcentaje`, `especificaciones_acabado`, `observaciones_taller`, `created_at`, `updated_at`) VALUES
	(1, 'OP-2026-0001', 1, 1, 'Don Santos Huamán', 'Ricardo Córdova', '2026-09-06', '2026-09-14', 'tapizado', 80, 'Tapiz Lino Suizo Gris Perla, espuma Zebra 28kg alta densidad', 'Patas torneadas en roble oscuro terminadas y laqueadas.', '2026-09-11 21:13:14', '2026-09-11 21:13:14'),
	(2, 'OP-2026-0002', 2, NULL, 'Marcos Benites', 'Alberto Mendoza', '2026-09-03', '2026-09-13', 'laqueado_pintura', 60, 'Madera cedro macizo acabado poliuretano satinado nogal', 'Mesa de 6 puestos armada, en proceso de segunda mano de laca.', '2026-09-11 21:13:14', '2026-09-11 21:13:14'),
	(3, 'OP-2026-0003', 3, NULL, 'Don Santos Huamán', NULL, '2026-09-09', '2026-09-17', 'armado_esqueleto', 35, 'Estructura King size con refuerzo metálico y tiras led', 'Esqueleto de fierro y madera tornillo terminado.', '2026-09-11 21:13:14', '2026-09-11 21:13:14');

-- Volcando estructura para tabla dev_muebleria.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.password_reset_tokens: ~0 rows (aproximadamente)
DELETE FROM `password_reset_tokens`;

-- Volcando estructura para tabla dev_muebleria.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.permissions: ~11 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'ver dashboard', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(2, 'gestionar inventario', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(3, 'gestionar ventas', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(4, 'gestionar compras', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(5, 'gestionar clientes', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(6, 'gestionar proveedores', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(7, 'gestionar caja', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(8, 'gestionar gastos', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(9, 'ver reportes', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(10, 'gestionar usuarios', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(11, 'configuracion sistema', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06');

-- Volcando estructura para tabla dev_muebleria.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_id` bigint unsigned NOT NULL,
  `marca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `largo` decimal(8,2) DEFAULT NULL COMMENT 'cm',
  `ancho` decimal(8,2) DEFAULT NULL COMMENT 'cm',
  `alto` decimal(8,2) DEFAULT NULL COMMENT 'cm',
  `peso` decimal(8,2) DEFAULT NULL COMMENT 'kg',
  `precio_compra` decimal(12,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(12,2) NOT NULL DEFAULT '0.00',
  `precio_minimo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `stock_actual` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `stock_maximo` int DEFAULT NULL,
  `unidad_medida` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UND',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_sku_unique` (`sku`),
  KEY `productos_categoria_id_activo_index` (`categoria_id`,`activo`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.productos: ~6 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `sku`, `nombre`, `descripcion`, `categoria_id`, `marca`, `material`, `color`, `largo`, `ancho`, `alto`, `peso`, `precio_compra`, `precio_venta`, `precio_minimo`, `igv`, `stock_actual`, `stock_minimo`, `stock_maximo`, `unidad_medida`, `imagen`, `activo`, `destacado`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'SOF-ROB-001', 'Sofá Seccional Nórdico 4 Cuerpos Roble', 'Estructura en madera tornillo seca al horno, espuma Zebra 28kg de alta resiliencia y tapiz lino antiarañazos.', 1, 'Elegance Design', 'Madera Tornillo y Lino Suizo', 'Gris Perla', 260.00, 170.00, 85.00, 68.00, 1650.00, 3199.00, 2950.00, 18.00, 7, 3, 20, 'UND', NULL, 1, 1, '2026-09-11 20:54:07', '2026-09-12 11:43:02', NULL),
	(2, 'COM-CED-002', 'Juego de Comedor Imperial 6 Sillas Cedro', 'Mesa de madera cedro macizo acabado poliuretano semi-mate con 6 sillas ergonómicas tapizadas en terciopelo beige.', 2, 'Master Wood', 'Cedro Selecto & Terciopelo', 'Nogal Clásico / Beige', 180.00, 95.00, 78.00, 85.00, 2200.00, 4490.00, 4100.00, 18.00, 4, 2, 15, 'JGO', NULL, 1, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(3, 'DOR-KIN-003', 'Cama King Size Flotante con Cabecera Led', 'Cama suspendida visualmente con estructura de fierro reforzado y madera roble, cabecera acolchada en cuero sintético y luz cálida indirecta.', 3, 'Elegance Design', 'Roble Americano & Cuero Sintético', 'Miel & Marrón Café', 210.00, 200.00, 110.00, 92.00, 1950.00, 3850.00, 3500.00, 18.00, 3, 3, 10, 'UND', NULL, 1, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(4, 'OFI-ESC-004', 'Escritorio Gerencial en L con Gavetero Móvil', 'Tablero de melamina Vesto 25mm con canto grueso, estructura metálica con pintura electrostática negra mate y pasacables integrado.', 4, 'Modulart', 'Melamina 25mm & Tubo Estructural', 'Carvalho & Negro', 160.00, 140.00, 75.00, 45.00, 580.00, 1250.00, 1100.00, 18.00, 12, 4, 25, 'UND', NULL, 1, 0, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(5, 'PAN-TV-005', 'Panel TV Alistonado Flotante hasta 75 Pulgadas', 'Panel acústico alistonado con acabado amaderado, repisa flotante con puertas biseladas y orificios ocultos para cableado.', 5, 'Elegance Design', 'MDF Enchapado y Poliuretano', 'Roble Claro & Grafito', 200.00, 35.00, 180.00, 54.00, 890.00, 1980.00, 1800.00, 18.00, 1, 4, 15, 'UND', NULL, 1, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(6, 'DEC-CEN-006', 'Mesa de Centro Nido Mármol y Madera Maciza', 'Juego de 2 mesas redondas auxiliares encajables. Base de fresno macizo con tapa de cuarzo blanco calacatta.', 6, 'Artisans Studio', 'Madera Fresno & Cuarzo Calacatta', 'Natural / Mármol Blanco', 80.00, 80.00, 45.00, 28.00, 420.00, 950.00, 850.00, 18.00, 15, 5, 30, 'JGO', NULL, 1, 0, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL);

-- Volcando estructura para tabla dev_muebleria.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_comercial` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto_nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Perú',
  `banco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuenta_bancaria` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saldo_pendiente` decimal(12,2) NOT NULL DEFAULT '0.00',
  `dias_credito` int NOT NULL DEFAULT '0',
  `notas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proveedores_codigo_unique` (`codigo`),
  UNIQUE KEY `proveedores_ruc_unique` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.proveedores: ~2 rows (aproximadamente)
DELETE FROM `proveedores`;
INSERT INTO `proveedores` (`id`, `codigo`, `razon_social`, `nombre_comercial`, `ruc`, `email`, `telefono`, `celular`, `contacto_nombre`, `direccion`, `ciudad`, `pais`, `banco`, `cuenta_bancaria`, `saldo_pendiente`, `dias_credito`, `notas`, `activo`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'PRV-001', 'Maderera El Bosque Amazónico S.R.L.', 'Maderas El Bosque', '20492817450', 'pedidos@maderaselbosque.pe', '01-5341290', '994112233', 'Ing. Juan Carlos Paredes', 'Carretera Central Km 18.5', 'Lima', 'Perú', 'BCP Soles', '193-45892147-0-12', 0.00, 30, NULL, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(2, 'PRV-002', 'Textiles y Espumas Industriales S.A.', 'TexFoam Perú', '20108955214', 'ventas@texfoamperu.com', '01-3269874', '987654320', 'Patricia Valdivia', 'Av. Colonial 1845', 'Callao', 'Perú', 'Interbank Soles', '200-3004589124', 0.00, 15, NULL, 1, '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL);

-- Volcando estructura para tabla dev_muebleria.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.roles: ~4 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(2, 'Vendedor', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(3, 'Almacenero', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06'),
	(4, 'Contador', 'web', '2026-09-11 20:54:06', '2026-09-11 20:54:06');

-- Volcando estructura para tabla dev_muebleria.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.role_has_permissions: ~22 rows (aproximadamente)
DELETE FROM `role_has_permissions`;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(5, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(10, 1),
	(11, 1),
	(1, 2),
	(3, 2),
	(5, 2),
	(1, 3),
	(2, 3),
	(4, 3),
	(6, 3),
	(1, 4),
	(7, 4),
	(8, 4),
	(9, 4);

-- Volcando estructura para tabla dev_muebleria.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.sessions: ~0 rows (aproximadamente)
DELETE FROM `sessions`;

-- Volcando estructura para tabla dev_muebleria.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.users: ~3 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `avatar`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'John Doe (Gerente)', 'admin@muebleria.com', NULL, '$2y$12$p6me2IqW/8YymykupUtCeO2/lwMojNL1rPT2uaVNj79UEi1cpBDYu', '+51 987 111 222', 'avatar-admin.png', 1, 'hU7Ua6cfr7RHUy1tcmj6uH6e4Zc34mlAhGhv84ipxoELtm80TB2R6aSl9UEp', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(2, 'Sofia Ramos', 'vendedor@muebleria.com', NULL, '$2y$12$kuUHNUdb0SUIhSBUc1QAk.OYHES2od3QPJY1mT1n3dcVXFy5figmi', '+51 987 333 444', 'avatar-vendedor.png', 1, NULL, '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(3, 'Miguel Torres', 'almacen@muebleria.com', NULL, '$2y$12$qWqrmahz/qYRN879SqbQzucb2I25F5vjsxA3LGhpfSL10h9vbPuBC', '+51 987 555 666', 'avatar-almacen.png', 1, NULL, '2026-09-11 20:54:07', '2026-09-11 20:54:07');

-- Volcando estructura para tabla dev_muebleria.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_comprobante` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_comprobante` enum('boleta','factura','nota_venta','cotizacion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'boleta',
  `cliente_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `condicion_pago` enum('contado','credito_15d','credito_30d','transferencia','tarjeta','yape_plin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contado',
  `estado` enum('cotizacion','pendiente','completada','anulada','en_entrega','entregado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completada',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `requiere_entrega` tinyint(1) NOT NULL DEFAULT '0',
  `requiere_armado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_entrega_programada` date DEFAULT NULL,
  `direccion_entrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_entrega` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `repartidor_asignado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_numero_comprobante_unique` (`numero_comprobante`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_user_id_foreign` (`user_id`),
  KEY `ventas_fecha_emision_estado_index` (`fecha_emision`,`estado`),
  CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.ventas: ~3 rows (aproximadamente)
DELETE FROM `ventas`;
INSERT INTO `ventas` (`id`, `numero_comprobante`, `tipo_comprobante`, `cliente_id`, `user_id`, `fecha_emision`, `fecha_vencimiento`, `condicion_pago`, `estado`, `subtotal`, `igv`, `descuento`, `total`, `monto_pagado`, `saldo`, `requiere_entrega`, `requiere_armado`, `fecha_entrega_programada`, `direccion_entrega`, `referencia_entrega`, `costo_envio`, `repartidor_asignado`, `observaciones`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'B001-0000142', 'boleta', 1, 1, '2026-09-10', NULL, 'tarjeta', 'en_entrega', 2711.02, 487.98, 0.00, 3199.00, 3199.00, 0.00, 1, 1, '2026-09-13', 'Calle Los Nogales 245 Dpto 402, San Isidro', 'Frente al parque Olivar, edificio blanco', 80.00, 'Transportes & Mudanzas Ramos', 'Subir por escalera caracol o ascensor de servicio amplio.', '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(2, 'F001-0000085', 'factura', 2, 2, '2026-09-08', NULL, 'transferencia', 'completada', 4567.80, 822.20, 100.00, 5440.00, 5440.00, 0.00, 1, 1, '2026-09-10', 'Av. Primavera 1280 Piso 8, Surco', 'Recepción Corporativa', 0.00, 'Móvil Interno 2', 'Entregado conforme y armado en sala de directorio.', '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(3, 'COT1-0000054', 'cotizacion', 3, 2, '2026-09-11', NULL, 'contado', 'cotizacion', 4940.68, 889.32, 0.00, 5830.00, 0.00, 5830.00, 1, 1, NULL, 'Jr. Batalla de Junín 320, Barranco', NULL, 60.00, NULL, 'Cotización válida por 15 días calendario.', '2026-09-11 20:54:07', '2026-09-11 20:54:07', NULL),
	(4, 'B001-0000002', 'boleta', 2, 1, '2026-09-12', NULL, 'contado', 'completada', 3199.00, 575.82, 0.00, 3774.82, 3774.82, 0.00, 0, 0, NULL, 'Av. Primavera 1280 Piso 8, Surco - Lima', NULL, 0.00, NULL, NULL, '2026-09-12 11:43:02', '2026-09-12 11:43:02', NULL);

-- Volcando estructura para tabla dev_muebleria.venta_detalles
CREATE TABLE IF NOT EXISTS `venta_detalles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `almacen_id` bigint unsigned DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL,
  `color_acabado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_personalizacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_detalles_venta_id_foreign` (`venta_id`),
  KEY `venta_detalles_producto_id_foreign` (`producto_id`),
  KEY `venta_detalles_almacen_id_foreign` (`almacen_id`),
  CONSTRAINT `venta_detalles_almacen_id_foreign` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `venta_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `venta_detalles_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla dev_muebleria.venta_detalles: ~5 rows (aproximadamente)
DELETE FROM `venta_detalles`;
INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `almacen_id`, `cantidad`, `precio_unitario`, `descuento`, `subtotal`, `color_acabado`, `notas_personalizacion`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 2, 1, 3199.00, 0.00, 3199.00, 'Gris Perla / Lino Suizo', 'Patas de madera laqueadas en roble oscuro a pedido del cliente.', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(2, 2, 2, 1, 1, 4490.00, 0.00, 4490.00, 'Nogal Clásico', 'Mesa de reuniones de 6 puestos para oficina principal.', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(3, 2, 6, 1, 1, 950.00, 0.00, 950.00, 'Mármol Blanco Calacatta', 'Mesas nido para sala de espera de gerencia.', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(4, 3, 3, 2, 1, 3850.00, 0.00, 3850.00, 'Miel & Marrón Café', 'Cama King con veladores a juego.', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(5, 3, 5, 2, 1, 1980.00, 0.00, 1980.00, 'Roble Claro', 'Panel flotante para TV 65 pulgadas.', '2026-09-11 20:54:07', '2026-09-11 20:54:07'),
	(6, 4, 1, 1, 1, 3199.00, 0.00, 3199.00, NULL, NULL, '2026-09-12 11:43:02', '2026-09-12 11:43:02');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
