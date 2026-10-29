<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Services\BackupService;
use App\Models\Configuracion;
use Exception;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Panel principal de Copias de Seguridad, Restauración y Reseteo.
     */
    public function index()
    {
        $backups = $this->backupService->getBackups();
        $totalBytes = array_sum(array_column($backups, 'size_bytes'));
        $totalSizeFormatted = $this->formatBytes($totalBytes);

        // Métricas de salud del sistema y base de datos
        $driver = DB::connection()->getDriverName();
        $dbName = config("database.connections.{$driver}.database", 'dev_muebleria');

        if ($driver === 'mysql') {
            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $totalTables = count($tables);

            $dbSizeQuery = DB::select("
                SELECT SUM(data_length + index_length) AS db_size
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$dbName]);
            $dbSizeBytes = $dbSizeQuery[0]->db_size ?? 0;
            $dbSizeFormatted = $this->formatBytes((int)$dbSizeBytes);
        } else {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $totalTables = count($tables);
            $dbSizeFormatted = 'Memoria / Local';
        }

        $lastBackup = !empty($backups) ? $backups[0] : null;
        $config = Configuracion::first() ?? new Configuracion();

        return view('backup.index', compact(
            'backups',
            'totalBytes',
            'totalSizeFormatted',
            'totalTables',
            'dbSizeFormatted',
            'lastBackup',
            'config'
        ));
    }

    /**
     * Crear una nueva copia de seguridad (SQL o ZIP con imágenes).
     */
    public function crearBackup(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:sql,zip'
        ]);

        try {
            $filename = $this->backupService->createBackup($request->tipo, 'respaldo');
            $tipoTexto = $request->tipo === 'zip' ? 'Copia completa (BD + Archivos)' : 'Copia de Base de Datos SQL';
            return redirect()->route('respaldos.index')
                ->with('success', "{$tipoTexto} generada correctamente con el archivo: {$filename}");
        } catch (Exception $e) {
            return redirect()->route('respaldos.index')
                ->with('error', 'Error al generar la copia de seguridad: ' . $e->getMessage());
        }
    }

    /**
     * Descargar un archivo de respaldo almacenado.
     */
    public function descargar(string $filename)
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!File::exists($path)) {
            return redirect()->route('respaldos.index')
                ->with('error', 'El archivo solicitado no existe en el servidor.');
        }

        return response()->download($path, basename($filename));
    }

    /**
     * Restaurar una copia de seguridad (desde archivo existente o subido).
     */
    public function restaurar(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'fuente' => 'required|in:servidor,archivo',
            'existing_filename' => 'required_if:fuente,servidor|nullable|string',
            'backup_file' => 'required_if:fuente,archivo|nullable|file|mimes:sql,zip,txt|max:102400', // max 100MB
        ], [
            'password.required' => 'Debe ingresar su contraseña de Administrador para confirmar la restauración.',
            'backup_file.mimes' => 'El archivo debe ser de formato .sql o .zip.',
            'backup_file.max' => 'El archivo no puede exceder los 100MB.'
        ]);

        // Validar contraseña de seguridad del administrador
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('respaldos.index')
                ->with('error', 'La contraseña de Administrador ingresada es incorrecta. Operación cancelada por seguridad.');
        }

        try {
            if ($request->fuente === 'servidor') {
                $path = $this->backupService->getBackupPath($request->existing_filename);
                $this->backupService->restoreBackup($path);
            } else {
                $uploadedFile = $request->file('backup_file');
                $tempPath = $uploadedFile->getRealPath();
                $this->backupService->restoreBackup($tempPath);
            }

            return redirect()->route('respaldos.index')
                ->with('success', '¡El sistema ha sido restaurado exitosamente! Todos los datos y archivos se han actualizado.');
        } catch (Exception $e) {
            return redirect()->route('respaldos.index')
                ->with('error', 'Ocurrió un error al restaurar la copia de seguridad: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un archivo de copia de seguridad.
     */
    public function eliminar(string $filename)
    {
        if ($this->backupService->deleteBackup($filename)) {
            return redirect()->route('respaldos.index')
                ->with('success', 'El archivo de respaldo ha sido eliminado permanentemente.');
        }

        return redirect()->route('respaldos.index')
            ->with('error', 'No se pudo eliminar el archivo o ya no existe.');
    }

    /**
     * Reseteo del sistema: modo transaccional o reseteo total para nueva empresa.
     */
    public function resetear(Request $request)
    {
        $request->validate([
            'modo' => 'required|in:transaccional,nueva_empresa',
            'password' => 'required|string',
            'confirm_phrase' => 'required|string',
        ]);

        // Verificación de frase clave y contraseña
        if (trim(strtoupper($request->confirm_phrase)) !== 'RESETEAR') {
            return redirect()->route('respaldos.index')
                ->with('error', 'La palabra clave de confirmación debe ser exactamente "RESETEAR".');
        }

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->route('respaldos.index')
                ->with('error', 'Contraseña de Administrador incorrecta. Operación de reseteo abortada.');
        }

        try {
            if ($request->modo === 'transaccional') {
                $this->backupService->resetTransaccional();
                return redirect()->route('respaldos.index')
                    ->with('success', 'Limpieza transaccional ejecutada con éxito. Ventas, compras y movimientos eliminados, manteniendo el catálogo y configuración. Se generó un respaldo preventivo automático.');
            }

            // Modo: Nueva Empresa (Factory Reset)
            $validatedData = $request->validate([
                'nombre_empresa' => 'required|string|max:200',
                'ruc' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:50',
                'email' => 'required|email|max:100',
                'sitio_web' => 'nullable|string|max:255',
                'moneda_simbolo' => 'required|string|max:10',
                'porcentaje_igv' => 'required|numeric',
                'serie_boleta' => 'required|string|max:10',
                'serie_factura' => 'required|string|max:10',
                'serie_nota_venta' => 'required|string|max:10',
                'serie_cotizacion' => 'nullable|string|max:10',
                'terminos_garantia' => 'nullable|string',
                'admin_name' => 'nullable|string|max:100',
                'admin_email' => 'nullable|email|max:100',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048'
            ]);

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('empresa', 'public');
                $validatedData['logo_path'] = $logoPath;
            }

            $this->backupService->resetNuevaEmpresa($validatedData, auth()->id());

            return redirect()->route('respaldos.index')
                ->with('success', '¡Sistema inicializado exitosamente para la nueva empresa! Catálogo y operaciones limpiados, series reiniciadas y datos de empresa actualizados. Respaldo preventivo guardado.');

        } catch (Exception $e) {
            return redirect()->route('respaldos.index')
                ->with('error', 'Ocurrió un error crítico durante el proceso de reseteo: ' . $e->getMessage());
        }
    }

    /**
     * Utilidad para formateo de bytes.
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
