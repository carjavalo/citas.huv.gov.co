<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportUsersFromCsv extends Command
{
    private const DEFAULT_HEADERS = [
        'id',
        'name',
        'apellido1',
        'apellido2',
        'tdocumento',
        'ndocumento',
        'telefono1',
        'telefono2',
        'eps',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $signature = 'users:import-csv
                            {file : Ruta absoluta o relativa del archivo CSV}
                            {--dry-run : Simula la importacion sin guardar cambios}
                            {--connection= : Conexion de base de datos a usar}
                            {--host= : Host de base de datos para la conexion mysql}
                            {--port= : Puerto de base de datos para la conexion mysql}
                            {--database= : Nombre de base de datos para la conexion mysql}
                            {--username= : Usuario de base de datos para la conexion mysql}
                            {--password= : Clave de base de datos para la conexion mysql}';

    protected $description = 'Importa usuarios desde un CSV, omitiendo correos o documentos ya existentes';

    public function handle(): int
    {
        $this->configureDatabaseConnection();

        $filePath = $this->resolveFilePath((string) $this->argument('file'));

        if (!is_file($filePath) || !is_readable($filePath)) {
            $this->error('No se puede leer el archivo: ' . $filePath);

            return self::FAILURE;
        }

        if (!Schema::hasTable('users')) {
            $this->error('La tabla users no existe en la base de datos actual.');

            return self::FAILURE;
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->error('No fue posible abrir el archivo CSV.');

            return self::FAILURE;
        }

        $firstRow = fgetcsv($handle);

        if ($firstRow === false) {
            fclose($handle);
            $this->error('El archivo CSV no contiene datos.');

            return self::FAILURE;
        }

        [$headers, $pendingRows] = $this->resolveHeaders($firstRow);

        $requiredHeaders = ['name', 'apellido1', 'tdocumento', 'ndocumento', 'telefono1', 'eps', 'email', 'password'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if ($missingHeaders !== []) {
            fclose($handle);
            $this->error('Faltan columnas obligatorias en el CSV: ' . implode(', ', $missingHeaders));

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $imported = 0;
        $skipped = 0;
        $invalid = 0;
        $rowNumber = 0;
        $seenEmails = [];
        $seenDocuments = [];

        foreach ($pendingRows as $row) {
            $rowNumber++;
            $result = $this->processRow($headers, $row, $rowNumber, $dryRun, $seenEmails, $seenDocuments);
            $imported += $result['imported'];
            $skipped += $result['skipped'];
            $invalid += $result['invalid'];
        }

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $result = $this->processRow($headers, $row, $rowNumber, $dryRun, $seenEmails, $seenDocuments);
            $imported += $result['imported'];
            $skipped += $result['skipped'];
            $invalid += $result['invalid'];
        }

        fclose($handle);

        $mode = $dryRun ? 'Simulacion completada' : 'Importacion completada';
        $this->info("{$mode}. Importados: {$imported}. Omitidos: {$skipped}. Invalidos: {$invalid}.");

        return self::SUCCESS;
    }

    private function configureDatabaseConnection(): void
    {
        $connection = $this->option('connection');
        $host = $this->option('host');
        $port = $this->option('port');
        $database = $this->option('database');
        $username = $this->option('username');
        $password = $this->option('password');

        if ($connection !== null && $connection !== '') {
            config(['database.default' => $connection]);
            DB::purge($connection);

            return;
        }

        if ($host === null && $port === null && $database === null && $username === null && $password === null) {
            return;
        }

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $host !== null && $host !== '' ? $host : config('database.connections.mysql.host'),
            'database.connections.mysql.port' => $port !== null && $port !== '' ? $port : config('database.connections.mysql.port'),
            'database.connections.mysql.database' => $database !== null && $database !== '' ? $database : config('database.connections.mysql.database'),
            'database.connections.mysql.username' => $username !== null && $username !== '' ? $username : config('database.connections.mysql.username'),
            'database.connections.mysql.password' => $password !== null ? $password : config('database.connections.mysql.password'),
        ]);

        DB::purge('mysql');
    }

    private function resolveFilePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:\\\\/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }

    private function resolveHeaders(array $firstRow): array
    {
        $normalizedFirstRow = array_map([$this, 'normalizeHeader'], $firstRow);
        $hasStandardHeaders = count(array_intersect(['name', 'apellido1', 'tdocumento', 'ndocumento', 'email'], $normalizedFirstRow)) >= 4;

        if ($hasStandardHeaders) {
            return [$normalizedFirstRow, []];
        }

        return [self::DEFAULT_HEADERS, [$firstRow]];
    }

    private function normalizeHeader(string $header): string
    {
        return trim($header, " \t\n\r\0\x0B\"'");
    }

    private function processRow(array $headers, array $row, int $rowNumber, bool $dryRun, array &$seenEmails, array &$seenDocuments): array
    {
        if ($this->isEmptyRow($row)) {
            return ['imported' => 0, 'skipped' => 0, 'invalid' => 0];
        }

        $data = $this->mapRow($headers, $row);
        $prepared = $this->prepareUserData($data);

        if ($prepared === null) {
            $this->warn("Fila {$rowNumber} invalida: faltan datos obligatorios o el correo no es valido.");

            return ['imported' => 0, 'skipped' => 0, 'invalid' => 1];
        }

        $email = $prepared['email'];
        $document = $prepared['ndocumento'];

        if (isset($seenEmails[$email]) || isset($seenDocuments[$document])) {
            $this->line("Fila {$rowNumber} omitida: duplicada dentro del mismo archivo.");

            return ['imported' => 0, 'skipped' => 1, 'invalid' => 0];
        }

        $exists = User::query()
            ->where('email', $email)
            ->orWhere('ndocumento', $document)
            ->exists();

        if ($exists) {
            $seenEmails[$email] = true;
            $seenDocuments[$document] = true;
            $this->line("Fila {$rowNumber} omitida: ya existe un usuario con ese correo o documento.");

            return ['imported' => 0, 'skipped' => 1, 'invalid' => 0];
        }

        if (!$dryRun) {
            DB::transaction(function () use ($prepared): void {
                $user = User::withoutEvents(function () use ($prepared) {
                    $user = new User();
                    $user->name = $prepared['name'];
                    $user->apellido1 = $prepared['apellido1'];
                    $user->apellido2 = $prepared['apellido2'];
                    $user->tdocumento = $prepared['tdocumento'];
                    $user->ndocumento = $prepared['ndocumento'];
                    $user->telefono1 = $prepared['telefono1'];
                    $user->telefono2 = $prepared['telefono2'];
                    $user->eps = $prepared['eps'];
                    $user->email = $prepared['email'];
                    $user->email_verified_at = $prepared['email_verified_at'];
                    $user->password = $prepared['password'];
                    $user->remember_token = $prepared['remember_token'];
                    $user->created_at = $prepared['created_at'] ?? now();
                    $user->updated_at = $prepared['updated_at'] ?? now();

                    if (Schema::hasColumn('users', 'two_factor_secret')) {
                        $user->two_factor_secret = $prepared['two_factor_secret'];
                    }

                    if (Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                        $user->two_factor_recovery_codes = $prepared['two_factor_recovery_codes'];
                    }

                    $user->save();

                    return $user;
                });

                if (!$user->hasRole('Usuario')) {
                    $user->assignRole('Usuario');
                }
            });
        }

        $seenEmails[$email] = true;
        $seenDocuments[$document] = true;

        return ['imported' => 1, 'skipped' => 0, 'invalid' => 0];
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function mapRow(array $headers, array $row): array
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            $mapped[$header] = $row[$index] ?? null;
        }

        return $mapped;
    }

    private function prepareUserData(array $data): ?array
    {
        $name = $this->normalizeValue($data['name'] ?? null);
        $apellido1 = $this->normalizeValue($data['apellido1'] ?? null);
        $tdocumento = $this->normalizeValue($data['tdocumento'] ?? null);
        $ndocumento = $this->normalizeValue($data['ndocumento'] ?? null);
        $telefono1 = $this->normalizeValue($data['telefono1'] ?? null);
        $eps = $this->normalizeValue($data['eps'] ?? null);
        $emailValue = $this->normalizeValue($data['email'] ?? null);
        $email = $emailValue !== null ? strtolower($emailValue) : '';
        $password = $this->normalizeValue($data['password'] ?? null);

        if ($name === null || $apellido1 === null || $tdocumento === null || $ndocumento === null || $telefono1 === null || $eps === null || $email === '' || $password === null) {
            return null;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        return [
            'name' => $name,
            'apellido1' => $apellido1,
            'apellido2' => $this->normalizeValue($data['apellido2'] ?? null),
            'tdocumento' => (string) $tdocumento,
            'ndocumento' => (string) $ndocumento,
            'telefono1' => (string) $telefono1,
            'telefono2' => $this->normalizeValue($data['telefono2'] ?? null),
            'eps' => (int) $eps,
            'email' => $email,
            'email_verified_at' => $this->normalizeDateValue($data['email_verified_at'] ?? null),
            'password' => (string) $password,
            'remember_token' => $this->normalizeValue($data['remember_token'] ?? null),
            'created_at' => $this->normalizeDateValue($data['created_at'] ?? null),
            'updated_at' => $this->normalizeDateValue($data['updated_at'] ?? null),
            'two_factor_secret' => $this->normalizeValue($data['two_factor_secret'] ?? null),
            'two_factor_recovery_codes' => $this->normalizeValue($data['two_factor_recovery_codes'] ?? null),
        ];
    }

    private function normalizeDateValue($value): ?string
    {
        $normalized = $this->normalizeValue($value);

        if ($normalized === null) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $normalized) === 1) {
            return $normalized;
        }

        $formats = ['d/m/Y H:i', 'd/m/Y H:i:s', 'Y-m-d H:i', 'Y-m-d H:i:s'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $normalized)->format('Y-m-d H:i:s');
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return null;
    }

    private function normalizeValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value, " \t\n\r\0\x0B\"");

        if ($normalized === '' || strtoupper($normalized) === 'NULL') {
            return null;
        }

        return $normalized;
    }
}