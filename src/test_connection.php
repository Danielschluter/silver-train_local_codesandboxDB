<?php
require_once __DIR__ . '/db.php';

// Simple test script — run from the project root: php src/test_connection.php
echo "Testing DB connection\n";
 $db = get_db();

try {
    if (is_array($db) && isset($db['type']) && $db['type'] === 'sqlsrv') {
        // Procedural sqlsrv connection fallback
        $conn = $db['conn'];
        $sql = 'SELECT 1 AS test';
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            throw new Exception('sqlsrv_query failed: ' . json_encode($errors));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo "Query result: ";
        print_r($row);
        echo "\nConnection OK\n";
    } else {
        // PDO connection
        $pdo = $db;
        $stmt = $pdo->query('SELECT 1 AS test');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Query result: ";
        print_r($row);
        echo "\nConnection OK\n";
    }
} catch (Exception $e) {
    echo "Test query failed: " . $e->getMessage() . "\n";
    exit(1);
}
