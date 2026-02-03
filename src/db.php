<?php
function get_db() {
    // Generic DB connector supporting Postgres and SQL Server (Azure)
    $driver = getenv('DB_DRIVER') ?: 'pgsql'; // 'pgsql' or 'sqlsrv'

    if ($driver === 'pgsql') {
        $host = getenv('DB_HOST') ?: 'db';
        $port = getenv('DB_PORT') ?: '5432';
        $db = getenv('DB_NAME') ?: getenv('POSTGRES_DB') ?: 'free-sql-db-0070009';
        $user = getenv('DB_USER') ?: getenv('POSTGRES_USER') ?: 'danields';
        $pass = getenv('DB_PASS') ?: getenv('POSTGRES_PASSWORD') ?: 'f2qV6WaOk@E64mKk';
        $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    } elseif ($driver === 'sqlsrv') {
        // SQL Server / Azure SQL
        // For Azure SQL the host is typically '<your-server>.database.windows.net'
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '1433';
        $db = getenv('DB_NAME') ?: 'free-sql-db-0070009';
        $user = getenv('DB_USER') ?: 'danields';
        $pass = getenv('DB_PASS') ?: 'f2qV6WaOk@E64mKk';

        // Build a sqlsrv DSN using Azure sample style.
        // Use tcp: prefix and enable encryption by default for Azure SQL.
        $encrypt = getenv('DB_ENCRYPT') ?: '1'; // 1 => Encrypt, 0 => no
        $trust = getenv('DB_TRUST_SERVER_CERT') ?: '0'; // 0 => do not trust server cert

        // Example: sqlsrv:server = tcp:yourserver.database.windows.net,1433; Database = yourdb; Encrypt=1; TrustServerCertificate=0
        $dsn = "sqlsrv:server = tcp:{$host},{$port}; Database = {$db}; Encrypt={$encrypt}; TrustServerCertificate={$trust}";
    } else {
        echo "Unsupported DB_DRIVER: " . htmlspecialchars($driver);
        exit(1);
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return $pdo;
    } catch (PDOException $e) {
        // If PDO failed and sqlsrv extension is available, try the sqlsrv_connect fallback
        if ($driver === 'sqlsrv' && function_exists('sqlsrv_connect')) {
            $serverName = "tcp:{$host},{$port}";
            $connectionInfo = [
                'UID' => $user,
                'PWD' => $pass,
                'Database' => $db,
                'LoginTimeout' => 30,
                'Encrypt' => (int)(getenv('DB_ENCRYPT') ?: 1),
                'TrustServerCertificate' => (int)(getenv('DB_TRUST_SERVER_CERT') ?: 0),
            ];

            $conn = sqlsrv_connect($serverName, $connectionInfo);
            if ($conn === false) {
                $msg = "sqlsrv_connect failed.";
                $errors = sqlsrv_errors();
                if ($errors !== null) {
                    foreach ($errors as $err) {
                        $msg .= " " . ($err['message'] ?? json_encode($err));
                    }
                }
                echo htmlspecialchars($msg);
                exit(1);
            }

            // Return a small descriptor so callers can handle either PDO or sqlsrv resource
            return ['type' => 'sqlsrv', 'conn' => $conn];
        }

        echo "DB connection failed: " . htmlspecialchars($e->getMessage());
        exit(1);
    }
}
