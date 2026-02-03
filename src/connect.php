<?php
// Read DB credentials from environment variables (recommended)
$server = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$encrypt = getenv('DB_ENCRYPT') ?: '1';
$trust = getenv('DB_TRUST_SERVER_CERT') ?: '0';

if (!$server || !$dbName || !$user || $pass === false) {
    echo "Missing DB environment variables. Set DB_HOST, DB_NAME, DB_USER, DB_PASS.\n";
    exit(1);
}

$pdo = null;
$sqlsrvConn = null;

// Try PDO (requires pdo_sqlsrv)
try {
    $dsn = "sqlsrv:server = tcp:{$server},1433; Database = {$dbName}; Encrypt={$encrypt}; TrustServerCertificate={$trust}";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected via PDO\n";
} catch (PDOException $e) {
    echo "PDO connect failed: " . $e->getMessage() . "\n";
}

// If PDO didn't connect, try sqlsrv_connect (procedural)
if ($pdo === null) {
    $connectionInfo = array('UID' => $user, 'PWD' => $pass, 'Database' => $dbName, 'LoginTimeout' => 30, 'Encrypt' => (int)$encrypt, 'TrustServerCertificate' => (int)$trust);
    $serverName = "tcp:{$server},1433";
    $sqlsrvConn = sqlsrv_connect($serverName, $connectionInfo);
    if ($sqlsrvConn === false) {
        echo "sqlsrv_connect failed:\n";
        var_dump(sqlsrv_errors());
        exit(1);
    }
    echo "Connected via sqlsrv extension\n";
}

// Query to run
$sql = "SELECT SYSDATETIMEOFFSET() AS now_offset, DB_NAME() AS db_name, ORIGINAL_LOGIN() AS orig_login";

// Run query using the successful connection
if ($pdo !== null) {
    try {
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "PDO result:\n";
        print_r($row);
    } catch (PDOException $e) {
        echo "PDO query failed: " . $e->getMessage() . "\n";
    }
}

if ($sqlsrvConn !== null) {
    $stmt = sqlsrv_query($sqlsrvConn, $sql);
    if ($stmt === false) {
        echo "sqlsrv_query failed:\n";
        var_dump(sqlsrv_errors());
    } else {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo "sqlsrv result:\n";
        print_r($row);
    }
}

?>