<?php
require 'db.php';
$pdo = get_db();

// 1. Initialize variables
$version = '';
$results = [];
$error = null;
$message = null;

// 2. Fetch Database Version (Keep original logic)
try {
    $stmt = $pdo->query('SELECT version()');
    $version = $stmt->fetchColumn();
} catch (Exception $e) {
    $version = 'Query failed: ' . $e->getMessage();
}

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sql'])) {
    try {
        // Execute the raw SQL provided by the user
        $stmt = $pdo->query($_POST['sql']);

        // Check if the query returns data (like SELECT)
        if ($stmt->columnCount() > 0) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // For INSERT, UPDATE, DELETE, DROP, etc.
            $message = "Query executed successfully. Affected rows: " . $stmt->rowCount();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>PHP + PostgreSQL</title>
    <style>
        /* Basic styling for readability */
        textarea { width: 100%; height: 100px; margin-bottom: 10px; font-family: monospace; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; background: #ffeeee; padding: 10px; border: 1px solid red; }
        .success { color: green; background: #eeffee; padding: 10px; border: 1px solid green; }
    </style>
  </head>
  <body>
    <h1>PHP + PostgreSQL</h1>
    <p>Postgres version: <?php echo htmlspecialchars($version); ?></p>

    <!-- SQL Input Form -->
    <form method="POST">
        <label for="sql"><strong>Enter SQL Query:</strong></label><br>
        <textarea name="sql" id="sql" placeholder="SELECT * FROM ..."><?php echo isset($_POST['sql']) ? htmlspecialchars($_POST['sql']) : ''; ?></textarea>
        <br>
        <button type="submit">Run Query</button>
    </form>

    <!-- Results Display -->
    <?php if ($error): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <!-- Dynamically generate headers from array keys -->
                    <?php foreach (array_keys($results[0]) as $header): ?>
                        <th><?php echo htmlspecialchars($header); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through data rows -->
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?php echo htmlspecialchars($cell !== null ? $cell : 'NULL'); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>


  </body>
</html>