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

$query = "SELECT * FROM [dbo].[sec_edgar]";
$stmt = $conn->prepare($query);
$stmt->execute();

// Fetching data
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generating HTML table
if ($results) {
    echo "<table border='1'>";
    echo "<tr>";
    foreach ($results[0] as $key => $value) {
        echo "<th>" . htmlspecialchars($key) . "</th>";
    }
    echo "</tr>";

    foreach ($results as $row) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "danields", "pwd" => "f2qV6WaOk@E64mKk", "Database" => "free-sql-db-0070009", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:danields-first-db.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>