<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\Configuracion;
use App\Models\Almacen;
use App\Models\User;
use ZipArchive;
use Exception;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Obtener lista de respaldos disponibles con metadatos.
     */
    public function getBackups(): array
    {
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['sql', 'zip'])) {
                continue;
            }

            $backups[] = [
                'filename' => $file->getFilename(),
                'path' => $file->getPathname(),
                'extension' => $extension,
                'type' => $extension === 'zip' ? 'Completo (BD + Multimedia)' : 'Base de Datos (SQL)',
                'size_bytes' => $file->getSize(),
                'size_formatted' => $this->formatBytes($file->getSize()),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'created_at_human' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->diffForHumans(),
            ];
        }

        // Ordenar más recientes primero
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    /**
     * Generar un respaldo en formato SQL o ZIP completo.
     */
    public function createBackup(string $type = 'sql', string $prefix = 'backup'): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $sqlContent = $this->generateSqlDump();

        if ($type === 'zip') {
            $zipFilename = "{$prefix}_completo_{$timestamp}.zip";
            $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $zipFilename;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception("No se pudo crear el archivo ZIP de respaldo.");
            }

            // Agregar dump de base de datos
            $zip->addFromString("database_{$timestamp}.sql", $sqlContent);

            // Agregar archivos multimedia (fotos, logo) de storage/app/public si existen
            $publicStorage = storage_path('app/public');
            if (File::exists($publicStorage)) {
                $files = File::allFiles($publicStorage);
                foreach ($files as $file) {
                    $relativePath = 'storage/' . str_replace('\\', '/', $file->getRelativePathname());
                    $zip->addFile($file->getPathname(), $relativePath);
                }
            }

            $zip->close();
            return $zipFilename;
        }

        // Por defecto: Dump SQL directo
        $sqlFilename = "{$prefix}_db_{$timestamp}.sql";
        $sqlPath = $this->backupDir . DIRECTORY_SEPARATOR . $sqlFilename;
        File::put($sqlPath, $sqlContent);

        return $sqlFilename;
    }

    /**
     * Generar el contenido SQL completo de la base de datos (DDL + DML).
     * Compatible con MySQL y SQLite.
     */
    public function generateSqlDump(): string
    {
        $driver = DB::connection()->getDriverName();
        $dbName = config("database.connections.{$driver}.database", 'dev_muebleria');

        $output = "-- ========================================================\n";
        $output .= "-- Respaldo de Base de Datos - Mueblería Elegance ERP\n";
        $output .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Motor: " . strtoupper($driver) . " | BD: " . $dbName . "\n";
        $output .= "-- ========================================================\n\n";

        if ($driver === 'sqlite') {
            $output .= "PRAGMA foreign_keys = OFF;\n\n";
            $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");

            foreach ($tables as $t) {
                $tableName = $t->name;
                $createTableSql = $t->sql;

                $output .= "-- --------------------------------------------------------\n";
                $output .= "-- Tabla: `{$tableName}`\n";
                $output .= "-- --------------------------------------------------------\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $createTableSql . ";\n\n";

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows->chunk(200) as $chunk) {
                        $insertStatements = [];
                        foreach ($chunk as $row) {
                            $values = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $values[] = 'NULL';
                                } elseif (is_numeric($value) && !preg_match('/^0[0-9]+/', (string)$value)) {
                                    $values[] = $value;
                                } else {
                                    $escaped = addslashes((string)$value);
                                    $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
                                    $values[] = "'{$escaped}'";
                                }
                            }
                            $insertStatements[] = "(" . implode(", ", $values) . ")";
                        }
                        $output .= "INSERT INTO `{$tableName}` VALUES \n" . implode(",\n", $insertStatements) . ";\n";
                    }
                    $output .= "\n";
                }
            }

            $output .= "PRAGMA foreign_keys = ON;\n";

        } else {
            // MySQL
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
            $output .= "SET time_zone = '+00:00';\n";
            $output .= "SET NAMES utf8mb4;\n\n";

            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');

            foreach ($tables as $tableObj) {
                $vars = get_object_vars($tableObj);
                $tableName = reset($vars);

                $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableVars = get_object_vars($createTableResult[0]);
                $createTableSql = $createTableVars['Create Table'] ?? end($createTableVars);

                $output .= "-- --------------------------------------------------------\n";
                $output .= "-- Estructura de tabla para `{$tableName}`\n";
                $output .= "-- --------------------------------------------------------\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $createTableSql . ";\n\n";

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    foreach ($rows->chunk(200) as $chunk) {
                        $insertStatements = [];
                        foreach ($chunk as $row) {
                            $values = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $values[] = 'NULL';
                                } elseif (is_numeric($value) && !preg_match('/^0[0-9]+/', (string)$value)) {
                                    $values[] = $value;
                                } else {
                                    $escaped = addslashes((string)$value);
                                    $escaped = str_replace(["\r", "\n"], ["\\r", "\\n"], $escaped);
                                    $values[] = "'{$escaped}'";
                                }
                            }
                            $insertStatements[] = "(" . implode(", ", $values) . ")";
                        }
                        $output .= "INSERT INTO `{$tableName}` VALUES \n" . implode(",\n", $insertStatements) . ";\n";
                    }
                    $output .= "\n";
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
        }

        $output .= "-- Fin del Respaldo\n";

        return $output;
    }

    /**
     * Restaurar la base de datos y archivos a partir de un archivo .sql o .zip.
     */
    public function restoreBackup(string $filePath): void
    {
        if (!File::exists($filePath)) {
            throw new Exception("El archivo de respaldo no existe.");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'zip') {
            $this->restoreFromZip($filePath);
        } elseif ($extension === 'sql' || $extension === 'txt') {
            $this->restoreFromSqlFile($filePath);
        } else {
            throw new Exception("Formato de archivo no compatible. Use archivos .sql o .zip.");
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
    }

    /**
     * Restauración a partir de archivo ZIP.
     */
    protected function restoreFromZip(string $zipPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception("No se pudo abrir el archivo ZIP para restauración.");
        }

        $tempExtractDir = storage_path('app/temp_restore_' . uniqid());
        File::makeDirectory($tempExtractDir, 0755, true);

        try {
            $zip->extractTo($tempExtractDir);
            $zip->close();

            $sqlFiles = File::glob($tempExtractDir . '/*.sql');
            if (empty($sqlFiles)) {
                throw new Exception("El archivo ZIP no contiene ningún archivo .sql de base de datos.");
            }

            $this->restoreFromSqlFile($sqlFiles[0]);

            $extractedStorage = $tempExtractDir . DIRECTORY_SEPARATOR . 'storage';
            if (File::exists($extractedStorage)) {
                $targetStorage = storage_path('app/public');
                File::copyDirectory($extractedStorage, $targetStorage);
            }
        } finally {
            File::deleteDirectory($tempExtractDir);
        }
    }

    /**
     * Restauración ejecutando el script SQL deshabilitando restricciones de clave foránea.
     */
    public function restoreFromSqlFile(string $sqlFilePath): void
    {
        $sql = File::get($sqlFilePath);
        if (empty(trim($sql))) {
            throw new Exception("El archivo SQL de respaldo está vacío.");
        }

        $this->disableForeignKeys();
        try {
            DB::unprepared($sql);
        } finally {
            $this->enableForeignKeys();
        }
    }

    /**
     * Eliminar un archivo de respaldo del servidor.
     */
    public function deleteBackup(string $filename): bool
    {
        $cleanFilename = basename($filename);
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;

        if (File::exists($path)) {
            return File::delete($path);
        }
        return false;
    }

    /**
     * Ruta completa para descarga.
     */
    public function getBackupPath(string $filename): string
    {
        $cleanFilename = basename($filename);
        return $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;
    }

    /**
     * Limpieza transaccional: elimina ventas, compras, kardex, gastos, órdenes de producción y cajas.
     * Mantiene catálogo de productos, categorías, clientes, proveedores, almacenes y usuarios.
     */
    public function resetTransaccional(): void
    {
        $this->createBackup('sql', 'preventivo_reset_transaccional');

        $this->disableForeignKeys();

        try {
            $this->clearTable('venta_detalles');
            $this->clearTable('ventas');
            $this->clearTable('compra_detalles');
            $this->clearTable('compras');
            $this->clearTable('ordenes_produccion');
            $this->clearTable('movimientos_stock');
            $this->clearTable('gastos');
            $this->clearTable('caja');

            DB::table('productos')->update(['stock_actual' => 0]);
            DB::table('clientes')->update(['saldo_pendiente' => 0]);
            DB::table('proveedores')->update(['saldo_pendiente' => 0]);

        } finally {
            $this->enableForeignKeys();
        }

        Artisan::call('cache:clear');
    }

    /**
     * Reseteo total para nueva empresa (Factory Reset con inicialización empresarial).
     */
    public function resetNuevaEmpresa(array $empresaData, ?int $adminIdToKeep = null): void
    {
        $this->createBackup('zip', 'preventivo_nueva_empresa');

        $this->disableForeignKeys();

        try {
            $this->clearTable('venta_detalles');
            $this->clearTable('ventas');
            $this->clearTable('compra_detalles');
            $this->clearTable('compras');
            $this->clearTable('ordenes_produccion');
            $this->clearTable('movimientos_stock');
            $this->clearTable('gastos');
            $this->clearTable('caja');

            $this->clearTable('productos');
            $this->clearTable('categorias');
            $this->clearTable('clientes');
            $this->clearTable('proveedores');
            $this->clearTable('almacenes');

            $productosStorage = storage_path('app/public/productos');
            if (File::exists($productosStorage)) {
                File::cleanDirectory($productosStorage);
            }

            Almacen::create([
                'nombre' => 'Almacén Principal',
                'ubicacion' => $empresaData['direccion'] ?? 'Local Central',
                'responsable' => 'Administrador',
                'telefono' => $empresaData['telefono'] ?? null,
                'es_activo' => true
            ]);

            $config = Configuracion::first() ?? new Configuracion();
            $config->nombre_empresa = $empresaData['nombre_empresa'];
            $config->ruc = $empresaData['ruc'];
            $config->direccion = $empresaData['direccion'];
            $config->telefono = $empresaData['telefono'];
            $config->email = $empresaData['email'];
            $config->sitio_web = $empresaData['sitio_web'] ?? null;
            $config->moneda_simbolo = $empresaData['moneda_simbolo'] ?? 'S/.';
            $config->porcentaje_igv = $empresaData['porcentaje_igv'] ?? 18;
            $config->serie_boleta = $empresaData['serie_boleta'] ?? 'B001';
            $config->serie_factura = $empresaData['serie_factura'] ?? 'F001';
            $config->serie_nota_venta = $empresaData['serie_nota_venta'] ?? 'NV01';
            $config->serie_cotizacion = $empresaData['serie_cotizacion'] ?? 'COT1';
            $config->terminos_garantia = $empresaData['terminos_garantia'] ?? 'Garantía legal contra defectos de fabricación.';
            
            if (isset($empresaData['logo_path'])) {
                $config->logo = $empresaData['logo_path'];
            }
            $config->save();

            if ($adminIdToKeep) {
                $adminUser = User::find($adminIdToKeep);
                if ($adminUser && !empty($empresaData['admin_name'])) {
                    $adminUser->name = $empresaData['admin_name'];
                    if (!empty($empresaData['admin_email'])) {
                        $adminUser->email = $empresaData['admin_email'];
                    }
                    $adminUser->save();
                }
            }

        } finally {
            $this->enableForeignKeys();
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
    }

    /**
     * Vaciado seguro de tabla compatible con MySQL y SQLite.
     */
    protected function clearTable(string $table): void
    {
        DB::table($table)->delete();
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
        }
    }

    /**
     * Deshabilitar restricciones foráneas según el motor.
     */
    protected function disableForeignKeys(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
    }

    /**
     * Habilitar restricciones foráneas según el motor.
     */
    protected function enableForeignKeys(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Formateo legible de bytes.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
