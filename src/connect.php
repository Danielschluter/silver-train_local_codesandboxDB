<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:danields-first-db.database.windows.net,1433; Database = free-sql-db-0070009", "danields", "f2qV6WaOk@E64mKk");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "danields", "pwd" => "f2qV6WaOk@E64mKk", "Database" => "free-sql-db-0070009", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:danields-first-db.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>

<?php

// Note: This file contains sensitive information. Do not share it publicly.

// Query
$sql = "SELECT SYSDATETIMEOFFSET(), DB_NAME(), ORIGINAL_LOGIN();";

// Execute the query using PDO
try {
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}

// Execute the query using sqlsrv extension
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
print_r($row);
?>