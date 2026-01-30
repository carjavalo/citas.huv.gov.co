<?php

namespace App\Http\Livewire\Operando;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SqlQueryExport;

class Consulta extends Component
{
    public $query = '';
    public $results = [];
    public $columns = [];
    public $message = '';
    public $messageType = '';
    public $executionTime = 0;
    public $affectedRows = 0;
    public $tables = [];
    public $selectedTable = '';
    public $tableStructure = [];
    public $maxResults = 500;

    public function mount()
    {
        // Solo Super Admin puede acceder
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'No tiene permisos para acceder a esta vista.');
        }
        
        $this->loadTables();
    }

    public function loadTables()
    {
        $this->tables = DB::select('SHOW TABLES');
    }

    public function selectTable($tableName)
    {
        $this->selectedTable = $tableName;
        $this->query = "SELECT * FROM `{$tableName}` LIMIT 100;";
        $this->loadTableStructure($tableName);
    }

    public function loadTableStructure($tableName)
    {
        $this->tableStructure = DB::select("DESCRIBE `{$tableName}`");
    }

    public function executeQuery()
    {
        $this->results = [];
        $this->columns = [];
        $this->message = '';
        $this->messageType = '';
        $this->affectedRows = 0;

        if (empty(trim($this->query))) {
            $this->message = 'Por favor ingrese una consulta SQL.';
            $this->messageType = 'warning';
            return;
        }

        $startTime = microtime(true);

        try {
            $queryTrimmed = trim($this->query);
            $queryType = strtoupper(strtok($queryTrimmed, ' '));

            switch ($queryType) {
                case 'SELECT':
                    // Agregar LIMIT automáticamente si no existe para evitar problemas de memoria
                    $queryLower = strtolower($queryTrimmed);
                    if (strpos($queryLower, ' limit ') === false) {
                        $queryTrimmed = rtrim($queryTrimmed, ';') . " LIMIT {$this->maxResults}";
                        $this->message = "Nota: Se aplicó LIMIT {$this->maxResults} automáticamente. ";
                    }
                    $this->results = DB::select(DB::raw($queryTrimmed));
                    
                    if (!empty($this->results)) {
                        $this->columns = array_keys((array) $this->results[0]);
                        $this->message .= 'Consulta ejecutada correctamente. ' . count($this->results) . ' fila(s) encontrada(s).';
                    } else {
                        $this->message .= 'Consulta ejecutada correctamente. 0 filas encontradas.';
                    }
                    $this->messageType = 'success';
                    break;
                    
                case 'SHOW':
                case 'DESCRIBE':
                case 'DESC':
                case 'EXPLAIN':
                    $this->results = DB::select(DB::raw($queryTrimmed));
                    
                    if (!empty($this->results)) {
                        $this->columns = array_keys((array) $this->results[0]);
                        $this->message = 'Consulta ejecutada correctamente. ' . count($this->results) . ' fila(s) encontrada(s).';
                    } else {
                        $this->message = 'Consulta ejecutada correctamente. 0 filas encontradas.';
                    }
                    $this->messageType = 'success';
                    break;

                case 'INSERT':
                    $this->affectedRows = DB::insert(DB::raw($queryTrimmed));
                    $this->message = 'INSERT ejecutado correctamente. Fila insertada.';
                    $this->messageType = 'success';
                    $this->loadTables();
                    break;

                case 'UPDATE':
                    $this->affectedRows = DB::update(DB::raw($queryTrimmed));
                    $this->message = "UPDATE ejecutado correctamente. {$this->affectedRows} fila(s) afectada(s).";
                    $this->messageType = 'success';
                    break;

                case 'DELETE':
                    $this->affectedRows = DB::delete(DB::raw($queryTrimmed));
                    $this->message = "DELETE ejecutado correctamente. {$this->affectedRows} fila(s) eliminada(s).";
                    $this->messageType = 'success';
                    break;

                case 'CREATE':
                case 'ALTER':
                case 'DROP':
                case 'TRUNCATE':
                    DB::statement(DB::raw($queryTrimmed));
                    $this->message = "{$queryType} ejecutado correctamente.";
                    $this->messageType = 'success';
                    $this->loadTables();
                    break;

                default:
                    DB::statement(DB::raw($queryTrimmed));
                    $this->message = 'Consulta ejecutada correctamente.';
                    $this->messageType = 'success';
                    break;
            }

        } catch (\Exception $e) {
            $this->message = 'Error SQL: ' . $e->getMessage();
            $this->messageType = 'error';
        }

        $this->executionTime = round((microtime(true) - $startTime) * 1000, 2);
    }

    public function clearQuery()
    {
        $this->query = '';
        $this->results = [];
        $this->columns = [];
        $this->message = '';
        $this->messageType = '';
        $this->affectedRows = 0;
        $this->tableStructure = [];
        $this->selectedTable = '';
    }

    public function exportToExcel()
    {
        if (empty($this->results)) {
            $this->message = 'No hay resultados para exportar.';
            $this->messageType = 'warning';
            return;
        }

        $fileName = 'sql_query_results_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new SqlQueryExport($this->results, $this->columns), $fileName);
    }

    public function backupDatabase()
    {
        // Aumentar tiempo de ejecución para backups grandes
        set_time_limit(300); // 5 minutos
        ini_set('max_execution_time', 300);
        
        try {
            // Crear directorio Bakcups si no existe
            $backupPath = base_path('Bakcups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0777, true);
            }

            // Obtener credenciales de la base de datos
            $host = config('database.connections.mysql.host', '127.0.0.1');
            $database = config('database.connections.mysql.database', 'citas');
            $username = config('database.connections.mysql.username', 'root');
            $password = config('database.connections.mysql.password', '');
            $port = config('database.connections.mysql.port', 3306);

            // Nombre del archivo con fecha y hora
            $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . '\\' . $fileName;

            // Ruta de mysqldump en XAMPP
            $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            
            // Verificar que mysqldump existe
            if (!file_exists($mysqldumpPath)) {
                $this->message = "Error: No se encontró mysqldump en: {$mysqldumpPath}";
                $this->messageType = 'error';
                return;
            }

            // Construir comando - usar shell_exec con redirección
            if (empty($password)) {
                $command = "\"$mysqldumpPath\" -h $host -P $port -u $username $database";
            } else {
                $command = "\"$mysqldumpPath\" -h $host -P $port -u $username -p$password $database";
            }

            // Ejecutar y capturar salida
            $sqlContent = shell_exec($command . ' 2>&1');

            if ($sqlContent && strlen($sqlContent) > 100 && strpos($sqlContent, 'CREATE TABLE') !== false) {
                // Guardar el contenido en el archivo
                file_put_contents($filePath, $sqlContent);
                
                if (file_exists($filePath)) {
                    $size = round(filesize($filePath) / 1024, 2);
                    $this->message = "✓ Backup creado: {$fileName} ({$size} KB) en Bakcups/";
                    $this->messageType = 'success';
                } else {
                    $this->message = "Error: No se pudo guardar el archivo de backup";
                    $this->messageType = 'error';
                }
            } else {
                $this->message = "Error al crear backup: " . substr($sqlContent, 0, 200);
                $this->messageType = 'error';
            }

        } catch (\Exception $e) {
            $this->message = 'Error: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.operando.consulta');
    }
}
